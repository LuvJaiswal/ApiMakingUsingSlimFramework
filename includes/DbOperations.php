<?php

class DbOperations{

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
           return USER_FAILURE;
        }

        }
        return USER_EXISTS;
    }

    public function userLogin($email, $password){
        if($this->isEmailExist($email)){
            $hashed_password = $this->getUsersPasswordByEmail($email);
            if(password_verify($password, $hashed_password)){

                return USER_AUTHENTICATED;

            }else{

                return USER_PASSWORD_DO_NOT_MATCH;
            }

        }else{
            return USER_NOT_FOUND;
        }

    }

    private function getUsersPasswordByEmail($email){
        $stmt = $this->con->prepare("SELECT password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($password);
        $stmt->fetch();
        return $password;
        
    }

    public function getUserByEmail($email){
        $stmt = $this->con->prepare("SELECT id, email, name FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($id, $email, $name);
        $stmt->fetch();
        $user = array();
        $user['id'] = $id;
        $user['email'] =$email;
        $user['password'] =$name;
        return $user;
    
    }
    
        private function isEmailExist($email){
            $stmt = $this->con->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s",$email);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        }

}
