<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Excluir Aluno</title>
    <link rel="icon" type="image/png" href="../../../img/FP006.png">
    <link rel="stylesheet" href="../../../style/geral.css">
</head>

<body>

<?php
include('../../conexao/conexao.php');

// Verifica se o ID foi passado corretamente
if (!isset($_GET['id_aluno']) || !is_numeric($_GET['id_aluno'])) {
    echo "<script>
        alert('ID inválido!');
        window.location.href='listar_aluno.php';
    </script>";
    exit;
}

$id_aluno = intval($_GET['id_aluno']);

// ===============================
// 📋 1. Busca informações do aluno antes de excluir
// ===============================

$sql_aluno = "SELECT nome, biometria FROM aluno WHERE id_aluno = $id_aluno";
$res_aluno = mysqli_query($id, $sql_aluno);

if (!$res_aluno || mysqli_num_rows($res_aluno) == 0) {
    echo "<script>
        alert('Aluno não encontrado!');
        window.location.href='listar_aluno.php';
    </script>";
    exit;
}

$dados_aluno = mysqli_fetch_assoc($res_aluno);
$biometria_id = $dados_aluno['biometria'];

// ===============================
// 🔒 2. Inicia transação (segurança)
// ===============================

mysqli_begin_transaction($id);

try {
    // ===============================
    // 🗑️ 3. Exclui registros de chamada vinculados
    // ===============================
    
    $sql_chamada = "DELETE FROM registro_chamada WHERE id_aluno = $id_aluno";
    $res_chamada = mysqli_query($id, $sql_chamada);
    
    if (!$res_chamada) {
        throw new Exception('Erro ao excluir registros de chamada: ' . mysqli_error($id));
    }
    
    $qtd_registros = mysqli_affected_rows($id);
    
    // ===============================
    // 🗑️ 4. Exclui o aluno
    // ===============================
    
    $sql_delete_aluno = "DELETE FROM aluno WHERE id_aluno = $id_aluno";
    $res_delete_aluno = mysqli_query($id, $sql_delete_aluno);
    
    if (!$res_delete_aluno) {
        throw new Exception('Erro ao excluir aluno: ' . mysqli_error($id));
    }
    
    // ===============================
    // ✅ 5. Confirma a transação
    // ===============================
    
    mysqli_commit($id);
    
    // ===============================
    // 🔐 6. Remove biometria do sensor (se existir)
    // ===============================
    
    if ($biometria_id) {
        $base_dir = 'C:/xampp/htdocs/TCC/VFP9.0/PI/FingerPass/BiometriaEscolar';
        $arquivo_delete = $base_dir . '/logs/delete_biometria.json';
        
        $delete_req = [
            'acao' => 'deletar',
            'sensor_id' => $biometria_id,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        file_put_contents($arquivo_delete, json_encode($delete_req, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        
        // Aguarda até 3 segundos para o Python processar
        $timeout = time() + 3;
        while (time() < $timeout) {
            if (!file_exists($arquivo_delete)) {
                break;
            }
            usleep(100000); // 0.1 segundos
        }
    }
    
    // ===============================
    // 🎉 7. Mensagem de sucesso
    // ===============================
    
    $mensagem = "Aluno excluído com sucesso!";
    
    if ($qtd_registros > 0) {
        $mensagem .= "\\n• $qtd_registros registro(s) de chamada excluído(s).";
    }
    
    if ($biometria_id) {
        $mensagem .= "\\n• Biometria (ID: $biometria_id) removida do sensor.";
    }
    
    echo "<script>
        alert('$mensagem');
        window.location.href='listar_aluno.php';
    </script>";
    
} catch (Exception $e) {
    // ===============================
    // ❌ 8. Em caso de erro, desfaz tudo
    // ===============================
    
    mysqli_rollback($id);
    
    $erro = addslashes($e->getMessage());
    echo "<script>
        alert('Erro ao excluir aluno! Nenhuma alteração foi feita.\\n\\nDetalhes: $erro');
        window.location.href='listar_aluno.php';
    </script>";
}
?>

</body>
</html>