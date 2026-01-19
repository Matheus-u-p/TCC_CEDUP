<?php
// =====================================================
// CONTROLADOR DO SERVIDOR PYTHON - VERSAO CORRIGIDA
// =====================================================

// IMPORTANTE: Nada pode ser enviado antes do header!
header('Content-Type: application/json; charset=utf-8');

// Desabilita qualquer output de erro que nao seja JSON
error_reporting(0);
ini_set('display_errors', 0);

$acao = $_GET['acao'] ?? '';

// CAMINHOS ABSOLUTOS
$base_dir = 'C:/xampp/htdocs/TCC/VFP9.0/PI/FingerPass/BiometriaEscolar';
$python_dir = $base_dir . '/python';
$logs_dir = $base_dir . '/logs';
$pid_file = $logs_dir . '/servidor_pid.txt';
$status_file = $logs_dir . '/status.json';

// CAMINHO CORRETO DO PYTHON
$python_exe = 'C:\\Users\\mathe\\AppData\\Local\\Programs\\Python\\Python313\\python.exe';

// Criar pasta logs se nao existir
if (!file_exists($logs_dir)) {
    @mkdir($logs_dir, 0777, true);
}

// ========== VERIFICAR SE SERVIDOR ESTA RODANDO ==========
function verificarServidor($pid_file) {
    if (!file_exists($pid_file)) {
        return false;
    }
    
    $pid = trim(file_get_contents($pid_file));
    if (empty($pid)) {
        return false;
    }
    
    // Verifica se processo existe
    exec("tasklist /FI \"PID eq $pid\" 2>NUL", $output);
    
    foreach ($output as $linha) {
        if (strpos($linha, $pid) !== false && 
            strpos($linha, 'python.exe') !== false) {
            return $pid;
        }
    }
    
    // PID nao existe mais, limpa arquivo
    @unlink($pid_file);
    return false;
}

// ========== INICIAR SERVIDOR ==========
if ($acao === 'iniciar') {
    // Verifica se ja esta rodando
    $pid_existente = verificarServidor($pid_file);
    
    if ($pid_existente) {
        echo json_encode([
            'status' => 'erro', 
            'mensagem' => 'Servidor ja esta rodando!',
            'pid' => $pid_existente
        ]);
        exit;
    }
    
    // Limpa arquivos antigos
    @unlink($pid_file);
    @unlink($status_file);
    @unlink($logs_dir . '/reconhecimento.json');
    @unlink($logs_dir . '/leitura_ativa.json');
    @unlink($logs_dir . '/ultimo_processado.txt');
    
    // CAMINHOS ABSOLUTOS (formato Windows)
    $script_path = 'C:\\xampp\\htdocs\\TCC\\VFP9.0\\PI\\FingerPass\\BiometriaEscolar\\python\\servidor_entrada.py';
    $log_file = 'C:\\xampp\\htdocs\\TCC\\VFP9.0\\PI\\FingerPass\\BiometriaEscolar\\logs\\servidor_output.log';
    
    // Verifica se arquivos existem
    if (!file_exists($python_exe)) {
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Python nao encontrado!',
            'caminho' => $python_exe
        ]);
        exit;
    }
    
    if (!file_exists($script_path)) {
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Script Python nao encontrado!',
            'caminho' => $script_path
        ]);
        exit;
    }
    
    // COMANDO CORRETO COM PYTHON ABSOLUTO
    $comando = "start \"ServidorBiometria\" /B /MIN \"$python_exe\" \"$script_path\" > \"$log_file\" 2>&1";
    
    // Executa comando
    pclose(popen($comando, 'r'));
    
    // Aguarda servidor iniciar (maximo 10 segundos)
    $timeout = time() + 10;
    $servidor_iniciado = false;
    
    while (time() < $timeout) {
        sleep(1);
        
        // Verifica se arquivo PID foi criado
        if (file_exists($pid_file)) {
            $pid = trim(file_get_contents($pid_file));
            
            // Confirma que processo existe
            exec("tasklist /FI \"PID eq $pid\" 2>NUL", $check_output);
            
            foreach ($check_output as $linha) {
                if (strpos($linha, $pid) !== false && 
                    strpos($linha, 'python.exe') !== false) {
                    
                    echo json_encode([
                        'status' => 'sucesso',
                        'mensagem' => 'Servidor iniciado com sucesso!',
                        'pid' => $pid
                    ]);
                    
                    $servidor_iniciado = true;
                    break 2;
                }
            }
        }
        
        // Tambem verifica se status.json foi criado
        if (file_exists($status_file)) {
            $status_content = @file_get_contents($status_file);
            $status_data = @json_decode($status_content, true);
            
            if ($status_data && isset($status_data['pid'])) {
                $pid = $status_data['pid'];
                
                file_put_contents($pid_file, $pid);
                
                echo json_encode([
                    'status' => 'sucesso',
                    'mensagem' => 'Servidor iniciado!',
                    'pid' => $pid
                ]);
                
                $servidor_iniciado = true;
                break;
            }
        }
    }
    
    if (!$servidor_iniciado) {
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Falha ao iniciar servidor apos 10 segundos',
            'dica' => 'Verifique se Arduino esta conectado e Python instalado'
        ]);
    }
}

// ========== PARAR SERVIDOR ==========
else if ($acao === 'parar') {
    $pid = verificarServidor($pid_file);
    
    if (!$pid) {
        // Tenta encontrar processo mesmo sem PID file
        exec('wmic process where "CommandLine like \'%servidor_entrada.py%\'" get ProcessId 2>NUL', $output);
        
        if (count($output) > 1 && !empty(trim($output[1]))) {
            $pid = trim($output[1]);
        } else {
            echo json_encode([
                'status' => 'sucesso',
                'mensagem' => 'Servidor ja estava parado'
            ]);
            exit;
        }
    }
    
    // Mata processo
    exec("taskkill /F /PID $pid 2>&1", $output, $return_code);
    
    // Limpa arquivos
    @unlink($pid_file);
    @unlink($status_file);
    @unlink($logs_dir . '/reconhecimento.json');
    @unlink($logs_dir . '/leitura_ativa.json');
    @unlink($logs_dir . '/ultimo_processado.txt');
    
    echo json_encode([
        'status' => 'sucesso',
        'mensagem' => 'Servidor parado com sucesso!'
    ]);
}

// ========== STATUS ==========
else if ($acao === 'status') {
    $pid = verificarServidor($pid_file);
    
    if ($pid) {
        $info_adicional = '';
        
        if (file_exists($status_file)) {
            $status_data = @json_decode(file_get_contents($status_file), true);
            if ($status_data && isset($status_data['timestamp'])) {
                $info_adicional = 'Ultimo update: ' . $status_data['timestamp'];
            }
        }
        
        echo json_encode([
            'status' => 'rodando',
            'mensagem' => 'Servidor ativo',
            'pid' => $pid,
            'info' => $info_adicional
        ]);
    } else {
        echo json_encode([
            'status' => 'parado',
            'mensagem' => 'Servidor nao esta rodando'
        ]);
    }
}

// ========== REINICIAR ==========
else if ($acao === 'reiniciar') {
    // Para servidor
    $pid = verificarServidor($pid_file);
    if ($pid) {
        exec("taskkill /F /PID $pid 2>&1");
        @unlink($pid_file);
        sleep(2);
    }
    
    // Limpa arquivos
    @unlink($status_file);
    @unlink($logs_dir . '/reconhecimento.json');
    @unlink($logs_dir . '/leitura_ativa.json');
    @unlink($logs_dir . '/ultimo_processado.txt');
    
    // Inicia novamente
    $script_path = 'C:\\xampp\\htdocs\\TCC\\VFP9.0\\PI\\FingerPass\\BiometriaEscolar\\python\\servidor_entrada.py';
    $log_file = 'C:\\xampp\\htdocs\\TCC\\VFP9.0\\PI\\FingerPass\\BiometriaEscolar\\logs\\servidor_output.log';
    
    $comando = "start \"ServidorBiometria\" /B /MIN \"$python_exe\" \"$script_path\" > \"$log_file\" 2>&1";
    pclose(popen($comando, 'r'));
    
    sleep(3);
    
    $novo_pid = verificarServidor($pid_file);
    
    if ($novo_pid) {
        echo json_encode([
            'status' => 'sucesso',
            'mensagem' => 'Servidor reiniciado!',
            'pid' => $novo_pid
        ]);
    } else {
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Falha ao reiniciar servidor'
        ]);
    }
}

// ========== STATUS DO SERVIDOR DE CADASTRO ==========
else if ($acao === 'status_cadastro') {
    $pid_file_cadastro = $logs_dir . '/servidor_cadastro_pid.txt';
    
    if (file_exists($pid_file_cadastro)) {
        $pid = trim(file_get_contents($pid_file_cadastro));
        
        if (empty($pid)) {
            @unlink($pid_file_cadastro);
            echo json_encode([
                'status' => 'parado',
                'mensagem' => 'Servidor de cadastro nao esta rodando'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        // Verifica se processo existe
        exec("tasklist /FI \"PID eq $pid\" 2>NUL", $output);
        
        foreach ($output as $linha) {
            if (strpos($linha, $pid) !== false && 
                strpos($linha, 'python.exe') !== false) {
                
                echo json_encode([
                    'status' => 'rodando',
                    'mensagem' => 'Servidor de cadastro ativo',
                    'pid' => $pid
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }
        
        // PID nao existe mais, limpa arquivo
        @unlink($pid_file_cadastro);
    }
    
    echo json_encode([
        'status' => 'parado',
        'mensagem' => 'Servidor de cadastro nao esta rodando'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ========== INICIAR SERVIDOR DE CADASTRO - VERSÃO MELHORADA ==========
else if ($acao === 'iniciar_cadastro') {
    $pid_file_cadastro = $logs_dir . '/servidor_cadastro_pid.txt';
    
    // Verifica se ja esta rodando
    if (file_exists($pid_file_cadastro)) {
        $pid = trim(file_get_contents($pid_file_cadastro));
        
        if (!empty($pid)) {
            exec("tasklist /FI \"PID eq $pid\" 2>NUL", $output);
            foreach ($output as $linha) {
                if (strpos($linha, $pid) !== false && 
                    strpos($linha, 'python.exe') !== false) {
                    
                    echo json_encode([
                        'status' => 'erro',
                        'mensagem' => 'Servidor de cadastro ja esta rodando!',
                        'pid' => $pid
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }
        }
        @unlink($pid_file_cadastro);
    }
    
    // Limpa arquivos antigos
    @unlink($logs_dir . '/status.json');
    @unlink($logs_dir . '/requisicao.json');
    
    // CAMINHOS CORRETOS
    $script_path = 'C:\\xampp\\htdocs\\TCC\\VFP9.0\\PI\\FingerPass\\BiometriaEscolar\\python\\servidor_biometria.py';
    $log_file = 'C:\\xampp\\htdocs\\TCC\\VFP9.0\\PI\\FingerPass\\BiometriaEscolar\\logs\\servidor_cadastro_output.log';
    $error_log = 'C:\\xampp\\htdocs\\TCC\\VFP9.0\\PI\\FingerPass\\BiometriaEscolar\\logs\\servidor_cadastro_errors.log';
    
    // Verifica se arquivos existem
    if (!file_exists($python_exe)) {
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Python nao encontrado!',
            'caminho' => $python_exe
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    if (!file_exists($script_path)) {
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Script Python nao encontrado!',
            'caminho' => $script_path
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // ✅ COMANDO COM LOGS SEPARADOS
    $comando = "start \"ServidorCadastro\" /B /MIN \"$python_exe\" \"$script_path\" > \"$log_file\" 2> \"$error_log\"";
    
    // Executa comando
    pclose(popen($comando, 'r'));
    
    // ✅ AGUARDA ATÉ 15 SEGUNDOS PARA CONFIRMAR INÍCIO
    $timeout = time() + 15;
    $servidor_iniciado = false;
    
    while (time() < $timeout) {
        sleep(1);
        
        // Verifica se PID foi criado
        if (file_exists($pid_file_cadastro)) {
            $pid = trim(file_get_contents($pid_file_cadastro));
            
            if (!empty($pid)) {
                // Confirma que processo existe
                exec("tasklist /FI \"PID eq $pid\" 2>NUL", $check_output);
                
                foreach ($check_output as $linha) {
                    if (strpos($linha, $pid) !== false && 
                        strpos($linha, 'python.exe') !== false) {
                        
                        echo json_encode([
                            'status' => 'sucesso',
                            'mensagem' => 'Servidor de cadastro iniciado com sucesso!',
                            'pid' => $pid
                        ], JSON_UNESCAPED_UNICODE);
                        
                        $servidor_iniciado = true;
                        break 2;
                    }
                }
            }
        }
    }
    
    // ❌ SE NÃO INICIOU, VERIFICA ERROS
    if (!$servidor_iniciado) {
        $erro_detalhes = '';
        
        // Lê erros do log
        if (file_exists($error_log)) {
            $erro_detalhes = file_get_contents($error_log);
            
            // Pega apenas últimas 500 caracteres
            if (strlen($erro_detalhes) > 500) {
                $erro_detalhes = '...' . substr($erro_detalhes, -500);
            }
        }
        
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Falha ao iniciar servidor apos 15 segundos',
            'dica' => 'Verifique: Arduino conectado? Porta COM correta no config.py?',
            'log_erro' => $erro_detalhes
        ], JSON_UNESCAPED_UNICODE);
    }
    
    exit;
}

// ========== PARAR SERVIDOR DE CADASTRO ==========
else if ($acao === 'parar_cadastro') {
    $pid_file_cadastro = $logs_dir . '/servidor_cadastro_pid.txt';
    
    if (file_exists($pid_file_cadastro)) {
        $pid = trim(file_get_contents($pid_file_cadastro));
        
        if (!empty($pid)) {
            // Mata processo
            exec("taskkill /F /PID $pid 2>&1");
        }
        
        // Limpa arquivos
        @unlink($pid_file_cadastro);
        @unlink($logs_dir . '/status.json');
        @unlink($logs_dir . '/requisicao.json');
        
        echo json_encode([
            'status' => 'sucesso',
            'mensagem' => 'Servidor de cadastro parado com sucesso!'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'status' => 'sucesso',
            'mensagem' => 'Servidor de cadastro ja estava parado'
        ], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// ========== VERIFICAR LOGS DO SERVIDOR DE CADASTRO ==========
else if ($acao === 'logs_cadastro') {
    $error_log = $logs_dir . '/servidor_cadastro_errors.log';
    $output_log = $logs_dir . '/servidor_cadastro_output.log';
    
    $resultado = [
        'status' => 'sucesso',
        'erros' => '',
        'output' => ''
    ];
    
    if (file_exists($error_log)) {
        $erros = file_get_contents($error_log);
        // Últimas 1000 caracteres
        if (strlen($erros) > 1000) {
            $resultado['erros'] = '...' . substr($erros, -1000);
        } else {
            $resultado['erros'] = $erros;
        }
    }
    
    if (file_exists($output_log)) {
        $output = file_get_contents($output_log);
        // Últimas 1000 caracteres
        if (strlen($output) > 1000) {
            $resultado['output'] = '...' . substr($output, -1000);
        } else {
            $resultado['output'] = $output;
        }
    }
    
    echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    exit;
}

// ========== ACAO INVALIDA ==========
else {
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Acao invalida',
        'acao_recebida' => $acao
    ]);
}
?>