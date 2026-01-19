<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Cadastrar Usuário</title>
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

    <a href="listar_usuario.php" class="btn-voltar">Cancelar</a>
  </header>

  <div class="caixa_usuario">
    <div class="titulo-container">
      <h1>Cadastrar Usuário</h1>
    </div>

    <form action="cadastro_usuario.php" method="post" onsubmit="return validarFormulario(this)">
        <p class="subtitulo">Preencha as Informações</p>
        
        <div class="input-container">
          <input 
            type="text" 
            name="email" 
            placeholder="Digite seu E-Mail:" 
            oninput="verificarEmailTempo(this)"
            required>
          <span id="erro-email" style="font-size:0.85rem; margin-top:5px; display:block;"></span>
        </div>
        
        <div class="input-container">
          <input type="password" name="senha" placeholder="Digite sua Senha:" required>
        </div>

        <div class="input-container">
          <label for="tipo" style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Tipo:
          </label>
          <select name="tipo" id="tipo" required>
            <option value="">Selecione</option>
            <option value="2">Administrador</option>
            <option value="1">Professor</option>
          </select>
        </div>

        <div class="botao-container">
          <input type="submit" value="Cadastrar">
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