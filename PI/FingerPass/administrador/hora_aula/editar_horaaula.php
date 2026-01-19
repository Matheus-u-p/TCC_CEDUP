<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Editar Horário de Aula</title>
    <link rel="icon" type="image/png" href="../../../img/FP006.png">
    <link rel="stylesheet" href="../../../style/cadastrar.css">
    <link href="https://fonts.googleapis.com/css2?family=Jura:wght@400;500;600&family=Changa:wght@400;700&display=swap" rel="stylesheet">
</head>

<?php
include('../../conexao/conexao.php');

// ✅ Pega o ID da URL (ex: editar_horaaula.php?id_horario=3)
if (isset($_GET['id_horario'])) {
    $id_horario = intval($_GET['id_horario']);
} else {
    header("Location: listar_horaaula.php");
    exit;
}

// 🔍 Busca os dados do horário
$sql = "SELECT * FROM horario_aula WHERE id_horario = $id_horario";
$res = mysqli_query($id, $sql);

if (mysqli_num_rows($res) == 0) {
    header("Location: listar_horaaula.php");
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

    <a href="listar_horaaula.php" class="btn-voltar">Cancelar</a>
  </header>

  <div class="caixa_usuario">
    <div class="titulo-container">
      <h1>Editar Horário de Aula</h1>
    </div>

    <form action="atualiza_horaaula.php" method="post">
        <p class="subtitulo">Altere as informações desejadas</p>

        <input type="hidden" name="id_horario" value="<?php echo $linha['id_horario']; ?>">

        <!-- Dia da semana -->
        <div class="input-container">
          <label for="dia_semana" style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Dia da Semana:
          </label>
          <select name="dia_semana" id="dia_semana" required>
            <option value="">Selecione</option>
            <option value="Segunda-Feira" <?php echo ($linha['dia_semana'] == 'Segunda-Feira') ? 'selected' : ''; ?>>Segunda-Feira</option>
            <option value="Terça-Feira" <?php echo ($linha['dia_semana'] == 'Terça-Feira') ? 'selected' : ''; ?>>Terça-Feira</option>
            <option value="Quarta-Feira" <?php echo ($linha['dia_semana'] == 'Quarta-Feira') ? 'selected' : ''; ?>>Quarta-Feira</option>
            <option value="Quinta-Feira" <?php echo ($linha['dia_semana'] == 'Quinta-Feira') ? 'selected' : ''; ?>>Quinta-Feira</option>
            <option value="Sexta-Feira" <?php echo ($linha['dia_semana'] == 'Sexta-Feira') ? 'selected' : ''; ?>>Sexta-Feira</option>
          </select>
        </div>

        <!-- Hora de início -->
        <div class="input-container">
          <label for="hora_inicio" style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Hora de Início:
          </label>
          <select name="hora_inicio" id="hora_inicio" required>
            <option value="">Selecione</option>
            <option value="07:45:00" <?php echo ($linha['hora_inicio'] == '07:45:00') ? 'selected' : ''; ?>>07:45</option>
            <option value="08:30:00" <?php echo ($linha['hora_inicio'] == '08:30:00') ? 'selected' : ''; ?>>08:30</option>
            <option value="13:15:00" <?php echo ($linha['hora_inicio'] == '13:15:00') ? 'selected' : ''; ?>>13:15</option>
            <option value="14:00:00" <?php echo ($linha['hora_inicio'] == '14:00:00') ? 'selected' : ''; ?>>14:00</option>
          </select>
        </div>

        <!-- Hora do fim -->
        <div class="input-container">
          <label for="hora_fim" style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Hora do Fim:
          </label>
          <select name="hora_fim" id="hora_fim" required>
            <option value="">Selecione</option>
            <option value="11:45:00" <?php echo ($linha['hora_fim'] == '11:45:00') ? 'selected' : ''; ?>>11:45</option>
            <option value="12:30:00" <?php echo ($linha['hora_fim'] == '12:30:00') ? 'selected' : ''; ?>>12:30</option>
            <option value="16:30:00" <?php echo ($linha['hora_fim'] == '16:30:00') ? 'selected' : ''; ?>>16:30</option>
            <option value="17:15:00" <?php echo ($linha['hora_fim'] == '17:15:00') ? 'selected' : ''; ?>>17:15</option>
          </select>
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
