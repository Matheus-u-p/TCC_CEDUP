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

// Verificar se o usuário é Professor
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'Professor') {
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
      Seja bem-vindo Professor(a)<br>
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
            <h2>PÁGINA DO PROFESSOR</h2>
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
                                    <a href="../registro/relatorio_turma_prof.php?turma_id=<?php echo $turma['id_turma']; ?>" 
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
                    Nenhum curso cadastrado.
                </div>
            <?php endif; ?>
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

</body>
</html>