<?php
$base_dir = 'C:/xampp/htdocs/TCC/VFP9.0/PI/FingerPass/BiometriaEscolar';
$logs_dir = $base_dir . '/logs';

$arquivos = [
    'reconhecimento.json',
    'leitura_ativa.json',
    'ultimo_processado.txt',
    'monitor_debug.log'
];

foreach ($arquivos as $arquivo) {
    $path = $logs_dir . '/' . $arquivo;
    if (file_exists($path)) {
        @unlink($path);
    }
}

echo "Logs limpos!";
?>