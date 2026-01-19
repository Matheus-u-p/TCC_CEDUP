<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Editar Turma</title>
    <link rel="icon" type="image/png" href="../../../img/FP006.png">
    <link rel="stylesheet" href="../../../style/cadastrar.css">
    <link href="https://fonts.googleapis.com/css2?family=Jura:wght@400;500;600&family=Changa:wght@400;700&display=swap" rel="stylesheet">
</head>

<?php
include('../../conexao/conexao.php');

// Pega o ID da URL (ex: editar_turma.php?id_turma=3)
if (isset($_GET['id_turma'])) {
    $id_turma = intval($_GET['id_turma']);
} else {
    header("Location: listar_turma.php");
    exit;
}

// Busca os dados da turma
$sql = "SELECT * FROM turma WHERE id_turma = $id_turma";
$res = mysqli_query($id, $sql);

if (mysqli_num_rows($res) == 0) {
    header("Location: listar_turma.php");
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

    <a href="listar_turma.php" class="btn-voltar">Cancelar</a>
  </header>

  <!-- Conteúdo principal -->
  <div class="caixa_usuario">
    <div class="titulo-container">
      <h1>Editar Turma</h1>
    </div>

    <form action="atualiza_turma.php" method="post" id="formTurma">
        <p class="subtitulo">Altere as informações desejadas</p>

        <input type="hidden" name="id_turma" value="<?php echo $linha['id_turma']; ?>">

        <!-- Número da turma -->
        <div class="input-container">
          <input type="text" name="n_turma" value="<?php echo htmlspecialchars($linha['n_turma']); ?>" placeholder="Número da Turma (ex: 3-51)" required>
        </div>

        <!-- Turno -->
        <div class="input-container">
          <label for="turno" style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Turno:
          </label>
          <select name="turno" id="turno" required>
            <option value="">Selecione o turno</option>
            <option value="Matutino" <?php echo ($linha['turno'] == 'Matutino') ? 'selected' : ''; ?>>Matutino</option>
            <option value="Vespertino" <?php echo ($linha['turno'] == 'Vespertino') ? 'selected' : ''; ?>>Vespertino</option>
            <option value="Noturno" <?php echo ($linha['turno'] == 'Noturno') ? 'selected' : ''; ?>>Noturno</option>
          </select>
        </div>

        <!-- Contra turno -->
        <div class="input-container">
          <label for="contra_turno" style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Contra Turno (opcional):
          </label>
          <select name="contra_turno" id="contra_turno">
            <option value="">Sem contra turno</option>
            <option value="Matutino" <?php echo ($linha['contra_turno'] == 'Matutino') ? 'selected' : ''; ?>>Matutino</option>
            <option value="Vespertino" <?php echo ($linha['contra_turno'] == 'Vespertino') ? 'selected' : ''; ?>>Vespertino</option>
            <option value="Noturno" <?php echo ($linha['contra_turno'] == 'Noturno') ? 'selected' : ''; ?>>Noturno</option>
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
              $sql_cursos = "SELECT id_curso, nome FROM curso ORDER BY nome ASC";
              $res_cursos = mysqli_query($id, $sql_cursos);
              while ($curso = mysqli_fetch_assoc($res_cursos)) {
                $selected = ($curso['id_curso'] == $linha['id_curso']) ? 'selected' : '';
                echo "<option value='{$curso['id_curso']}' $selected>{$curso['nome']}</option>";
              }
            ?>
          </select>
        </div>

        <!-- Botão -->
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