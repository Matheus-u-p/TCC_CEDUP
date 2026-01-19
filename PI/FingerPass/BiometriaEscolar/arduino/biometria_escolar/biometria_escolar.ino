#include <Adafruit_Fingerprint.h>
#include <SoftwareSerial.h>

SoftwareSerial mySerial(2, 3);
Adafruit_Fingerprint finger = Adafruit_Fingerprint(&mySerial);

uint8_t id;

void setup() {
  Serial.begin(9600);
  while (!Serial);
  delay(100);
  
  Serial.println(F("SISTEMA_INICIADO"));
  
  mySerial.begin(57600);
  finger.begin(57600);
  
  if (finger.verifyPassword()) {
    Serial.println(F("SENSOR_OK"));
    finger.getTemplateCount();
    Serial.print(F("INFO:Digitais_cadastradas:"));
    Serial.println(finger.templateCount);
  } else {
    Serial.println(F("ERRO:Sensor nao encontrado"));
    while (1) { delay(1); }
  }
  
  Serial.println(F("AGUARDANDO_COMANDOS"));
}

void loop() {
  if (Serial.available() > 0) {
    String cmd = Serial.readStringUntil('\n');
    cmd.trim();
    
    if (cmd == "CADASTRAR") {
      cadastrar();
    }
    else if (cmd == "RECONHECER") {
      reconhecer();
    }
    else if (cmd.startsWith("DELETAR:")) {
      deletar(cmd.substring(8).toInt());
    }
    else if (cmd == "PROXIMO_ID") {
      proximoID();
    }
    else if (cmd == "LIMPAR_TUDO") {
      limpar();
    }
    else if (cmd == "CONTAR") {
      contar();
    }
  }
  delay(10);
}

void cadastrar() {
  Serial.println(F("STATUS:Preparando..."));
  
  id = buscarID();
  if (id == 0) {
    Serial.println(F("ERRO:Memoria cheia"));
    return;
  }
  
  Serial.print(F("STATUS:Usando ID #"));
  Serial.println(id);
  
  // 🔧 FORÇA DELETAR O SLOT ANTES (IMPORTANTE!)
  finger.deleteModel(id);
  delay(200);
  
  Serial.println(F("STATUS:Coloque o dedo"));
  
  // LEITURA 1
  int p = -1;
  uint8_t t = 0;
  
  while (p != FINGERPRINT_OK && t < 200) {
    p = finger.getImage();
    if (p == FINGERPRINT_NOFINGER) {
      delay(100);
      t++;
    }
    else if (p != FINGERPRINT_OK) {
      Serial.println(F("ERRO:Falha captura"));
      return;
    }
  }
  
  if (t >= 200) {
    Serial.println(F("ERRO:Timeout"));
    return;
  }
  
  Serial.println(F("STATUS:Capturada!"));
  
  p = finger.image2Tz(1);
  if (p != FINGERPRINT_OK) {
    Serial.println(F("ERRO:Falha processar"));
    return;
  }
  
  Serial.println(F("STATUS:Retire o dedo"));
  delay(2000);
  
  while (finger.getImage() != FINGERPRINT_NOFINGER);
  
  // LEITURA 2
  Serial.println(F("STATUS:Coloque novamente"));
  
  p = -1;
  t = 0;
  
  while (p != FINGERPRINT_OK && t < 200) {
    p = finger.getImage();
    if (p == FINGERPRINT_NOFINGER) {
      delay(100);
      t++;
    }
  }
  
  if (t >= 200) {
    Serial.println(F("ERRO:Timeout 2"));
    return;
  }
  
  Serial.println(F("STATUS:Capturada!"));
  
  p = finger.image2Tz(2);
  if (p != FINGERPRINT_OK) {
    Serial.println(F("ERRO:Falha processar 2"));
    return;
  }
  
  // CRIAR MODELO
  Serial.println(F("STATUS:Criando modelo"));
  p = finger.createModel();
  
  if (p == FINGERPRINT_ENROLLMISMATCH) {
    Serial.println(F("ERRO:Digitais NAO coincidem"));
    return;
  }
  else if (p != FINGERPRINT_OK) {
    Serial.print(F("ERRO:Falha modelo codigo="));
    Serial.println(p);
    return;
  }
  
  // SALVAR COM VERIFICAÇÃO EXTRA
  Serial.println(F("STATUS:Salvando"));
  
  // 🔧 TENTA DELETAR NOVAMENTE ANTES DE SALVAR
  finger.deleteModel(id);
  delay(100);
  
  p = finger.storeModel(id);
  
  if (p == FINGERPRINT_OK) {
    // ✅ VERIFICA SE SALVOU MESMO
    delay(200);
    uint8_t verifica = finger.loadModel(id);
    
    if (verifica == FINGERPRINT_OK) {
      Serial.print(F("CADASTRADO:"));
      Serial.println(id);
    } else {
      Serial.println(F("ERRO:Salvou mas nao consegue ler"));
    }
  }
  else if (p == FINGERPRINT_BADLOCATION) {
    Serial.println(F("ERRO:ID invalido ou fora do alcance"));
  }
  else if (p == FINGERPRINT_FLASHERR) {
    Serial.println(F("ERRO:Erro de escrita na flash"));
  }
  else {
    Serial.print(F("ERRO:Falha salvar codigo="));
    Serial.println(p);
  }
}

void reconhecer() {
  Serial.println(F("STATUS:Aguardando"));
  
  unsigned long inicio = millis();
  uint8_t p = FINGERPRINT_NOFINGER;
  
  while (p == FINGERPRINT_NOFINGER) {
    p = finger.getImage();
    if (millis() - inicio > 15000) {
      Serial.println(F("ERRO:Timeout"));
      return;
    }
    delay(100);
  }
  
  if (p != FINGERPRINT_OK) {
    Serial.println(F("ERRO:Falha"));
    return;
  }
  
  p = finger.image2Tz();
  if (p != FINGERPRINT_OK) {
    Serial.println(F("ERRO:Processar"));
    return;
  }
  
  p = finger.fingerFastSearch();
  
  if (p == FINGERPRINT_OK) {
    Serial.print(F("RECONHECIDO:"));
    Serial.print(finger.fingerID);
    Serial.print(F(","));
    Serial.println(finger.confidence);
  }
  else if (p == FINGERPRINT_NOTFOUND) {
    Serial.println(F("NAO_CADASTRADO"));
  }
  else {
    Serial.println(F("ERRO:Busca"));
  }
}

void deletar(uint8_t id) {
  uint8_t p = finger.deleteModel(id);
  
  if (p == FINGERPRINT_OK) {
    Serial.print(F("DELETADO:"));
    Serial.println(id);
  } 
  else if (p == FINGERPRINT_BADLOCATION) {
    Serial.println(F("ERRO:ID invalido"));
  }
  else {
    Serial.print(F("ERRO:Deletar codigo="));
    Serial.println(p);
  }
}

void limpar() {
  if (finger.emptyDatabase() == FINGERPRINT_OK) {
    Serial.println(F("LIMPO:OK"));
  } else {
    Serial.println(F("ERRO:Limpar"));
  }
}

void contar() {
  finger.getTemplateCount();
  Serial.print(F("CONTAGEM:"));
  Serial.println(finger.templateCount);
}

// 🔧 FUNÇÃO BUSCAR ID COMPLETAMENTE REESCRITA
uint8_t buscarID() {
  // Atualiza contagem
  finger.getTemplateCount();
  
  // Se vazio, retorna ID 1
  if (finger.templateCount == 0) {
    return 1;
  }
  
  // Se cheio, retorna 0
  if (finger.templateCount >= 162) {
    return 0;
  }
  
  // 🔧 BUSCA SEQUENCIAL MAIS CONFIÁVEL
  // Começa do ID 1 e vai até 162
  for (uint8_t i = 1; i <= 162; i++) {
    // Tenta carregar o modelo
    uint8_t p = finger.loadModel(i);
    
    // Se retornar erro de localização/recepção, o slot está LIVRE
    if (p == FINGERPRINT_PACKETRECIEVEERR || 
        p == FINGERPRINT_BADLOCATION ||
        p != FINGERPRINT_OK) {
      
      // ✅ VERIFICA SE REALMENTE ESTÁ LIVRE tentando deletar
      finger.deleteModel(i);
      delay(50);
      
      return i;
    }
  }
  
  // Não encontrou nenhum livre (não deveria acontecer)
  return 0;
}

void proximoID() {
  uint8_t proximo = buscarID();
  Serial.print(F("PROXIMO_ID:"));
  Serial.println(proximo);
}