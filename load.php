<?php  
// make all files accesible in this file
include 'database/connection.php';
include 'classes/users.php';
include 'classes/post.php';
// declare a global variable pdo
global $pdo;

//create object for the classes
$loadFromUser = new User($pdo);
$loadFromPost = new Post($pdo);

//define base url
define("BASE_URL", "http://localhost/Rutgers");


?>