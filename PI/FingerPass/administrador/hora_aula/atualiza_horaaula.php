<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Editar Horário Aula</title>
    <link rel="icon" type="image/png" href="../../../img/FP006.png">
    <link rel="stylesheet" href="../../../style/cadastrar.css">
    <link href="https://fonts.googleapis.com/css2?family=Jura:wght@400;500;600&family=Changa:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>

<?php
include('../../conexao/conexao.php');

// Coleta os dados do formulário
$id_horario = $_POST['id_horario'];
$dia_semana = $_POST['dia_semana'];
$hora_inicio = $_POST['hora_inicio'];
$hora_fim = $_POST['hora_fim'];

// Monta o SQL de atualização
$sql = "UPDATE horario_aula 
        SET dia_semana = '$dia_semana',
            hora_inicio = '$hora_inicio',
            hora_fim = '$hora_fim'
        WHERE id_horario = $id_horario";

$ret = mysqli_query($id, $sql);

// Verifica resultado
if ($ret) {
    echo "<script>
        alert('Horário atualizado com sucesso!');
        window.location.href='listar_horaaula.php';
    </script>";
} else {
    echo "<script>
        alert('Erro ao atualizar o horário!');
        window.location.href='listar_horaaula.php';
    </script>";
}
?>

</body>
</html>
