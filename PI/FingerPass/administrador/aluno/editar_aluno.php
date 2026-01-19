<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Editar Aluno</title>
    <link rel="icon" type="image/png" href="../../../img/FP006.png">
    <link rel="stylesheet" href="../../../style/cadastrar.css">
    <link href="https://fonts.googleapis.com/css2?family=Jura:wght@400;500;600&family=Changa:wght@400;700&display=swap" rel="stylesheet">
</head>

<?php
include('../../conexao/conexao.php');

// Pega o ID da URL
if (isset($_GET['id_aluno'])) {
    $id_aluno = intval($_GET['id_aluno']);
} else {
    header("Location: listar_aluno.php");
    exit;
}

// Busca os dados do aluno
$sql = "SELECT * FROM aluno WHERE id_aluno = $id_aluno";
$res = mysqli_query($id, $sql);

if (mysqli_num_rows($res) == 0) {
    header("Location: listar_aluno.php");
    exit;
}

$linha = mysqli_fetch_array($res);
$biometria_atual = $linha['biometria'];
?>

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

  <div class="caixa_usuario">
    <div class="titulo-container">
      <h1>Editar Aluno</h1>
    </div>

    <form action="atualiza_aluno.php" method="post">
        <p class="subtitulo">Altere as informações desejadas</p>

        <input type="hidden" name="id_aluno" value="<?php echo $linha['id_aluno']; ?>">
        <input type="hidden" name="biometria_antiga" value="<?php echo $biometria_atual; ?>">
        <input type="hidden" name="biometria" id="biometria_nova" value="<?php echo $biometria_atual; ?>">

        <div class="input-container">
          <label style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Nome:
          </label>
          <input type="text" name="nome" value="<?php echo htmlspecialchars($linha['nome']); ?>" placeholder="Nome completo do aluno" maxlength="70" required>
        </div>

        <div class="input-container">
          <label style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Matrícula:
          </label>
          <input type="text" name="matricula" id="matricula" value="<?php echo htmlspecialchars($linha['matricula']); ?>" placeholder="Matrícula do aluno" maxlength="15">
        </div>

        <div class="input-container">
          <label style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Telefone:
          </label>
          <input type="text" name="telefone" value="<?php echo htmlspecialchars($linha['telefone']); ?>" placeholder="(xx) xxxxx-xxxx" maxlength="15">
        </div>

        <div class="input-container">
          <label for="data_nascimento" style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Data de Nascimento:
          </label>
          <input type="date" name="data_nascimento" id="data_nascimento" value="<?php echo $linha['data_nascimento']; ?>">
        </div>

        <div class="input-container">
          <label style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Sexo:
          </label>
          <select name="sexo">
            <option value="">Selecione o sexo</option>
            <option value="M" <?php echo ($linha['sexo'] == 'M') ? 'selected' : ''; ?>>Masculino</option>
            <option value="F" <?php echo ($linha['sexo'] == 'F') ? 'selected' : ''; ?>>Feminino</option>
          </select>
        </div>

        <!-- SEÇÃO DE BIOMETRIA -->
        <div class="input-container" style="border: 2px solid #3498db; padding: 15px; border-radius: 8px; background: rgba(52, 152, 219, 0.1);">
          <label style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Biometria:
          </label>

            <!-- ✅ NOVO: Botão Servidor -->
          <button type="button" onclick="toggleServidorCadastro()" id="btnServidorCadastro" class="btn-servidor-biometria" style="background: #3A5A8C; color: white; padding: 12px; border: none; border-radius: 5px; cursor: pointer; width: 100%; font-size: 1rem; font-weight: bold; margin-bottom: 10px;">
            Verificando servidor...
          </button>
                  
          <?php if ($biometria_atual): ?>
            <div style="background: rgba(46, 204, 113, 0.2); padding: 12px; border-radius: 5px; margin-bottom: 10px; border: 1px solid #2ecc71;">
              <p style="margin: 0; color: #2ecc71; font-weight: bold;">
                Biometria Cadastrada - ID: <?php echo $biometria_atual; ?>
              </p>
            </div>
            
            <button type="button" onclick="editarBiometria()" class="btn-biometria" style="background: #f39c12; color: white; padding: 12px; border: none; border-radius: 5px; cursor: pointer; width: 100%; font-size: 1rem; font-weight: bold;">
              Atualizar Biometria
            </button>
            
            <button type="button" onclick="removerBiometria()" class="btn-biometria" style="background: #e74c3c; color: white; padding: 12px; border: none; border-radius: 5px; cursor: pointer; width: 100%; font-size: 1rem; font-weight: bold; margin-top: 8px;">
              Remover Biometria
            </button>
          <?php else: ?>
            <div style="background: rgba(231, 76, 60, 0.2); padding: 12px; border-radius: 5px; margin-bottom: 10px; border: 1px solid #e74c3c;">
              <p style="margin: 0; color: #e74c3c; font-weight: bold;">
                Nenhuma Biometria Cadastrada
              </p>
            </div>
            
            <button type="button" onclick="editarBiometria()" class="btn-biometria" style="background: #28a745; color: white; padding: 12px; border: none; border-radius: 5px; cursor: pointer; width: 100%; font-size: 1rem; font-weight: bold;">
              Cadastrar Biometria
            </button>
          <?php endif; ?>
          
          <span id="status_biometria" style="margin-top: 10px; display: block; font-weight: bold;"></span>
        </div>

        <div class="input-container">
          <label style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Turma:
          </label>
          <select name="id_turma" required>
            <option value="">Selecione a Turma</option>
            <?php
              $sql_turmas = "SELECT t.id_turma, t.n_turma, c.nome AS curso_nome 
                             FROM turma t 
                             LEFT JOIN curso c ON t.id_curso = c.id_curso 
                             ORDER BY c.nome ASC, t.n_turma ASC";
              $res_turmas = mysqli_query($id, $sql_turmas);
              while ($turma = mysqli_fetch_assoc($res_turmas)) {
                  $curso = $turma['curso_nome'] ? $turma['curso_nome'] : 'Sem Curso';
                  $selected = ($turma['id_turma'] == $linha['id_turma']) ? 'selected' : '';
                  echo "<option value='{$turma['id_turma']}' $selected>
                          {$turma['n_turma']} - {$curso}
                        </option>";
              }
            ?>
          </select>
        </div>

        <div class="botao-container">
          <input type="submit" value="Salvar Alterações">
        </div>
    </form>
  </div>

  <script>
    // Função para editar/atualizar biometria
    async function editarBiometria() {
      const matricula = document.getElementById('matricula').value;
      const nome = document.querySelector('input[name="nome"]').value;
      const biometriaAntiga = <?php echo $biometria_atual ? $biometria_atual : 'null'; ?>;
      
      if (!matricula || !nome) {
        alert('Preencha o nome e matrícula antes de cadastrar a biometria!');
        return;
      }
      
      // Confirma ação
      if (biometriaAntiga) {
        const confirma = confirm('Isso vai DELETAR a biometria antiga (ID: ' + biometriaAntiga + ') e cadastrar uma nova. Deseja continuar?');
        if (!confirma) return;
      }
      
      // Exibe mensagem
      document.getElementById('status_biometria').innerHTML = 'Aguarde... Coloque o dedo no sensor!';
      document.getElementById('status_biometria').style.color = '#ffc107';
      
      try {
        // Chama API para editar biometria
        const response = await fetch(`/TCC/VFP9.0/PI/FingerPass/BiometriaEscolar/api/api_biometria.php?acao=editar&matricula=${matricula}&nome=${encodeURIComponent(nome)}&biometria_antiga=${biometriaAntiga || ''}`);
        const data = await response.json();
        
        if (data.status === 'sucesso') {
          document.getElementById('biometria_nova').value = data.sensor_id;
          document.getElementById('status_biometria').innerHTML = `Biometria ${biometriaAntiga ? 'atualizada' : 'cadastrada'}! Novo ID: ${data.sensor_id}`;
          document.getElementById('status_biometria').style.color = '#28a745';
          
          // Atualiza a página após 2 segundos para mostrar novo status
          setTimeout(() => {
            alert('Biometria ' + (biometriaAntiga ? 'atualizada' : 'cadastrada') + ' com sucesso! Salve as alterações.');
          }, 500);
        } else {
          document.getElementById('status_biometria').innerHTML = `${data.mensagem}`;
          document.getElementById('status_biometria').style.color = '#dc3545';
        }
      } catch (error) {
        document.getElementById('status_biometria').innerHTML = 'Erro de comunicação! Verifique se o servidor Python está rodando.';
        document.getElementById('status_biometria').style.color = '#dc3545';
        console.error('Erro:', error);
      }
    }
    
    // Função para remover biometria
    function removerBiometria() {
      const biometriaAntiga = <?php echo $biometria_atual ? $biometria_atual : 'null'; ?>;
      
      if (!biometriaAntiga) {
        alert('Não há biometria para remover!');
        return;
      }
      
      const confirma = confirm('Tem certeza que deseja REMOVER a biometria atual (ID: ' + biometriaAntiga + ')?\n\nEla será deletada do sensor!');
      if (!confirma) return;
      
      document.getElementById('biometria_nova').value = '';
      document.getElementById('status_biometria').innerHTML = 'Biometria será removida ao salvar';
      document.getElementById('status_biometria').style.color = '#e74c3c';
      
      alert('Não esqueça de SALVAR as alterações para confirmar a remoção!');
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
      btn.textContent = '🔍 Verificando...';
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