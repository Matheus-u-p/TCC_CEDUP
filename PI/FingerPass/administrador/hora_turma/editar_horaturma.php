<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Editar Horário da Turma</title>
    <link rel="icon" type="image/png" href="../../../img/FP006.png">
    <link rel="stylesheet" href="../../../style/cadastrar.css">
    <link href="https://fonts.googleapis.com/css2?family=Jura:wght@400;500;600&family=Changa:wght@400;700&display=swap" rel="stylesheet">
</head>

<?php
include('../../conexao/conexao.php');

// Pega o ID da URL (ex: editar_horaturma.php?id_hora_turma=3)
if (isset($_GET['id_hora_turma'])) {
    $id_hora_turma = intval($_GET['id_hora_turma']);
} else {
    // Se não tiver id, volta para listagem
    header("Location: listar_horaturma.php");
    exit;
}

// Busca os dados da relação hora/turma
$sql = "SELECT * FROM hora_turma WHERE id_hora_turma = $id_hora_turma";
$res = mysqli_query($id, $sql);

if (mysqli_num_rows($res) == 0) {
    header("Location: listar_horaturma.php");
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

    <a href="listar_horaturma.php" class="btn-voltar">Cancelar</a>
  </header>

  <!-- Caixa principal -->
  <div class="caixa_usuario">
    <div class="titulo-container">
      <h1>Editar Hora/Turma</h1>
    </div>

    <form action="atualiza_horaturma.php" method="post">
        <p class="subtitulo">Altere as informações desejadas</p>

        <input type="hidden" name="id_hora_turma" value="<?php echo $linha['id_hora_turma']; ?>">

        <!-- Seleção da Turma -->
        <div class="input-container">
          <label for="id_turma" style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Selecione a Turma:
          </label>
          <select name="id_turma" id="id_turma" required>
            <option value="">Selecione</option>
            <?php
              $sql_turmas = "SELECT t.id_turma, t.n_turma, c.nome AS curso_nome 
                             FROM turma t 
                             LEFT JOIN curso c ON t.id_curso = c.id_curso 
                             ORDER BY c.nome ASC, t.n_turma ASC";
              $res_turmas = mysqli_query($id, $sql_turmas);
              while ($turma = mysqli_fetch_assoc($res_turmas)) {
                  $curso_nome = $turma['curso_nome'] ? $turma['curso_nome'] : 'Sem Curso';
                  $selected = ($turma['id_turma'] == $linha['id_turma']) ? 'selected' : '';
                  echo "<option value='{$turma['id_turma']}' $selected>
                          {$turma['n_turma']} - {$curso_nome}
                        </option>";
              }
            ?>
          </select>
        </div>

        <!-- Seleção do Horário -->
        <div class="input-container">
          <label for="id_horario" style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Selecione o Horário:
          </label>
          <select name="id_horario" id="id_horario" required>
            <option value="">Selecione</option>
            <?php
              $sql_horarios = "SELECT * FROM horario_aula 
                               ORDER BY FIELD(dia_semana, 
                               'Domingo','Segunda-feira','Terca-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sabado'),
                               hora_inicio";
              $res_horarios = mysqli_query($id, $sql_horarios);
              while ($horario = mysqli_fetch_assoc($res_horarios)) {
                  $texto = "{$horario['dia_semana']} - " . substr($horario['hora_inicio'], 0, 5) . " às " . substr($horario['hora_fim'], 0, 5);
                  $selected = ($horario['id_horario'] == $linha['id_horario']) ? 'selected' : '';
                  echo "<option value='{$horario['id_horario']}' $selected>{$texto}</option>";
              }
            ?>
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
