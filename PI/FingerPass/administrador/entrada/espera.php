<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FingerPass - Aguardando</title>
  <link rel="icon" type="image/png" href="../../../img/FP006.png">
  <link rel="stylesheet" href="../../../style/entrada.css">
  <link href="https://fonts.googleapis.com/css2?family=Jura:wght@400;500;600&family=Changa:wght@400;700&display=swap" rel="stylesheet">
  <style>
    @keyframes pulse {
      0%, 100% { transform: scale(1); opacity: 1; }
      50% { transform: scale(1.1); opacity: 0.8; }
    }
    .icon_bio {
      animation: pulse 2s infinite;
    }
    
    .btn-servidor {
      padding: 12px 20px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 700;
      font-family: 'Changa', sans-serif;
      font-size: 1rem;
      transition: all 0.3s ease;
    }
    
    .btn-servidor:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }
  </style>
</head>
<body>

  <header>
    <div class="datetime-container">
      <div class="date" id="currentDate"></div>
      <div class="time" id="currentTime"></div>
    </div>

    <div class="logo">
      <img src="../../../img/FP002.png" alt="FingerPass Logo" class="logo_cabecalho">
    </div>

    <div style="display: flex; gap: 10px;">
      <button class="btn-servidor" id="btnServidor" onclick="toggleServidor()">
        Verificando...
      </button>
      
      <button class="btn-terminar" onclick="terminarEntrada()">
        Terminar Entrada de Alunos
      </button>
    </div>
  </header>

  <main class="main-content">
    <div class="biometria-container">
      <div class="fingerprint-icon">
        <img src="../../../img/icon_bio.png" alt="Biometria" class="icon_bio">
      </div>

      <div class="content-right">
        <h1 class="title">APRESENTE SUA<br>DIGITAL AO SENSOR</h1>
        <div class="status-message" style="font-size: 24px; color: #00ff9d; margin-top: 20px;">
          Aguardando leitura...
        </div>
      </div>
    </div>
  </main>

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
  // ========== DATA E HORA ==========
  function updateDateTime() {
    const now = new Date();
    const day = String(now.getDate()).padStart(2, '0');
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const year = now.getFullYear();
    document.getElementById('currentDate').textContent = `${day}/${month}/${year}`;
    
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    document.getElementById('currentTime').textContent = `${hours}:${minutes}:${seconds}`;
  }
  updateDateTime();
  setInterval(updateDateTime, 1000);

  // ========== CONTROLE DO SERVIDOR ==========
  let servidorAtivo = false;
  let verificandoServidor = false;

  function atualizarBotaoServidor(status) {
    const btn = document.getElementById('btnServidor');
    
    if (status === 'rodando') {
      servidorAtivo = true;
      btn.textContent = 'Parar Servidor';
      btn.style.background = '#852527';
      btn.style.color = '#fff';
    } else {
      servidorAtivo = false;
      btn.textContent = 'Iniciar Servidor';
      btn.style.background = '#1b7e2a';
      btn.style.color = '#fff';
    }
  }

  async function verificarStatusServidor() {
    if (verificandoServidor) return; // Evita múltiplas verificações simultâneas
    
    verificandoServidor = true;
    
    try {
      const response = await fetch('../../BiometriaEscolar/api/controlar_servidor.php?acao=status&_=' + Date.now());
      const data = await response.json();
      atualizarBotaoServidor(data.status);
    } catch (error) {
      console.error('[ERRO] Ao verificar status:', error);
    } finally {
      verificandoServidor = false;
    }
  }

  async function toggleServidor() {
    const btn = document.getElementById('btnServidor');
    btn.disabled = true;
    btn.textContent = 'Processando...';
    btn.style.background = '#666';
    
    const acao = servidorAtivo ? 'parar' : 'iniciar';
    
    try {
      const response = await fetch('../../BiometriaEscolar/api/controlar_servidor.php?acao=' + acao + '&_=' + Date.now());
      const data = await response.json();
      
      if (data.status === 'sucesso') {
        alert(data.mensagem);
        await verificarStatusServidor();
      } else {
        alert('Erro: ' + data.mensagem);
      }
    } catch (error) {
      alert('Erro ao controlar servidor: ' + error);
      await verificarStatusServidor();
    } finally {
      btn.disabled = false;
    }
  }

  // Verifica status ao carregar e a cada 30 segundos (não 10)
  verificarStatusServidor();
  setInterval(verificarStatusServidor, 30000);

  // ========== MONITOR DE ENTRADA (SEM PISCADAS) ==========
  let monitorAtivo = true;
  let ultimaPagina = 'espera.php';
  let requisicaoEmAndamento = false;

  async function monitorar() {
    if (!monitorAtivo || requisicaoEmAndamento) return;
    
    requisicaoEmAndamento = true;
    
    try {
      const response = await fetch('entrada_controller.php?_=' + Date.now(), {
        cache: 'no-store',
        headers: {
          'Cache-Control': 'no-cache'
        }
      });
      
      const pagina = (await response.text()).trim();
      
      // Debug
      if (pagina !== ultimaPagina) {
        console.log('[MUDANÇA] De:', ultimaPagina, '→ Para:', pagina);
      }
      
      // Só redireciona se for REALMENTE diferente E não for espera.php
      if (pagina && 
          pagina !== 'espera.php' && 
          pagina !== ultimaPagina &&
          !pagina.includes('<!DOCTYPE')) { // Evita redirecionamento se vier HTML
        
        ultimaPagina = pagina;
        monitorAtivo = false; // Para o loop
        
        console.log('[REDIRECIONANDO] Para:', pagina);
        window.location.href = pagina;
      } else {
        ultimaPagina = pagina;
      }
      
    } catch (error) {
      console.error('[ERRO] Monitor:', error);
    } finally {
      requisicaoEmAndamento = false;
      
      // Próxima verificação em 800ms
      if (monitorAtivo) {
        setTimeout(monitorar, 800);
      }
    }
  }

  // Inicia monitoramento após 1.5 segundos (dá tempo da página carregar)
  setTimeout(() => {
    console.log('[INIT] Iniciando monitoramento...');
    monitorar();
  }, 1500);

  // ========== TERMINAR ENTRADA ==========
  function terminarEntrada() {
    if (confirm('Deseja terminar a entrada de alunos e PARAR o servidor?')) {
      monitorAtivo = false; // Para monitoramento
      
      fetch('../../BiometriaEscolar/api/controlar_servidor.php?acao=parar&_=' + Date.now())
        .then(() => {
          window.location.href = '../home/tela_inicial_admin.php';
        })
        .catch(() => {
          // Redireciona mesmo com erro
          window.location.href = '../home/tela_inicial_admin.php';
        });
    }
  }

  // Para monitoramento ao sair da página
  window.addEventListener('beforeunload', () => {
    monitorAtivo = false;
  });
</script>
</body>
</html>