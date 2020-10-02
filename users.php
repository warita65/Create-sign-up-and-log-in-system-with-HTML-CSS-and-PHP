<?php

class User {
        protected $pdo;
    
        function __construct($pdo){
            $this->pdo = $pdo;
        }

    public function checkInput($variable){
        $variable = htmlspecialchars($variable);
        $varriable = trim($variable);
        $variable = stripslashes($variable);
        return $variable;
        }
  //function too check if the email is already in use cap17
    public function checkEmail($email_mobile){
        $stmt = $this->pdo->prepare("SELECT email FROM users WHERE email = :email");
        $stmt->bindParam(":email", $email_mobile, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->rowCount();
            if($count>0){
                return true;
            }else{
                return false;
            }
        }
    // create a method with two parameters the table and the array
    public function create($table, $fields=array() ){
        $columns = implode(',', array_keys($fields));
        //array keys will take all the columns 
        $values = ':'.implode(', :' , array_keys($fields));
        //example print
        //: first-name, :last-name, :mobil
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$values})"; 

            if($stmt = $this->pdo->prepare($sql)){
                foreach($fields as $key => $data){
                    $stmt->bindValue(':'.$key, $data);
                }
                $stmt->execute();
                return $this->pdo->lastInsertId();
            }
        }
}
    

?>
