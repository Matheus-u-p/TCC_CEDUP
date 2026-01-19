<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Cadastrar Horário de Aula</title>
    <link rel="icon" type="image/png" href="../../../img/FP006.png">
    <link rel="stylesheet" href="../../../style/cadastrar.css">
    <link href="https://fonts.googleapis.com/css2?family=Jura:wght@400;500;600&family=Changa:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>

<?php
include('../../conexao/conexao.php');
mysqli_report(MYSQLI_REPORT_OFF); // evita erros fatais

$dia_semana = $_POST['dia_semana'];
$hora_inicio = $_POST['hora_inicio'];
$hora_fim = $_POST['hora_fim'];

// 🔍 Verifica se já existe um horário igual
$sql_verifica = "SELECT COUNT(*) AS qtd 
                 FROM horario_aula 
                 WHERE dia_semana = '$dia_semana' 
                 AND hora_inicio = '$hora_inicio' 
                 AND hora_fim = '$hora_fim'";
$res_verifica = mysqli_query($id, $sql_verifica);
$dados = mysqli_fetch_assoc($res_verifica);

if ($dados['qtd'] > 0) {
    echo "<script>
        alert('Já existe um horário com estas informações!');
        window.location.href='listar_horaaula.php';
    </script>";
    exit;
}

// 🧾 Inserção do novo horário
$sql = "INSERT INTO horario_aula (dia_semana, hora_inicio, hora_fim)
        VALUES ('$dia_semana', '$hora_inicio', '$hora_fim')";

$ret = mysqli_query($id, $sql);

// 🟢 Resultado da operação
if ($ret) {
    echo "<script>
        alert('Horário de aula cadastrado com sucesso!');
        window.location.href='listar_horaaula.php';
    </script>";
} else {
    echo "<script>
        alert('Erro ao cadastrar horário!');
        window.location.href='listar_horaaula.php';
    </script>";
}
?>

</body>
</html>
