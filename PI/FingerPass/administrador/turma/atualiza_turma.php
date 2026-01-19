<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Editar Turma</title>
    <link rel="icon" type="image/png" href="../../../img/FP006.png">
    <link rel="stylesheet" href="../../../style/cadastrar.css">
    <link href="https://fonts.googleapis.com/css2?family=Jura:wght@400;500;600&family=Changa:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>

<?php
include('../../conexao/conexao.php');

$id_turma = $_POST['id_turma'];
$n_turma = $_POST['n_turma'];
$turno = $_POST['turno'];
$contra_turno = isset($_POST['contra_turno']) ? $_POST['contra_turno'] : null;
$id_curso = $_POST['id_curso'];

// Monta o SQL com tratamento para contra_turno opcional
$sql = "UPDATE turma 
        SET n_turma = '$n_turma', 
            turno = '$turno', 
            contra_turno = " . ($contra_turno ? "'$contra_turno'" : "NULL") . ",
            id_curso = '$id_curso'
        WHERE id_turma = $id_turma";

$ret = mysqli_query($id, $sql);

if ($ret) {
    echo "<script>
        alert('Turma atualizada com sucesso!');
        window.location.href='listar_turma.php';
    </script>";
} else {
    echo "<script>
        alert('Erro ao atualizar a turma!');
        window.location.href='listar_turma.php';
    </script>";
}
?>

</body>
</html>
