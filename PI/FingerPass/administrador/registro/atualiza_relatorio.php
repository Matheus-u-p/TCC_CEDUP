<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Atualizando</title>
    <link rel="icon" type="image/png" href="../../../img/FP006.png">
</head>

<body>

<?php
session_start();
include('../../conexao/conexao.php');

$id_registro = $_POST['id_registro'];
$presenca = $_POST['presenca'];
$hora_biometria = !empty($_POST['hora_biometria']) ? $_POST['hora_biometria'] : NULL;
$hora_saida = !empty($_POST['hora_saida']) ? $_POST['hora_saida'] : NULL;
$id_turma = $_POST['id_turma'];
$data_filtro = $_POST['data_filtro'];

// Validações
if ($presenca == 'F') {
    // Se faltou, limpa os horários
    $hora_biometria = NULL;
    $hora_saida = NULL;
}

// Monta o SQL com tratamento para valores nulos
$sql = "UPDATE registro_chamada 
        SET presenca = '$presenca',
            hora_biometria = " . ($hora_biometria ? "'$hora_biometria'" : "NULL") . ",
            hora_saida = " . ($hora_saida ? "'$hora_saida'" : "NULL") . "
        WHERE id_registro = $id_registro";

$ret = mysqli_query($id, $sql);

if ($ret) {
    echo "<script>
        alert('Registro atualizado com sucesso!');
        window.location.href='relatorio_turma.php?turma_id=$id_turma&data=$data_filtro';
    </script>";
} else {
    echo "<script>
        alert('Erro ao atualizar o registro: " . mysqli_error($id) . "');
        window.history.back();
    </script>";
}

mysqli_close($id);
?>

</body>
</html>