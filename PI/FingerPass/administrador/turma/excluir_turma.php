<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Excluir Turma</title>
    <link rel="icon" type="image/png" href="../../../img/FP006.png">
    <link rel="stylesheet" href="../../../style/geral.css">
</head>

<body>

<?php
include('../../conexao/conexao.php');

// Verifica se o ID foi passado corretamente
if (!isset($_GET['id_turma']) || !is_numeric($_GET['id_turma'])) {
    echo "<script>
        alert('ID inválido!');
        window.location.href='listar_turma.php';
    </script>";
    exit;
}

$id_turma = intval($_GET['id_turma']);

// ===============================
// 🔍 1. Verifica vínculos existentes
// ===============================

// Verifica se há alunos vinculados
$sql_aluno = "SELECT COUNT(*) AS qtd FROM aluno WHERE id_turma = $id_turma";
$res_aluno = mysqli_query($id, $sql_aluno);
$dados_aluno = mysqli_fetch_assoc($res_aluno);

// Verifica se há horários vinculados
$sql_hora = "SELECT COUNT(*) AS qtd FROM hora_turma WHERE id_turma = $id_turma";
$res_hora = mysqli_query($id, $sql_hora);
$dados_hora = mysqli_fetch_assoc($res_hora);

// ===============================
// ⚠️ 2. Impede exclusão se houver vínculos
// ===============================

if ($dados_aluno['qtd'] > 0 && $dados_hora['qtd'] > 0) {
    echo "<script>
        alert('Não é possível excluir esta turma, pois existem alunos e horários vinculados a ela.');
        window.location.href='listar_turma.php';
    </script>";
    exit;
} elseif ($dados_aluno['qtd'] > 0) {
    echo "<script>
        alert('Não é possível excluir esta turma, pois existem alunos vinculados a ela.');
        window.location.href='listar_turma.php';
    </script>";
    exit;
} elseif ($dados_hora['qtd'] > 0) {
    echo "<script>
        alert('Não é possível excluir esta turma, pois existem horários vinculados a ela.');
        window.location.href='listar_turma.php';
    </script>";
    exit;
}

// ===============================
// 🧹 3. Se não houver vínculos, exclui
// ===============================

$sql = "DELETE FROM turma WHERE id_turma = $id_turma";
$res = mysqli_query($id, $sql);

if ($res) {
    echo "<script>
        alert('Turma excluída com sucesso!');
        window.location.href='listar_turma.php';
    </script>";
} else {
    // Em caso de falha inesperada
    $erro = mysqli_error($id);
    echo "<script>
        alert('Erro ao excluir turma! Detalhes: " . addslashes($erro) . "');
        window.location.href='listar_turma.php';
    </script>";
}
?>

</body>
</html>
