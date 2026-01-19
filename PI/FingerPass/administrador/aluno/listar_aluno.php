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
      <h2>Listar Alunos</h2>
      <a href="../aluno/form_aluno.php" class="btn-adicionar">Adicionar +</a>
    </div>

    <div class="table-container">
      <table class="tabela-10col">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Biometria</th>
            <th>Data de Nascimento</th>
            <th>Sexo</th>
            <th>Telefone</th>
            <th>Matrícula</th>
            <th>Turma</th>
            <th>Editar</th>
            <th>Excluir</th>
          </tr>
        </thead>
        <tbody>
          <?php
          include('../../conexao/conexao.php');

          $sql = "SELECT a.id_aluno, a.nome, a.biometria, a.data_nascimento, a.sexo, a.telefone, a.matricula,
                         t.n_turma AS turma_nome
                  FROM aluno a
                  LEFT JOIN turma t ON a.id_turma = t.id_turma
                  ORDER BY a.nome ASC";

          $res = mysqli_query($id, $sql);

          if (mysqli_num_rows($res) > 0) {
            while ($linha = mysqli_fetch_assoc($res)) {
              echo '<tr>';
              echo '<td>' . $linha['id_aluno'] . '</td>';
              echo '<td>' . htmlspecialchars($linha['nome']) . '</td>';
              echo '<td>' . ($linha['biometria'] ? '<em>Registrada</em>' : '<em>Não registrada</em>') . '</td>';
              echo '<td>' . ($linha['data_nascimento'] ? date("d/m/Y", strtotime($linha['data_nascimento'])) : '-') . '</td>';
              echo '<td>' . ($linha['sexo'] ? htmlspecialchars($linha['sexo']) : '-') . '</td>';
              echo '<td>' . ($linha['telefone'] ? htmlspecialchars($linha['telefone']) : '-') . '</td>';
              echo '<td>' . ($linha['matricula'] ? htmlspecialchars($linha['matricula']) : '-') . '</td>';
              echo '<td>' . ($linha['turma_nome'] ? htmlspecialchars($linha['turma_nome']) : '<em>Sem turma</em>') . '</td>';
              echo '<td>
                      <a href="editar_aluno.php?id_aluno=' . $linha['id_aluno'] . '" class="btn-icon">
                        <img src="../../../img/EDT001.png" alt="Editar">
                      </a>
                    </td>';
              echo '<td>
                      <a href="excluir_aluno.php?id_aluno=' . $linha['id_aluno'] . '" class="btn-icon excluir" onclick="return confirm(\'Tem certeza que deseja excluir este aluno?\')">
                        <img src="../../../img/LIX001.png" alt="Excluir">
                      </a>
                    </td>';
              echo '</tr>';
            }
          } else {
            echo '<tr><td colspan="10">Nenhum aluno cadastrado.</td></tr>';
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
