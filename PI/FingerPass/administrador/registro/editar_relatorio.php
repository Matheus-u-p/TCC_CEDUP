<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FingerPass - Editar Registro de Chamada</title>
    <link rel="icon" type="image/png" href="../../../img/FP006.png">
    <link rel="stylesheet" href="../../../style/cadastrar.css">
    <link href="https://fonts.googleapis.com/css2?family=Jura:wght@400;500;600&family=Changa:wght@400;700&display=swap" rel="stylesheet">
</head>

<?php
session_start();
include('../../conexao/conexao.php');

// Pega o ID da URL (ex: editar_relatorio.php?id_registro=5)
if (isset($_GET['id_registro'])) {
    $id_registro = intval($_GET['id_registro']);
} else {
    header("Location: relatorio_turma.php");
    exit;
}

// Busca os dados do registro com informações do aluno
$sql = "SELECT 
            rc.*,
            a.nome as nome_aluno,
            a.matricula,
            t.n_turma,
            t.id_turma
        FROM registro_chamada rc
        INNER JOIN aluno a ON rc.id_aluno = a.id_aluno
        LEFT JOIN turma t ON a.id_turma = t.id_turma
        WHERE rc.id_registro = $id_registro";

$res = mysqli_query($id, $sql);

if (mysqli_num_rows($res) == 0) {
    echo "<script>
        alert('Registro não encontrado!');
        window.history.back();
    </script>";
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
            echo isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuário';
            ?>
        </strong>
    </div>

    <a href="javascript:history.back()" class="btn-voltar">Cancelar</a>
  </header>

  <!-- Conteúdo principal -->
  <div class="caixa_usuario">
    <div class="titulo-container">
      <h1>Editar Registro de Chamada</h1>
    </div>

    <form action="atualiza_relatorio.php" method="post">
        <p class="subtitulo">Altere as informações do registro de presença</p>

        <input type="hidden" name="id_registro" value="<?php echo $linha['id_registro']; ?>">
        <input type="hidden" name="id_turma" value="<?php echo $linha['id_turma']; ?>">
        <input type="hidden" name="data_filtro" value="<?php echo $linha['data_biometria']; ?>">

        <!-- Informações do aluno (apenas leitura) -->
        <div class="input-container">
          <label style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Aluno:
          </label>
          <input type="text" value="<?php echo htmlspecialchars($linha['nome_aluno']); ?>" disabled>
        </div>

        <div class="input-container">
          <label style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Matrícula:
          </label>
          <input type="text" value="<?php echo htmlspecialchars($linha['matricula']); ?>" disabled>
        </div>

        <div class="input-container">
          <label style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Turma:
          </label>
          <input type="text" value="<?php echo htmlspecialchars($linha['n_turma'] ?? 'Não atribuída'); ?>" disabled>
        </div>

        <div class="input-container">
          <label style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Data:
          </label>
          <input type="text" value="<?php echo date('d/m/Y', strtotime($linha['data_biometria'])); ?>" disabled>
        </div>

        <!-- STATUS (editável) -->
        <div class="input-container">
          <label for="presenca" style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Status da Presença: *
          </label>
          <select name="presenca" id="presenca" required>
            <option value="">Selecione o status</option>
            <option value="E" <?php echo ($linha['presenca'] == 'E') ? 'selected' : ''; ?>>Presente (em aula)</option>
            <option value="P" <?php echo ($linha['presenca'] == 'P') ? 'selected' : ''; ?>>Presente até o fim</option>
            <option value="S" <?php echo ($linha['presenca'] == 'S') ? 'selected' : ''; ?>>Saiu mais cedo</option>
            <option value="F" <?php echo ($linha['presenca'] == 'F') ? 'selected' : ''; ?>>Faltou</option>
          </select>
        </div>

        <!-- HORÁRIO DE ENTRADA (editável) -->
        <div class="input-container">
          <label for="hora_biometria" style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Horário de Entrada:
          </label>
          <input 
            type="time" 
            name="hora_biometria" 
            id="hora_biometria" 
            value="<?php echo $linha['hora_biometria'] ? substr($linha['hora_biometria'], 0, 5) : ''; ?>"
          >
          <small style="color: #888; font-size: 0.85rem;">Deixe em branco se o aluno faltou</small>
        </div>

        <!-- HORÁRIO DE SAÍDA (editável) -->
        <div class="input-container">
          <label for="hora_saida" style="display:block; text-align:left; font-size:0.9rem; color:#bcbcbc; margin-bottom:6px;">
            Horário de Saída:
          </label>
          <input 
            type="time" 
            name="hora_saida" 
            id="hora_saida" 
            value="<?php echo $linha['hora_saida'] ? substr($linha['hora_saida'], 0, 5) : ''; ?>"
          >
          <small style="color: #888; font-size: 0.85rem;">Deixe em branco se ainda estiver em aula</small>
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

</body>
</html>