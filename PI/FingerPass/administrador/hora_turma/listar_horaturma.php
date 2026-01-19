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
      Seja bem-vindo Administrador(a)<br>
      <strong>
        <?php 
        if (session_status() === PHP_SESSION_NONE) session_start();
        echo isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuário';
        ?>
      </strong>
    </div>

    <a href="../home/tela_inicial_admin.php" class="btn-voltar">Voltar</a>
  </header>

  <!-- CONTEÚDO -->
  <div class="table-wrapper">
    <div class="table-header">
      <h2>Listar Horários das Turmas</h2>
      <a href="../hora_turma/form_horaturma.php" class="btn-adicionar">Adicionar +</a>
    </div>

    <div class="table-container">
      <table class="tabela-8col">
        <thead>
          <tr>
            <th>ID</th>
            <th>Turma</th>
            <th>Curso</th>
            <th>Dia da Semana</th>
            <th>Hora de Início</th>
            <th>Hora de Fim</th>
            <th>Editar</th>
            <th>Excluir</th>
          </tr>
        </thead>
        <tbody>
          <?php
          include('../../conexao/conexao.php');

          // Consulta com JOINs para mostrar dados completos
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
        ORDER BY 
          t.n_turma ASC,
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

          if (mysqli_num_rows($res) > 0) {
            while ($linha = mysqli_fetch_assoc($res)) {
              echo '<tr>';
              echo '<td>' . $linha['id_hora_turma'] . '</td>';
              echo '<td>' . $linha['n_turma'] . '</td>';
              echo '<td>' . ($linha['curso_nome'] ? $linha['curso_nome'] : '<em>Sem curso</em>') . '</td>';
              echo '<td>' . $linha['dia_semana'] . '</td>';
              echo '<td>' . substr($linha['hora_inicio'], 0, 5) . '</td>';
              echo '<td>' . substr($linha['hora_fim'], 0, 5) . '</td>';

              // Botões separados (Editar e Excluir)
              echo '<td>
                      <a href="editar_horaturma.php?id_hora_turma=' . $linha['id_hora_turma'] . '" class="btn-icon">
                        <img src="../../../img/EDT001.png" alt="Editar">
                      </a>
                    </td>';
              echo '<td>
                      <a href="excluir_horaturma.php?id_hora_turma=' . $linha['id_hora_turma'] . '" class="btn-icon excluir" onclick="return confirm(\'Tem certeza que deseja excluir este horário?\')">
                        <img src="../../../img/LIX001.png" alt="Excluir">
                      </a>
                    </td>';
              echo '</tr>';
            }
          } else {
            echo '<tr><td colspan="8">Nenhum horário cadastrado.</td></tr>';
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
