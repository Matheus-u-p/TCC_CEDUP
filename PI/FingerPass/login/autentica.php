<?php
include('../conexao/conexao.php');

if (empty($_POST['email']) || empty($_POST['senha'])) {
    header("Location: tela_de_login.php?erro=campos");
    exit;
}

$senha = md5($_POST['senha']);
$email = $_POST['email'];

$sql = "SELECT * FROM usuario WHERE email = '$email' AND senha = '$senha'";
$res = mysqli_query($id, $sql);
$linha = mysqli_fetch_array($res);

if ($linha) {
    session_start();
    $_SESSION['usuario'] = $linha['email'];
    $_SESSION['tipo'] = $linha['tipo']; // 'Administrador' ou 'Professor'

    if ($linha['tipo'] == 'Administrador') {
        $destino = "../administrador/home/tela_inicial_admin.php";
    } elseif ($linha['tipo'] == 'Professor') {
        $destino = "../professor/home/tela_inicial_prof.php";
    } else {
        header("Location: tela_de_login.php?erro=tipo");
        exit;
    }

    // Redireciona para a tela de login com msg de sucesso + destino
    header("Location: tela_de_login.php?sucesso=1&destino=" . urlencode($destino));
    exit;
} else {
    header("Location: tela_de_login.php?erro=login");
    exit;
}

?>