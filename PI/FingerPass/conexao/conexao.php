<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conexão</title>
</head>

<body>
    

<?php

    $dbname = "bd_biometria_tcc";

    if (!/*Falso*/($id = mysqli_connect("localhost","root"))){
        echo "<h1>Não foi possível estabelecer uma conexão com o gerenciador MySQL.</h1>";
        exit;
    }

    if (!/*Falso*/($con = mysqli_select_db($id, $dbname))){
        echo "<h1>Não foi possível estabelecer uma conexão com o banco de dados $dbname.</h1>";
        exit;
    }

?>

</body>
</html>