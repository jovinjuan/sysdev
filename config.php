<?php

session_start();

$username = 'root';
$password = '';
try{
    $conn = new PDO('mysql:host=localhost;dbname=gudang', $username, $password);
}
catch(PDOException $e){
}
function cekLogin(){
    if(isset($_SESSION['username'])){
        return true;
    } else {
        return false;
    }
}