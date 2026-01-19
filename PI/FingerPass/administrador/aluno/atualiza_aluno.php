<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Atualizar Aluno</title>
    <link rel="icon" type="image/png" href="../../../img/FP006.png">
    <link rel="stylesheet" href="../../../style/cadastrar.css">
    <link href="https://fonts.googleapis.com/css2?family=Jura:wght@400;500;600&family=Changa:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>

<?php
include('../../conexao/conexao.php');
mysqli_report(MYSQLI_REPORT_OFF);

// Recebe os dados do formulário
$id_aluno        = $_POST['id_aluno'];
$nome            = trim($_POST['nome']);
$matricula       = trim($_POST['matricula']);
$telefone        = trim($_POST['telefone']);
$data_nasc       = $_POST['data_nascimento'];
$sexo            = $_POST['sexo'];
$biometria_nova  = isset($_POST['biometria']) && trim($_POST['biometria']) !== '' ? trim($_POST['biometria']) : NULL;
$biometria_antiga = isset($_POST['biometria_antiga']) && trim($_POST['biometria_antiga']) !== '' ? trim($_POST['biometria_antiga']) : NULL;
$id_turma        = $_POST['id_turma'];

// 🔍 Verifica se já existe outro aluno com a mesma matrícula
$sql_verifica = "SELECT COUNT(*) AS qtd 
                 FROM aluno 
                 WHERE matricula = '$matricula' 
                 AND id_aluno <> $id_aluno";
$res_verifica = mysqli_query($id, $sql_verifica);
$dados = mysqli_fetch_assoc($res_verifica);

if ($dados['qtd'] > 0) {
    echo "<script>
        alert('Já existe outro aluno cadastrado com esta matrícula!');
        window.location.href='listar_aluno.php';
    </script>";
    exit;
}

// 🗑️ Se a biometria mudou ou foi removida, deleta a antiga do sensor
if ($biometria_antiga && ($biometria_antiga != $biometria_nova)) {
    // ✅ CAMINHO CORRETO
    $base_dir = 'C:/xampp/htdocs/TCC/VFP9.0/PI/FingerPass/BiometriaEscolar';
    $arquivo_delete = $base_dir . '/logs/delete_biometria.json';
    
    $delete_req = [
        'acao' => 'deletar',
        'sensor_id' => $biometria_antiga,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    file_put_contents($arquivo_delete, json_encode($delete_req, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    
    // Aguarda até 3 segundos para o Python processar
    $timeout = time() + 3;
    while (time() < $timeout) {
        if (!file_exists($arquivo_delete)) {
            break; // Python já processou e removeu o arquivo
        }
        usleep(100000); // 0.1 segundos
    }
}

// 🧾 Atualiza o aluno no banco
$sql = "UPDATE aluno SET
            nome = '$nome',
            matricula = '$matricula',
            telefone = '$telefone',
            data_nascimento = " . ($data_nasc ? "'$data_nasc'" : "NULL") . ",
            sexo = " . ($sexo ? "'$sexo'" : "NULL") . ",
            biometria = " . ($biometria_nova ? "$biometria_nova" : "NULL") . ",
            id_turma = " . ($id_turma ? "'$id_turma'" : "NULL") . "
        WHERE id_aluno = $id_aluno";

$ret = mysqli_query($id, $sql);

// 🟢 Resultado da operação
if ($ret) {
    $mensagem = "Aluno atualizado com sucesso!";
    
    if ($biometria_antiga && !$biometria_nova) {
        $mensagem .= "\\nBiometria antiga (ID: $biometria_antiga) foi removida.";
    } else if ($biometria_antiga && $biometria_nova && $biometria_antiga != $biometria_nova) {
        $mensagem .= "\\nBiometria atualizada de ID $biometria_antiga para ID $biometria_nova.";
    } else if (!$biometria_antiga && $biometria_nova) {
        $mensagem .= "\\nBiometria cadastrada com ID: $biometria_nova.";
    }
    
    echo "<script>
        alert('$mensagem');
        window.location.href='listar_aluno.php';
    </script>";
} else {
    $erro = addslashes(mysqli_error($id));
    echo "<script>
        alert('Erro ao atualizar o aluno! Detalhes: $erro');
        window.location.href='listar_aluno.php';
    </script>";
}
?>

</body>
</html>