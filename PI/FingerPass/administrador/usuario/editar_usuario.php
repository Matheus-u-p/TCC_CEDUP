<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Editar Usuário</title>
    <link rel="icon" type="image/png" href="../../../img/FP006.png">
    <link rel="stylesheet" href="../../../style/cadastrar.css">
    <link href="https://fonts.googleapis.com/css2?family=Jura:wght@400;500;600&family=Changa:wght@400;700&display=swap" rel="stylesheet">
    
    <script>
        function validarEmail(email) {
            const regex = /^(?!.*\.\.)[a-zA-Z0-9](\.?[a-zA-Z0-9_-])*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[A-Za-z]{2,}$/;
            return regex.test(email);
        }

        function verificarEmailTempo(input) {
            const email = input.value.trim();
            const mensagemErro = document.getElementById('erro-email');
            
            if (email === '') {
                mensagemErro.textContent = '';
                input.style.borderColor = '';
                return;
            }
            
            if (!validarEmail(email)) {
                mensagemErro.textContent = 'Email inválido!';
                mensagemErro.style.color = 'red';
                input.style.borderColor = 'red';
            } else {
                mensagemErro.textContent = 'Email válido';
                mensagemErro.style.color = 'green';
                input.style.borderColor = 'green';
            }
        }

        function validarFormulario(form) {
            const emailInput = form.querySelector('input[name="email"]');
            const email = emailInput.value.trim();
            
            if (!validarEmail(email)) {
                alert('Por favor, insira um email válido!');
                emailInput.focus();
                return false;
            }
            return true;
        }
    </script>
</head>

<?php
include('../../conexao/conexao.php');

if (isset($_GET['id'])) {
    $id_usuario = intval($_GET['id']);
} else {
    header("Location: listar_usuario.php");
    exit;
}

$sql = "SELECT * FROM usuario WHERE id_usuario = $id_usuario";
$res = mysqli_query($id, $sql);

if (mysqli_num_rows($res) == 0) {
    header("Location: listar_usuario.php");
    exit;
}

$linha = mysqli_fetch_array($res);
?>

<body>

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

    <a href="listar_usuario.php" class="btn-voltar">Cancelar</a>
  </header>

  <div class="caixa_usuario">
    <div class="titulo-container">
      <h1>Editar Usuário</h1>
    </div>

    <form action="atualiza_usuario.php" method="post" onsubmit="return validarFormulario(this)">
        <p class="subtitulo">Editar as Informações</p>
        <input type="hidden" value='<?php echo $linha['id_usuario']; ?>' name="id_usuario">
        
        <div class="input-container">
          <input 
            type="text" 
            value='<?php echo $linha['email']; ?>' 
            name="email" 
            placeholder="Digite seu Email:" 
            oninput="verificarEmailTempo(this)"
            required>
          <span id="erro-email" style="font-size:0.85rem; margin-top:5px; display:block;"></span>
        </div>
        
        <div class="input-container">
          <input type="text" value='' name="senha" placeholder="Digite sua nova senha:" required>
        </div>

        <div class="input-container">
          <label for="turno" style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Tipo:
          </label>
          <select name="tipo" id="tipo" required>
            <option value="">Selecione</option>
            <option value="2" <?php echo ($linha['tipo'] == '2') ? 'selected' : ''; ?>>Administrador</option>
            <option value="1" <?php echo ($linha['tipo'] == '1') ? 'selected' : ''; ?>>Professor</option>
          </select>
        </div>

        <div class="botao-container">
          <input type="submit" value="Salvar Alterações">
        </div>
    </form>
  </div>

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