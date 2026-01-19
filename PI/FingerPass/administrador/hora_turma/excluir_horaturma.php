<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Excluir Horário da Turma</title>
    <link rel="icon" type="image/png" href="../../../img/FP006.png">
    <link rel="stylesheet" href="../../../style/geral.css">
</head>

<body>

<?php
include('../../conexao/conexao.php');

// Verifica se o ID foi passado corretamente
if (!isset($_GET['id_hora_turma']) || !is_numeric($_GET['id_hora_turma'])) {
    echo "<script>
        alert('ID inválido!');
        window.location.href='listar_horaturma.php';
    </script>";
    exit;
}

$id_hora_turma = intval($_GET['id_hora_turma']);

// ===============================
// 🔍 1. Verifica se o registro existe
// ===============================
$sql_verifica = "SELECT * FROM hora_turma WHERE id_hora_turma = $id_hora_turma";
$res_verifica = mysqli_query($id, $sql_verifica);

if (mysqli_num_rows($res_verifica) == 0) {
    echo "<script>
        alert('Registro não encontrado!');
        window.location.href='listar_horaturma.php';
    </script>";
    exit;
}

// ===============================
// 🧹 2. Exclui o vínculo da tabela hora_turma
// ===============================
$sql = "DELETE FROM hora_turma WHERE id_hora_turma = $id_hora_turma";
$res = mysqli_query($id, $sql);

// ===============================
// ✅ 3. Exibe o resultado
// ===============================
if ($res) {
    echo "<script>
        alert('Horário da turma excluído com sucesso!');
        window.location.href='listar_horaturma.php';
    </script>";
} else {
    $erro = addslashes(mysqli_error($id));
    echo "<script>
        alert('Erro ao excluir horário! Detalhes: $erro');
        window.location.href='listar_horaturma.php';
    </script>";
}
?>

</body>
</html>
