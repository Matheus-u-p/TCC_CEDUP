<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Cadastrar Turma</title>
    <link rel="icon" type="image/png" href="../../../img/FP006.png">
    <link rel="stylesheet" href="../../../style/cadastrar.css">
    <link href="https://fonts.googleapis.com/css2?family=Jura:wght@400;500;600&family=Changa:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>

<?php
include('../../conexao/conexao.php');
mysqli_report(MYSQLI_REPORT_OFF); // Evita erro fatal de mysqli

$n_turma = $_POST['n_turma'];
$turno = $_POST['turno'];
$contra_turno = isset($_POST['contra_turno']) ? $_POST['contra_turno'] : null;
$id_curso = $_POST['id_curso'];

// 🔍 Verifica se já existe uma turma com o mesmo número e curso
$sql_verifica = "SELECT COUNT(*) AS qtd FROM turma WHERE n_turma = '$n_turma' AND id_curso = '$id_curso'";
$res_verifica = mysqli_query($id, $sql_verifica);
$dados = mysqli_fetch_assoc($res_verifica);

if ($dados['qtd'] > 0) {
    echo "<script>
        alert('Já existe uma turma com este número neste curso!');
        window.location.href='listar_turma.php';
    </script>";
    exit;
}

// 🧾 Monta o SQL de inserção (tratando o campo opcional contra_turno)
$sql = "INSERT INTO turma (n_turma, turno, contra_turno, id_curso)
        VALUES ('$n_turma', '$turno', " . ($contra_turno ? "'$contra_turno'" : "NULL") . ", '$id_curso')";

$ret = mysqli_query($id, $sql);

// 🟢 Resultado da operação
if ($ret) {
    echo "<script>
        alert('Turma cadastrada com sucesso!');
        window.location.href='listar_turma.php';
    </script>";
} else {
    echo "<script>
        alert('Erro ao cadastrar turma!');
        window.location.href='listar_turma.php';
    </script>";
}
?>

</body>
</html>
