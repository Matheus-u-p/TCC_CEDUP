# config.py
# Configuracoes do Sistema de Biometria Escolar
# VERSAO FINAL - SEM EMOJIS, SEM PROBLEMAS DE ENCODING

import os
import json

# Banco de dados MySQL
DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'bd_biometria_tcc'
}

# Porta Serial do Arduino
SERIAL_PORT = 'COM6'
SERIAL_BAUD = 9600

# CAMINHOS ABSOLUTOS
BASE_DIR = 'C:/xampp/htdocs/TCC/VFP9.0/PI/FingerPass/BiometriaEscolar'
LOGS_DIR = BASE_DIR + '/logs'
PYTHON_DIR = BASE_DIR + '/python'

# Arquivos de comunicacao
STATUS_FILE = LOGS_DIR + '/status.json'
REQUISICAO_FILE = LOGS_DIR + '/requisicao.json'
DELETE_FILE = LOGS_DIR + '/delete_biometria.json'
DEBUG_LOG = LOGS_DIR + '/api_debug.log'

# Timeout de cadastro (segundos)
TIMEOUT_CADASTRO = 60

# ========================================
# INICIALIZACAO AUTOMATICA
# ========================================

# Cria pasta logs se nao existir
if not os.path.exists(LOGS_DIR):
    print(f"[INFO] Criando pasta logs: {LOGS_DIR}")
    os.makedirs(LOGS_DIR, exist_ok=True)
    print(f"[OK] Pasta criada!")

# Cria arquivos iniciais
if not os.path.exists(STATUS_FILE):
    with open(STATUS_FILE, 'w', encoding='utf-8') as f:
        json.dump({
            'status': 'aguardando',
            'mensagem': '',
            'sensor_id': None,
            'timestamp': ''
        }, f)
    print(f"[OK] status.json criado")

if not os.path.exists(DEBUG_LOG):
    with open(DEBUG_LOG, 'w', encoding='utf-8') as f:
        f.write(f'# Log iniciado\n')
    print(f"[OK] api_debug.log criado")

print(f"\n{'='*60}")
print(f"CONFIGURACAO CARREGADA:")
print(f"{'='*60}")
print(f"BASE_DIR:     {BASE_DIR}")
print(f"LOGS_DIR:     {LOGS_DIR}")
print(f"STATUS:       {STATUS_FILE}")
print(f"REQUISICAO:   {REQUISICAO_FILE}")
print(f"DELETE:       {DELETE_FILE}")
print(f"DEBUG:        {DEBUG_LOG}")
print(f"SERIAL_PORT:  {SERIAL_PORT}")
print(f"{'='*60}\n")