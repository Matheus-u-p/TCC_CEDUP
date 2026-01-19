#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script MELHORADO para Limpar Memória do Sensor
Usa o comando LIMPAR_TUDO do Arduino (emptyDatabase)
"""

import serial
import time
import sys

try:
    from config import SERIAL_PORT, SERIAL_BAUD
except ImportError:
    print("⚠️  Usando configurações padrão")
    SERIAL_PORT = 'COM6'
    SERIAL_BAUD = 9600

def limpar_memoria_completa():
    """Limpa TODA a memória do sensor usando emptyDatabase"""
    print("\n" + "="*70)
    print("🗑️  LIMPEZA COMPLETA DE MEMÓRIA DO SENSOR")
    print("="*70)
    print(f"📌 Porta: {SERIAL_PORT}")
    print("⚠️  ATENÇÃO: Isso vai deletar TODAS as digitais!")
    print("="*70 + "\n")
    
    resposta = input("⚠️  Deseja continuar? (S/N): ").strip().upper()
    
    if resposta != 'S':
        print("\n❌ Operação cancelada!\n")
        return False
    
    print("\n🔌 Conectando Arduino...")
    try:
        arduino = serial.Serial(SERIAL_PORT, SERIAL_BAUD, timeout=2)
        time.sleep(2)
        arduino.reset_input_buffer()
        print("✅ Arduino conectado!\n")
    except Exception as e:
        print(f"❌ Erro ao conectar: {e}")
        print("\nVerifique:")
        print("  1. Arduino está conectado")
        print("  2. Porta COM está correta no config.py")
        print("  3. Arduino IDE está FECHADO")
        print("  4. Servidor Python está PARADO\n")
        return False
    
    print("="*70)
    print("🚀 INICIANDO LIMPEZA USANDO emptyDatabase()")
    print("="*70 + "\n")
    
    try:
        # Envia comando LIMPAR_TUDO (usa emptyDatabase do sensor)
        print("📤 Enviando comando LIMPAR_TUDO...")
        arduino.write("LIMPAR_TUDO\n".encode())
        time.sleep(1)
        
        # Aguarda resposta
        timeout = time.time() + 5
        limpo = False
        
        while time.time() < timeout:
            if arduino.in_waiting > 0:
                linha = arduino.readline().decode('utf-8', errors='ignore').strip()
                
                if linha:
                    print(f"   🤖 Arduino: {linha}")
                    
                    if linha.startswith('LIMPO:'):
                        limpo = True
                        print("\n" + "="*70)
                        print("✅ ✅ ✅  MEMÓRIA COMPLETAMENTE LIMPA!  ✅ ✅ ✅")
                        print("="*70)
                        break
                    elif linha.startswith('ERRO:'):
                        print(f"\n❌ {linha}")
                        break
            
            time.sleep(0.1)
        
        if not limpo and time.time() >= timeout:
            print("\n⏱️  Timeout - Sem resposta do Arduino")
            print("⚠️  Tente novamente ou carregue o código Arduino novamente\n")
            arduino.close()
            return False
        
        # Verifica se limpou mesmo
        print("\n🔍 Verificando limpeza...")
        time.sleep(0.5)
        arduino.reset_input_buffer()
        arduino.write("CONTAR\n".encode())
        time.sleep(0.5)
        
        while arduino.in_waiting > 0:
            linha = arduino.readline().decode('utf-8', errors='ignore').strip()
            if linha.startswith('CONTAGEM:'):
                qtd = int(linha.split(':')[1])
                print(f"   📊 Digitais restantes: {qtd}")
                
                if qtd == 0:
                    print("   ✅ Memória ZERADA com sucesso!")
                else:
                    print(f"   ⚠️  Ainda há {qtd} digitais na memória")
        
        print("\n" + "="*70)
        print("✅ LIMPEZA CONCLUÍDA!")
        print("="*70)
        print("\n💡 Próximos passos:")
        print("   1. Feche este script")
        print("   2. Inicie o servidor_biometria.py")
        print("   3. Tente cadastrar novamente\n")
        
        arduino.close()
        print("👋 Arduino desconectado\n")
        return True
        
    except Exception as e:
        print(f"\n❌ Erro durante limpeza: {e}\n")
        try:
            arduino.close()
        except:
            pass
        return False

def limpar_ids_especificos():
    """Limpa apenas IDs problemáticos (150-162)"""
    print("\n" + "="*70)
    print("🎯 LIMPEZA DE IDs ESPECÍFICOS (150-162)")
    print("="*70 + "\n")
    
    try:
        arduino = serial.Serial(SERIAL_PORT, SERIAL_BAUD, timeout=1)
        time.sleep(2)
        arduino.reset_input_buffer()
        print("✅ Arduino conectado!\n")
    except Exception as e:
        print(f"❌ Erro ao conectar: {e}\n")
        return False
    
    ids_problematicos = list(range(150, 163))  # 150 a 162
    deletados = 0
    
    print("🗑️  Deletando IDs problemáticos...\n")
    
    for id_teste in ids_problematicos:
        print(f"   Deletando ID #{id_teste}...", end=' ')
        arduino.write(f"DELETAR:{id_teste}\n".encode())
        time.sleep(0.3)
        
        timeout = time.time() + 2
        while time.time() < timeout:
            if arduino.in_waiting > 0:
                linha = arduino.readline().decode('utf-8', errors='ignore').strip()
                if linha.startswith('DELETADO:'):
                    print("✅")
                    deletados += 1
                    break
                elif linha.startswith('ERRO:'):
                    print("⚠️  (não existia)")
                    break
            time.sleep(0.05)
    
    print(f"\n✅ {deletados} IDs deletados")
    arduino.close()
    return True

if __name__ == "__main__":
    print("\n" + "="*70)
    print("🛠️  MENU DE LIMPEZA")
    print("="*70)
    print("\n1. Limpeza COMPLETA (recomendado)")
    print("2. Deletar apenas IDs problemáticos (150-162)")
    print("3. Cancelar\n")
    
    opcao = input("Escolha uma opção (1-3): ").strip()
    
    try:
        if opcao == '1':
            sucesso = limpar_memoria_completa()
        elif opcao == '2':
            sucesso = limpar_ids_especificos()
        else:
            print("\n❌ Operação cancelada\n")
            sucesso = False
        
        if sucesso:
            print("="*70)
            print("🎉 SUCESSO!")
            print("="*70)
        
    except KeyboardInterrupt:
        print("\n\n⚠️  Operação interrompida\n")
    except Exception as e:
        print(f"\n❌ Erro: {e}\n")
    finally:
        input("\nPressione ENTER para sair...")