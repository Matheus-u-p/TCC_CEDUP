<?php
session_start();       // Inicia a sessão
session_unset();       // Limpa todas as variáveis da sessão
session_destroy();     // Encerra a sessão atual

// Redireciona de forma segura
header("Location: ../login/tela_de_login.php");
exit;
?>
