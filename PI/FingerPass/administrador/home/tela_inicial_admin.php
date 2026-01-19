<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FingerPass - Home Administrador</title>
  <link rel="icon" type="image/png" href="../../../img/FP006.png">
  <link rel="stylesheet" href="../../../style/home.css">
  <link href="https://fonts.googleapis.com/css2?family=Jura:wght@400;500;600&family=Changa:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>


<?php
// home_admin.php
session_start();

// Verificar se o usuário é administrador
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'Administrador') {
    header('Location: ../../login/tela_de_login.php');
    exit;
}

// Incluir conexão com o banco
include('../../conexao/conexao.php');

// Buscar todos os cursos em ordem alfabética com suas turmas
$sql = "
    SELECT 
        c.id_curso, 
        c.nome,
        COUNT(t.id_turma) as total_turmas
    FROM curso c
    LEFT JOIN turma t ON c.id_curso = t.id_curso
    GROUP BY c.id_curso, c.nome
    ORDER BY c.nome ASC
";

$resultado = mysqli_query($id, $sql);

if (!$resultado) {
    $erro = 'Erro ao buscar cursos: ' . mysqli_error($id);
    $cursos = [];
} else {
    $cursos = [];
    while ($row = mysqli_fetch_assoc($resultado)) {
        $cursos[] = $row;
    }
}
?>


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

    <a href="../../login/Sair.php" class="btn-voltar">Sair</a>
  </header>

<!-- CONTEÚDO PRINCIPAL -->
<main class="main-content">
    <div class="container">
        <!-- Título da Seção -->
        <div class="section-header">
            <h2>PÁGINA DE ADMINISTRADOR</h2>
            <button class="btn-entrada" onclick="iniciarEntradaComServidor()" id="btnIniciarEntrada">
                Iniciar Entrada de Alunos
            </button>
        </div>

        <!-- Título do Registro de Chamada -->
        <h3 class="registro-title">Registro da Chamada</h3>

        <?php if (isset($erro)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <!-- Grid de Cursos -->
        <div class="cursos-grid">
            <?php if (!empty($cursos)): ?>
                <?php foreach ($cursos as $curso): ?>
                    <div class="curso-card">
                        <div class="curso-header">
                            <h4><?php echo htmlspecialchars($curso['nome']); ?></h4>
                            <span class="turmas-badge"><?php echo $curso['total_turmas']; ?> turma(s)</span>
                        </div>
                        
                        <?php
                        // Buscar turmas do curso
                        $sqlTurmas = "
                            SELECT id_turma, n_turma, turno, contra_turno 
                            FROM turma 
                            WHERE id_curso = {$curso['id_curso']}
                            ORDER BY n_turma ASC
                        ";
                        $resultTurmas = mysqli_query($id, $sqlTurmas);
                        ?>
                        
                        <div class="turmas-grid">
                            <?php 
                            $temTurmas = false;
                            if ($resultTurmas && mysqli_num_rows($resultTurmas) > 0):
                                $temTurmas = true;
                                while ($turma = mysqli_fetch_assoc($resultTurmas)):
                                    // Montar o texto da turma
                                    $turmaTexto = htmlspecialchars($turma['n_turma']);
                                    
                            ?>
                                    <a href="../registro/relatorio_turma.php?turma_id=<?php echo $turma['id_turma']; ?>" 
                                       class="turma-btn" 
                                       title="<?php echo htmlspecialchars($turma['n_turma']); ?>">
                                        <?php echo $turmaTexto; ?>
                                    </a>
                            <?php 
                                endwhile;
                            endif;
                            
                            if (!$temTurmas): 
                            ?>
                                <p class="sem-turmas">Sem turmas cadastradas</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    Nenhum curso cadastrado. <a href="../curso/form_curso.php">Cadastrar novo curso</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Seção Outros -->
        <div class="outros-section">
            <h3 class="outros-title">Outros</h3>
            
            <div class="outros-grid">
                <div class="outros-card">
                    <h4>Turmas</h4>
                    <button class="btn-acessar" onclick="window.location.href='../turma/listar_turma.php'">
                        Acessar
                    </button>
                </div>

                <div class="outros-card">
                    <h4>Alunos</h4>
                    <button class="btn-acessar" onclick="window.location.href='../aluno/listar_aluno.php'">
                        Acessar
                    </button>
                </div>

                <div class="outros-card">
                    <h4>Usuários</h4>
                    <button class="btn-acessar" onclick="window.location.href='../usuario/listar_usuario.php'">
                        Acessar
                    </button>
                </div>

                <div class="outros-card">
                    <h4>Horários de Aula</h4>
                    <button class="btn-acessar" onclick="window.location.href='../hora_aula/listar_horaaula.php'">
                        Acessar
                    </button>
                </div>

                <div class="outros-card">
                    <h4>Cursos</h4>
                    <button class="btn-acessar" onclick="window.location.href='../curso/listar_curso.php'">
                        Acessar
                    </button>
                </div>

                <div class="outros-card">
                    <h4>Horários das Turmas</h4>
                    <button class="btn-acessar" onclick="window.location.href='../hora_turma/listar_horaturma.php'">
                        Acessar
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

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
<script>
// ✅ SCRIPT ATUALIZADO PARA tela_inicial_admin.php
// Agora redireciona para espera.php (não entrada_alunos.php)

function iniciarEntradaComServidor() {
    const btn = document.getElementById('btnIniciarEntrada');
    const textoOriginal = btn.textContent;
    
    // Desabilita botão
    btn.disabled = true;
    btn.textContent = 'Verificando servidor...';
    btn.style.opacity = '0.6';
    
    // ✅ CAMINHO CORRETO - 2 níveis acima
    const apiPath = '../../BiometriaEscolar/api/controlar_servidor.php';
    
    console.log('Verificando status do servidor...');
    
    // Verifica status do servidor
    fetch(apiPath + '?acao=status&_=' + Date.now())
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro HTTP: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Status recebido:', data);
            
            if (data.status === 'parado') {
                // Servidor está parado, pergunta se quer iniciar
                if (confirm('O servidor de biometria não está rodando.\n\n' +
                           'Para registrar entradas de alunos, o servidor Python precisa estar ativo.\n\n' +
                           'Deseja iniciar o servidor automaticamente?')) {
                    
                    btn.textContent = 'Iniciando servidor...';
                    console.log('Iniciando servidor...');
                    
                    // Inicia o servidor
                    fetch(apiPath + '?acao=iniciar&_=' + Date.now())
                        .then(response => response.json())
                        .then(result => {
                            console.log('Resultado:', result);
                            
                            if (result.status === 'sucesso') {
                                btn.textContent = 'Servidor iniciado! Redirecionando...';
                                
                                // Aguarda 3 segundos para Arduino conectar
                                setTimeout(() => {
                                    // ✅ MUDANÇA AQUI: espera.php em vez de entrada_alunos.php
                                    window.location.href = '../entrada/espera.php';
                                }, 3000);
                            } else {
                                // Erro ao iniciar
                                alert('Erro ao iniciar servidor:\n\n' + result.mensagem + 
                                      '\n\n Verifique:\n' +
                                      '• Python está instalado?\n' +
                                      '• Arduino está conectado?\n' +
                                      '• Porta COM está correta no config.py?');
                                
                                btn.disabled = false;
                                btn.textContent = textoOriginal;
                                btn.style.opacity = '1';
                            }
                        })
                        .catch(error => {
                            console.error('Erro ao iniciar:', error);
                            alert('Erro na comunicação ao iniciar servidor:\n' + error);
                            btn.disabled = false;
                            btn.textContent = textoOriginal;
                            btn.style.opacity = '1';
                        });
                } else {
                    // Usuário não quer iniciar
                    btn.disabled = false;
                    btn.textContent = textoOriginal;
                    btn.style.opacity = '1';
                }
            } 
            else if (data.status === 'rodando') {
                // Servidor já está rodando, vai direto
                console.log('Servidor já rodando, redirecionando...');
                btn.textContent = 'Redirecionando...';
                setTimeout(() => {
                    // ✅ MUDANÇA AQUI: espera.php em vez de entrada_alunos.php
                    window.location.href = '../entrada/espera.php';
                }, 500);
            } 
            else {
                // Status desconhecido
                console.warn('Status desconhecido:', data);
                if (confirm('Status do servidor desconhecido: ' + data.status + '\n\nIr para tela de entrada mesmo assim?')) {
                    // ✅ MUDANÇA AQUI: espera.php em vez de entrada_alunos.php
                    window.location.href = '../entrada/espera.php';
                } else {
                    btn.disabled = false;
                    btn.textContent = textoOriginal;
                    btn.style.opacity = '1';
                }
            }
        })
        .catch(error => {
            console.error('Erro ao verificar servidor:', error);
            
            alert('Erro ao verificar o servidor de biometria.\n\n' +
                  'Erro: ' + error.message + '\n\n' +
                  'Caminho: ' + apiPath + '\n\n' +
                  'Verifique:\n' +
                  '• O arquivo controlar_servidor.php existe?\n' +
                  '• O XAMPP está rodando?\n' +
                  '• A estrutura de pastas está correta?');
            
            if (confirm('Continuar para tela de entrada mesmo assim?')) {
                // ✅ MUDANÇA AQUI: espera.php em vez de entrada_alunos.php
                window.location.href = '../entrada/espera.php';
            } else {
                btn.disabled = false;
                btn.textContent = textoOriginal;
                btn.style.opacity = '1';
            }
        });
}
</script>
</body>
</html>