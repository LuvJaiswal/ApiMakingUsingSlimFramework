<?php

class DbOpertaions{

    private $con;
    function __construct(){  //constructor made

        require_once dirname(__FILE__) . '/DbConnect.php';

        $db = new DbConnect;
        $this->con =$db->connect();



    }

    public function createUser($email, $password, $name){
       if(!$this->isEmailExist($email)){
       $stmt = $this->con->prepare("INSERT INTO users (email, password, name) VALUES (?, ?, ?)");
       $stmt->bind_param("sss", $email, $password, $name);  // 3 strings values as s s s

       if($stmt->execute()){
           return USER_CREATED;

        }else{
            USER_FAILURE;
        }

        }
        return USER_EXSISTS;

    }
    
        private function isEmailExist($email){
            $stmt = $this->con->preapre("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s",$email);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        }

}
