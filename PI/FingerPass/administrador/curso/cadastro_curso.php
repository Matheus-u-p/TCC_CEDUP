<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Cadastrar Curso</title>
    <link rel="icon" type="image/png" href="../../../img/FP006.png">
    <link rel="stylesheet" href="../../../style/cadastrar.css">
    <link href="https://fonts.googleapis.com/css2?family=Jura:wght@400;500;600&family=Changa:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>

<?php
include('../../conexao/conexao.php');
mysqli_report(MYSQLI_REPORT_OFF); // Evita erro fatal de mysqli

$nome = trim($_POST['nome']);

// 🔍 Verifica se o curso já existe
$sql_verifica = "SELECT COUNT(*) AS qtd FROM curso WHERE nome = '$nome'";
$res_verifica = mysqli_query($id, $sql_verifica);
$dados = mysqli_fetch_assoc($res_verifica);

if ($dados['qtd'] > 0) {
    echo "<script>
        alert('Já existe um curso com este nome!');
        window.location.href='listar_curso.php';
    </script>";
    exit;
}

// 🧾 Monta o SQL de inserção
$sql = "INSERT INTO curso (nome) VALUES ('$nome')";
$ret = mysqli_query($id, $sql);

// 🟢 Resultado da operação
if ($ret) {
    echo "<script>
        alert('Curso cadastrado com sucesso!');
        window.location.href='listar_curso.php';
    </script>";
} else {
    $erro = mysqli_error($id);
    echo "<script>
        alert('Erro ao cadastrar curso! Detalhes: " . addslashes($erro) . "');
        window.location.href='listar_curso.php';
    </script>";
}
?>

</body>
</html>
