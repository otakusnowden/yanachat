<?php 


class Chat_model extends CI_Model {

    
    public function getQuestions($query)
    {
        //questions y responses estan indexadas sus claves foraneas
        $sql = "SELECT q.id,q.question FROM questions q join responses r on q.id = r.qid WHERE q.question REGEXP ?";
        $res = $this->db->query($sql, array($query))->result();
        return $res;
    }


    public function getResponses($id)
    {
        $query = $this->db->get_where('responses', ['qid'=>$id]);
        return $query->result();
    }

}