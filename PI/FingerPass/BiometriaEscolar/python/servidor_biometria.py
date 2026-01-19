#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Sistema de Biometria Escolar - Servidor Principal
Monitora requisições PHP e se comunica com Arduino
"""

import serial
import json
import time
import mysql.connector
from datetime import datetime
import os
import sys

# Importa configurações
try:
    from config import DB_CONFIG, SERIAL_PORT, SERIAL_BAUD, STATUS_FILE, REQUISICAO_FILE, LOGS_DIR
except ImportError:
    print("[ERRO] Arquivo config.py nao encontrado!")
    print("Crie o arquivo config.py com as configuracoes necessarias.")
    input("Pressione ENTER para sair...")
    sys.exit(1)

class SistemaBiometria:
    def __init__(self):
        self.arduino = None
        self.db = None
        self.cursor = None
        self.arquivo_pid_cadastro = os.path.join(LOGS_DIR, 'servidor_cadastro_pid.txt')
    
    def criar_pid(self):
        """Cria arquivo com PID do processo"""
        try:
            pid_file = os.path.join(LOGS_DIR, 'servidor_pid.txt')
            pid = os.getpid()
            with open(pid_file, 'w') as f:
                f.write(str(pid))
            print(f"[OK] PID criado: {pid}")
            return True
        except Exception as e:
            print(f"[AVISO] Erro ao criar PID: {e}")
            return False
    
    def limpar_arquivos_finalizacao(self):
        """Limpa TODOS os arquivos ao encerrar"""
        print("\n[INFO] Limpando arquivos temporarios...")
        arquivo_reconhecimento = os.path.join(LOGS_DIR, 'reconhecimento.json')
        arquivo_leitura_ativa = os.path.join(LOGS_DIR, 'leitura_ativa.json')
        arquivo_pid = os.path.join(LOGS_DIR, 'servidor_pid.txt')
        arquivo_status = os.path.join(LOGS_DIR, 'status.json')
        
        arquivos = [
            arquivo_reconhecimento,
            arquivo_leitura_ativa,
            arquivo_pid,
            arquivo_status,
            self.arquivo_pid_cadastro
        ]
        for arquivo in arquivos:
            if os.path.exists(arquivo):
                try:
                    os.remove(arquivo)
                    print(f"   [OK] Removido: {os.path.basename(arquivo)}")
                except Exception as e:
                    print(f"   [AVISO] Erro ao remover {os.path.basename(arquivo)}: {e}")
        
    def conectar_arduino(self):
        """Conecta com Arduino via Serial"""
        print(f"\n[CONECTANDO] Arduino na porta {SERIAL_PORT}...")
        try:
            self.arduino = serial.Serial(SERIAL_PORT, SERIAL_BAUD, timeout=1)
            time.sleep(2)
            self.arduino.reset_input_buffer()
            
            print("   Aguardando resposta do Arduino...")
            inicio = time.time()
            while (time.time() - inicio) < 5:
                if self.arduino.in_waiting > 0:
                    linha = self.arduino.readline().decode('utf-8', errors='ignore').strip()
                    print(f"   Arduino: {linha}")
                    if "AGUARDANDO" in linha or "SISTEMA_INICIADO" in linha:
                        print("[OK] Arduino conectado com sucesso!\n")
                        return True
                time.sleep(0.1)
            
            print("[OK] Arduino conectado (sem confirmacao de status)\n")
            return True
            
        except serial.SerialException as e:
            print(f"[ERRO] Erro ao conectar Arduino: {e}")
            print(f"   Verifique se:")
            print(f"   1. Arduino esta conectado na porta {SERIAL_PORT}")
            print(f"   2. Arduino IDE esta FECHADO")
            print(f"   3. Nenhum outro programa esta usando a porta")
            return False
        except Exception as e:
            print(f"[ERRO] Erro inesperado: {e}")
            return False
    
    def conectar_banco(self):
        """Conecta com banco de dados MySQL"""
        print("[CONECTANDO] Banco de dados...")
        try:
            self.db = mysql.connector.connect(**DB_CONFIG)
            self.cursor = self.db.cursor(dictionary=True)
            print("[OK] Banco de dados conectado!\n")
            return True
        except mysql.connector.Error as e:
            print(f"[ERRO] Erro ao conectar banco: {e}")
            print(f"   Verifique se:")
            print(f"   1. XAMPP MySQL esta rodando")
            print(f"   2. Banco '{DB_CONFIG['database']}' existe")
            print(f"   3. Usuario e senha estao corretos no config.py")
            return False
    
    def escrever_status(self, status, mensagem='', sensor_id=None):
        """Escreve status em arquivo JSON para PHP ler"""
        data = {
            'status': status,
            'mensagem': mensagem,
            'sensor_id': sensor_id,
            'timestamp': datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        }
        
        try:
            with open(STATUS_FILE, 'w', encoding='utf-8') as f:
                json.dump(data, f, ensure_ascii=False, indent=2)
            print(f"   [STATUS] {status} - {mensagem}")
        except Exception as e:
            print(f"   [AVISO] Erro ao escrever status: {e}")
    
    def enviar_comando(self, comando):
        """Envia comando para Arduino"""
        if self.arduino:
            self.arduino.write(f"{comando}\n".encode())
            print(f"   [CMD] Comando enviado: {comando}")
            time.sleep(0.2)
    
    def cadastrar_digital(self, matricula, nome):
        """Cadastra nova digital no sensor"""
        print(f"\n{'='*70}")
        print(f"[CADASTRO] INICIANDO CADASTRO DE BIOMETRIA")
        print(f"{'='*70}")
        print(f"[ALUNO] {nome}")
        print(f"[MATRICULA] {matricula}")
        print(f"{'='*70}\n")
        
        self.escrever_status('processando', 'Preparando sensor... Aguarde!')
        
        if self.arduino.in_waiting > 0:
            self.arduino.reset_input_buffer()
        
        self.enviar_comando("CADASTRAR")
        
        sensor_id = None
        timeout = time.time() + 60
        ultima_mensagem = ""
        
        while time.time() < timeout:
            if self.arduino.in_waiting > 0:
                linha = self.arduino.readline().decode('utf-8', errors='ignore').strip()
                
                if linha:
                    print(f"   [ARDUINO] {linha}")
                    
                    if linha.startswith('STATUS:'):
                        mensagem = linha.split(':', 1)[1].strip()
                        if mensagem != ultima_mensagem:
                            self.escrever_status('processando', mensagem)
                            ultima_mensagem = mensagem
                    
                    elif linha.startswith('CADASTRADO:'):
                        sensor_id = int(linha.split(':')[1].strip())
                        print(f"\n{'='*70}")
                        print(f"[OK] DIGITAL CADASTRADA COM SUCESSO!")
                        print(f"[ID] Sensor ID: {sensor_id}")
                        print(f"{'='*70}\n")
                        break
                    
                    elif linha.startswith('ERRO:'):
                        mensagem = linha.split(':', 1)[1].strip()
                        print(f"\n{'='*70}")
                        print(f"[ERRO] ERRO NO CADASTRO")
                        print(f"[MSG] {mensagem}")
                        print(f"{'='*70}\n")
                        self.escrever_status('erro', mensagem)
                        return None
            
            time.sleep(0.1)
        
        if sensor_id is None:
            print(f"\n{'='*70}")
            print(f"[TIMEOUT] TEMPO ESGOTADO")
            print(f"{'='*70}\n")
            self.escrever_status('erro', 'Tempo esgotado! Tente novamente.')
            return None
        
        print("[SALVANDO] Salvando no banco de dados...")
        try:
            sql = "UPDATE aluno SET biometria = %s WHERE matricula = %s"
            self.cursor.execute(sql, (sensor_id, matricula))
            self.db.commit()
            
            print(f"[OK] Biometria vinculada ao aluno no banco!\n")
            self.escrever_status('sucesso', f'Biometria cadastrada! ID: {sensor_id}', sensor_id)
            return sensor_id
            
        except mysql.connector.Error as e:
            print(f"[ERRO] Erro ao salvar no banco: {e}\n")
            self.escrever_status('erro', f'Erro ao salvar no banco: {e}')
            return None
    
    def editar_digital(self, matricula, nome, biometria_antiga):
        """Edita biometria: deleta antiga e cadastra nova"""
        print(f"\n{'='*70}")
        print(f"[EDITAR] EDITANDO BIOMETRIA")
        print(f"{'='*70}")
        print(f"[ALUNO] {nome}")
        print(f"[MATRICULA] {matricula}")
        
        if biometria_antiga and biometria_antiga != '' and biometria_antiga != 'null':
            print(f"[DELETE] Biometria antiga: ID {biometria_antiga}")
            print(f"{'='*70}\n")
            
            print("[DELETE] Deletando biometria antiga do sensor...")
            self.enviar_comando(f"DELETAR:{biometria_antiga}")
            time.sleep(1)
            
            timeout = time.time() + 3
            while time.time() < timeout:
                if self.arduino.in_waiting > 0:
                    linha = self.arduino.readline().decode('utf-8', errors='ignore').strip()
                    if linha:
                        print(f"   [ARDUINO] {linha}")
                        
                        if linha.startswith('DELETADO:'):
                            print("[OK] Biometria antiga deletada!\n")
                            break
                        elif linha.startswith('ERRO:'):
                            print("[AVISO] Nao foi possivel deletar (pode nao existir mais)\n")
                            break
                time.sleep(0.1)
        else:
            print(f"[INFO] Sem biometria anterior (primeiro cadastro)")
            print(f"{'='*70}\n")
        
        print("[CADASTRO] Cadastrando nova biometria...")
        sensor_id = self.cadastrar_digital(matricula, nome)
        
        if sensor_id:
            self.escrever_status('sucesso', f'Biometria atualizada! Novo ID: {sensor_id}', sensor_id)
        
        return sensor_id
    
    def processar_requisicao(self, requisicao):
        """Processa requisição do PHP"""
        acao = requisicao.get('acao')
        
        if acao == 'cadastrar':
            matricula = requisicao.get('matricula')
            nome = requisicao.get('nome')
            
            if matricula and nome:
                return self.cadastrar_digital(matricula, nome)
        
        elif acao == 'editar':
            matricula = requisicao.get('matricula')
            nome = requisicao.get('nome')
            biometria_antiga = requisicao.get('biometria_antiga')
            
            if matricula and nome:
                return self.editar_digital(matricula, nome, biometria_antiga)
        
        return None
    
    def processar_delecao_biometria(self):
        """Processa solicitações de deleção de biometria do atualiza_aluno.php"""
        arquivo_delete = os.path.join(LOGS_DIR, 'delete_biometria.json')
        
        if os.path.exists(arquivo_delete):
            try:
                with open(arquivo_delete, 'r', encoding='utf-8') as f:
                    delete_req = json.load(f)
                
                sensor_id = delete_req.get('sensor_id')
                
                if sensor_id:
                    print(f"\n{'='*70}")
                    print(f"[DELETE] SOLICITACAO DE DELECAO DETECTADA")
                    print(f"{'='*70}")
                    print(f"[ID] Sensor ID: {sensor_id}")
                    print(f"{'='*70}\n")
                    
                    self.enviar_comando(f"DELETAR:{sensor_id}")
                    time.sleep(1)
                    
                    if self.arduino.in_waiting > 0:
                        linha = self.arduino.readline().decode('utf-8', errors='ignore').strip()
                        print(f"   [ARDUINO] {linha}")
                
                os.remove(arquivo_delete)
                print("[OK] Arquivo de delecao processado e removido\n")
                
            except Exception as e:
                print(f"[AVISO] Erro ao processar delecao: {e}")
                try:
                    os.remove(arquivo_delete)
                except:
                    pass
    
    def monitorar_requisicoes(self):
        """Monitora arquivo de requisições do PHP"""
        print("="*70)
        print("[MONITOR] MONITORAMENTO ATIVO")
        print("="*70)
        print(f"[LOGS] {LOGS_DIR}")
        print(f"[REQ] {REQUISICAO_FILE}")
        print(f"[STATUS] {STATUS_FILE}")
        print("="*70)
        print("[AGUARDANDO] Aguardando requisicoes do sistema web...\n")
        
        ultimo_timestamp = None
        contador = 0
        
        while True:
            try:
                contador += 1
                if contador % 20 == 0:
                    print(f"   [ATIVO] Sistema ativo - {datetime.now().strftime('%H:%M:%S')}")
                
                self.processar_delecao_biometria()
                
                if os.path.exists(REQUISICAO_FILE):
                    print(f"\n{'='*70}")
                    print(f"[NOVA] NOVA REQUISICAO DETECTADA!")
                    print(f"{'='*70}")
                    
                    try:
                        with open(REQUISICAO_FILE, 'r', encoding='utf-8') as f:
                            requisicao = json.load(f)
                        
                        print(f"[DADOS] Dados da requisicao:")
                        print(f"   Acao: {requisicao.get('acao')}")
                        print(f"   Matricula: {requisicao.get('matricula')}")
                        print(f"   Nome: {requisicao.get('nome')}")
                        
                        if requisicao.get('biometria_antiga'):
                            print(f"   Biometria Antiga: {requisicao.get('biometria_antiga')}")
                        
                        print(f"   Timestamp: {requisicao.get('timestamp')}")
                        
                        timestamp = requisicao.get('timestamp')
                        if timestamp != ultimo_timestamp:
                            ultimo_timestamp = timestamp
                            
                            self.processar_requisicao(requisicao)
                            
                            try:
                                os.remove(REQUISICAO_FILE)
                                print(f"[LIMPO] Arquivo de requisicao removido\n")
                            except:
                                pass
                        else:
                            print("[DUPLICADO] Requisicao duplicada - ignorando\n")
                    
                    except json.JSONDecodeError as e:
                        print(f"[ERRO] Erro ao ler JSON: {e}")
                        os.remove(REQUISICAO_FILE)
                
                time.sleep(0.5)
                
            except KeyboardInterrupt:
                print("\n\n" + "="*70)
                print("[STOP] SISTEMA ENCERRADO PELO USUARIO")
                print("="*70 + "\n")
                break
            except Exception as e:
                print(f"[AVISO] Erro ao processar: {e}")
                time.sleep(1)
    
    def iniciar(self):
        """Inicia o sistema"""
        print("\n" + "="*70)
        print("[INICIO] SISTEMA DE BIOMETRIA ESCOLAR - FINGERPASS")
        print("="*70)
        print(f"[DATA] {datetime.now().strftime('%d/%m/%Y %H:%M:%S')}")
        print("="*70 + "\n")
        
        if not os.path.exists(LOGS_DIR):
            print(f"[CRIANDO] Criando pasta: {LOGS_DIR}")
            os.makedirs(LOGS_DIR, exist_ok=True)
            print("[OK] Pasta criada!\n")
        else:
            print(f"[OK] Pasta logs encontrada: {LOGS_DIR}\n")
        
        # Cria PID normal
        if not self.criar_pid():
            print("[AVISO] Falha ao criar arquivo PID (continuando mesmo assim)")
        
        # Cria PID de cadastro
        try:
            with open(self.arquivo_pid_cadastro, 'w') as f:
                f.write(str(os.getpid()))
            print(f"[OK] PID de cadastro criado: {os.getpid()}\n")
        except Exception as e:
            print(f"[AVISO] Erro ao criar PID de cadastro: {e}\n")
        
        if not self.conectar_arduino():
            print("\n" + "="*70)
            print("[ERRO] FALHA AO CONECTAR ARDUINO")
            print("="*70)
            input("\nPressione ENTER para sair...")
            return
        
        if not self.conectar_banco():
            print("\n" + "="*70)
            print("[ERRO] FALHA AO CONECTAR BANCO DE DADOS")
            print("="*70)
            input("\nPressione ENTER para sair...")
            return
        
        print("="*70)
        print("[OK] TODOS OS SISTEMAS PRONTOS!")
        print("="*70 + "\n")
        
        try:
            self.monitorar_requisicoes()
        except Exception as e:
            print(f"\n[ERRO] Erro fatal: {e}")
        finally:
            self.limpar_arquivos_finalizacao()
            
            if self.arduino:
                try:
                    self.arduino.close()
                    print("[OK] Conexao serial fechada")
                except:
                    pass
            
            if self.db:
                try:
                    self.db.close()
                    print("[OK] Banco de dados desconectado")
                except:
                    pass
            
            print("\n[INFO] Servidor encerrado\n")
            input("Pressione ENTER para fechar...")

# Execução principal
if __name__ == "__main__":
    try:
        sistema = SistemaBiometria()
        sistema.iniciar()
    except Exception as e:
        print(f"\n[ERRO] Erro critico: {e}")
        input("\nPressione ENTER para sair...")