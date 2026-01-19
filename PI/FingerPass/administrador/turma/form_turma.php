<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Cadastrar Turma</title>
    <link rel="icon" type="image/png" href="../../../img/FP006.png">
    <link rel="stylesheet" href="../../../style/cadastrar.css">
    <link href="https://fonts.googleapis.com/css2?family=Jura:wght@400;500;600&family=Changa:wght@400;700&display=swap" rel="stylesheet">
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

    <a href="listar_turma.php" class="btn-voltar">Cancelar</a>
  </header>

  <!-- Caixa principal -->
  <div class="caixa_usuario">
    <div class="titulo-container">
      <h1>Cadastrar Turma</h1>
    </div>

    <form action="cadastro_turma.php" method="post" id="formTurma">
        <p class="subtitulo">Preencha as Informações</p>

        <!-- Número da Turma -->
        <div class="input-container">
          <input type="text" name="n_turma" placeholder="Número da Turma (ex: 3-53)" maxlength="10" required>
        </div>

        <!-- Turno -->
        <div class="input-container">
          <label for="turno" style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Turno:
          </label>
          <select name="turno" id="turno" required>
            <option value="">Selecione o turno</option>
            <option value="Matutino">Matutino</option>
            <option value="Vespertino">Vespertino</option>
            <option value="Noturno">Noturno</option>
          </select>
        </div>

        <!-- Contra Turno -->
        <div class="input-container">
          <label for="contra_turno" style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Contra Turno (opcional):
          </label>
          <select name="contra_turno" id="contra_turno">
            <option value="">Sem contra turno</option>
            <option value="Matutino">Matutino</option>
            <option value="Vespertino">Vespertino</option>
            <option value="Noturno">Noturno</option>
          </select>
        </div>

        <!-- Curso -->
        <div class="input-container">
          <label for="id_curso" style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Curso:
          </label>
          <select name="id_curso" id="id_curso" required>
            <option value="">Selecione o Curso</option>
            <?php
              include('../../conexao/conexao.php');
              $sql_cursos = "SELECT id_curso, nome FROM curso ORDER BY nome ASC";
              $res_cursos = mysqli_query($id, $sql_cursos);
              while ($curso = mysqli_fetch_assoc($res_cursos)) {
                echo "<option value='{$curso['id_curso']}'>{$curso['nome']}</option>";
              }
            ?>
          </select>
        </div>

        <!-- Botão -->
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

  <script>
  // ========== MÁSCARA DE NÚMERO DA TURMA ==========
  function aplicarMascaraTurma(input) {
    let valor = input.value.replace(/\D/g, ''); // Remove tudo que não é número
    
    // Limita a 3 dígitos no total (1 antes do hífen, 2 depois)
    if (valor.length > 3) {
      valor = valor.substring(0, 3);
    }
    
    // Aplica formato X-XX
    if (valor.length >= 2) {
      valor = valor.replace(/^(\d{1})(\d{0,2}).*/, '$1-$2');
    }
    
    input.value = valor;
  }

  document.addEventListener('DOMContentLoaded', function() {
    const campoTurma = document.querySelector('input[name="n_turma"]');
    
    if (campoTurma) {
      campoTurma.maxLength = 4; // X-XX = 4 caracteres
      
      campoTurma.addEventListener('input', function() {
        aplicarMascaraTurma(this);
      });
      
      campoTurma.addEventListener('paste', function() {
        setTimeout(() => aplicarMascaraTurma(this), 10);
      });
    }
  });

  // ========== VALIDAÇÃO: TURNO ≠ CONTRATURNO ==========
  document.getElementById('formTurma').addEventListener('submit', function(e) {
    const turno = document.getElementById('turno').value;
    const contraTurno = document.getElementById('contra_turno').value;
    
    // Se contraturno foi selecionado E é igual ao turno
    if (contraTurno && turno === contraTurno) {
      e.preventDefault(); // Impede o envio
      alert('❌ Erro: O turno e o contra turno não podem ser iguais!');
      document.getElementById('contra_turno').focus();
      return false;
    }
  });

  // ========== VALIDAÇÃO EM TEMPO REAL (OPCIONAL) ==========
  document.getElementById('turno').addEventListener('change', validarTurnos);
  document.getElementById('contra_turno').addEventListener('change', validarTurnos);

  function validarTurnos() {
    const turno = document.getElementById('turno').value;
    const contraTurno = document.getElementById('contra_turno').value;
    const selectContraTurno = document.getElementById('contra_turno');
    
    if (contraTurno && turno === contraTurno) {
      selectContraTurno.style.border = '2px solid red';
      selectContraTurno.style.backgroundColor = '#ffebee';
    } else {
      selectContraTurno.style.border = '';
      selectContraTurno.style.backgroundColor = '';
    }
  }
  </script>

</body>
</html>