<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FingerPass - Listar Horário da Turma</title>
  <link rel="icon" type="image/png" href="../../../img/FP006.png">
  <link rel="stylesheet" href="../../../style/listar.css">
  <link href="https://fonts.googleapis.com/css2?family=Jura:wght@400;500;600&family=Changa:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

  <!-- HEADER -->
  <header>
    <div class="logo">
      <img src="../../../img/FP001.png" alt="FingerPass Logo" class="logo_cabecalho">
    </div>

    <div class="usuario">
      Seja bem-vindo Professor(a)<br>
      <strong>
        <?php 
        if (session_status() === PHP_SESSION_NONE) session_start();
        echo isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuário';
        ?>
      </strong>
    </div>

    <?php
    // Recebe o ID da turma via URL
    $turma_id = isset($_GET['turma_id']) ? intval($_GET['turma_id']) : 0;
    ?>
    <a href="../registro/relatorio_turma_prof.php?turma_id=<?php echo $turma_id; ?>" class="btn-voltar">Voltar</a>
  </header>

  <!-- CONTEÚDO -->
  <div class="table-wrapper">
    <div class="table-header">
      <?php
      include('../../conexao/conexao.php');
      
      // Busca o número da turma
      if ($turma_id > 0) {
          $sqlTurma = "SELECT n_turma FROM turma WHERE id_turma = $turma_id";
          $resultTurma = mysqli_query($id, $sqlTurma);
          
          if ($resultTurma && mysqli_num_rows($resultTurma) > 0) {
              $turma = mysqli_fetch_assoc($resultTurma);
              $nomeTurma = htmlspecialchars($turma['n_turma']);
              echo "<h2>Horários da Turma $nomeTurma</h2>";
          } else {
              echo "<h2>Horários da Turma</h2>";
          }
      } else {
          echo "<h2>Horários da Turma</h2>";
      }
      ?>
    </div>

    <div class="table-container">
      <table class="tabela-3col_prof">
        <thead>
          <tr>
            <th>Dia da Semana</th>
            <th>Hora de Início</th>
            <th>Hora de Fim</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Validação do ID da turma
          if ($turma_id > 0) {
              // Consulta com JOINs filtrando pela turma específica
              $sql = "SELECT 
                        ht.id_hora_turma, 
                        t.n_turma, 
                        c.nome AS curso_nome,
                        h.dia_semana, 
                        h.hora_inicio, 
                        h.hora_fim
                      FROM hora_turma ht
                      JOIN turma t ON ht.id_turma = t.id_turma
                      LEFT JOIN curso c ON t.id_curso = c.id_curso
                      JOIN horario_aula h ON ht.id_horario = h.id_horario
                      WHERE ht.id_turma = $turma_id
                      ORDER BY 
                        FIELD(h.dia_semana, 
                              'Domingo', 
                              'Segunda-feira', 
                              'Terca-feira', 
                              'Quarta-feira', 
                              'Quinta-feira', 
                              'Sexta-feira', 
                              'Sabado'),
                        h.hora_inicio";

              $res = mysqli_query($id, $sql);

              if ($res && mysqli_num_rows($res) > 0) {
                while ($linha = mysqli_fetch_assoc($res)) {
                  echo '<tr>';
                  echo '<td>' . htmlspecialchars($linha['dia_semana']) . '</td>';
                  echo '<td>' . substr($linha['hora_inicio'], 0, 5) . '</td>';
                  echo '<td>' . substr($linha['hora_fim'], 0, 5) . '</td>';
                  echo '</tr>';
                }
              } else {
                echo '<tr><td colspan="3">Nenhum horário cadastrado para esta turma.</td></tr>';
              }
          } else {
              echo '<tr><td colspan="3">Turma não encontrada ou ID inválido.</td></tr>';
          }
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