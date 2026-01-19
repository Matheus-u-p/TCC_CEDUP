#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Inicializa todos os arquivos de log necessários
Execute este script ANTES de iniciar o servidor
"""

import os
import json
from datetime import datetime

# Configurações
BASE_DIR = 'C:/xampp/htdocs/TCC/VFP9.0/PI/FingerPass/BiometriaEscolar'
LOGS_DIR = os.path.join(BASE_DIR, 'logs')

# Arquivos necessários
arquivos = {
    'status.json': {
        'status': 'aguardando',
        'mensagem': '',
        'sensor_id': None,
        'timestamp': ''
    },
    'requisicao.json': {
        'acao': '',
        'matricula': '',
        'nome': '',
        'timestamp': ''
    },
    'delete_biometria.json': {
        'acao': 'deletar',
        'sensor_id': None,
        'timestamp': ''
    }
}

def inicializar():
    print("\n" + "="*70)
    print("🔧 INICIALIZANDO SISTEMA DE LOGS")
    print("="*70)
    print(f"📂 Diretório: {LOGS_DIR}\n")
    
    # Cria pasta logs se não existir
    if not os.path.exists(LOGS_DIR):
        os.makedirs(LOGS_DIR, exist_ok=True)
        print(f"✅ Pasta logs criada: {LOGS_DIR}\n")
    else:
        print(f"✅ Pasta logs já existe\n")
    
    # Cria/limpa arquivos JSON
    print("📝 Criando arquivos de log...\n")
    
    for arquivo, conteudo in arquivos.items():
        caminho = os.path.join(LOGS_DIR, arquivo)
        
        try:
            with open(caminho, 'w', encoding='utf-8') as f:
                json.dump(conteudo, f, ensure_ascii=False, indent=2)
            print(f"   ✅ {arquivo} criado")
        except Exception as e:
            print(f"   ❌ Erro ao criar {arquivo}: {e}")
    
    # Cria/limpa arquivo de debug
    debug_log = os.path.join(LOGS_DIR, 'api_debug.log')
    try:
        with open(debug_log, 'w', encoding='utf-8') as f:
            f.write(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] Sistema inicializado\n")
        print(f"   ✅ api_debug.log criado\n")
    except Exception as e:
        print(f"   ❌ Erro ao criar api_debug.log: {e}\n")
    
    print("="*70)
    print("✅ LOGS INICIALIZADOS COM SUCESSO!")
    print("="*70)
    print("\n💡 Próximos passos:")
    print("   1. Execute: python servidor_biometria.py")
    print("   2. Tente cadastrar novamente\n")

if __name__ == "__main__":
    try:
        inicializar()
    except Exception as e:
        print(f"\n❌ Erro: {e}\n")
    finally:
        input("Pressione ENTER para sair...")