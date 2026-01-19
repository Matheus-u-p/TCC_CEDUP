<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Editar Curso</title>
    <link rel="icon" type="image/png" href="../../../img/FP006.png">
    <link rel="stylesheet" href="../../../style/cadastrar.css">
    <link href="https://fonts.googleapis.com/css2?family=Jura:wght@400;500;600&family=Changa:wght@400;700&display=swap" rel="stylesheet">
</head>

<?php
include('../../conexao/conexao.php');

// Pega o ID da URL (ex: editar_curso.php?id_curso=3)
if (isset($_GET['id_curso'])) {
    $id_curso = intval($_GET['id_curso']);
} else {
    // Se não tiver id, volta para listagem
    header("Location: listar_curso.php");
    exit;
}

// Busca os dados do curso
$sql = "SELECT * FROM curso WHERE id_curso = $id_curso";
$res = mysqli_query($id, $sql);

if (mysqli_num_rows($res) == 0) {
    header("Location: listar_curso.php");
    exit;
}

$linha = mysqli_fetch_array($res);
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

    <a href="listar_curso.php" class="btn-voltar">Cancelar</a>
  </header>

  <!-- Conteúdo -->
  <div class="caixa_usuario">
    <div class="titulo-container">
      <h1>Editar Curso</h1>
    </div>

    <form action="atualiza_curso.php" method="post">
        <p class="subtitulo">Altere as informações desejadas</p>

        <input type="hidden" name="id_curso" value="<?php echo $linha['id_curso']; ?>">

        <div class="input-container">
          <input type="text" name="nome" value="<?php echo htmlspecialchars($linha['nome']); ?>" placeholder="Nome do Curso" maxlength="100" required>
        </div>

        <div class="botao-container">
          <input type="submit" value="Salvar Alterações">
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
