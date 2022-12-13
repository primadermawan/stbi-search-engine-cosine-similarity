<?php

class Stbi_admin extends CI_Controller
{
    function index()
    {
        $x = $this->stbi_model->simple_select(array('*'), 'tutorial', array());
        $data['content'] = $x->result_array();
        $this->load->view('stbi_admin_dash', $data);
    }

    function make_tutorial()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('nama', 'Tutorial Name', 'required');
        if (!$this->form_validation->run()) {
            $this->index();
        } else {
            $prev_id = $this->stbi_model->simple_select(array('*'), 'tutorial', array());
            if ($prev_id->num_rows() > 0) {
                $last = $prev_id->last_row();
                $next_id = $last->id + 1;
            } else {
                $next_id = 1;
            }

            $data['data'] = array(
                'id' => $next_id,
                'tutorial_name' => $this->input->post('nama')
            );
            $data['table'] = 'tutorial';
            $tutor = $this->stbi_model->insert($data);
            $this->see_tutor($next_id);
        }
    }

    function see_tutor($id)
    {
        $tutor_data['tut_name'] = $this->stbi_model->simple_select(array('*'), 'tutorial', array('id'=>$id))->row_array();
        $tutor_data['extract_doc'] = $this->stbi_model->extract_doc($id)->result_array();
        // var_dump($tutor_data);
        $this->load->view('tut_single', $tutor_data);
    }

    function upload_doc($id)
    {
        $config = array(
            'upload_path' => './assets/tut_files',
            'allowed_types'=>'txt|rtf|jpg|jpeg',
            'max_size' => 2000
        );
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('txt')) {
            $data['error'] = $this->upload->display_errors();
        } else {
            $address['txt'] = $this->upload->data();
        }
        $count_jpg = $_FILES['jpg']['name'];
        $count_rtf = $_FILES['rtf']['name'];
        
        if (isset($count_jpg)&&count($count_jpg)>0) {
            foreach ($count_jpg as $key => $value) {
                $_FILES['jpg_new']['name'] = $_FILES['jpg']['name'][$key];
                $_FILES['jpg_new']['type'] = $_FILES['jpg']['type'][$key];
                $_FILES['jpg_new']['tmp_name'] = $_FILES['jpg']['tmp_name'][$key];
                $_FILES['jpg_new']['error'] = $_FILES['jpg']['error'][$key];
                $_FILES['jpg_new']['size'] = $_FILES['jpg']['size'][$key];
                if (!$this->upload->do_upload('jpg_new')) {
                    $data['error'][$key] = $this->upload->display_errors();
                } else {
                    $address['jpg'][$key] = $this->upload->data();
                }
            }
        } else{
            $data['error'] = "You must upload jpg file";
        }
        
        if (isset($count_rtf)&&count($count_rtf)>0 && $count_rtf[0]!= "") {
            foreach ($count_rtf as $key => $value) {
                $_FILES['rtf_new']['name'] = $_FILES['rtf']['name'][$key];
                $_FILES['rtf_new']['type'] = $_FILES['rtf']['type'][$key];
                $_FILES['rtf_new']['tmp_name'] = $_FILES['rtf']['tmp_name'][$key];
                $_FILES['rtf_new']['error'] = $_FILES['rtf']['error'][$key];
                $_FILES['rtf_new']['size'] = $_FILES['rtf']['size'][$key];
                if (!$this->upload->do_upload('rtf_new')) {
                    $data['error'][$key] = $this->upload->display_errors();
                } else {
                    $address['rtf'][$key] = $this->upload->data();
                }
            }
        }
        

        if (isset($data)) {
            foreach ($data as $key => $value) {
                var_dump($value);
                echo "<br>";
                var_dump(($count_rtf));
            };
        } else {
            //inserting metadatas to table
            $insert['table'] = 'document';
            $insert['data'] = $address['txt'];
            $first = $this->stbi_model->insert($insert);
            $second = $this->stbi_model->insert_batch($address['jpg'], $insert['table']);
            if (isset($count_rtf)&&count($count_rtf)>1) {
                $third = $this->stbi_model->insert_batch($address['rtf'], $insert['table']);
            } else {
                $third = 0;
            }
            

            //inserting tutorial id paired to document id
            for ($i=$first; $i<=$second+$third+$first ; $i++) { 
                $pair_data[] = array(
                    'id_tut'=>$id,
                    'id_doc'=>$i
                );
            }

            $this->stbi_model->insert_batch($pair_data, 'tut_doc');

            //Tokenisasi
            $this->tokenize($address['txt'], $id);
            // var_dump($address['txt']);
        }       

    }

    function tokenize($address, $id)
    {
        $q = 0;
        // $datum = $this->stbi_model->simple_select(array('*'), 'document', array('file_ext'=>".txt"))->row_array();
        $file = file($address['full_path']);
        // include composer autoloader
        require_once '/var/www/html/vendor/autoload.php';
        // cukup dijalankan sekali saja, biasanya didaftarkan di service container
        $stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
        $stemmer  = $stemmerFactory->createStemmer();
        //Start of stemming the jpg file name
        foreach ($file as $key => $value) {
            if ($this->rem_unknown_char($value) == "-") {
                $document[] = $key;
            }
            if ($this->rem_unknown_char($value) == ".") {
                $end_of_document = $key;
            }
        }
        foreach ($document as $key => $value) {
            $jpg = str_split($this->rem_unknown_char($file[$value+1]));
            $jpg_count = count($jpg);
            if ($jpg[$jpg_count-4] == "." && $jpg[$jpg_count-1] == "g" && $jpg[$jpg_count-2] == "p" && $jpg[$jpg_count-3] == "j") {
                $dash = array_search("\\", $jpg);
                for ($i=$dash+1; $i < $jpg_count-4; $i++) { 
                    $new_jpg[] = $jpg[$i];
                }
                $implode_new_jpg = implode($new_jpg);
                $stemmed_jpg = $stemmer->stem($implode_new_jpg);
            }
            $id_doc = $this->stbi_model->simple_select(array('*'), 'document', array('client_name'=>$implode_new_jpg.'.jpg'))->row();
            
            //in attemp to search the rtf file later
            $array_implode_new_jpg[] = implode($new_jpg);
            $token_stemmed_jpg = explode(' ', $stemmed_jpg);
            foreach ($token_stemmed_jpg as $key1 => $value1) {
                $data[$q] = array(
                    'token' => $value1,
                    'documentID' => $id_doc->docID,
                    'x_axis' => $this->rem_unknown_char($file[$value+8]),
                    'y_axis' => $this->rem_unknown_char($file[$value+9])
                );
                $q++;
            }
            //making new array with the grouping of jpg document
            if (isset($document[$key+1])) {
                for ($i=$value; $i <= $document[$key+1]; $i++) { 
                    $one_label[$id_doc->docID][$i] = $file[$i];
                    
                   
                }
                
            } elseif($key == count($document)-1) {
                for ($i=$value; $i <= $end_of_document; $i++) { 
                    $one_label[$id_doc->docID][$i] = $file[$i];
                    
                   
                }
                
            }
            unset($jpg);
            unset($new_jpg);
            unset($id_doc);
        }
        //in attempt to detect the labels
        foreach ($one_label as $key => $value) {
            $l = 0;
            $array_first = array_key_first($value);
            $array_last = array_key_last($value);
            foreach ($value as $key1 => $value1) {
                
                if ($this->rem_unknown_char($value1) == ";-") {
                    $semicolon[$key][$l]['first'] = $key1;
                    
                }
                elseif ($this->rem_unknown_char($value1) == ";" && $key1 != $array_first+2) {
                    $semicolon[$key][$l]['second'] = $key1;
                } elseif ($this->rem_unknown_char($value1) == "-" && $key1 != $array_first) {
                    $semicolon[$key][$l]['second'] = $key1;
                }  elseif ($this->rem_unknown_char($value1) == "." && $key1 != $array_first) {
                    $semicolon[$key][$l]['second'] = $key1;
                }
                if (isset($semicolon[$key][$l]['second'])) {
                    $l++;
                }
            }
        }
        //making the array that to be inserted to database
        foreach ($semicolon as $key2 => $value2) {
            foreach ($value2 as $key3 => $value3) {
                for ($k=$value3['first']+1; $k < $value3['second']; $k++) { 
                    $stemmed_label = $stemmer->stem($file[$k]);

                    $exploded_stemmed_label = explode(" ",$stemmed_label);
                    // print_r($exploded_stemmed_label);echo "<br>";
                    foreach ($exploded_stemmed_label as $key4 => $value4) {
                        $data[$q] = array(
                            'token' =>$value4,
                            'documentID'=>$key2,
                            'x_axis'=>$this->rem_unknown_char($file[$value3['first']-4]),
                            'y_axis'=>$this->rem_unknown_char($file[$value3['first']-3]),
                        );
                        $q++;
                    }    
                }   
            }
        }

        //atempting to receive the rtf file, stemm it, then insert it to array
        foreach ($array_implode_new_jpg as $key => $value) {
            $rtf = $this->stbi_model->simple_select(array('docID', 'full_path'), 'document', array('client_name'=>$value.".rtf"))->row();
            if (!is_null($rtf)) {
                $rt_file_path = file($rtf->full_path);
                $document_rtf = new Jstewmc\Rtf\Document($rtf->full_path);
                $rtf_text = $document_rtf->write('text');
                $stemmed_rtf_text = $stemmer->stem($rtf_text);
                $exploded_stemmed_rtf_text = explode(' ', $stemmed_rtf_text);
                foreach ($exploded_stemmed_rtf_text as $key1 => $value1) {
                    $data[$q] = array(
                        'token'=>$value1,
                        'documentID'=>$rtf->docID,
                        'x_axis'=>NULL,
                        'y_axis'=>NULL
                    );
                    $q++;
                }
            }
        }
        if ($this->stbi_model->insert_batch($data, 'inverted_index')) {
            // $this->make_tfidf();
            redirect(site_url('stbi_admin/see_tutor/').$id);
        } else {
            echo "inverted index ";
        }
    }

    function rem_unknown_char($word)
    {
        $words = str_split($word);
        $hitung = count($words);
        for ($i=0; $i < $hitung-2; $i++) { 
            $new_words[$i] = $words[$i];
        }
        
        return(implode($new_words));
    }

}