#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Diagnóstico Rápido do Sensor
Testa quais IDs estão funcionando
"""

import serial
import time

try:
    from config import SERIAL_PORT, SERIAL_BAUD
except:
    SERIAL_PORT = 'COM6'
    SERIAL_BAUD = 9600

def testar_ids():
    """Testa IDs de 1 a 20"""
    print("\n" + "="*70)
    print("🔬 TESTE DE IDs DO SENSOR")
    print("="*70 + "\n")
    
    try:
        arduino = serial.Serial(SERIAL_PORT, SERIAL_BAUD, timeout=1)
        time.sleep(2)
        arduino.reset_input_buffer()
        print("✅ Arduino conectado!\n")
    except Exception as e:
        print(f"❌ Erro: {e}\n")
        input("Pressione ENTER...")
        return
    
    print("🔍 Testando IDs de 1 a 162...\n")
    print("ID  | Status")
    print("----|--------")
    
    ids_livres = []
    ids_ocupados = []
    
    for test_id in range(1, 163):
        # Tenta deletar (se não existir, dá erro)
        arduino.write(f"DELETAR:{test_id}\n".encode())
        time.sleep(0.2)
        
        timeout = time.time() + 1
        status = "?"
        
        while time.time() < timeout:
            if arduino.in_waiting > 0:
                linha = arduino.readline().decode('utf-8', errors='ignore').strip()
                
                if linha.startswith('DELETADO:'):
                    status = "OCUPADO"
                    ids_ocupados.append(test_id)
                    break
                elif linha.startswith('ERRO:'):
                    status = "LIVRE"
                    ids_livres.append(test_id)
                    break
            time.sleep(0.05)
        
        print(f"{test_id:3d} | {status}")
    
    print("\n" + "="*70)
    print(f"✅ IDs LIVRES: {ids_livres}")
    print(f"🔒 IDs OCUPADOS: {ids_ocupados}")
    print("="*70)
    
    if len(ids_livres) > 0:
        print(f"\n💡 Próximo ID recomendado: {ids_livres[0]}")
    else:
        print("\n⚠️  Nenhum ID livre nos primeiros 20 slots!")
    
    arduino.close()
    print("\n👋 Teste concluído\n")

if __name__ == "__main__":
    try:
        testar_ids()
    except KeyboardInterrupt:
        print("\n\n⚠️  Interrompido\n")
    except Exception as e:
        print(f"\n❌ Erro: {e}\n")
    finally:
        input("Pressione ENTER para sair...")