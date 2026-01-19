<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Excluir Curso</title>
    <link rel="icon" type="image/png" href="../../../img/FP006.png">
    <link rel="stylesheet" href="../../../style/geral.css">
</head>

<body>

<?php
include('../../conexao/conexao.php');

// Verifica se o ID foi passado corretamente
if (!isset($_GET['id_curso']) || !is_numeric($_GET['id_curso'])) {
    echo "<script>
        alert('ID inválido!');
        window.location.href='listar_curso.php';
    </script>";
    exit;
}

$id_curso = intval($_GET['id_curso']);

// ===============================
// 🔍 1. Verifica vínculos existentes
// ===============================

// Verifica se há turmas vinculadas a este curso
$sql_turma = "SELECT COUNT(*) AS qtd FROM turma WHERE id_curso = $id_curso";
$res_turma = mysqli_query($id, $sql_turma);
$dados_turma = mysqli_fetch_assoc($res_turma);

// ===============================
// ⚠️ 2. Impede exclusão se houver vínculos
// ===============================

if ($dados_turma['qtd'] > 0) {
    echo "<script>
        alert('Não é possível excluir este curso, pois existem turmas vinculadas a ele.');
        window.location.href='listar_curso.php';
    </script>";
    exit;
}

// ===============================
// 🧹 3. Se não houver vínculos, exclui
// ===============================

$sql = "DELETE FROM curso WHERE id_curso = $id_curso";
$res = mysqli_query($id, $sql);

if ($res) {
    echo "<script>
        alert('Curso excluído com sucesso!');
        window.location.href='listar_curso.php';
    </script>";
} else {
    // Em caso de falha inesperada
    $erro = mysqli_error($id);
    echo "<script>
        alert('Erro ao excluir curso! Detalhes: " . addslashes($erro) . "');
        window.location.href='listar_curso.php';
    </script>";
}
?>

</body>
</html>
