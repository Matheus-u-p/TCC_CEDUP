<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Cadastrar Aluno</title>
    <link rel="icon" type="image/png" href="../../../img/FP006.png">
    <link rel="stylesheet" href="../../../style/cadastrar.css">
    <link href="https://fonts.googleapis.com/css2?family=Jura:wght@400;500;600&family=Changa:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>

<?php
include('../../conexao/conexao.php');
mysqli_report(MYSQLI_REPORT_OFF); // evita erros fatais

// Recebe os dados do formulário
$nome        = trim($_POST['nome']);
$matricula   = trim($_POST['matricula']);
$telefone    = trim($_POST['telefone']);
$data_nasc   = $_POST['data_nasc'];
$sexo        = $_POST['sexo'];
$biometria   = isset($_POST['biometria']) && $_POST['biometria'] !== '' ? $_POST['biometria'] : NULL;
$id_turma    = $_POST['id_turma'];

// 🔍 Verifica se já existe um aluno com a mesma matrícula
$sql_verifica = "SELECT COUNT(*) AS qtd 
                 FROM aluno 
                 WHERE matricula = '$matricula'";
$res_verifica = mysqli_query($id, $sql_verifica);
$dados = mysqli_fetch_assoc($res_verifica);

if ($dados['qtd'] > 0) {
    echo "<script>
        alert('Já existe um aluno cadastrado com esta matrícula!');
        window.location.href='listar_aluno.php';
    </script>";
    exit;
}

// 🧾 Insere o aluno no banco
$sql = "INSERT INTO aluno (nome, matricula, telefone, data_nascimento, sexo, biometria, id_turma)
        VALUES ('$nome', '$matricula', '$telefone', " . 
        ($data_nasc ? "'$data_nasc'" : "NULL") . ", 
        " . ($sexo ? "'$sexo'" : "NULL") . ",
        " . ($biometria ? "$biometria" : "NULL") . ",
        " . ($id_turma ? "'$id_turma'" : "NULL") . ")";

$ret = mysqli_query($id, $sql);

// 🟢 Resultado da operação
if ($ret) {
    $mensagem = $biometria 
        ? "Aluno cadastrado com sucesso! Biometria ID: $biometria" 
        : "Aluno cadastrado com sucesso!";
    
    echo "<script>
        alert('$mensagem');
        window.location.href='listar_aluno.php';
    </script>";
} else {
    $erro = addslashes(mysqli_error($id));
    echo "<script>
        alert('Erro ao cadastrar aluno! Detalhes: $erro');
        window.location.href='listar_aluno.php';
    </script>";
}
?>

</body>
</html>