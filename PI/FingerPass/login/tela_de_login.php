<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass</title>
    <link rel="icon" type="image/png" href="../../img/FP006.png">
    <link rel="stylesheet" href="../../style/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Jura:wght@400;500;600&family=Changa:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="header">
        <img src="../../img/FP001.png" alt="FingerPass Logo" class="logo_cabecalho">
    </div>

    <div class="main-container">
        <div class="left-box">
            <img src="../../img/FFP002.png" alt="FingerPass Logo" class="img_login">
        </div>

        <div class="right-box">
            <h2>Realize seu Login</h2>
            <form action="autentica.php" method="post" class="login-form">
                <input type="text" name="email" placeholder="Digite seu E-Mail:" required>
                <input type="password" name="senha" placeholder="Digite sua Senha:" required>
                <button type="submit">Acessar</button>

          <!-- Tirar as sugestões -->
          <!--  <form action="autentica.php" method="post" class="login-form" autocomplete="off">
                <input type="text" name="email" placeholder="Digite seu E-Mail:" autocomplete="off" required>
                <input type="password" name="senha" placeholder="Digite sua Senha:" autocomplete="new-password" required>
                <button type="submit">Acessar</button> -->


                <!-- Mensagens de erro -->
                <?php if (isset($_GET['erro'])): ?>
                  <p class="erro">
                    <?php if ($_GET['erro'] == 'campos') echo 'Usuário e senha devem ser preenchidos.'; ?>
                    <?php if ($_GET['erro'] == 'login') echo 'Usuário ou senha incorretos.'; ?>
                    <?php if ($_GET['erro'] == 'tipo') echo 'Tipo de usuário inválido.'; ?>
                  </p>
                  <script>
                    // Remove os parâmetros da URL sem recarregar a página
                    if (window.history.replaceState) {
                        const url = window.location.protocol + "//" + window.location.host + window.location.pathname;
                        window.history.replaceState({path:url}, "", url);
                    }
                  </script>
                <?php endif; ?>

                <!-- Mensagem de sucesso -->
                <?php if (isset($_GET['sucesso']) && isset($_GET['destino'])): ?>
                  <p class="sucesso">Redirecionando...</p>
                  <script>
                    setTimeout(() => {
                        window.location.href = "<?php echo $_GET['destino']; ?>";
                    }, 1000);

                    // Limpa a URL depois de exibir
                    if (window.history.replaceState) {
                        const url = window.location.protocol + "//" + window.location.host + window.location.pathname;
                        window.history.replaceState({path:url}, "", url);
                    }
                  </script>
                <?php endif; ?>


            </form>
        </div>
    </div>

    <footer class="site-footer">
      <div class="footer-inner">
        <div class="footer-left">
          <img src="../../img/EMAIL001.png" alt="email" class="img_email">
          <div class="contact-text">
            <span class="label">Contato:</span>
            <a href="mailto:fingerpass353@gmail.com">fingerpass353@gmail.com</a>
          </div>
        </div>

        <div class="footer-center">
          <img src="../../img/FP005.png" alt="logo-rodape" class="logo_rodape">
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
