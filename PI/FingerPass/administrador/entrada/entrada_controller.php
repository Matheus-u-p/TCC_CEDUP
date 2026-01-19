<?php
// =====================================================
// CONTROLADOR DE ENTRADA/SAÍDA - COM HORÁRIOS
// =====================================================

date_default_timezone_set('America/Sao_Paulo');
ob_start();

header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

$base_dir = 'C:/xampp/htdocs/TCC/VFP9.0/PI/FingerPass/BiometriaEscolar';
$arquivo_reconhecimento = $base_dir . '/logs/reconhecimento.json';
$arquivo_leitura_ativa = $base_dir . '/logs/leitura_ativa.json';
$arquivo_ultimo_processado = $base_dir . '/logs/ultimo_processado.txt';
$arquivo_lock = $base_dir . '/logs/controller.lock';

$resultado = 'espera.php';

// ========== SISTEMA DE LOCK ==========
if (file_exists($arquivo_lock)) {
    $lock_time = filemtime($arquivo_lock);
    if ((time() - $lock_time) < 1) {
        $ultimo_estado = @file_get_contents($arquivo_lock);
        if ($ultimo_estado) {
            ob_end_clean();
            echo trim($ultimo_estado);
            exit;
        }
    } else {
        @unlink($arquivo_lock);
    }
}

file_put_contents($arquivo_lock, 'espera.php');

// ========== PRIORIDADE 1: LEITURA ATIVA ==========
if (file_exists($arquivo_leitura_ativa)) {
    $tempo_leitura = time() - filemtime($arquivo_leitura_ativa);
    if ($tempo_leitura < 2) {
        $resultado = 'processando.php';
        file_put_contents($arquivo_lock, $resultado);
        ob_end_clean();
        echo $resultado;
        exit;
    } else {
        @unlink($arquivo_leitura_ativa);
    }
}

// ========== PRIORIDADE 2: RECONHECIMENTO ==========
if (file_exists($arquivo_reconhecimento)) {
    $conteudo = @file_get_contents($arquivo_reconhecimento);
    
    if ($conteudo) {
        $dados = json_decode($conteudo, true);
        
        if ($dados && isset($dados['sensor_id'])) {
            $sensor_id = intval($dados['sensor_id']);
            $timestamp = $dados['timestamp'] ?? date('Y-m-d H:i:s');
            
            // Verifica duplicata
            $ultimo_processado = '';
            if (file_exists($arquivo_ultimo_processado)) {
                $ultimo_processado = trim(file_get_contents($arquivo_ultimo_processado));
            }
            
            if ($timestamp !== $ultimo_processado) {
                file_put_contents($arquivo_ultimo_processado, $timestamp);
                
                // Digital não cadastrada
                if ($sensor_id === 0) {
                    @unlink($arquivo_reconhecimento);
                    @unlink($arquivo_leitura_ativa);
                    @unlink($arquivo_lock);
                    $resultado = 'negada.php';
                    ob_end_clean();
                    echo $resultado;
                    exit;
                }
                
                // Conecta ao banco
                $id = @mysqli_connect('localhost', 'root', '', 'bd_biometria_tcc');
                
                if ($id) {
                    // Busca aluno com horários da turma
                    $sql = "SELECT 
                                a.*, 
                                t.n_turma, 
                                c.nome as nome_curso,
                                ht.id_horario
                            FROM aluno a 
                            LEFT JOIN turma t ON a.id_turma = t.id_turma
                            LEFT JOIN curso c ON t.id_curso = c.id_curso
                            LEFT JOIN hora_turma ht ON t.id_turma = ht.id_turma
                            WHERE a.biometria = $sensor_id
                            LIMIT 1";
                    
                    $result = mysqli_query($id, $sql);
                    
                    if ($result && mysqli_num_rows($result) > 0) {
                        $aluno = mysqli_fetch_assoc($result);
                        
                        $id_aluno = $aluno['id_aluno'];
                        $id_turma = $aluno['id_turma'];
                        $data_hoje = date('Y-m-d');
                        $hora_agora = date('H:i:s');
                        $dia_semana_nome = obterDiaSemana(date('w'));
                        
                        // Busca horário de FIM da aula da turma para HOJE
                        $sql_horario = "SELECT ha.hora_fim 
                                       FROM hora_turma ht
                                       INNER JOIN horario_aula ha ON ht.id_horario = ha.id_horario
                                       WHERE ht.id_turma = $id_turma 
                                       AND ha.dia_semana = '$dia_semana_nome'
                                       LIMIT 1";
                        
                        $result_horario = mysqli_query($id, $sql_horario);
                        $horario_fim_aula = null;
                        
                        if ($result_horario && mysqli_num_rows($result_horario) > 0) {
                            $horario_dados = mysqli_fetch_assoc($result_horario);
                            $horario_fim_aula = $horario_dados['hora_fim'];
                        }
                        
                        // Verifica se já existe registro HOJE
                        $sql_check = "SELECT * FROM registro_chamada 
                                     WHERE id_aluno = $id_aluno 
                                     AND data_biometria = '$data_hoje'";
                        
                        $check = mysqli_query($id, $sql_check);
                        
                        if (mysqli_num_rows($check) == 0) {
                            // ===== PRIMEIRA PASSADA (ENTRADA) =====
                            $sql_insert = "INSERT INTO registro_chamada 
                                          (id_aluno, presenca, data_biometria, hora_biometria, tipo_ultimo_registro) 
                                          VALUES ($id_aluno, 'E', '$data_hoje', '$hora_agora', 'entrada')";
                            mysqli_query($id, $sql_insert);
                            
                            $mensagem = 'Entrada registrada!';
                            
                        } else {
                            // ===== SEGUNDA PASSADA (SAÍDA) =====
                            $registro_existente = mysqli_fetch_assoc($check);
                            $tipo_anterior = $registro_existente['tipo_ultimo_registro'];
                            
                            // Se último registro foi ENTRADA, agora é SAÍDA
                            if ($tipo_anterior == 'entrada') {
                                // Verifica se está saindo ANTES do fim da aula
                                if ($horario_fim_aula && $hora_agora < $horario_fim_aula) {
                                    // SAIU MAIS CEDO
                                    $sql_update = "UPDATE registro_chamada 
                                                  SET hora_saida = '$hora_agora', 
                                                      presenca = 'S',
                                                      tipo_ultimo_registro = 'saida'
                                                  WHERE id_aluno = $id_aluno 
                                                  AND data_biometria = '$data_hoje'";
                                    mysqli_query($id, $sql_update);
                                    
                                    $mensagem = 'Saída antecipada registrada!';
                                    
                                } else {
                                    // SAÍDA NORMAL (após fim da aula ou sem horário)
                                    $hora_saida_final = $horario_fim_aula ?? $hora_agora;
                                    
                                    $sql_update = "UPDATE registro_chamada 
                                                  SET hora_saida = '$hora_saida_final', 
                                                      presenca = 'P',
                                                      tipo_ultimo_registro = 'saida'
                                                  WHERE id_aluno = $id_aluno 
                                                  AND data_biometria = '$data_hoje'";
                                    mysqli_query($id, $sql_update);
                                    
                                    $mensagem = 'Saída registrada!';
                                }
                                
                            } else {
                                // Se último registro foi SAÍDA, agora é nova ENTRADA (retorno)
                                $sql_update = "UPDATE registro_chamada 
                                              SET hora_biometria = '$hora_agora',
                                                  presenca = 'E',
                                                  hora_saida = NULL,
                                                  tipo_ultimo_registro = 'entrada'
                                              WHERE id_aluno = $id_aluno 
                                              AND data_biometria = '$data_hoje'";
                                mysqli_query($id, $sql_update);
                                
                                $mensagem = 'Retorno registrado!';
                            }
                        }
                        
                        mysqli_close($id);
                        
                        @unlink($arquivo_reconhecimento);
                        @unlink($arquivo_leitura_ativa);
                        @unlink($arquivo_lock);
                        
                        $data_formatada = date('d/m/Y', strtotime($data_hoje));
                        
                        $resultado = 'liberada.php?' . http_build_query([
                            'nome' => $aluno['nome'],
                            'matricula' => $aluno['matricula'],
                            'turma' => $aluno['n_turma'] ?? 'Não informada',
                            'data' => $data_formatada,
                            'hora' => $hora_agora,
                            'mensagem' => $mensagem ?? 'Registrado!'
                        ]);
                        
                        ob_end_clean();
                        echo $resultado;
                        exit;
                    } else {
                        mysqli_close($id);
                        @unlink($arquivo_reconhecimento);
                        @unlink($arquivo_leitura_ativa);
                        @unlink($arquivo_lock);
                        
                        $resultado = 'negada.php';
                        ob_end_clean();
                        echo $resultado;
                        exit;
                    }
                }
            }
        }
    }
}

// ========== PADRÃO: AGUARDANDO ==========
file_put_contents($arquivo_lock, $resultado);
ob_end_clean();
echo $resultado;

// ========== FUNÇÃO AUXILIAR ==========
function obterDiaSemana($numero) {
    $dias = [
        0 => 'Domingo',
        1 => 'Segunda-feira',
        2 => 'Terca-feira',
        3 => 'Quarta-feira',
        4 => 'Quinta-feira',
        5 => 'Sexta-feira',
        6 => 'Sabado'
    ];
    return $dias[$numero] ?? 'Segunda-feira';
}
?>