@echo off
echo ========================================
echo TESTE COMPLETO DO SISTEMA
echo ========================================

echo.
echo [1/4] Verificando Python...
python --version
if errorlevel 1 (
    echo ERRO: Python nao encontrado!
    pause
    exit
)

echo.
echo [2/4] Testando imports do config.py...
python -c "from config import SERIAL_PORT, LOGS_DIR; print('OK')"
if errorlevel 1 (
    echo ERRO: Problema no config.py
    pause
    exit
)

echo.
echo [3/4] Verificando pasta logs...
if not exist "..\logs" (
    echo Pasta logs nao existe! Criando...
    mkdir ..\logs
)

echo.
echo [4/4] Testando servidor_entrada.py...
python servidor_entrada.py
pause