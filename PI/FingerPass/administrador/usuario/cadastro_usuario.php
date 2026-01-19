<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Cadastrar Usuário</title>
    <link rel="icon" type="image/png" href="../../../img/FP006.png">
    <link rel="stylesheet" href="../../../style/cadastrar.css">
    <link href="https://fonts.googleapis.com/css2?family=Jura:wght@400;500;600&family=Changa:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>

<?php
include('../../conexao/conexao.php');
mysqli_report(MYSQLI_REPORT_OFF); //Evitar erro fatal

$email = $_POST['email'];
$senha = $_POST['senha'];
$tipo  = $_POST['tipo'];

// Validação de email no servidor
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Email inválido!'); window.history.back();</script>";
    exit;
}

$sql = "INSERT INTO usuario (email, senha, tipo)
        VALUES ('" . $email . "', '" . md5($senha) . "', '" . $tipo . "')";

$ret = mysqli_query($id, $sql);

if ($ret) {
    //echo "Usuário cadastrado com sucesso!";
    echo "<script>alert('Usuário cadastrado com sucesso!'); window.location.href='listar_usuario.php';</script>";
    //echo "<script>alert('Usuário cadastrado com sucesso!'); window.history.back();</script>";
} else {
    if (mysqli_errno($id) == 1062) {
        echo "<script>alert('E-mail já está cadastrado!'); window.location.href='listar_usuario.php';</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar.'); window.location.href='listar_usuario.php';</script>";
    }
}
?>

</body>
</html>
