<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Cadastrar Horário da Turma</title>
    <link rel="icon" type="image/png" href="../../../img/FP006.png">
    <link rel="stylesheet" href="../../../style/cadastrar.css">
    <link href="https://fonts.googleapis.com/css2?family=Jura:wght@400;500;600&family=Changa:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>

<?php
include('../../conexao/conexao.php');
mysqli_report(MYSQLI_REPORT_OFF); // evita warnings fatais

$id_turma = $_POST['id_turma'];
$id_horario = $_POST['id_horario'];

// 🔍 Verifica se o vínculo já existe
$sql_verifica = "SELECT COUNT(*) AS qtd 
                 FROM hora_turma 
                 WHERE id_turma = '$id_turma' AND id_horario = '$id_horario'";
$res_verifica = mysqli_query($id, $sql_verifica);
$dados = mysqli_fetch_assoc($res_verifica);

if ($dados['qtd'] > 0) {
    echo "<script>
        alert('Esta turma já possui este horário cadastrado!');
        window.location.href='listar_horaturma.php';
    </script>";
    exit;
}

// 🧾 Insere o vínculo entre turma e horário
$sql = "INSERT INTO hora_turma (id_turma, id_horario)
        VALUES ('$id_turma', '$id_horario')";

$ret = mysqli_query($id, $sql);

// 🟢 Resultado da operação
if ($ret) {
    echo "<script>
        alert('Horário vinculado à turma com sucesso!');
        window.location.href='listar_horaturma.php';
    </script>";
} else {
    $erro = addslashes(mysqli_error($id));
    echo "<script>
        alert('Erro ao cadastrar horário! Detalhes: $erro');
        window.location.href='listar_horaturma.php';
    </script>";
}
?>

</body>
</html>
