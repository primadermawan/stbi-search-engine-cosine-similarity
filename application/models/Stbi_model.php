<?php

class Stbi_model extends CI_Model{
    function simple_select($select_column, $from, $where_clause)
    {
        foreach ($select_column as $select => $sel) {
            $this->db->select($sel);
        }
        $this->db->from($from);
        foreach ($where_clause as $where => $whe) {
            $this->db->where($where, $whe);
        }
        return $this->db->get();
    }

    function insert($data)
    {
        $this->db->insert($data['table'], $data['data']);
        return $this->db->insert_id();
    }

    function update($table, $data, $where)
    {
        return $this->db->update($table, $data, $where);
    }

    function insert_batch($data, $table)
    {
        return $this->db->insert_batch($table, $data);
    }

    function delete($table, $where)
    {
        $this->db->delete($table, $where);
    }

    function extract_doc($id)
    {
        $this->db->select('b.*');
        $this->db->from('tut_doc a');
        $this->db->join('document b', 'on a.id_doc = b.docID');
        $this->db->where('a.id_tut', $id);
        return $this->db->get();
        
    }

    function extract_tf_idf()
    {

        $query = "WITH total_doc AS
        (SELECT COUNT(DISTINCT documentID) as total FROM `inverted_index`),
        count_term AS
        (SELECT token, COUNT(id) as jumlah_kata FROM `inverted_index` GROUP BY token),
        idf AS
        (SELECT token, LOG(10, (total/jumlah_kata)) as idf FROM count_term, total_doc),
        tabel_master AS
        (SELECT token, documentID, COUNT(id) as tf FROM `inverted_index` GROUP BY token, documentID)
        SELECT a.token, a.documentID, a.tf, b.idf, (a.tf*b.idf) as tfidf FROM tabel_master a JOIN idf b ON a.token = b.token";
    }

    function vectorize_document()
    {
        $query = "SELECT documentID, sqrt(sum(power(tfidf, 2))) as vektor FROM `tfidf_table`  GROUP BY documentID";
        return $this->db->query($query);
    }

    function extract_retrieval($id_doc)
    {
        $this->db->select('a.*');
        $this->db->select('c.tutorial_name');
        $this->db->from('document a');
        $this->db->join('tut_doc b', 'on a.docID = b.id_doc');
        $this->db->join('tutorial c', 'on b.id_tut = c.id');
        $this->db->where('a.docID', $id_doc);
        return $this->db->get();
    }

}