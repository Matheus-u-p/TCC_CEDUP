#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script para Limpar Memória do Sensor de Biometria
Deleta todas as digitais cadastradas (IDs 1-162)
"""

import serial
import time
import sys

try:
    from config import SERIAL_PORT, SERIAL_BAUD
except ImportError:
    print("❌ Erro: Arquivo config.py não encontrado!")
    SERIAL_PORT = 'COM6'  # Defina aqui se não tiver config.py
    SERIAL_BAUD = 9600

def limpar_sensor():
    """Deleta todas as digitais do sensor"""
    print("\n" + "="*70)
    print("🗑️  LIMPEZA DE MEMÓRIA DO SENSOR BIOMÉTRICO")
    print("="*70)
    print(f"📌 Porta: {SERIAL_PORT}")
    print("⚠️  ATENÇÃO: Isso vai deletar TODAS as digitais cadastradas!")
    print("="*70 + "\n")
    
    resposta = input("Deseja continuar? (S/N): ").strip().upper()
    
    if resposta != 'S':
        print("\n❌ Operação cancelada!\n")
        return
    
    print("\n🔌 Conectando Arduino...")
    try:
        arduino = serial.Serial(SERIAL_PORT, SERIAL_BAUD, timeout=1)
        time.sleep(2)
        arduino.reset_input_buffer()
        print("✅ Arduino conectado!\n")
    except Exception as e:
        print(f"❌ Erro ao conectar: {e}")
        print("\nVerifique se:")
        print("  1. Arduino está conectado")
        print("  2. Porta COM está correta")
        print("  3. Arduino IDE está FECHADO\n")
        input("Pressione ENTER para sair...")
        return
    
    print("🗑️  Iniciando limpeza da memória...\n")
    deletados = 0
    
    # Deleta IDs de 1 a 162
    for id_digital in range(1, 163):
        print(f"   Deletando ID #{id_digital}...", end=' ')
        
        try:
            # Envia comando para deletar
            arduino.write(f"DELETAR:{id_digital}\n".encode())
            time.sleep(0.1)
            
            # Aguarda resposta
            timeout = time.time() + 2
            while time.time() < timeout:
                if arduino.in_waiting > 0:
                    linha = arduino.readline().decode('utf-8', errors='ignore').strip()
                    
                    if linha.startswith('DELETADO:'):
                        print("✅ Deletado")
                        deletados += 1
                        break
                    elif linha.startswith('ERRO:'):
                        print("⚠️  Não existia")
                        break
                time.sleep(0.05)
            
        except Exception as e:
            print(f"❌ Erro: {e}")
        
        # Mostra progresso a cada 10 deletados
        if id_digital % 10 == 0:
            print(f"   Progresso: {id_digital}/162 verificados\n")
    
    print("\n" + "="*70)
    print(f"✅ LIMPEZA CONCLUÍDA!")
    print(f"🗑️  {deletados} digitais foram deletadas")
    print(f"💾 Memória livre: 162 posições")
    print("="*70 + "\n")
    
    arduino.close()
    print("👋 Arduino desconectado\n")

if __name__ == "__main__":
    try:
        limpar_sensor()
    except KeyboardInterrupt:
        print("\n\n⚠️  Operação interrompida pelo usuário\n")
    except Exception as e:
        print(f"\n❌ Erro: {e}\n")
    finally:
        input("Pressione ENTER para sair...")