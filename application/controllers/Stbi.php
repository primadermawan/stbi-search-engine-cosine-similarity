<?php
class Stbi extends CI_Controller
{
    function index(){
        
        $this->load->view('stbi_view');
    }

    function search()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('keyword', 'Keyword', 'required');
        if (!$this->form_validation->run()) {
            $this->index();
        } else {
            $start_time = microtime(true);
            $sum_sqr_tfidf_query = 0;
            $keyword = $this->input->post('keyword');
            require_once '/var/www/html/vendor/autoload.php';
            // cukup dijalankan sekali saja, biasanya didaftarkan di service container
            $stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
            $stopWordRemoverFactory = new \Sastrawi\StopWordRemover\StopWordRemoverFactory();
            $stemmer  = $stemmerFactory->createStemmer();
            $stopword = $stopWordRemoverFactory->createStopWordRemover();
            $stemmed_keyword = $stemmer->stem($keyword);
            $stemmed_keyword = $stopword->remove($keyword);
            //start with the perhitungan cosine similarity
            $stemmed_keyword_array = explode(" ", $stemmed_keyword);
            $array_keyword_value = array_count_values($stemmed_keyword_array);
            $all_document = $this->stbi_model->simple_select(array('*'), 'document_vector', array());
            foreach ($array_keyword_value as $key => $value) {
                $tfidf_token_document = $this->stbi_model->simple_select(array('*'), 'tfidf_table', array('token'=>$key))->row();
                // $tfidf_token_document = $tfidf_token_document1->row();
                
                if ($tfidf_token_document) {
                    $tfidf_query[$key] = $value * $tfidf_token_document->idf;
                    $sqr_tfidf_query = pow($tfidf_query[$key], 2);
                    $sum_sqr_tfidf_query = $sum_sqr_tfidf_query + $sqr_tfidf_query;
                    // $tfidf_query_array[$key] = $tfidf_query;
                } 
                
            }
            $query_vector = sqrt($sum_sqr_tfidf_query);
            foreach ($all_document->result() as $key => $value) {
                $y=0;
                // $vec_doc = $this->stbi_model->simple_select(array('*'), 'document_vector', array('documentID'=>$value->documentID))->row();
                $penyebut = $value->vektor*$query_vector;
                foreach ($array_keyword_value as $key1 => $value1) {
                    
                    $tfidf_doc_query = $this->stbi_model->simple_select(array('token', 'documentID', 'tfidf'), 'tfidf_table', array('token'=>$key1, 'documentID'=>$value->documentID))->row();
                    if ($tfidf_doc_query) {
                        $x = $tfidf_query[$key1] * $tfidf_doc_query->tfidf;
                        
                    } else {
                        $x = 0;
                    }
                    $y = $y+$x;
                    
                }
                if ($penyebut != 0) {
                    $cosine[$value->documentID] = $y/$penyebut;
                } else {
                    $cosine[$value->documentID] = 0;
                }
            }
            
            arsort($cosine);
            $end_time = microtime(true);
            $exec = ($end_time - $start_time);
            foreach ($cosine as $key => $value) {
                if ($value>0) {
                    $retrieval[$key] = $this->stbi_model->extract_retrieval($key)->row_array();
                    $retrieval[$key]['cosine'] = $value;
                }
            }
            
            if (isset($retrieval)) {
                $data['hasil'] = $retrieval;
            } else {
                $data['hasil'] = NULL;
            }
            
            $data['exec_time'] = $exec;
            $data['keyword'] = $keyword;
            $data['first'] = true;
            $this->load->view('stbi_view', $data);
        }
    }

    function see_one_result($idDoc)
    {
        $document = $this->stbi_model->extract_retrieval($idDoc)->row();
        if ($document->is_image == 0) {
            $image_address = $document->raw_name.".jpg";
            $rtf_address = $document->full_path;
        } else {
            $image_address = $document->file_name;
            $rtf_address_buf = $this->stbi_model->simple_select(array('*'), 'document', array('file_name'=>$document->raw_name.".rtf"));
            if ($rtf_address_buf->row_num()>0) {
                $rtf_address = $rtf_address_buf->row_array();
            } else {
                $rtf_address = NULL;
            }
        }
    }
}