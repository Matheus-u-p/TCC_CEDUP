<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FingerPass - Processando</title>
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
      background: linear-gradient(135deg, #2e4a7c 0%, #3d5f9e 100%);
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
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 50px;
      animation: fadeIn 0.5s;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    .spinner {
      width: 120px;
      height: 120px;
      border: 10px solid rgba(255, 255, 255, 0.1);
      border-top: 10px solid #3d5f9e;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      flex-shrink: 0;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    .text-wrapper {
      text-align: left;
    }

    .text-wrapper h1 {
      font-size: 5rem;
      font-weight: 800;
      font-family: 'Changa', sans-serif;
      letter-spacing: 3px;
      margin-bottom: 10px;
      color: #fff;
      text-shadow: 0 4px 10px rgba(0,0,0,0.6);
      text-transform: uppercase;
    }

    .text-wrapper p {
      font-size: 2rem;
      font-weight: 400;
      color: #3d5f9e;
      font-family: 'Jura', sans-serif;
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
      .content-wrapper { flex-direction: column; gap: 30px; }
      .text-wrapper { text-align: center; }
      .text-wrapper h1 { font-size: 4rem; }
      .text-wrapper p { font-size: 1.8rem; }
    }

    @media (max-width: 768px) {
      .text-wrapper h1 { font-size: 3rem; }
      .text-wrapper p { font-size: 1.5rem; }
      .spinner { width: 100px; height: 100px; border-width: 8px; }
      
      .footer-inner {
        flex-direction: column;
        gap: 10px;
        text-align: center;
      }
    }

    @media (max-width: 480px) {
      .text-wrapper h1 { font-size: 2.5rem; }
      .text-wrapper p { font-size: 1.2rem; }
      .spinner { width: 80px; height: 80px; border-width: 6px; }
      header { padding: 20px; }
      .logo-header { height: 80px; }
    }
  </style>
</head>
<body>

  <!-- HEADER AZUL -->
  <header>
    <img src="../../../img/FP002.png" alt="FingerPass Logo" class="logo-header">
  </header>

  <!-- CONTEÚDO PRINCIPAL -->
  <main class="main-content">
    <div class="content-wrapper">
      <div class="spinner"></div>
      <div class="text-wrapper">
        <h1>Processando...</h1>
        <p>Validando os Dados</p>
      </div>
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
  // Monitora mudanças de estado a cada 300ms
  let monitorAtivo = true;

  async function monitorar() {
    if (!monitorAtivo) return;
    
    try {
      const response = await fetch('entrada_controller.php?_=' + Date.now(), {
        cache: 'no-store',
        headers: {
          'Cache-Control': 'no-cache'
        }
      });
      
      const pagina = (await response.text()).trim();
      
      // Se não for mais processando, redireciona
      if (pagina && pagina !== 'processando.php' && !pagina.includes('<!DOCTYPE')) {
        monitorAtivo = false;
        window.location.href = pagina;
        return;
      }
      
    } catch (error) {
      console.error('[ERRO] Monitor:', error);
    }
    
    // Próxima verificação em 300ms
    if (monitorAtivo) {
      setTimeout(monitorar, 300);
    }
  }

  // Inicia monitoramento após 500ms
  setTimeout(() => {
    console.log('[PROCESSANDO] Iniciando monitoramento...');
    monitorar();
  }, 500);

  // Para monitoramento ao sair da página
  window.addEventListener('beforeunload', () => {
    monitorAtivo = false;
  });
</script>
</body>
</html>