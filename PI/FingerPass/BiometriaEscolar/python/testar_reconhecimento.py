#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script para Testar Reconhecimento de Biometria
Aguarda você colocar o dedo no sensor
"""

import serial
import time
import mysql.connector

try:
    from config import SERIAL_PORT, SERIAL_BAUD, DB_CONFIG
except ImportError:
    print("❌ Erro: Arquivo config.py não encontrado!")
    SERIAL_PORT = 'COM6'
    SERIAL_BAUD = 9600
    DB_CONFIG = {
        'host': 'localhost',
        'user': 'root',
        'password': '',
        'database': 'bd_biometria_tcc'
    }

def testar_reconhecimento():
    """Testa reconhecimento de digitais"""
    print("\n" + "="*70)
    print("🔍 TESTE DE RECONHECIMENTO BIOMÉTRICO")
    print("="*70)
    print(f"📌 Porta: {SERIAL_PORT}")
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
    
    # Conecta Banco
    print("🗄️  Conectando banco de dados...")
    try:
        db = mysql.connector.connect(**DB_CONFIG)
        cursor = db.cursor(dictionary=True)
        print("✅ Banco conectado!\n")
    except Exception as e:
        print(f"❌ Erro ao conectar banco: {e}\n")
        arduino.close()
        input("Pressione ENTER para sair...")
        return
    
    print("="*70)
    print("🎯 MODO DE TESTE CONTÍNUO")
    print("="*70)
    print("⚠️  Pressione Ctrl+C para sair")
    print("👆 Mantenha o dedo NO SENSOR por 2-3 segundos\n")
    
    try:
        while True:
            print("\n" + "─"*70)
            input("📍 Pressione ENTER e COLOQUE O DEDO NO SENSOR...")
            print("🔄 Tentando reconhecer... Mantenha o dedo no sensor!\n")
            
            # Envia comando
            arduino.write("RECONHECER\n".encode())
            
            # Aguarda respostas por até 10 segundos
            timeout = time.time() + 10
            reconhecido = False
            mensagens = []
            
            while time.time() < timeout:
                if arduino.in_waiting > 0:
                    linha = arduino.readline().decode('utf-8', errors='ignore').strip()
                    
                    if linha and linha not in mensagens:
                        mensagens.append(linha)
                        print(f"   🤖 {linha}")
                        
                        if linha.startswith('RECONHECIDO:'):
                            reconhecido = True
                            partes = linha.split(':')[1].split(',')
                            sensor_id = int(partes[0])
                            confianca = int(partes[1])
                            
                            print("\n" + "="*70)
                            print("✅ ✅ ✅  DIGITAL RECONHECIDA!  ✅ ✅ ✅")
                            print("="*70)
                            print(f"🆔 Sensor ID: {sensor_id}")
                            print(f"📊 Confiança: {confianca}%")
                            
                            # Busca aluno no banco
                            cursor.execute("SELECT * FROM aluno WHERE biometria = %s", (sensor_id,))
                            aluno = cursor.fetchone()
                            
                            if aluno:
                                print("\n📋 DADOS DO ALUNO:")
                                print(f"   👤 Nome: {aluno['nome']}")
                                print(f"   🎫 Matrícula: {aluno['matricula']}")
                                print(f"   📞 Telefone: {aluno['telefone'] or 'Não informado'}")
                                print(f"   🎂 Nascimento: {aluno['data_nascimento'] or 'Não informado'}")
                                print(f"   ⚧  Sexo: {aluno['sexo'] or 'Não informado'}")
                                
                                # Busca turma
                                if aluno['id_turma']:
                                    cursor.execute("""
                                        SELECT t.n_turma, c.nome as curso 
                                        FROM turma t 
                                        LEFT JOIN curso c ON t.id_curso = c.id_curso 
                                        WHERE t.id_turma = %s
                                    """, (aluno['id_turma'],))
                                    turma = cursor.fetchone()
                                    if turma:
                                        print(f"   🎓 Turma: {turma['n_turma']} - {turma['curso']}")
                                else:
                                    print(f"   🎓 Turma: Não atribuída")
                            else:
                                print("\n⚠️  ID reconhecido, mas aluno não encontrado no banco!")
                                print("   (Pode ter sido deletado ou não cadastrado corretamente)")
                            
                            print("="*70)
                            break
                        
                        elif linha.startswith('NAO_CADASTRADO'):
                            print("\n" + "="*70)
                            print("⚠️  DIGITAL NÃO CADASTRADA")
                            print("="*70)
                            print("Esta digital não está no sistema.")
                            print("Certifique-se de ter cadastrado a digital antes.")
                            print("="*70)
                            break
                        
                        elif linha.startswith('ERRO:'):
                            print(f"\n❌ ERRO: {linha.split(':', 1)[1]}")
                            break
                        
                        elif 'Nenhum dedo' in linha or 'NOFINGER' in linha:
                            print("⚠️  Sensor não detectou o dedo. Tente novamente!")
                
                time.sleep(0.1)
            
            if not reconhecido and not any('NAO_CADASTRADO' in m or 'ERRO:' in m for m in mensagens):
                print("\n⏱️  Timeout - Não foi possível ler a digital.")
                print("💡 Dicas:")
                print("   • Coloque o dedo ANTES de pressionar ENTER")
                print("   • Mantenha o dedo firme no sensor por 2-3 segundos")
                print("   • Certifique-se que o dedo está limpo e seco")
                print("   • Use o mesmo dedo que cadastrou")
    
    except KeyboardInterrupt:
        print("\n\n⚠️  Teste interrompido pelo usuário")
    except Exception as e:
        print(f"\n❌ Erro: {e}")
    finally:
        arduino.close()
        db.close()
        print("\n👋 Conexões encerradas\n")

if __name__ == "__main__":
    try:
        testar_reconhecimento()
    except Exception as e:
        print(f"\n❌ Erro: {e}\n")
    finally:
        input("Pressione ENTER para sair...")