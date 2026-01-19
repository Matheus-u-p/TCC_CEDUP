<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FingerPass - Relatório de Chamada</title>
  <link rel="icon" type="image/png" href="../../../img/FP006.png">
  <link rel="stylesheet" href="../../../style/listar.css">
  <link href="https://fonts.googleapis.com/css2?family=Jura:wght@400;500;600&family=Changa:wght@400;700&display=swap" rel="stylesheet">
  <style>
    /* Filtro de data */
    .filtro-data {
      background-color: #181818;
      padding: 20px;
      margin: 0 auto 20px;
      max-width: 1200px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 15px;
    }

    .filtro-data label {
      color: #fff;
      font-family: 'Changa', sans-serif;
      font-weight: 600;
      font-size: 1rem;
    }

    .filtro-data input[type="date"] {
      padding: 10px 15px;
      font-size: 1rem;
      border: 2px solid #3A5A8C;
      background: #0e0e0e;
      color: #fff;
      border-radius: 6px;
      font-family: 'Jura', sans-serif;
    }

    .filtro-data button {
      padding: 10px 20px;
      background-color: #1b7e2a;
      color: #fff;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 700;
      font-family: 'Changa', sans-serif;
      font-size: 1rem;
      transition: all 0.3s ease;
    }

    .filtro-data button:hover {
      background-color: #166723;
      transform: translateY(-1px);
    }

    .filtro-data button.btn-hoje {
      background-color: #3A5A8C;
    }

    .filtro-data button.btn-hoje:hover {
      background-color: #325a94;
    }

    /* Estatísticas */
    .stats-container {
      max-width: 1200px;
      margin: 0 auto 30px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 15px;
    }

    .stat-card {
      background: linear-gradient(135deg, #1a1a1a 0%, #252525 100%);
      border: 2px solid #3A5A8C;
      border-radius: 10px;
      padding: 20px;
      text-align: center;
    }

    .stat-card h3 {
      color: #3A5A8C;
      font-size: 1rem;
      margin-bottom: 10px;
      font-family: 'Changa', sans-serif;
      font-weight: 600;
    }

    .stat-card .number {
      color: #fff;
      font-size: 2.5rem;
      font-weight: bold;
      font-family: 'Jura', sans-serif;
    }

    .stat-card.presentes .number {
      color: #1b7e2a;
    }

    .stat-card.faltosos .number {
      color: #852527;
    }

    .stat-card.saiu-cedo .number {
      color: #ff9800;
    }

    /* Status na tabela */
    .presente {
      color: #1b7e2a !important;
      font-weight: bold;
    }

    .faltou {
      color: #852527 !important;
      font-weight: bold;
    }

    .saiu-cedo {
      color: #ff9800 !important;
      font-weight: bold;
    }
  </style>
</head>
<body>

<?php
// =====================================================
// ATUALIZAÇÃO AUTOMÁTICA DE PRESENÇAS
// =====================================================

session_start();
include('../../conexao/conexao.php');

date_default_timezone_set('America/Sao_Paulo');

$data_filtro = isset($_GET['data']) ? $_GET['data'] : date('Y-m-d');
$turma_id = isset($_GET['turma_id']) ? intval($_GET['turma_id']) : 0;
$hora_agora = date('H:i:s');
$dia_semana_nome = obterDiaSemana(date('w'));

// Atualização automática de presenças
$sql_horarios = "SELECT DISTINCT
                    ht.id_turma,
                    ha.hora_fim
                FROM hora_turma ht
                INNER JOIN horario_aula ha ON ht.id_horario = ha.id_horario
                WHERE ha.dia_semana = '$dia_semana_nome'
                AND ha.hora_fim <= '$hora_agora'";

$result_horarios = mysqli_query($id, $sql_horarios);

if ($result_horarios && mysqli_num_rows($result_horarios) > 0) {
    while ($horario = mysqli_fetch_assoc($result_horarios)) {
        $id_turma_horario = $horario['id_turma'];
        $hora_fim = $horario['hora_fim'];
        
        $sql_update = "UPDATE registro_chamada rc
                      INNER JOIN aluno a ON rc.id_aluno = a.id_aluno
                      SET rc.presenca = 'P',
                          rc.hora_saida = '$hora_fim',
                          rc.tipo_ultimo_registro = 'saida'
                      WHERE a.id_turma = $id_turma_horario
                      AND rc.data_biometria = '$data_filtro'
                      AND rc.presenca = 'E'
                      AND rc.hora_saida IS NULL";
        
        mysqli_query($id, $sql_update);
    }
}

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

// Valida se turma foi selecionada
if ($turma_id == 0) {
    echo '<div class="alert alert-error">Nenhuma turma selecionada!</div>';
    exit;
}

// Busca informações da turma
$sql_turma = "SELECT t.n_turma, c.nome as nome_curso 
              FROM turma t 
              LEFT JOIN curso c ON t.id_curso = c.id_curso
              WHERE t.id_turma = $turma_id";
$result_turma = mysqli_query($id, $sql_turma);
$info_turma = mysqli_fetch_assoc($result_turma);

// Total de alunos DA TURMA
$sql_total = "SELECT COUNT(*) as total FROM aluno WHERE id_turma = $turma_id";
$result_total = mysqli_query($id, $sql_total);
$total_alunos = mysqli_fetch_assoc($result_total)['total'];

// Presentes DA TURMA (E ou P)
$sql_presentes = "SELECT COUNT(DISTINCT r.id_aluno) as total 
                 FROM registro_chamada r
                 INNER JOIN aluno a ON r.id_aluno = a.id_aluno
                 WHERE r.data_biometria = '$data_filtro' 
                 AND r.presenca IN ('E', 'P')
                 AND a.id_turma = $turma_id";
$result_presentes = mysqli_query($id, $sql_presentes);
$total_presentes = mysqli_fetch_assoc($result_presentes)['total'];

// Saíram cedo
$sql_saiu_cedo = "SELECT COUNT(DISTINCT r.id_aluno) as total 
                  FROM registro_chamada r
                  INNER JOIN aluno a ON r.id_aluno = a.id_aluno
                  WHERE r.data_biometria = '$data_filtro' 
                  AND r.presenca = 'S'
                  AND a.id_turma = $turma_id";
$result_saiu_cedo = mysqli_query($id, $sql_saiu_cedo);
$total_saiu_cedo = mysqli_fetch_assoc($result_saiu_cedo)['total'];

// Faltosos DA TURMA
$total_faltosos = $total_alunos - $total_presentes - $total_saiu_cedo;

// Percentual
$percentual = $total_alunos > 0 ? round(($total_presentes / $total_alunos) * 100, 1) : 0;
?>

  <!-- HEADER -->
  <header>
    <div class="logo">
      <img src="../../../img/FP001.png" alt="FingerPass Logo" class="logo_cabecalho">
    </div>

    <div class="usuario">
      Seja bem-vindo Administrador(a)<br>
      <strong>
        <?php 
        echo isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuário';
        ?>
      </strong>
    </div>

    <a href="../home/tela_inicial_admin.php" class="btn-voltar">Voltar</a>
  </header>

  <!-- FILTRO DE DATA -->
  <div class="filtro-data">
    <form method="GET" action="" style="display: flex; gap: 15px; align-items: center;">
      <input type="hidden" name="turma_id" value="<?php echo $turma_id; ?>">
      <label>Data:</label>
      <input type="date" name="data" value="<?php echo $data_filtro; ?>" required>
      <button type="submit">Filtrar</button>
      <button type="button" class="btn-hoje" onclick="window.location.href='?turma_id=<?php echo $turma_id; ?>&data=<?php echo date('Y-m-d'); ?>'">Hoje</button>
    </form>
  </div>

  <!-- ESTATÍSTICAS -->
  <div class="stats-container">
    <div class="stat-card">
      <h3>Total de Alunos</h3>
      <div class="number"><?php echo $total_alunos; ?></div>
    </div>
    
    <div class="stat-card presentes">
      <h3>Presentes</h3>
      <div class="number"><?php echo $total_presentes; ?></div>
    </div>

    <div class="stat-card saiu-cedo">
      <h3>Saíram Cedo</h3>
      <div class="number"><?php echo $total_saiu_cedo; ?></div>
    </div>
    
    <div class="stat-card faltosos">
      <h3>Faltosos</h3>
      <div class="number"><?php echo $total_faltosos; ?></div>
    </div>
    
    <div class="stat-card">
      <h3>Percentual de Presença</h3>
      <div class="number"><?php echo $percentual; ?>%</div>
    </div>
  </div>

  <!-- TABELA -->
  <div class="table-wrapper">
    <div class="table-header">
      <h2>Relatório - Turma <?php echo htmlspecialchars($info_turma['n_turma'] ?? 'Não encontrada'); ?> 
          (<?php echo htmlspecialchars($info_turma['nome_curso'] ?? ''); ?>) - 
          <?php echo date('d/m/Y', strtotime($data_filtro)); ?>
      </h2>
    </div>

    <div class="table-container">
      <table class="tabela-7col">
        <thead>
          <tr>
            <th>Aluno</th>
            <th>Matrícula</th>
            <th>Turma</th>
            <th>Status</th>
            <th>Horário (Entrada → Saída)</th>
            <th>Editar</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $sql = "SELECT 
                    a.id_aluno,
                    a.nome,
                    a.matricula,
                    t.n_turma,
                    r.id_registro,
                    r.presenca,
                    r.hora_biometria,
                    r.hora_saida
                  FROM aluno a
                  LEFT JOIN turma t ON a.id_turma = t.id_turma
                  LEFT JOIN registro_chamada r ON a.id_aluno = r.id_aluno 
                    AND r.data_biometria = '$data_filtro'
                  WHERE a.id_turma = $turma_id
                  ORDER BY a.nome";
          
          $result = mysqli_query($id, $sql);
          
          if (!$result) {
            echo '<tr><td colspan="6">Erro ao buscar dados: ' . mysqli_error($id) . '</td></tr>';
          } elseif (mysqli_num_rows($result) > 0) {
            while ($linha = mysqli_fetch_assoc($result)) {
              echo '<tr>';
              echo '<td>' . htmlspecialchars($linha['nome']) . '</td>';
              echo '<td>' . htmlspecialchars($linha['matricula']) . '</td>';
              echo '<td>' . htmlspecialchars($linha['n_turma'] ?? 'Não atribuída') . '</td>';
              
              // Status
              if ($linha['presenca'] == 'E') {
                  echo '<td class="presente">Presente (em aula)</td>';
              } elseif ($linha['presenca'] == 'P') {
                  echo '<td class="presente">Presente até o fim</td>';
              } elseif ($linha['presenca'] == 'S') {
                  echo '<td class="saiu-cedo">Saiu mais cedo</td>';
              } else {
                  echo '<td class="faltou">Faltou</td>';
              }
              
              // Horário (Entrada → Saída)
              if ($linha['hora_biometria']) {
                  $entrada = substr($linha['hora_biometria'], 0, 5);
                  $saida = $linha['hora_saida'] ? substr($linha['hora_saida'], 0, 5) : '-';
                  echo '<td>' . $entrada . ' → ' . $saida . '</td>';
              } else {
                  echo '<td>-</td>';
              }
              
              // Botões
              if ($linha['id_registro']) {
                echo '<td>
                        <a href="editar_relatorio.php?id_registro=' . $linha['id_registro'] . '" class="btn-icon">
                          <img src="../../../img/EDT001.png" alt="Editar">
                        </a>
                      </td>';
              } else {
                echo '<td>-</td>';
              }
              
              echo '</tr>';
            }
          } else {
            echo '<tr><td colspan="6">Nenhum aluno encontrado nesta turma.</td></tr>';
          }
          
          mysqli_close($id);
          ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- FOOTER -->
  <footer class="site-footer">
    <div class="footer-inner">
      <div class="footer-left">
        <img src="../../../img/EMAIL001.png" alt="email" class="img_email">
        <div class="contact-text">
          <span class="label">Contato:</span>
          <a href="mailto:fingerpass353@gmail.com">fingerpass353@gmail.com</a>
        </div>
      </div>

      <div class="footer-center">
        <img src="../../../img/FP005.png" alt="logo-rodape" class="logo_rodape">
        <p class="center-text">
          Proposta de Análise e Desenvolvimento de um Sistema de Chamada Escolar por Biometria: FINGERPASS
        </p>
      </div>

      <div class="footer-right">
        <span class="year">2025</span>
      </div>
    </div>
  </footer>

</body>
</html>