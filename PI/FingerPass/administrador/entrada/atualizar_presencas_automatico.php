<?php
// =====================================================
// SCRIPT DE ATUALIZAÇÃO AUTOMÁTICA DE PRESENÇAS
// Roda a cada minuto via Cron ou Task Scheduler
// =====================================================

date_default_timezone_set('America/Sao_Paulo');

include('../../conexao/conexao.php');

$data_hoje = date('Y-m-d');
$hora_agora = date('H:i:s');
$dia_semana_nome = obterDiaSemana(date('w'));

// Busca todos os horários de aula que JÁ TERMINARAM hoje
$sql = "SELECT DISTINCT
            ht.id_turma,
            ha.hora_fim
        FROM hora_turma ht
        INNER JOIN horario_aula ha ON ht.id_horario = ha.id_horario
        WHERE ha.dia_semana = '$dia_semana_nome'
        AND ha.hora_fim <= '$hora_agora'";

$result = mysqli_query($id, $sql);

$atualizados = 0;

if ($result && mysqli_num_rows($result) > 0) {
    while ($horario = mysqli_fetch_assoc($result)) {
        $id_turma = $horario['id_turma'];
        $hora_fim = $horario['hora_fim'];
        
        // Atualiza alunos que estão com ENTRADA (E) e não registraram saída
        $sql_update = "UPDATE registro_chamada rc
                      INNER JOIN aluno a ON rc.id_aluno = a.id_aluno
                      SET rc.presenca = 'P',
                          rc.hora_saida = '$hora_fim',
                          rc.tipo_ultimo_registro = 'saida'
                      WHERE a.id_turma = $id_turma
                      AND rc.data_biometria = '$data_hoje'
                      AND rc.presenca = 'E'
                      AND rc.hora_saida IS NULL";
        
        if (mysqli_query($id, $sql_update)) {
            $atualizados += mysqli_affected_rows($id);
        }
    }
}

mysqli_close($id);

// Log (opcional)
$log_file = '../../BiometriaEscolar/logs/atualizacao_automatica.log';
$log_msg = "[" . date('Y-m-d H:i:s') . "] Atualizados: $atualizados registros\n";
file_put_contents($log_file, $log_msg, FILE_APPEND);

echo "OK - $atualizados registros atualizados";

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