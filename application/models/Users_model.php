<?php 


class Users_model extends CI_Model {

    public $name;
    public $email;
    public $password;

    
    public function getUser($email)
    {
        $query = $this->db->get_where('users', ['email'=>$email]);
        return $query->result();
    }    

    public function add($request)
    {       
        $request = json_decode($request);
        $this->name = $request->name;
        $this->email = $request->email;
        $this->password = sha1($request->password);
        try{
            $this->db->insert('users', $this);
            $res['uid'] = $this->db->insert_id();
            return $res;
        }catch(Exception $e) {     
            return $e->getMessage();
        }

    }

}