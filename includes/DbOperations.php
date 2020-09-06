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

    public function getAllUsers(){
        $stmt = $this->con->prepare("SELECT id, email, name FROM users;");
        $stmt->execute();
        $stmt->bind_result($id, $email, $name);
        $users = array();
       while($stmt->fetch()){
            $user = array();
            $user['id'] = $id;
            $user['email'] =$email;
            $user['name'] =$name;
           array_push($users, $user);
        }
        return $users;

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
        $user['name'] =$name;
        return $user;
    
    }


    public function updateUser($email, $name , $id){
        $stmt = $this->con->prepare("UPDATE users SET email = ?, name = ? WHERE id = ?");
        $stmt->bind_param("ssi", $email, $name, $id);
        if($stmt->execute())
        return true;
        return false;
    }

    public function updatePassword($currentpassword, $newpassword, $email){
        $hashed_password = $this->getUsersPasswordByEmail($email);
        if(password_verify($currentpassword, $hashed_password)){

            $hash_password = password_hash($newpassword, PASSWORD_DEFAULT);
        
            $stmt= $this->con->prepare("UPDATE users SET password = ? WHERE email = ?");

        
            $stmt->bind_param('ss',$hash_password, $email);

            if($stmt->execute())
            return PASSWORD_CHANGED;
            return PASSWORD_NOT_CHANGED;



        }else{
           return PASSWORD_DO_NOT_MATCH;

        }


    }

    public function deleteUser($id){
        $stmt = $this->con->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        if($stmt->execute())
            return true; 
        return false; 
    }

    
     private function isEmailExist($email){
            $stmt = $this->con->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s",$email);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        }




        public function addProducts($pname, $pdescription, $pprice){
            $stmt = $this->con->prepare("INSERT INTO products (pname, pdescription , pprice) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $pname, $pdescription, $pprice);  // 3 strings values as s s s
     
            if($stmt->execute()){
                return PRODUCT_ADDED;
     
             }else{
                return PRODUCT_ADD_FAILED;
             }
    
             return PRODUCT_EXISTS;
         }




}
