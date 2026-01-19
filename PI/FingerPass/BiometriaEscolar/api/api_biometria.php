<?php
// =====================================================
// API DE BIOMETRIA - VERSÃO CORRIGIDA
// =====================================================

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// ✅ CAMINHO ABSOLUTO CORRETO
$base_dir = 'C:/xampp/htdocs/TCC/VFP9.0/PI/FingerPass/BiometriaEscolar';
$logs_dir = $base_dir . '/logs';
$arquivo_status = $logs_dir . '/status.json';
$arquivo_req = $logs_dir . '/requisicao.json';
$arquivo_debug = $logs_dir . '/api_debug.log';

// 📝 LOG DE DEBUG
$debug_msg = "[" . date('Y-m-d H:i:s') . "] API CHAMADA\n";
$debug_msg .= "GET: " . print_r($_GET, true) . "\n";
$debug_msg .= "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
file_put_contents($arquivo_debug, $debug_msg, FILE_APPEND);

$acao = $_GET['acao'] ?? '';

// ✅ Verifica se pasta logs existe
if (!file_exists($logs_dir)) {
    if (!mkdir($logs_dir, 0777, true)) {
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Não foi possível criar a pasta logs!',
            'debug' => [
                'logs_dir' => $logs_dir,
                'base_dir' => $base_dir
            ]
        ]);
        exit;
    }
}

// ========== TESTE DE CONEXÃO ==========
if ($acao === 'teste') {
    echo json_encode([
        'status' => 'ok',
        'mensagem' => 'API funcionando!',
        'timestamp' => date('Y-m-d H:i:s'),
        'paths' => [
            'base_dir' => $base_dir,
            'logs_dir' => $logs_dir,
            'status_file' => $arquivo_status,
            'req_file' => $arquivo_req,
            'logs_exists' => file_exists($logs_dir),
            'logs_writable' => is_writable($logs_dir)
        ]
    ]);
    exit;
}
// ========== LIMPAR STATUS ==========
if ($acao === 'limpar_status') {
    $status_limpo = [
        'status' => 'aguardando',
        'mensagem' => '',
        'sensor_id' => null,
        'timestamp' => ''
    ];
    
    file_put_contents($arquivo_status, json_encode($status_limpo, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    
    echo json_encode([
        'status' => 'ok',
        'mensagem' => 'Status limpo'
    ]);
    exit;
}

// ========== CADASTRAR NOVA BIOMETRIA ==========
if ($acao === 'cadastrar') {
    $matricula = $_GET['matricula'] ?? '';
    $nome = $_GET['nome'] ?? '';
    
    if (empty($matricula) || empty($nome)) {
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Matrícula e nome são obrigatórios'
        ]);
        exit;
    }
    
    // Cria arquivo de requisição para Python
    $requisicao = [
        'acao' => 'cadastrar',
        'matricula' => $matricula,
        'nome' => $nome,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    $resultado = file_put_contents(
        $arquivo_req, 
        json_encode($requisicao, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
    );
    
    if ($resultado === false) {
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Erro ao criar requisição. Verifique permissões da pasta logs/',
            'debug' => [
                'arquivo' => $arquivo_req,
                'logs_dir' => $logs_dir,
                'writable' => is_writable($logs_dir)
            ]
        ]);
        exit;
    }
    
    // Aguarda Python processar (máximo 60 segundos)
    $timeout = time() + 60;
    
    while (time() < $timeout) {
        if (file_exists($arquivo_status)) {
            $conteudo = file_get_contents($arquivo_status);
            $status = json_decode($conteudo, true);
            
            if ($status && isset($status['status'])) {
                if ($status['status'] === 'sucesso') {
                    @unlink($arquivo_req);
                    
                    echo json_encode([
                        'status' => 'sucesso',
                        'sensor_id' => $status['sensor_id'],
                        'mensagem' => $status['mensagem'] ?? 'Biometria cadastrada com sucesso!'
                    ]);
                    exit;
                }
                else if ($status['status'] === 'erro') {
                    @unlink($arquivo_req);
                    
                    echo json_encode([
                        'status' => 'erro',
                        'mensagem' => $status['mensagem'] ?? 'Erro desconhecido'
                    ]);
                    exit;
                }
            }
        }
        
        usleep(500000); // 0.5 segundos
    }
    
    // Timeout
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Tempo esgotado! Verifique se o servidor Python está rodando.',
        'debug' => [
            'req_exists' => file_exists($arquivo_req),
            'status_exists' => file_exists($arquivo_status),
            'servidor_rodando' => 'Verifique terminal Python'
        ]
    ]);
}

// ========== EDITAR BIOMETRIA ==========
else if ($acao === 'editar') {
    $matricula = $_GET['matricula'] ?? '';
    $nome = $_GET['nome'] ?? '';
    $biometria_antiga = $_GET['biometria_antiga'] ?? '';
    
    if (empty($matricula) || empty($nome)) {
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Matrícula e nome são obrigatórios'
        ]);
        exit;
    }
    
    $requisicao = [
        'acao' => 'editar',
        'matricula' => $matricula,
        'nome' => $nome,
        'biometria_antiga' => $biometria_antiga,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    $resultado = file_put_contents(
        $arquivo_req, 
        json_encode($requisicao, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
    );
    
    if ($resultado === false) {
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Erro ao criar requisição'
        ]);
        exit;
    }
    
    $timeout = time() + 60;
    
    while (time() < $timeout) {
        if (file_exists($arquivo_status)) {
            $conteudo = file_get_contents($arquivo_status);
            $status = json_decode($conteudo, true);
            
            if ($status && isset($status['status'])) {
                if ($status['status'] === 'sucesso') {
                    @unlink($arquivo_req);
                    
                    echo json_encode([
                        'status' => 'sucesso',
                        'sensor_id' => $status['sensor_id'],
                        'mensagem' => $status['mensagem'] ?? 'Biometria atualizada!'
                    ]);
                    exit;
                }
                else if ($status['status'] === 'erro') {
                    @unlink($arquivo_req);
                    
                    echo json_encode([
                        'status' => 'erro',
                        'mensagem' => $status['mensagem'] ?? 'Erro ao atualizar'
                    ]);
                    exit;
                }
            }
        }
        
        usleep(500000);
    }
    
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Tempo esgotado! Verifique servidor Python.'
    ]);
}

// ========== STATUS ==========
else if ($acao === 'status') {
    if (file_exists($arquivo_status)) {
        echo file_get_contents($arquivo_status);
    } else {
        echo json_encode([
            'status' => 'aguardando',
            'mensagem' => '',
            'sensor_id' => null,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}

// ========== AÇÃO INVÁLIDA ==========
else {
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Ação inválida. Use: ?acao=cadastrar, ?acao=editar, ?acao=status ou ?acao=teste',
        'acao_recebida' => $acao
    ]);
}
?>