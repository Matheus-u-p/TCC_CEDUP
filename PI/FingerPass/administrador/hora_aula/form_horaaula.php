<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Cadastrar Horário de Aula</title>
    <link rel="icon" type="image/png" href="../../../img/FP006.png">
    <link rel="stylesheet" href="../../../style/cadastrar.css">
    <link href="https://fonts.googleapis.com/css2?family=Jura:wght@400;500;600&family=Changa:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

  <!-- Cabeçalho -->
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

    <a href="listar_horaaula.php" class="btn-voltar">Cancelar</a>
  </header>

  <!-- Corpo principal -->
  <div class="caixa_usuario">
    <div class="titulo-container">
      <h1>Cadastrar Horário de Aula</h1>
    </div>

    <form action="cadastro_horaaula.php" method="post">
      <p class="subtitulo">Selecione as opções abaixo</p>

      <!-- Dia da Semana -->
      <div class="input-container">
        <label for="dia_semana" style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
          Dia da Semana:
        </label>
        <select name="dia_semana" id="dia_semana" required>
          <option value="">Selecione</option>
          <option value="Segunda-Feira">Segunda-Feira</option>
          <option value="Terça-Feira">Terça-Feira</option>
          <option value="Quarta-Feira">Quarta-Feira</option>
          <option value="Quinta-Feira">Quinta-Feira</option>
          <option value="Sexta-Feira">Sexta-Feira</option>
        </select>
      </div>

      <!-- Hora de Início -->
      <div class="input-container">
        <label for="hora_inicio" style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
          Hora de Início:
        </label>
        <select name="hora_inicio" id="hora_inicio" required>
          <option value="">Selecione</option>
          <option value="07:45:00">07:45</option>
          <option value="08:30:00">08:30</option>
          <option value="13:15:00">13:15</option>
          <option value="14:00:00">14:00</option>
        </select>
      </div>

      <!-- Hora de Fim -->
      <div class="input-container">
        <label for="hora_fim" style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
          Hora de Fim:
        </label>
        <select name="hora_fim" id="hora_fim" required>
          <option value="">Selecione</option>
          <option value="11:45:00">11:45</option>
          <option value="12:30:00">12:30</option>
          <option value="16:30:00">16:30</option>
          <option value="17:15:00">17:15</option>
        </select>
      </div>

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
