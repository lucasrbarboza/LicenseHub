@echo off
REM =====================================================
REM Script de Instalação - LicenseHub (Windows)
REM =====================================================

setlocal enabledelayedexpansion

echo.
echo 🚀 Iniciando instalacao do LicenseHub...
echo.

REM Verificar pré-requisitos
echo 📋 Verificando pre-requisitos...
echo.

where php >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ❌ PHP nao encontrado. Por favor, instale PHP 8.0+
    pause
    exit /b 1
)
echo ✅ PHP encontrado
php -v | find "PHP" 

where mysql >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ❌ MySQL nao encontrado. Por favor, instale MySQL 8.0+
    pause
    exit /b 1
)
echo ✅ MySQL encontrado

where composer >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ❌ Composer nao encontrado. Por favor, instale Composer
    pause
    exit /b 1
)
echo ✅ Composer encontrado
composer --version

echo.

REM Criar arquivo .env
echo 📝 Criando arquivo .env...
if not exist .env (
    copy .env.example .env
    echo ✅ Arquivo .env criado
) else (
    echo ⚠️  Arquivo .env ja existe, pulando...
)

echo.

REM Instalar dependências do Composer
echo 📦 Instalando dependencias do Composer...
call composer install

echo.

REM Prompt para banco de dados
echo 🗄️  Configurando banco de dados...
echo.

set /p DB_HOST="Qual e o host do MySQL (padrao: localhost): "
if "%DB_HOST%"=="" set DB_HOST=localhost

set /p DB_USER="Qual e o usuario do MySQL (padrao: root): "
if "%DB_USER%"=="" set DB_USER=root

set /p DB_PASSWORD="Qual e a senha do MySQL (deixe em branco se nao houver): "

set /p DB_NAME="Qual e o nome do banco (padrao: licensehub): "
if "%DB_NAME%"=="" set DB_NAME=licensehub

echo.
echo ✅ Banco de dados configurado:
echo    Host: !DB_HOST!
echo    Usuario: !DB_USER!
echo    Banco: !DB_NAME!
echo.

REM Atualizar arquivo .env
echo Atualizando arquivo .env...
powershell -Command "(Get-Content .env) -replace 'DB_HOST=.*', 'DB_HOST=!DB_HOST!' | Set-Content .env"
powershell -Command "(Get-Content .env) -replace 'DB_USER=.*', 'DB_USER=!DB_USER!' | Set-Content .env"
powershell -Command "(Get-Content .env) -replace 'DB_NAME=.*', 'DB_NAME=!DB_NAME!' | Set-Content .env"
if not "!DB_PASSWORD!"=="" (
    powershell -Command "(Get-Content .env) -replace 'DB_PASSWORD=.*', 'DB_PASSWORD=!DB_PASSWORD!' | Set-Content .env"
)

echo ✅ Arquivo .env atualizado
echo.

REM Criar banco de dados
echo 🗄️  Criando banco de dados...
if "!DB_PASSWORD!"=="" (
    mysql -h !DB_HOST! -u !DB_USER! -e "CREATE DATABASE IF NOT EXISTS !DB_NAME! CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    mysql -h !DB_HOST! -u !DB_USER! !DB_NAME! < database.sql
) else (
    mysql -h !DB_HOST! -u !DB_USER! -p!DB_PASSWORD! -e "CREATE DATABASE IF NOT EXISTS !DB_NAME! CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    mysql -h !DB_HOST! -u !DB_USER! -p!DB_PASSWORD! !DB_NAME! < database.sql
)

echo ✅ Banco de dados criado e populado
echo.

REM Resumo
echo ═══════════════════════════════════════════════════════
echo ✅ Instalacao concluida com sucesso!
echo ═══════════════════════════════════════════════════════
echo.
echo 📝 Proximos passos:
echo.
echo 1. Inicie o servidor PHP:
echo    php -S localhost:8000 -t public/
echo.
echo 2. Teste a API:
echo    curl http://localhost:8000/health
echo.
echo 3. Consulte a documentacao:
echo    Abra o arquivo: API_DOCUMENTATION.md
echo.
echo 📚 Documentacao completa em: SETUP_GUIDE.md
echo.

pause
