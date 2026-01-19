#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Diagnóstico do Sensor de Biometria
Verifica quais IDs estão ocupados e testa cadastro
"""

import serial
import time

try:
    from config import SERIAL_PORT, SERIAL_BAUD
except ImportError:
    SERIAL_PORT = 'COM6'
    SERIAL_BAUD = 9600

def diagnostico():
    print("\n" + "="*70)
    print("🔍 DIAGNÓSTICO DO SENSOR BIOMÉTRICO")
    print("="*70 + "\n")
    
    # Conecta Arduino
    print("🔌 Conectando Arduino...")
    try:
        arduino = serial.Serial(SERIAL_PORT, SERIAL_BAUD, timeout=1)
        time.sleep(2)
        arduino.reset_input_buffer()
        print("✅ Arduino conectado!\n")
    except Exception as e:
        print(f"❌ Erro ao conectar: {e}\n")
        input("Pressione ENTER para sair...")
        return
    
    print("="*70)
    print("📊 VERIFICANDO STATUS DO SENSOR")
    print("="*70 + "\n")
    
    # Conta digitais cadastradas
    print("1️⃣  Verificando quantidade de digitais...")
    arduino.write("CONTAR\n".encode())
    time.sleep(0.5)
    
    while arduino.in_waiting > 0:
        linha = arduino.readline().decode('utf-8', errors='ignore').strip()
        if linha.startswith('CONTAGEM:'):
            qtd = linha.split(':')[1]
            print(f"   📈 Total de digitais: {qtd}")
            if int(qtd) >= 162:
                print("   ⚠️  MEMÓRIA CHEIA! Execute o script de limpeza.")
    
    print()
    
    # Verifica próximo ID disponível
    print("2️⃣  Verificando próximo ID disponível...")
    arduino.write("PROXIMO_ID\n".encode())
    time.sleep(0.5)
    
    proximo_id = None
    while arduino.in_waiting > 0:
        linha = arduino.readline().decode('utf-8', errors='ignore').strip()
        if linha.startswith('PROXIMO_ID:'):
            proximo_id = linha.split(':')[1]
            print(f"   🆔 Próximo ID livre: {proximo_id}")
    
    print()
    
    # Limpa buffer
    time.sleep(0.5)
    arduino.reset_input_buffer()
    
    # Testa deletar IDs problemáticos
    print("3️⃣  Testando deletar IDs problemáticos (150-162)...")
    ids_problematicos = [150, 151, 152, 160, 161, 162]
    
    for id_teste in ids_problematicos:
        print(f"   🗑️  Tentando deletar ID {id_teste}...", end=' ')
        arduino.write(f"DELETAR:{id_teste}\n".encode())
        time.sleep(0.3)
        
        timeout = time.time() + 2
        deletado = False
        while time.time() < timeout:
            if arduino.in_waiting > 0:
                linha = arduino.readline().decode('utf-8', errors='ignore').strip()
                if linha.startswith('DELETADO:'):
                    print("✅ Deletado")
                    deletado = True
                    break
                elif linha.startswith('ERRO:'):
                    print("❌ Não existia ou erro")
                    break
            time.sleep(0.05)
        
        if not deletado and time.time() >= timeout:
            print("⏱️  Timeout")
    
    print()
    
    # Verifica novamente o próximo ID
    print("4️⃣  Verificando próximo ID após limpeza...")
    arduino.write("PROXIMO_ID\n".encode())
    time.sleep(0.5)
    
    while arduino.in_waiting > 0:
        linha = arduino.readline().decode('utf-8', errors='ignore').strip()
        if linha.startswith('PROXIMO_ID:'):
            novo_id = linha.split(':')[1]
            print(f"   🆔 Novo próximo ID: {novo_id}")
            
            if novo_id == '0':
                print("\n" + "="*70)
                print("⚠️  PROBLEMA DETECTADO: MEMÓRIA PARECE CHEIA")
                print("="*70)
                print("\n💡 SOLUÇÕES:")
                print("   1. Execute: python limpar_sensor.py")
                print("   2. Ou carregue o código de limpeza no Arduino")
                print("="*70 + "\n")
    
    print()
    print("="*70)
    print("✅ DIAGNÓSTICO CONCLUÍDO")
    print("="*70)
    print("\n💡 Próximos passos:")
    print("   • Se o próximo ID for válido (1-162), tente cadastrar novamente")
    print("   • Se aparecer 0, limpe a memória do sensor")
    print("   • Verifique se o problema persiste\n")
    
    arduino.close()
    print("👋 Arduino desconectado\n")

if __name__ == "__main__":
    try:
        diagnostico()
    except KeyboardInterrupt:
        print("\n\n⚠️  Operação interrompida\n")
    except Exception as e:
        print(f"\n❌ Erro: {e}\n")
    finally:
        input("Pressione ENTER para sair...")