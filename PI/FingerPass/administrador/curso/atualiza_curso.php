<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Editar Curso</title>
    <link rel="icon" type="image/png" href="../../../img/FP006.png">
    <link rel="stylesheet" href="../../../style/cadastrar.css">
    <link href="https://fonts.googleapis.com/css2?family=Jura:wght@400;500;600&family=Changa:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>

<?php
include('../../conexao/conexao.php');

$id_curso = $_POST['id_curso'];
$nome = trim($_POST['nome']);

// 🔍 Verifica se já existe outro curso com o mesmo nome
$sql_verifica = "SELECT COUNT(*) AS qtd FROM curso WHERE nome = '$nome' AND id_curso <> $id_curso";
$res_verifica = mysqli_query($id, $sql_verifica);
$dados = mysqli_fetch_assoc($res_verifica);

if ($dados['qtd'] > 0) {
    echo "<script>
        alert('Já existe outro curso com este nome!');
        window.location.href='listar_curso.php';
    </script>";
    exit;
}

// 🧾 Atualiza o curso
$sql = "UPDATE curso SET nome = '$nome' WHERE id_curso = $id_curso";
$ret = mysqli_query($id, $sql);

if ($ret) {
    echo "<script>
        alert('Curso atualizado com sucesso!');
        window.location.href='listar_curso.php';
    </script>";
} else {
    $erro = addslashes(mysqli_error($id));
    echo "<script>
        alert('Erro ao atualizar o curso! Detalhes: $erro');
        window.location.href='listar_curso.php';
    </script>";
}
?>

</body>
</html>
