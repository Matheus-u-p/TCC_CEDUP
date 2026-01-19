<?php
// =====================================================
// TESTE DE DIAGNÓSTICO DO SISTEMA
// =====================================================
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Diagnóstico do Sistema</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #1a1a1a; color: #fff; }
        .ok { color: #00ff00; }
        .erro { color: #ff0000; }
        .aviso { color: #ffaa00; }
        .box { background: #2a2a2a; padding: 15px; margin: 10px 0; border-radius: 5px; }
        button { padding: 10px 20px; margin: 5px; cursor: pointer; font-size: 16px; }
        pre { background: #000; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 Diagnóstico do Sistema FingerPass</h1>

    <div class="box">
        <h2>1️⃣ Verificação de Arquivos</h2>
        <?php
        $base_dir = 'C:/xampp/htdocs/TCC/VFP9.0/PI/FingerPass/BiometriaEscolar';
        
        $arquivos = [
            'Pasta Logs' => $base_dir . '/logs',
            'controlar_servidor.php' => $base_dir . '/api/controlar_servidor.php',
            'entrada_monitor.php' => 'C:/xampp/htdocs/TCC/VFP9.0/PI/FingerPass/administrador/entrada/entrada_monitor.php',
            'servidor_entrada.py' => $base_dir . '/python/servidor_entrada.py',
            'config.py' => $base_dir . '/python/config.py'
        ];
        
        foreach ($arquivos as $nome => $caminho) {
            $existe = file_exists($caminho);
            $classe = $existe ? 'ok' : 'erro';
            $icone = $existe ? '✅' : '❌';
            echo "<p class='$classe'>$icone <strong>$nome:</strong> $caminho</p>";
        }
        ?>
    </div>

    <div class="box">
        <h2>2️⃣ Verificação do Python</h2>
        <?php
        $python_exe = 'C:\\Users\\mathe\\AppData\\Local\\Programs\\Python\\Python313\\python.exe';
        
        if (file_exists($python_exe)) {
            echo "<p class='ok'>✅ <strong>Python encontrado:</strong> $python_exe</p>";
            
            exec("\"$python_exe\" --version 2>&1", $output);
            echo "<p class='ok'>📦 <strong>Versão:</strong> " . implode(' ', $output) . "</p>";
        } else {
            echo "<p class='erro'>❌ <strong>Python NÃO encontrado!</strong></p>";
            echo "<p class='aviso'>⚠️ Procure Python em: C:\\Users\\[SEU_USUARIO]\\AppData\\Local\\Programs\\Python\\</p>";
        }
        ?>
    </div>

    <div class="box">
        <h2>3️⃣ Status do Servidor</h2>
        <?php
        $pid_file = $base_dir . '/logs/servidor_pid.txt';
        
        if (file_exists($pid_file)) {
            $pid = trim(file_get_contents($pid_file));
            echo "<p class='ok'>📋 <strong>PID encontrado:</strong> $pid</p>";
            
            exec("tasklist /FI \"PID eq $pid\" 2>NUL", $check);
            $rodando = false;
            foreach ($check as $linha) {
                if (strpos($linha, $pid) !== false) {
                    $rodando = true;
                    break;
                }
            }
            
            if ($rodando) {
                echo "<p class='ok'>✅ <strong>Servidor RODANDO</strong></p>";
            } else {
                echo "<p class='erro'>❌ <strong>PID existe mas processo NÃO está rodando</strong></p>";
            }
        } else {
            echo "<p class='aviso'>⚠️ <strong>Arquivo PID não existe</strong> (servidor parado)</p>";
        }
        ?>
    </div>

    <div class="box">
        <h2>4️⃣ Arquivos de Log</h2>
        <?php
        $logs = [
            'status.json' => $base_dir . '/logs/status.json',
            'reconhecimento.json' => $base_dir . '/logs/reconhecimento.json',
            'leitura_ativa.json' => $base_dir . '/logs/leitura_ativa.json',
            'servidor_output.log' => $base_dir . '/logs/servidor_output.log'
        ];
        
        foreach ($logs as $nome => $caminho) {
            if (file_exists($caminho)) {
                $conteudo = file_get_contents($caminho);
                $tamanho = filesize($caminho);
                echo "<p class='ok'>📄 <strong>$nome</strong> ($tamanho bytes)</p>";
                echo "<pre>" . htmlspecialchars(substr($conteudo, 0, 500)) . "</pre>";
            } else {
                echo "<p class='aviso'>⚠️ <strong>$nome</strong> não existe</p>";
            }
        }
        ?>
    </div>

    <div class="box">
        <h2>5️⃣ Teste de API</h2>
        <button onclick="testarAPI('status')">Testar Status</button>
        <button onclick="testarAPI('iniciar')">Testar Iniciar</button>
        <button onclick="testarAPI('parar')">Testar Parar</button>
        <pre id="resultado"></pre>
    </div>

    <script>
        function testarAPI(acao) {
            document.getElementById('resultado').textContent = 'Testando...';
            
            fetch('../../BiometriaEscolar/api/controlar_servidor.php?acao=' + acao + '&_=' + Date.now())
                .then(response => {
                    console.log('Response OK:', response.ok);
                    console.log('Response Status:', response.status);
                    return response.text();
                })
                .then(text => {
                    console.log('Response Text:', text);
                    
                    try {
                        const json = JSON.parse(text);
                        document.getElementById('resultado').textContent = 
                            JSON.stringify(json, null, 2);
                    } catch(e) {
                        document.getElementById('resultado').textContent = 
                            'ERRO: Resposta não é JSON válido!\n\n' + text;
                    }
                })
                .catch(error => {
                    document.getElementById('resultado').textContent = 
                        'ERRO: ' + error;
                });
        }
    </script>

    <div class="box">
        <h2>6️⃣ Conexão com Banco</h2>
        <?php
        include('../../conexao/conexao.php');
        
        if ($id) {
            echo "<p class='ok'>✅ <strong>Conectado ao banco MySQL</strong></p>";
            
            $result = mysqli_query($id, "SELECT COUNT(*) as total FROM aluno");
            if ($result) {
                $row = mysqli_fetch_assoc($result);
                echo "<p class='ok'>👥 <strong>Total de alunos:</strong> " . $row['total'] . "</p>";
            }
            
            $result = mysqli_query($id, "SELECT COUNT(*) as total FROM aluno WHERE biometria IS NOT NULL");
            if ($result) {
                $row = mysqli_fetch_assoc($result);
                echo "<p class='ok'>👆 <strong>Alunos com biometria:</strong> " . $row['total'] . "</p>";
            }
            
            mysqli_close($id);
        } else {
            echo "<p class='erro'>❌ <strong>Erro ao conectar ao banco</strong></p>";
        }
        ?>
    </div>

    <div class="box">
        <h2>7️⃣ Processos Python Rodando</h2>
        <?php
        exec('tasklist /FI "IMAGENAME eq python.exe" 2>NUL', $processos);
        
        if (count($processos) > 2) {
            echo "<p class='ok'>✅ <strong>Processos Python encontrados:</strong></p>";
            echo "<pre>" . htmlspecialchars(implode("\n", $processos)) . "</pre>";
        } else {
            echo "<p class='aviso'>⚠️ <strong>Nenhum processo Python rodando</strong></p>";
        }
        ?>
    </div>

    <a href="../home/tela_inicial_admin.php" style="display: inline-block; margin-top: 20px; padding: 10px 20px; background: #007bff; color: #fff; text-decoration: none; border-radius: 5px;">
        ⬅️ Voltar para Home
    </a>
</body>
</html>