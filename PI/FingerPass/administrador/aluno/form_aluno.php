<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Cadastrar Aluno</title>
    <link rel="icon" type="image/png" href="../../../img/FP006.png">
    <link rel="stylesheet" href="../../../style/cadastrar.css">
    <link href="https://fonts.googleapis.com/css2?family=Jura:wght@400;500;600&family=Changa:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Estilos para os avisos em tempo real */
        .avisos-container {
            display: none;
            max-height: 300px;
            overflow-y: auto;
            background: #1e1e2e;
            border: 1px solid #3498db;
            border-radius: 5px;
            padding: 10px;
            margin-top: 15px;
        }
        
        .avisos-container.ativo {
            display: block;
        }
        
        .aviso-item {
            padding: 8px 12px;
            margin-bottom: 8px;
            border-radius: 4px;
            font-size: 0.9rem;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .aviso-info {
            background: rgba(52, 152, 219, 0.2);
            border-left: 4px solid #3498db;
            color: #3498db;
        }
        
        .aviso-processando {
            background: rgba(241, 196, 15, 0.2);
            border-left: 4px solid #f1c40f;
            color: #f1c40f;
        }
        
        .aviso-sucesso {
            background: rgba(46, 204, 113, 0.2);
            border-left: 4px solid #2ecc71;
            color: #2ecc71;
        }
        
        .aviso-erro {
            background: rgba(231, 76, 60, 0.2);
            border-left: 4px solid #e74c3c;
            color: #e74c3c;
        }
        
        .aviso-icone {
            font-size: 1.2rem;
            margin-right: 8px;
        }
        
        .btn-biometria.processando {
            cursor: not-allowed;
            opacity: 0.7;
        }
    </style>
</head>
<body>

  <!-- Header -->
  <header>
    <div class="logo">
        <img src="../../../img/FP001.png" alt="FingerPass Logo" class="logo_cabecalho">
    </div>
    
    <div class="usuario">
        Seja bem-vindo Administrador(a)<br>
        <strong>
            <?php 
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            echo isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuário';
            ?>
        </strong>
    </div>

    <a href="listar_aluno.php" class="btn-voltar">Cancelar</a>
  </header>

  <!-- Caixa principal -->
  <div class="caixa_usuario">
    <div class="titulo-container">
      <h1>Cadastrar Aluno</h1>
    </div>

    <form action="cadastro_aluno.php" method="post" id="formAluno">
        <p class="subtitulo">Preencha as Informações</p>

        <div class="input-container">
          <label style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Nome:
          </label>
          <input type="text" name="nome" id="nome" placeholder="Nome completo do aluno" maxlength="70" required>
        </div>

        <div class="input-container">
          <label style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Matrícula:
          </label>
          <input type="text" name="matricula" id="matricula" placeholder="xxxxxxxxxx" maxlength="15" required>
        </div>

        <div class="input-container">
          <label style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Telefone:
          </label>
          <input type="text" name="telefone" placeholder="(xx) xxxxx-xxxx" maxlength="15">
        </div>

        <div class="input-container">
          <label for="data_nasc" style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Data de Nascimento:
          </label>
          <input type="date" name="data_nasc" id="data_nasc">
        </div>

        <div class="input-container">
          <label style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Sexo:
          </label>
          <select name="sexo">
            <option value="">Selecione o sexo</option>
            <option value="M">Masculino</option>
            <option value="F">Feminino</option>
          </select>
        </div>

        <div class="input-container">
          <label style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Turma:
          </label>
          <select name="id_turma" required>
            <option value="">Selecione a Turma</option>
            <?php
              include('../../conexao/conexao.php');
              $query = "SELECT t.id_turma, t.n_turma, c.nome AS curso_nome 
                        FROM turma t 
                        LEFT JOIN curso c ON t.id_curso = c.id_curso 
                        ORDER BY c.nome ASC, t.n_turma ASC";
              $result = mysqli_query($id, $query);
              while ($turma = mysqli_fetch_assoc($result)) {
                  $curso = $turma['curso_nome'] ? $turma['curso_nome'] : 'Sem Curso';
                  echo "<option value='{$turma['id_turma']}'>
                          {$turma['n_turma']} - {$curso}
                        </option>";
              }
            ?>
          </select>
        </div>

        <!-- Campo de Biometria -->
        <div class="input-container" style="border: 2px solid #3498db; padding: 15px; border-radius: 8px; background: rgba(52, 152, 219, 0.1);">
          <label style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Biometria:
          </label>

            <!-- ✅ NOVO: Botão Servidor -->
          <button type="button" onclick="toggleServidorCadastro()" id="btnServidorCadastro" class="btn-servidor-biometria" style="background: #3A5A8C; color: white; padding: 12px; border: none; border-radius: 5px; cursor: pointer; width: 100%; font-size: 1rem; font-weight: bold; margin-bottom: 10px;">
            Verificando servidor...
          </button>

          <input type="hidden" name="biometria" id="biometria" value="">
          <button type="button" onclick="cadastrarBiometria()" id="btnBiometria" class="btn-biometria" style="background: #28a745; color: white; padding: 12px; border: none; border-radius: 5px; cursor: pointer; width: 100%; font-size: 1rem; font-weight: bold;">
            Cadastrar Biometria
          </button>
          
          <!-- Container de Avisos -->
          <div id="avisosContainer" class="avisos-container">
            <div id="avisosList"></div>
          </div>
          
          <span id="status_biometria" style="margin-top: 10px; display: block; font-weight: bold;"></span>
        </div>

        <script>
            let monitoramentoAtivo = false;
            let ultimaMensagem = '';
            let intervaloMonitoramento = null;
            let mensagensExibidas = new Set();

            // 🔧 FUNÇÃO PRINCIPAL
            async function cadastrarBiometria() {
              const matricula = document.getElementById('matricula').value.trim();
              const nome = document.getElementById('nome').value.trim();
              const btnBiometria = document.getElementById('btnBiometria');
              const avisosContainer = document.getElementById('avisosContainer');
              const avisosList = document.getElementById('avisosList');
              const statusBiometria = document.getElementById('status_biometria');
              
              // ✅ Validação
              if (!matricula || !nome) {
                alert('Preencha o nome e matrícula antes de cadastrar a biometria!');
                return;
              }
              
              // 🚫 IMPEDE CLIQUES MÚLTIPLOS
              if (btnBiometria.disabled) {
                alert('Aguarde o processo atual terminar!');
                return;
              }
              
              // 🧹 Limpa avisos anteriores e reseta variáveis
              avisosList.innerHTML = '';
              avisosContainer.classList.add('ativo');
              ultimaMensagem = '';
              mensagensExibidas.clear();
              
              // 🚫 Desabilita botão
              btnBiometria.disabled = true;
              btnBiometria.classList.add('processando');
              btnBiometria.innerHTML = 'Processando...';
              
              // 📍 PASSO 1: Limpar status.json antes de começar
              adicionarAviso('info', '', 'Limpando status anterior...');
              try {
                await fetch(`/TCC/VFP9.0/PI/FingerPass/BiometriaEscolar/api/api_biometria.php?acao=limpar_status&_=${Date.now()}`);
              } catch (e) {
                console.log('Erro ao limpar status (ignorado):', e);
              }
              
              await new Promise(r => setTimeout(r, 500)); // Aguarda 0.5s
              
              // 📍 PASSO 2: Iniciar monitoramento ANTES da requisição
              statusBiometria.innerHTML = '';
              statusBiometria.style.color = '#ffc107';
              
              monitoramentoAtivo = true;
              monitorarStatus();
              
              // 📍 PASSO 3: Enviar requisição
              adicionarAviso('info', '', 'Enviando requisição para o servidor...');
              
              try {
                const apiUrl = `/TCC/VFP9.0/PI/FingerPass/BiometriaEscolar/api/api_biometria.php?acao=cadastrar&matricula=${encodeURIComponent(matricula)}&nome=${encodeURIComponent(nome)}&_=${Date.now()}`;
                
                console.log('URL da API:', apiUrl);
                
                // ⏳ Aguarda resposta com timeout de 65 segundos
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 65000);
                
                const response = await fetch(apiUrl, { signal: controller.signal });
                clearTimeout(timeoutId);
                
                if (!response.ok) {
                  throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                console.log('Resposta da API:', data);
                
                // ⏹️ Para monitoramento
                monitoramentoAtivo = false;
                if (intervaloMonitoramento) {
                  clearInterval(intervaloMonitoramento);
                }
                
                // ✅ SUCESSO
                if (data.status === 'sucesso') {
                  document.getElementById('biometria').value = data.sensor_id;
                  
                  // Só adiciona mensagem se ainda não foi adicionada
                  if (!ultimaMensagem.includes('cadastrada')) {
                    adicionarAviso('sucesso', '', `Concluído! ID: ${data.sensor_id}`);
                  }
                  
                  statusBiometria.innerHTML = `Biometria cadastrada! ID: ${data.sensor_id}`;
                  statusBiometria.style.color = '#28a745';
                  
                  // Reabilita botão após 2 segundos
                  setTimeout(() => {
                    btnBiometria.disabled = false;
                    btnBiometria.classList.remove('processando');
                    btnBiometria.innerHTML = 'Cadastrado - ID: ' + data.sensor_id;
                    btnBiometria.style.background = '#28a745';
                  }, 2000);
                  
                } 
                // ❌ ERRO
                else {
                  const mensagemErro = data.mensagem || 'Erro desconhecido ao cadastrar biometria';
                  adicionarAviso('erro', '', mensagemErro);
                  statusBiometria.innerHTML = `${mensagemErro}`;
                  statusBiometria.style.color = '#dc3545';
                  
                  // Reabilita botão
                  btnBiometria.disabled = false;
                  btnBiometria.classList.remove('processando');
                  btnBiometria.innerHTML = 'Tentar Novamente';
                  btnBiometria.style.background = '#dc3545';
                }
                
              } catch (error) {
                // ⏹️ Para monitoramento em caso de erro
                monitoramentoAtivo = false;
                if (intervaloMonitoramento) {
                  clearInterval(intervaloMonitoramento);
                }
                
                console.error('Erro detalhado:', error);
                
                let mensagemErro = 'Erro de comunicação!';
                
                if (error.name === 'AbortError') {
                  mensagemErro = 'Tempo esgotado! Processo demorou mais de 60 segundos.';
                } else if (error.message.includes('HTTP 404')) {
                  mensagemErro = 'Arquivo api_biometria.php não encontrado!';
                  adicionarAviso('erro', 'Verifique o caminho: /BiometriaEscolar/api/api_biometria.php');
                } else if (error.message.includes('Failed to fetch')) {
                  mensagemErro = 'Servidor Python não está rodando ou API inacessível';
                  adicionarAviso('info', 'Inicie o servidor: python servidor_biometria.py');
                }
                
                adicionarAviso('erro', mensagemErro);
                
                statusBiometria.innerHTML = mensagemErro;
                statusBiometria.style.color = '#dc3545';
                
                // Reabilita botão
                btnBiometria.disabled = false;
                btnBiometria.classList.remove('processando');
                btnBiometria.innerHTML = 'Tentar Novamente';
                btnBiometria.style.background = '#dc3545';
              }
            }

            // 📡 Monitora status.json em tempo real
            async function monitorarStatus() {
              intervaloMonitoramento = setInterval(async () => {
                if (!monitoramentoAtivo) {
                  clearInterval(intervaloMonitoramento);
                  return;
                }
                
                try {
                  const statusUrl = `/TCC/VFP9.0/PI/FingerPass/BiometriaEscolar/api/api_biometria.php?acao=status&_=${Date.now()}`;
                  const response = await fetch(statusUrl);
                  const data = await response.json();
                  
                  // Se houver mensagem nova, exibe
                  if (data.mensagem && data.mensagem !== ultimaMensagem && !mensagensExibidas.has(data.mensagem)) {
                    ultimaMensagem = data.mensagem;
                    mensagensExibidas.add(data.mensagem);
                    
                    let icone = '';
                    let tipo = 'info';
                    
                    const msg = data.mensagem.toLowerCase();
                    
                    // Define ícone e tipo baseado na mensagem
                    if (msg.includes('preparando') || msg.includes('aguarde')) {
                      icone = '';
                      tipo = 'processando';
                    } 
                    else if (msg.includes('id #') || msg.includes('usando id')) {
                      icone = '';
                      tipo = 'info';
                    }
                    else if (msg.includes('coloque o dedo') && !msg.includes('novamente')) {
                      icone = '';
                      tipo = 'processando';
                    } 
                    else if (msg.includes('coloque') && msg.includes('novamente')) {
                      icone = '';
                      tipo = 'processando';
                    }
                    else if (msg.includes('retire')) {
                      icone = '';
                      tipo = 'info';
                    } 
                    else if (msg.includes('capturada')) {
                      icone = '';
                      tipo = 'sucesso';
                    }
                    else if (msg.includes('criando modelo')) {
                      icone = '';
                      tipo = 'processando';
                    }
                    else if (msg.includes('salvando')) {
                      icone = '';
                      tipo = 'processando';
                    }
                    else if (msg.includes('cadastrada') || msg.includes('concluído')) {
                      icone = '';
                      tipo = 'sucesso';
                    } 
                    else if (msg.includes('falha') || msg.includes('erro')) {
                      icone = '';
                      tipo = 'erro';
                    }
                    else if (msg.includes('timeout') || msg.includes('tempo esgotado')) {
                      icone = '';
                      tipo = 'erro';
                    }
                    
                    adicionarAviso(tipo, icone, data.mensagem);
                  }
                } catch (error) {
                  console.error('Erro ao monitorar status:', error);
                }
              }, 500); // Verifica a cada 0.5 segundos
            }

            // 📌 Adiciona aviso na lista
            function adicionarAviso(tipo, icone, mensagem) {
              const avisosList = document.getElementById('avisosList');
              const aviso = document.createElement('div');
              aviso.className = `aviso-item aviso-${tipo}`;
              aviso.innerHTML = `
                <span class="aviso-icone">${icone}</span>
                <span>${mensagem}</span>
              `;
              avisosList.appendChild(aviso);
              
              // Scroll automático para o último aviso
              const container = document.getElementById('avisosContainer');
              container.scrollTop = container.scrollHeight;
            }

// ========== CONTROLE DO SERVIDOR DE CADASTRO - VERSÃO CORRIGIDA ==========
let servidorCadastroAtivo = false;

async function verificarStatusServidorCadastro() {
  try {
    const response = await fetch('/TCC/VFP9.0/PI/FingerPass/BiometriaEscolar/api/controlar_servidor.php?acao=status_cadastro&_=' + Date.now());
    
    if (!response.ok) {
      console.error('[ERRO HTTP]', response.status, response.statusText);
      atualizarBotaoServidorCadastro('erro');
      return;
    }
    
    const texto = await response.text();
    console.log('[DEBUG] Resposta status_cadastro:', texto);
    
    if (!texto || texto.trim() === '') {
      console.error('[ERRO] Resposta vazia do servidor');
      atualizarBotaoServidorCadastro('erro');
      return;
    }
    
    let data;
    try {
      data = JSON.parse(texto);
    } catch (e) {
      console.error('[ERRO JSON]', e);
      console.error('[TEXTO RECEBIDO]', texto.substring(0, 200));
      atualizarBotaoServidorCadastro('erro');
      return;
    }
    
    console.log('[DEBUG] Status:', data.status);
    atualizarBotaoServidorCadastro(data.status);
    
  } catch (error) {
    console.error('[ERRO] Ao verificar servidor de cadastro:', error);
    atualizarBotaoServidorCadastro('erro');
  }
}

function atualizarBotaoServidorCadastro(status) {
  const btn = document.getElementById('btnServidorCadastro');
  
  if (status === 'rodando') {
    servidorCadastroAtivo = true;
    btn.textContent = 'Servidor Ativo - Clique para Parar';
    btn.style.background = '#28a745';
    btn.disabled = false; // ✅ MUDANÇA: Habilita o botão para poder parar
    btn.onclick = pararServidorCadastro; // ✅ MUDANÇA: Troca função para parar
  } else if (status === 'parado') {
    servidorCadastroAtivo = false;
    btn.textContent = 'Iniciar Servidor';
    btn.style.background = '#3A5A8C';
    btn.disabled = false;
    btn.onclick = toggleServidorCadastro;
  } else {
    servidorCadastroAtivo = false;
    btn.textContent = 'Servidor Offline';
    btn.style.background = '#dc3545';
    btn.disabled = false;
    btn.onclick = toggleServidorCadastro;
  }
}

// ✅ NOVA FUNÇÃO: Para o servidor
async function pararServidorCadastro() {
  const btn = document.getElementById('btnServidorCadastro');
  
  if (!confirm('Deseja realmente PARAR o servidor de cadastro?\n\nIsso interromperá qualquer cadastro em andamento.')) {
    return;
  }
  
  btn.disabled = true;
  btn.textContent = 'Parando servidor...';
  btn.style.background = '#666';
  
  try {
    const response = await fetch('/TCC/VFP9.0/PI/FingerPass/BiometriaEscolar/api/controlar_servidor.php?acao=parar_cadastro&_=' + Date.now());
    
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }
    
    const texto = await response.text();
    console.log('[DEBUG] Resposta parar:', texto);
    
    if (!texto || texto.trim() === '') {
      throw new Error('Resposta vazia do servidor');
    }
    
    let data;
    try {
      data = JSON.parse(texto);
    } catch (e) {
      console.error('[ERRO PARSE]', texto.substring(0, 200));
      throw new Error('Resposta inválida: não é JSON');
    }
    
    if (data.status === 'sucesso') {
      alert('Servidor de cadastro parado com sucesso!');
      await verificarStatusServidorCadastro();
    } else {
      throw new Error(data.mensagem || 'Erro desconhecido');
    }
    
  } catch (error) {
    console.error('[ERRO PARAR]', error);
    alert('Erro ao parar servidor:\n\n' + error.message);
    
    btn.disabled = false;
    btn.textContent = 'Servidor Ativo - Clique para Parar';
    btn.style.background = '#28a745';
  }
}

async function toggleServidorCadastro() {
  const btn = document.getElementById('btnServidorCadastro');
  btn.disabled = true;
  btn.textContent = 'Iniciando servidor...';
  btn.style.background = '#666';
  
  try {
    const response = await fetch('/TCC/VFP9.0/PI/FingerPass/BiometriaEscolar/api/controlar_servidor.php?acao=iniciar_cadastro&_=' + Date.now());
    
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }
    
    const texto = await response.text();
    console.log('[DEBUG] Resposta iniciar:', texto);
    
    if (!texto || texto.trim() === '') {
      throw new Error('Resposta vazia do servidor');
    }
    
    let data;
    try {
      data = JSON.parse(texto);
    } catch (e) {
      console.error('[ERRO PARSE]', texto.substring(0, 200));
      throw new Error('Resposta inválida: não é JSON');
    }
    
    console.log('[DEBUG] Data:', data);
    
    if (data.status === 'sucesso') {
      btn.textContent = 'Aguarde 5 segundos...';
      
      // Contador regressivo visual
      for (let i = 5; i > 0; i--) {
        btn.textContent = `Aguarde ${i} segundo${i > 1 ? 's' : ''}...`;
        await new Promise(r => setTimeout(r, 1000));
      }
      
      // Verifica status
      btn.textContent = 'Verificando...';
      await verificarStatusServidorCadastro();
      
      // Verifica novamente após 2 segundos
      setTimeout(async () => {
        await verificarStatusServidorCadastro();
        
        if (!servidorCadastroAtivo) {
          alert('O servidor pode demorar um pouco para iniciar.\n\nSe após 10 segundos ainda não funcionar, verifique:\n• Arduino está conectado?\n• Python está instalado?\n• Porta COM está correta no config.py?');
        } else {
          alert('Servidor de cadastro iniciado com sucesso!\n\nAgora você pode cadastrar biometrias.');
        }
      }, 2000);
      
    } else {
      let mensagemErro = data.mensagem || 'Erro desconhecido';
      alert('Erro ao iniciar servidor:\n\n' + mensagemErro);
      
      btn.disabled = false;
      btn.textContent = 'Tentar Novamente';
      btn.style.background = '#dc3545';
    }
  } catch (error) {
    console.error('[ERRO FETCH]', error);
    
    alert('Erro de comunicação:\n\n' + error.message + 
          '\n\n Possíveis causas:\n' +
          '• Arquivo controlar_servidor.php não encontrado\n' +
          '• XAMPP Apache não está rodando\n' +
          '• Caminho da API está incorreto\n' +
          '• PHP gerando erro antes do JSON');
    
    btn.disabled = false;
    btn.textContent = 'Tentar Novamente';
    btn.style.background = '#dc3545';
  }
}

// ========== INICIALIZAÇÃO ==========
window.addEventListener('DOMContentLoaded', () => {
  console.log('[INIT] Verificando status do servidor de cadastro...');
  verificarStatusServidorCadastro();
  
  // Verifica a cada 10 segundos se o servidor ainda está rodando
  setInterval(() => {
    verificarStatusServidorCadastro();
  }, 10000);
});
        </script>

<script>
  // ========== MÁSCARA DE TELEFONE ==========
  function aplicarMascaraTelefone(input) {
    let valor = input.value.replace(/\D/g, '');
    
    if (valor.length <= 10) {
      valor = valor.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
    } else {
      valor = valor.replace(/^(\d{2})(\d{5})(\d{0,4}).*/, '($1) $2-$3');
    }
    
    input.value = valor;
  }

  document.addEventListener('DOMContentLoaded', function() {
    const campoTelefone = document.querySelector('input[name="telefone"]');
    
    if (campoTelefone) {
      campoTelefone.addEventListener('input', function() {
        aplicarMascaraTelefone(this);
      });
      
      campoTelefone.addEventListener('paste', function() {
        setTimeout(() => aplicarMascaraTelefone(this), 10);
      });
    }
  });
</script>

        <div class="botao-container">
          <input type="submit" value="Cadastrar">
        </div>
    </form>
  </div>

  <!-- Rodapé -->
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