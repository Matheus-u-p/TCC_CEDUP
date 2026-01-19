<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FingerPass - Listar Aluno</title>
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
              echo "<h2>Alunos da Turma $nomeTurma</h2>";
          } else {
              echo "<h2>Alunos da Turma</h2>";
          }
      } else {
          echo "<h2>Alunos da Turma</h2>";
      }
      ?>
    </div>

    <div class="table-container">
      <table class="tabela-5col_prof">
        <thead>
          <tr>
            <th>Nome</th>
            <th>Data de Nascimento</th>
            <th>Sexo</th>
            <th>Telefone</th>
            <th>Matrícula</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Validação do ID da turma
          if ($turma_id > 0) {
              // Query filtrando pela turma específica e ordenando por nome
              $sql = "SELECT a.id_aluno, a.nome, a.biometria, a.data_nascimento, a.sexo, a.telefone, a.matricula,
                             t.n_turma AS turma_nome
                      FROM aluno a
                      LEFT JOIN turma t ON a.id_turma = t.id_turma
                      WHERE a.id_turma = $turma_id
                      ORDER BY a.nome ASC";

              $res = mysqli_query($id, $sql);

              if ($res && mysqli_num_rows($res) > 0) {
                while ($linha = mysqli_fetch_assoc($res)) {
                  echo '<tr>';
                  echo '<td>' . htmlspecialchars($linha['nome']) . '</td>';
                  echo '<td>' . ($linha['data_nascimento'] ? date("d/m/Y", strtotime($linha['data_nascimento'])) : '-') . '</td>';
                  echo '<td>' . ($linha['sexo'] ? htmlspecialchars($linha['sexo']) : '-') . '</td>';
                  echo '<td>' . ($linha['telefone'] ? htmlspecialchars($linha['telefone']) : '-') . '</td>';
                  echo '<td>' . ($linha['matricula'] ? htmlspecialchars($linha['matricula']) : '-') . '</td>';
                  echo '</tr>';
                }
              } else {
                echo '<tr><td colspan="5">Nenhum aluno cadastrado nesta turma.</td></tr>';
              }
          } else {
              echo '<tr><td colspan="5">Turma não encontrada ou ID inválido.</td></tr>';
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