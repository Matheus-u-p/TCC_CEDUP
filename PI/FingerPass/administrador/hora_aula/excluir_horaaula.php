<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Excluir Horário de Aula</title>
    <link rel="icon" type="image/png" href="../../../img/FP006.png">
    <link rel="stylesheet" href="../../../style/geral.css">
</head>

<body>

<?php
include('../../conexao/conexao.php');

// ===============================
// ⚙️ 1. Verifica se o ID foi passado corretamente
// ===============================
if (!isset($_GET['id_horario']) || !is_numeric($_GET['id_horario'])) {
    echo "<script>
        alert('ID inválido!');
        window.location.href='listar_horaaula.php';
    </script>";
    exit;
}

$id_horario = intval($_GET['id_horario']);

// ===============================
// 🔍 2. Verifica vínculos existentes
// ===============================

// Verifica se este horário está vinculado a alguma turma (tabela hora_turma)
$sql_vinculo = "SELECT COUNT(*) AS qtd FROM hora_turma WHERE id_horario = $id_horario";
$res_vinculo = mysqli_query($id, $sql_vinculo);
$dados_vinculo = mysqli_fetch_assoc($res_vinculo);

// ===============================
// ⚠️ 3. Impede exclusão se houver vínculos
// ===============================
if ($dados_vinculo['qtd'] > 0) {
    echo "<script>
        alert('Não é possível excluir este horário, pois ele está vinculado a uma ou mais turmas.');
        window.location.href='listar_horaaula.php';
    </script>";
    exit;
}

// ===============================
// 🧹 4. Se não houver vínculos, exclui o registro
// ===============================
$sql = "DELETE FROM horario_aula WHERE id_horario = $id_horario";
$res = mysqli_query($id, $sql);

if ($res) {
    echo "<script>
        alert('Horário excluído com sucesso!');
        window.location.href='listar_horaaula.php';
    </script>";
} else {
    // Em caso de erro inesperado
    $erro = mysqli_error($id);
    echo "<script>
        alert('Erro ao excluir horário! Detalhes: " . addslashes($erro) . "');
        window.location.href='listar_horaaula.php';
    </script>";
}
?>

</body>
</html>
