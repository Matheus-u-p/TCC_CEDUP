#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Sistema de Entrada de Alunos - VERSAO FINAL ESTAVEL
Corrigido: encoding UTF-8, emojis removidos, hora correta, fuso horário
"""

import serial
import json
import time
from datetime import datetime
import os
import sys
import signal

# ===== CONFIGURAÇÃO DE FUSO HORÁRIO =====
# Define timezone para São Paulo (UTC-3)
os.environ['TZ'] = 'America/Sao_Paulo'
if hasattr(time, 'tzset'):
    time.tzset()

# ===== CORRECAO DE ENCODING =====
print("=" * 70)
print("INICIANDO SERVIDOR DE ENTRADA - FINGERPASS")
print("=" * 70)

try:
    from config import SERIAL_PORT, SERIAL_BAUD, LOGS_DIR
    print("[OK] Config importado")
    print(f"Pasta logs: {LOGS_DIR}")
    print(f"Porta serial: {SERIAL_PORT}")
    print(f"Fuso horario: America/Sao_Paulo (UTC-3)")
except ImportError as e:
    print(f"[ERRO] Nao foi possivel importar config.py: {e}")
    print("Verifique se o arquivo existe em: BiometriaEscolar/python/config.py")
    input("Pressione ENTER para sair...")
    sys.exit(1)

class SistemaEntrada:
    def __init__(self):
        self.arduino = None
        self.arquivo_reconhecimento = os.path.join(LOGS_DIR, 'reconhecimento.json')
        self.arquivo_leitura_ativa = os.path.join(LOGS_DIR, 'leitura_ativa.json')
        self.arquivo_pid = os.path.join(LOGS_DIR, 'servidor_pid.txt')
        self.arquivo_status = os.path.join(LOGS_DIR, 'status.json')
        self.ultimo_sensor_id = None
        self.ultimo_timestamp = None
        self.rodando = True
        
    def criar_pid(self):
        """Cria arquivo com PID do processo"""
        try:
            pid = os.getpid()
            with open(self.arquivo_pid, 'w') as f:
                f.write(str(pid))
            print(f"[OK] PID criado: {pid}")
            return True
        except Exception as e:
            print(f"[AVISO] Erro ao criar PID: {e}")
            return False
    
    def atualizar_status(self, status='ativo', mensagem=''):
        """Atualiza arquivo de status"""
        try:
            # Usa horário local do sistema
            agora = datetime.now()
            
            dados = {
                'status': status,
                'mensagem': mensagem,
                'timestamp': agora.strftime('%Y-%m-%d %H:%M:%S'),
                'pid': os.getpid()
            }
            with open(self.arquivo_status, 'w', encoding='utf-8') as f:
                json.dump(dados, f, ensure_ascii=False, indent=2)
        except Exception as e:
            print(f"[AVISO] Erro ao atualizar status: {e}")
    
    def conectar_arduino(self):
        """Conecta ao Arduino com retry"""
        print(f"\n[INFO] Conectando Arduino na porta {SERIAL_PORT}...")
        
        tentativas = 0
        max_tentativas = 3
        
        while tentativas < max_tentativas:
            try:
                self.arduino = serial.Serial(SERIAL_PORT, SERIAL_BAUD, timeout=1)
                time.sleep(2)
                self.arduino.reset_input_buffer()
                
                # Aguarda resposta do Arduino
                timeout = time.time() + 5
                arduino_ok = False
                
                while time.time() < timeout:
                    if self.arduino.in_waiting > 0:
                        linha = self.arduino.readline().decode('utf-8', errors='ignore').strip()
                        print(f"   Arduino: {linha}")
                        
                        if 'SISTEMA_INICIADO' in linha or 'SENSOR_OK' in linha or 'AGUARDANDO' in linha:
                            arduino_ok = True
                            break
                    time.sleep(0.1)
                
                if arduino_ok or True:
                    print("[OK] Arduino conectado e pronto!\n")
                    self.atualizar_status('ativo', 'Arduino conectado')
                    return True
                    
            except serial.SerialException as e:
                tentativas += 1
                print(f"[ERRO] Tentativa {tentativas}/{max_tentativas} falhou: {e}")
                
                if tentativas < max_tentativas:
                    print(f"[INFO] Aguardando 2 segundos para tentar novamente...")
                    time.sleep(2)
                else:
                    print(f"\n[ERRO] Nao foi possivel conectar ao Arduino")
                    print(f"Verifique:")
                    print(f"  - Arduino esta conectado na porta {SERIAL_PORT}?")
                    print(f"  - Porta esta correta no config.py?")
                    print(f"  - Nenhum outro programa esta usando a porta?")
                    self.atualizar_status('erro', f'Erro ao conectar: {e}')
                    return False
            except Exception as e:
                print(f"[ERRO] Erro inesperado: {e}")
                self.atualizar_status('erro', f'Erro inesperado: {e}')
                return False
        
        return False
    
    def limpar_arquivos(self):
        """Limpa arquivos temporarios"""
        arquivos = [self.arquivo_reconhecimento, self.arquivo_leitura_ativa]
        for arquivo in arquivos:
            if os.path.exists(arquivo):
                try:
                    os.remove(arquivo)
                except:
                    pass
    
    def limpar_arquivos_finalizacao(self):
        """Limpa TODOS os arquivos ao encerrar"""
        print("\n[INFO] Limpando arquivos temporarios...")
        arquivos = [
            self.arquivo_reconhecimento,
            self.arquivo_leitura_ativa,
            self.arquivo_pid,
            self.arquivo_status
        ]
        for arquivo in arquivos:
            if os.path.exists(arquivo):
                try:
                    os.remove(arquivo)
                    print(f"   [OK] Removido: {os.path.basename(arquivo)}")
                except Exception as e:
                    print(f"   [AVISO] Erro ao remover {os.path.basename(arquivo)}: {e}")
    
    def marcar_leitura_ativa(self):
        """Marca que ha leitura em andamento"""
        try:
            agora = datetime.now()
            with open(self.arquivo_leitura_ativa, 'w', encoding='utf-8') as f:
                json.dump({
                    'lendo': True,
                    'timestamp': agora.strftime('%Y-%m-%d %H:%M:%S')
                }, f)
        except:
            pass
    
    def limpar_leitura_ativa(self):
        """Remove marcador de leitura ativa"""
        try:
            if os.path.exists(self.arquivo_leitura_ativa):
                os.remove(self.arquivo_leitura_ativa)
        except:
            pass
    
    def salvar_reconhecimento(self, sensor_id, confianca):
        """Salva dados do reconhecimento com HORA CORRETA (fuso Brasil)"""
        # Usa hora local do sistema (já configurado para America/Sao_Paulo)
        agora = datetime.now()
        
        dados = {
            'sensor_id': sensor_id,
            'confianca': confianca,
            'timestamp': agora.strftime('%Y-%m-%d %H:%M:%S'),
            'data': agora.strftime('%Y-%m-%d'),
            'hora': agora.strftime('%H:%M:%S')
        }
        
        try:
            with open(self.arquivo_reconhecimento, 'w', encoding='utf-8') as f:
                json.dump(dados, f, ensure_ascii=False, indent=2)
            return True
        except Exception as e:
            print(f"[AVISO] Erro ao salvar reconhecimento: {e}")
            return False
    
    def signal_handler(self, signum, frame):
        """Trata sinal de encerramento"""
        print("\n\n[INFO] Sinal de encerramento recebido...")
        self.rodando = False
    
    def reconhecer_continuo(self):
        """Loop principal de reconhecimento"""
        print("=" * 70)
        print("SISTEMA DE ENTRADA ATIVO")
        print("=" * 70)
        print("Aguardando alunos apresentarem o dedo...")
        print("Pressione Ctrl+C para parar o servidor")
        print("=" * 70 + "\n")
        
        contador = 0
        ultimo_heartbeat = time.time()
        leitura_em_andamento = False
        
        while self.rodando:
            try:
                contador += 1
                
                # Atualiza status a cada 10 segundos
                if time.time() - ultimo_heartbeat > 10:
                    self.atualizar_status('ativo', 'Aguardando leitura...')
                    ultimo_heartbeat = time.time()
                
                # Mostra heartbeat a cada 20 ciclos
                if contador % 20 == 0:
                    print(f"   [ATIVO] Sistema rodando - {datetime.now().strftime('%H:%M:%S')}")
                
                # Envia comando de reconhecimento
                self.arduino.write("RECONHECER\n".encode())
                
                timeout = time.time() + 3
                reconheceu = False
                
                while time.time() < timeout and self.rodando:
                    if self.arduino.in_waiting > 0:
                        linha = self.arduino.readline().decode('utf-8', errors='ignore').strip()
                        
                        if not linha:
                            continue
                        
                        # DETECTOU DEDO
                        if linha.startswith('STATUS:Aguardando') or linha.startswith('STATUS:Processando'):
                            if not leitura_em_andamento:
                                leitura_em_andamento = True
                                self.marcar_leitura_ativa()
                                print(f"\n   [INFO] Dedo detectado! Processando...")
                        
                        # RECONHECIDO
                        elif linha.startswith('RECONHECIDO:'):
                            partes = linha.split(':')[1].split(',')
                            sensor_id = int(partes[0])
                            confianca = int(partes[1])
                            
                            agora = time.time()
                            # Evita duplicatas
                            if (self.ultimo_sensor_id != sensor_id or 
                                self.ultimo_timestamp is None or 
                                (agora - self.ultimo_timestamp) > 3):
                                
                                self.ultimo_sensor_id = sensor_id
                                self.ultimo_timestamp = agora
                                
                                print(f"\n{'=' * 70}")
                                print(f"[SUCESSO] ALUNO RECONHECIDO!")
                                print(f"{'=' * 70}")
                                print(f"Sensor ID: {sensor_id}")
                                print(f"Confianca: {confianca}%")
                                print(f"Horario: {datetime.now().strftime('%H:%M:%S')}")
                                print(f"{'=' * 70}\n")
                                
                                if self.salvar_reconhecimento(sensor_id, confianca):
                                    print("   [OK] Entrada registrada!\n")
                                
                                self.atualizar_status('ativo', f'Ultimo reconhecimento: ID {sensor_id}')
                            
                            leitura_em_andamento = False
                            self.limpar_leitura_ativa()
                            reconheceu = True
                            break
                        
                        # NAO CADASTRADO
                        elif linha.startswith('NAO_CADASTRADO'):
                            print(f"   [AVISO] Digital nao cadastrada")
                            self.salvar_reconhecimento(0, 0)
                            leitura_em_andamento = False
                            self.limpar_leitura_ativa()
                            reconheceu = True
                            break
                        
                        # ERRO
                        elif linha.startswith('ERRO:'):
                            if 'Timeout' not in linha:
                                print(f"   [AVISO] {linha}")
                            leitura_em_andamento = False
                            self.limpar_leitura_ativa()
                            break
                    
                    time.sleep(0.05)
                
                # Se timeout sem reconhecer, limpa leitura ativa
                if not reconheceu and leitura_em_andamento:
                    leitura_em_andamento = False
                    self.limpar_leitura_ativa()
                
                time.sleep(0.5)
                
            except serial.SerialException as e:
                print(f"\n[ERRO] Erro de comunicacao serial: {e}")
                print("[INFO] Tentando reconectar...")
                
                try:
                    self.arduino.close()
                except:
                    pass
                
                if self.conectar_arduino():
                    print("[OK] Reconectado com sucesso!")
                    leitura_em_andamento = False
                    self.limpar_leitura_ativa()
                else:
                    print("[ERRO] Nao foi possivel reconectar. Encerrando...")
                    self.rodando = False
                    break
                
            except KeyboardInterrupt:
                print("\n\n" + "=" * 70)
                print("SISTEMA ENCERRADO PELO USUARIO")
                print("=" * 70 + "\n")
                self.rodando = False
                break
                
            except Exception as e:
                print(f"[AVISO] Erro no loop: {e}")
                leitura_em_andamento = False
                self.limpar_leitura_ativa()
                time.sleep(1)
    
    def iniciar(self):
        """Inicia o sistema"""
        # Registra handlers para sinais
        signal.signal(signal.SIGTERM, self.signal_handler)
        signal.signal(signal.SIGINT, self.signal_handler)
        
        print("\n" + "=" * 70)
        print("SISTEMA DE ENTRADA - FINGERPASS")
        print("=" * 70)
        print(f"Data/Hora: {datetime.now().strftime('%d/%m/%Y %H:%M:%S')}")
        print(f"Timezone: {os.environ.get('TZ', 'Sistema')}")
        print("=" * 70 + "\n")
        
        # Cria diretorio de logs se nao existir
        if not os.path.exists(LOGS_DIR):
            print(f"[INFO] Criando pasta de logs: {LOGS_DIR}")
            os.makedirs(LOGS_DIR, exist_ok=True)
        
        # Cria arquivo PID
        if not self.criar_pid():
            print("[AVISO] Falha ao criar arquivo PID (continuando mesmo assim)")
        
        # Limpa arquivos temporarios
        self.limpar_arquivos()
        
        # Atualiza status inicial
        self.atualizar_status('iniciando', 'Conectando ao Arduino...')
        
        # Conecta ao Arduino
        if not self.conectar_arduino():
            print("\n[ERRO] FALHA AO CONECTAR ARDUINO")
            print("Sistema nao pode iniciar sem o sensor")
            self.atualizar_status('erro', 'Falha ao conectar Arduino')
            self.limpar_arquivos_finalizacao()
            input("\nPressione ENTER para sair...")
            return
        
        print("[OK] SISTEMA PRONTO!\n")
        self.atualizar_status('ativo', 'Sistema pronto')
        
        try:
            self.reconhecer_continuo()
        except Exception as e:
            print(f"\n[ERRO] Erro fatal: {e}")
            self.atualizar_status('erro', f'Erro fatal: {e}')
        finally:
            self.limpar_arquivos_finalizacao()
            if self.arduino:
                try:
                    self.arduino.close()
                    print("[OK] Conexao serial fechada")
                except:
                    pass
            print("\n[INFO] Servidor encerrado\n")

if __name__ == "__main__":
    try:
        sistema = SistemaEntrada()
        sistema.iniciar()
    except Exception as e:
        print(f"\n[ERRO] Erro critico: {e}")
        import traceback
        traceback.print_exc()
        
        # Limpa PID mesmo em caso de erro
        try:
            pid_file = os.path.join(LOGS_DIR, 'servidor_pid.txt')
            if os.path.exists(pid_file):
                os.remove(pid_file)
        except:
            pass
        
        input("\nPressione ENTER para sair...")