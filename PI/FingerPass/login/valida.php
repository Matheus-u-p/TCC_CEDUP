<?php
session_start();

if(!isset($_SESSION ['usuario'])){
    header("Location: tela_de_login.php");
    exit;
}

?>