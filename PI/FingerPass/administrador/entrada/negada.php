<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FingerPass - Acesso Negado</title>
  <link rel="icon" type="image/png" href="../../../img/FP006.png">
  <link href="https://fonts.googleapis.com/css2?family=Jura:wght@400;500;600;700&family=Changa:wght@400;700;800&display=swap" rel="stylesheet">
  <style>
    * { 
      margin: 0; 
      padding: 0; 
      box-sizing: border-box; 
    }
    
    body {
      font-family: 'Jura', sans-serif;
      background-color: #0c0c0c;
      color: #fff;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    /* ==================== HEADER ==================== */
    header {
      background: linear-gradient(135deg, #8b2325 0%, #a83032 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 30px 36px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.4);
      position: relative;
      z-index: 10;
    }

    .logo-header {
      height: 110px;
      width: auto;
    }

    /* ================ CONTEÚDO PRINCIPAL ================ */
    .main-content {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #0c0c0c 0%, #1a1a1a 100%);
      padding: 40px 20px;
    }

    .content-wrapper {
      text-align: center;
      max-width: 1200px;
      width: 100%;
      animation: shake 0.5s, fadeIn 0.5s;
    }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-15px); }
      75% { transform: translateX(15px); }
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    h1 {
      font-size: 5rem;
      font-weight: 800;
      font-family: 'Changa', sans-serif;
      letter-spacing: 3px;
      margin-bottom: 20px;
      color: #fff;
      text-shadow: 0 4px 10px rgba(0,0,0,0.6);
      text-transform: uppercase;
    }

    .subtitulo {
      font-size: 2rem;
      font-weight: 400;
      color: #e74c3c;
      font-family: 'Jura', sans-serif;
      margin-top: 20px;
    }



    /* ==================== FOOTER ==================== */
    .site-footer {
      background-color: #111;
      color: #fff;
      border-top: 1px solid #222;
      position: relative;
      z-index: 10;
    }

    .footer-inner {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px 40px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
    }

    .footer-left {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .img_email {
      height: 20px;
      width: auto;
    }

    .contact-text {
      font-size: 0.95rem;
      font-weight: 500;
      font-family: 'Jura', sans-serif;
      color: #fff;
    }

    .footer-center {
      display: flex;
      align-items: center;
      gap: 8px;
      text-align: center;
    }

    .logo_rodape {
      height: 36px;
      width: auto;
    }

    .center-text {
      font-size: 0.8rem;
      color: rgba(255,255,255,0.85);
      max-width: 420px;
      line-height: 1.3;
    }

    .footer-right {
      font-size: 0.9rem;
      font-family: 'Jura', sans-serif;
      color: rgba(255,255,255,0.8);
    }

    /* ==================== RESPONSIVIDADE ==================== */
    @media (max-width: 1200px) {
      h1 { font-size: 4rem; }
      .subtitulo { font-size: 1.8rem; }
    }

    @media (max-width: 768px) {
      h1 { font-size: 3rem; }
      .subtitulo { font-size: 1.5rem; }
      
      .footer-inner {
        flex-direction: column;
        gap: 10px;
        text-align: center;
      }
    }

    @media (max-width: 480px) {
      h1 { font-size: 2.5rem; }
      .subtitulo { font-size: 1.2rem; }
      header { padding: 20px; }
      .logo-header { height: 80px; }
    }
  </style>
</head>
<body>

  <!-- HEADER VERMELHO -->
  <header>
    <img src="../../../img/FP002.png" alt="FingerPass Logo" class="logo-header">
  </header>

  <!-- CONTEÚDO PRINCIPAL -->
  <main class="main-content">
    <div class="content-wrapper">
      <h1>ACESSO NEGADO!</h1>
      <p class="subtitulo">Verifique sua biometria com sua instituição</p>
    </div>
  </main>

  <!-- FOOTER -->
  <footer class="site-footer">
    <div class="footer-inner">
      <div class="footer-left">
        <img src="../../../img/EMAIL001.png" alt="email" class="img_email">
        <div class="contact-text">
          <span class="label">Contato: fingerpass353@gmail.com</span>
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

<script>
  // Volta para tela de espera após 4 segundos
  setTimeout(() => {
    window.location.href = 'espera.php';
  }, 4000);
</script>
</body>
</html>