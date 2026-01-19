<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FingerPass - Listar Usuário</title>
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

  <!-- TABELA -->
  <div class="table-wrapper">
  <div class="table-header">
    <h2>Listar Usuários</h2>
    <a href="../usuario/form_usuario.php" class="btn-adicionar">Adicionar +</a>
  </div>

  <div class="table-container">
    <table class="tabela-6col">
      <thead>
        <tr>
          <th>ID</th>
          <th>Email</th>
          <th>Senha</th>
          <th>Tipo</th>
          <th>Editar</th>
          <th>Excluir</th>
        </tr>
      </thead>
      <tbody>
        <?php
        include('../../conexao/conexao.php');
        $sql = "SELECT * FROM usuario";
        $res = mysqli_query($id, $sql);
        while ($linha = mysqli_fetch_array($res)) { ?>
          <tr>
            <td><?php echo $linha['id_usuario']; ?></td>
            <td><?php echo $linha['email']; ?></td>
            <td><?php /*echo $linha['senha'];*/ ?><em>*Protegido*</em></td>
            <td><?php echo $linha['tipo']; ?></td>
            <td>
              <a href="editar_usuario.php?id=<?php echo $linha['id_usuario']; ?>" class="btn-icon">
                <img src="../../../img/EDT001.png" alt="Editar">
              </a>
            </td>
            <td>
              <a href="../usuario/excluir_usuario.php?id_usuario=<?php echo $linha['id_usuario']; ?>" class="btn-icon excluir">
                <img src="../../../img/LIX001.png" alt="Excluir" />
              </a>
            </td>
          </tr>
        <?php } ?>
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
