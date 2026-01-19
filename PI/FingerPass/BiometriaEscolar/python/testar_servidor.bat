@echo off
chcp 65001 >nul
cls

echo ====================================================================
echo TESTE COMPLETO DO SISTEMA FINGERPASS
echo ====================================================================
echo.

set BASE_DIR=C:\xampp\htdocs\TCC\VFP9.0\PI\FingerPass\BiometriaEscolar
set PYTHON_DIR=%BASE_DIR%\python
set PYTHON_EXE=C:\Users\mathe\AppData\Local\Programs\Python\Python313\python.exe

cd /d "%PYTHON_DIR%"

echo [1/5] Verificando arquivos...
echo.

if not exist "%PYTHON_EXE%" (
    echo [ERRO] Python NAO encontrado em: %PYTHON_EXE%
    echo Procure em: C:\Users\%USERNAME%\AppData\Local\Programs\Python\
    pause
    exit /b 1
)
echo    [OK] Python encontrado

if not exist "servidor_entrada.py" (
    echo [ERRO] servidor_entrada.py NAO encontrado!
    pause
    exit /b 1
)
echo    [OK] servidor_entrada.py

if not exist "config.py" (
    echo [ERRO] config.py NAO encontrado!
    pause
    exit /b 1
)
echo    [OK] config.py
echo.

echo [2/5] Limpando arquivos antigos...
del /Q "%BASE_DIR%\logs\*.json" 2>nul
del /Q "%BASE_DIR%\logs\*.txt" 2>nul
echo    [OK] Logs limpos
echo.

echo [3/5] Testando config.py...
"%PYTHON_EXE%" -c "from config import SERIAL_PORT, LOGS_DIR; print('[OK] Config carregado')"
if errorlevel 1 (
    echo [ERRO] Problema no config.py
    pause
    exit /b 1
)
echo.

echo [4/5] Testando imports Python...
"%PYTHON_EXE%" -c "import serial; print('[OK] pyserial instalado')"
if errorlevel 1 (
    echo [ERRO] pyserial NAO instalado!
    echo Execute: pip install pyserial
    pause
    exit /b 1
)
echo.

echo [5/5] Verificando porta COM...
mode COM6: >nul 2>&1
if errorlevel 1 (
    echo [AVISO] Porta COM6 pode nao estar disponivel
) else (
    echo    [OK] Porta COM6 detectada
)
echo.

echo ====================================================================
echo TUDO PRONTO! INICIANDO SERVIDOR...
echo ====================================================================
echo.
echo INSTRUCOES:
echo - O servidor vai iniciar em 3 segundos
echo - Pressione Ctrl+C para parar
echo - Apresente o dedo ao sensor para testar
echo.

timeout /t 3 >nul

"%PYTHON_EXE%" servidor_entrada.py

echo.
echo ====================================================================
echo Servidor encerrado
echo ====================================================================
pause