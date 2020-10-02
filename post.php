<?php

class Post extends User {
    //the pdo parameter comes form connection.php
    function __construct($pdo){
        //this makes the protected pdo from users be workable in this page
        $this->pdo = $pdo;
    }
}


?>