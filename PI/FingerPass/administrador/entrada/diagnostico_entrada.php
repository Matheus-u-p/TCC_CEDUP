<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Diagnóstico de Entrada</title>
  <style>
    body { font-family: monospace; padding: 20px; background: #1a1a1a; color: #00ff00; }
    .box { background: #000; padding: 15px; margin: 10px 0; border: 1px solid #00ff00; }
    .erro { color: #ff0000; }
    .ok { color: #00ff00; }
    .aviso { color: #ffaa00; }
    pre { white-space: pre-wrap; }
    button { padding: 10px 20px; margin: 5px; background: #00ff00; border: none; cursor: pointer; }
  </style>
</head>
<body>
  <h1>DIAGNÓSTICO DO SISTEMA DE ENTRADA</h1>

  <?php
  $base_dir = 'C:/xampp/htdocs/TCC/VFP9.0/PI/FingerPass/BiometriaEscolar';
  $logs_dir = $base_dir . '/logs';
  
  echo "<div class='box'>";
  echo "<h2>1️ Arquivos de Log</h2>";
  
  $arquivos = [
    'reconhecimento.json',
    'leitura_ativa.json',
    'ultimo_processado.txt',
    'status.json',
    'monitor_debug.log'
  ];
  
  foreach ($arquivos as $arquivo) {
    $path = $logs_dir . '/' . $arquivo;
    if (file_exists($path)) {
      $tamanho = filesize($path);
      $modified = date('H:i:s', filemtime($path));
      echo "<p class='ok'>$arquivo ($tamanho bytes) - Última modificação: $modified</p>";
      
      $conteudo = file_get_contents($path);
      echo "<pre>" . htmlspecialchars(substr($conteudo, 0, 500)) . "</pre>";
    } else {
      echo "<p class='aviso'>$arquivo NÃO EXISTE</p>";
    }
  }
  echo "</div>";
  
  echo "<div class='box'>";
  echo "<h2>2️ Conexão com Banco de Dados</h2>";
  
  include('../../conexao/conexao.php');
  
  if ($id) {
    echo "<p class='ok'>Conectado ao banco MySQL</p>";
    
    // Testa query
    $sql_test = "SELECT a.id_aluno, a.nome, a.biometria, a.matricula, t.n_turma 
                 FROM aluno a 
                 LEFT JOIN turma t ON a.id_turma = t.id_turma 
                 WHERE a.biometria IS NOT NULL";
    
    $result = mysqli_query($id, $sql_test);
    
    if ($result) {
      echo "<p class='ok'>Query de teste executada com sucesso</p>";
      echo "<p>Alunos com biometria cadastrada:</p>";
      echo "<pre>";
      while ($row = mysqli_fetch_assoc($result)) {
        echo "ID: {$row['id_aluno']} | Nome: {$row['nome']} | Sensor: {$row['biometria']} | Matrícula: {$row['matricula']} | Turma: {$row['n_turma']}\n";
      }
      echo "</pre>";
    } else {
      echo "<p class='erro'>Erro na query: " . mysqli_error($id) . "</p>";
    }
    
    // Verifica registros de hoje
    $hoje = date('Y-m-d');
    $sql_hoje = "SELECT COUNT(*) as total FROM registro_chamada WHERE data_biometria = '$hoje'";
    $result_hoje = mysqli_query($id, $sql_hoje);
    $total_hoje = mysqli_fetch_assoc($result_hoje)['total'];
    
    echo "<p class='ok'>Registros de hoje ($hoje): $total_hoje</p>";
    
    mysqli_close($id);
  } else {
    echo "<p class='erro'>Erro ao conectar ao banco</p>";
  }
  echo "</div>";
  
  echo "<div class='box'>";
  echo "<h2>3️ Status do Servidor Python</h2>";
  
  $pid_file = $logs_dir . '/servidor_pid.txt';
  
  if (file_exists($pid_file)) {
    $pid = trim(file_get_contents($pid_file));
    echo "<p class='ok'>PID encontrado: $pid</p>";
    
    exec("tasklist /FI \"PID eq $pid\" 2>NUL", $check);
    $rodando = false;
    foreach ($check as $linha) {
      if (strpos($linha, $pid) !== false) {
        $rodando = true;
        break;
      }
    }
    
    if ($rodando) {
      echo "<p class='ok'>Servidor RODANDO</p>";
    } else {
      echo "<p class='erro'>PID existe mas processo NÃO está rodando</p>";
    }
  } else {
    echo "<p class='aviso'>Arquivo PID não existe (servidor parado)</p>";
  }
  echo "</div>";
  
  echo "<div class='box'>";
  echo "<h2>4️ Ações</h2>";
  echo "<button onclick='location.reload()'>Atualizar</button>";
  echo "<button onclick='limparLogs()'>Limpar Logs</button>";
  echo "<button onclick='testarMonitor()'>Testar Monitor</button>";
  echo "<button onclick='window.location.href=\"entrada_alunos.php\"'>Ir para Entrada</button>";
  echo "</div>";
  ?>

  <div class='box' id='resultado' style='display:none;'>
    <h2>Resultado do Teste</h2>
    <pre id='resultadoTexto'></pre>
  </div>

  <script>
    function limparLogs() {
      if (confirm('Deseja limpar todos os arquivos de log?')) {
        fetch('limpar_logs.php')
          .then(response => response.text())
          .then(data => {
            alert('Logs limpos!');
            location.reload();
          });
      }
    }

    function testarMonitor() {
      document.getElementById('resultado').style.display = 'block';
      document.getElementById('resultadoTexto').textContent = 'Testando...';
      
      fetch('entrada_monitor.php?_=' + Date.now())
        .then(response => response.text())
        .then(data => {
          document.getElementById('resultadoTexto').textContent = data;
        })
        .catch(error => {
          document.getElementById('resultadoTexto').textContent = 'ERRO: ' + error;
        });
    }
  </script>
</body>
</html>