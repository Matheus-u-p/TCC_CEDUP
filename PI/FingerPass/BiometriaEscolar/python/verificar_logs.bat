@echo off
echo ========================================
echo  VERIFICACAO DA PASTA LOGS
echo ========================================
echo.

cd /d %~dp0

:: Verifica se pasta logs existe
if not exist "logs" (
    echo [!] Pasta logs NAO existe!
    echo [+] Criando pasta logs...
    mkdir logs
    echo [OK] Pasta criada!
) else (
    echo [OK] Pasta logs existe
)

echo.

:: Cria arquivos se não existirem
cd logs

if not exist "status.json" (
    echo [!] status.json NAO existe
    echo [+] Criando status.json...
    echo {"status":"aguardando","mensagem":"","sensor_id":null,"timestamp":""} > status.json
    echo [OK] Criado!
) else (
    echo [OK] status.json existe
)

if not exist "api_debug.log" (
    echo [!] api_debug.log NAO existe
    echo [+] Criando api_debug.log...
    echo. > api_debug.log
    echo [OK] Criado!
) else (
    echo [OK] api_debug.log existe
)

echo.
echo ========================================
echo  CONFIGURANDO PERMISSOES
echo ========================================
echo.

cd ..
icacls logs /grant Everyone:(OI)(CI)F

echo.
echo ========================================
echo  ESTRUTURA FINAL
echo ========================================
echo.

dir logs

echo.
echo ========================================
echo  VERIFICACAO CONCLUIDA!
echo ========================================
echo.
echo Agora execute: python\servidor_biometria.py
echo.

pause