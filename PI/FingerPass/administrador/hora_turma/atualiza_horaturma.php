<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Editar Horário da Turma</title>
    <link rel="icon" type="image/png" href="../../../img/FP006.png">
    <link rel="stylesheet" href="../../../style/cadastrar.css">
    <link href="https://fonts.googleapis.com/css2?family=Jura:wght@400;500;600&family=Changa:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>

<?php
include('../../conexao/conexao.php');

// Recebe os dados do formulário
$id_hora_turma = $_POST['id_hora_turma'];
$id_turma = $_POST['id_turma'];
$id_horario = $_POST['id_horario'];

// Verifica se a combinação já existe (evita duplicação)
$sql_verifica = "SELECT COUNT(*) AS qtd 
                 FROM hora_turma 
                 WHERE id_turma = '$id_turma' 
                 AND id_horario = '$id_horario' 
                 AND id_hora_turma <> '$id_hora_turma'";
$res_verifica = mysqli_query($id, $sql_verifica);
$dados = mysqli_fetch_assoc($res_verifica);

if ($dados['qtd'] > 0) {
    echo "<script>
        alert('Esta combinação de turma e horário já existe!');
        window.location.href='listar_horaturma.php';
    </script>";
    exit;
}

// Atualiza os dados da relação
$sql = "UPDATE hora_turma 
        SET id_turma = '$id_turma', 
            id_horario = '$id_horario'
        WHERE id_hora_turma = $id_hora_turma";

$ret = mysqli_query($id, $sql);

if ($ret) {
    echo "<script>
        alert('Horário da turma atualizado com sucesso!');
        window.location.href='listar_horaturma.php';
    </script>";
} else {
    $erro = addslashes(mysqli_error($id));
    echo "<script>
        alert('Erro ao atualizar o horário da turma! Detalhes: $erro');
        window.location.href='listar_horaturma.php';
    </script>";
}
?>

</body>
</html>
