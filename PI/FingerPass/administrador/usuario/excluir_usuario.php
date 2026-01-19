<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Excluir Usuário</title>
    <link rel="icon" type="image/png" href="../../../img/FP006.png">
    <link rel="stylesheet" href="../../../style/cadastrar.css">
    <link href="https://fonts.googleapis.com/css2?family=Jura:wght@400;500;600&family=Changa:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>

<?php
include('../../conexao/conexao.php');

$id_usuario = $_GET['id_usuario'];

// ⚠️ Proteção extra: impede que o próprio administrador se exclua acidentalmente
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$usuario_logado = $_SESSION['usuario'] ?? null;

// Busca o e-mail do usuário que será excluído
$sql_busca = "SELECT email FROM usuario WHERE id_usuario = $id_usuario";
$res_busca = mysqli_query($id, $sql_busca);
$dados = mysqli_fetch_assoc($res_busca);

if ($usuario_logado && isset($dados['email']) && $dados['email'] === $usuario_logado) {
    echo "<script>
        alert('Você não pode excluir o usuário que está logado no momento.');
        window.location.href='listar_usuario.php';
    </script>";
    exit;
}

// 🚮 Executa a exclusão
$sql = "DELETE FROM usuario WHERE id_usuario = $id_usuario";
$res = mysqli_query($id, $sql);

if ($res) {
    echo "<script>
        alert('Usuário excluído com sucesso!');
        window.location.href='listar_usuario.php';
    </script>";
} else {
    echo "<script>
        alert('Erro ao excluir o usuário!');
        window.location.href='listar_usuario.php';
    </script>";
}
?>

</body>
</html>
