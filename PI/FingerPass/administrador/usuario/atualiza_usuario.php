<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Editar Usuário</title>
    <link rel="icon" type="image/png" href="../../../img/FP006.png">
    <link rel="stylesheet" href="../../../style/cadastrar.css">
    <link href="https://fonts.googleapis.com/css2?family=Jura:wght@400;500;600&family=Changa:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>

<?php
include('../../conexao/conexao.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_usuario = $_POST['id_usuario'];
$email = trim($_POST['email']);
$senha = trim($_POST['senha']);
$tipo = $_POST['tipo'];

// Validação de email no servidor
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Email inválido!'); window.history.back();</script>";
    exit;
}

// ⚠️ Proteção extra: impede que o administrador perca privilégios alterando o próprio tipo
$usuario_logado = $_SESSION['usuario'] ?? null;
if ($usuario_logado && $usuario_logado === $email && $tipo !== 'Administrador') {
    echo "<script>
        alert('Você não pode alterar seu próprio tipo de usuário para não ser mais administrador.');
        window.location.href='listar_usuario.php';
    </script>";
    exit;
}

// 🧱 Atualiza os dados do usuário normalmente
$sql = "UPDATE usuario 
        SET email = '$email', 
            senha = '".md5($senha)."', 
            tipo = '$tipo'
        WHERE id_usuario = $id_usuario";

$ret = mysqli_query($id, $sql);

if ($ret) {
    echo "<script>
        alert('Usuário atualizado com sucesso!');
        window.location.href='listar_usuario.php';
    </script>";
} else {
    echo "<script>
        alert('Erro ao atualizar o usuário!');
        window.location.href='listar_usuario.php';
    </script>";
}
?>

</body>
</html>
