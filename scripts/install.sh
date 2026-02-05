#!/bin/bash

# =====================================================
# Script de Instalação - LicenseHub
# =====================================================

echo "🚀 Iniciando instalação do LicenseHub..."

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Verificar pré-requisitos
echo -e "${YELLOW}📋 Verificando pré-requisitos...${NC}"

# Verificar PHP
if ! command -v php &> /dev/null; then
    echo -e "${RED}❌ PHP não encontrado. Por favor, instale PHP 8.0+${NC}"
    exit 1
fi
echo -e "${GREEN}✅ PHP encontrado: $(php -v | head -n 1)${NC}"

# Verificar MySQL
if ! command -v mysql &> /dev/null; then
    echo -e "${RED}❌ MySQL não encontrado. Por favor, instale MySQL 8.0+${NC}"
    exit 1
fi
echo -e "${GREEN}✅ MySQL encontrado${NC}"

# Verificar Composer
if ! command -v composer &> /dev/null; then
    echo -e "${RED}❌ Composer não encontrado. Por favor, instale Composer${NC}"
    exit 1
fi
echo -e "${GREEN}✅ Composer encontrado: $(composer --version)${NC}"

# Criar arquivo .env
echo -e "${YELLOW}📝 Criando arquivo .env...${NC}"
if [ ! -f .env ]; then
    cp .env.example .env
    echo -e "${GREEN}✅ Arquivo .env criado${NC}"
else
    echo -e "${YELLOW}⚠️  Arquivo .env já existe, pulando...${NC}"
fi

# Instalar dependências do Composer
echo -e "${YELLOW}📦 Instalando dependências do Composer...${NC}"
composer install

# Prompt para banco de dados
echo -e "${YELLOW}🗄️  Configurando banco de dados...${NC}"
read -p "Qual é o host do MySQL? (padrão: localhost): " DB_HOST
DB_HOST=${DB_HOST:-localhost}

read -p "Qual é o usuário do MySQL? (padrão: root): " DB_USER
DB_USER=${DB_USER:-root}

read -sp "Qual é a senha do MySQL? (deixe em branco se não houver): " DB_PASSWORD
echo

read -p "Qual é o nome do banco? (padrão: licensehub): " DB_NAME
DB_NAME=${DB_NAME:-licensehub}

# Atualizar arquivo .env
sed -i.bak "s/DB_HOST=.*/DB_HOST=$DB_HOST/" .env
sed -i.bak "s/DB_USER=.*/DB_USER=$DB_USER/" .env
sed -i.bak "s/DB_NAME=.*/DB_NAME=$DB_NAME/" .env
if [ -n "$DB_PASSWORD" ]; then
    sed -i.bak "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASSWORD/" .env
fi

echo -e "${GREEN}✅ Arquivo .env atualizado${NC}"

# Criar banco de dados
echo -e "${YELLOW}🗄️  Criando banco de dados...${NC}"

if [ -n "$DB_PASSWORD" ]; then
    mysql -h $DB_HOST -u $DB_USER -p$DB_PASSWORD -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    mysql -h $DB_HOST -u $DB_USER -p$DB_PASSWORD $DB_NAME < database.sql
else
    mysql -h $DB_HOST -u $DB_USER -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    mysql -h $DB_HOST -u $DB_USER $DB_NAME < database.sql
fi

echo -e "${GREEN}✅ Banco de dados criado e populado${NC}"

# Verificar permissões
echo -e "${YELLOW}🔐 Configurando permissões...${NC}"
chmod -R 755 app/
chmod -R 755 config/
chmod -R 755 routes/
chmod -R 755 public/

echo -e "${GREEN}✅ Permissões configuradas${NC}"

# Resumo
echo ""
echo -e "${GREEN}═══════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}✅ Instalação concluída com sucesso!${NC}"
echo -e "${GREEN}═══════════════════════════════════════════════════════${NC}"
echo ""
echo "📝 Próximos passos:"
echo ""
echo "1. Inicie o servidor PHP:"
echo -e "   ${YELLOW}php -S localhost:8000 -t public/${NC}"
echo ""
echo "2. Teste a API:"
echo -e "   ${YELLOW}curl http://localhost:8000/health${NC}"
echo ""
echo "3. Consulte a documentação:"
echo -e "   ${YELLOW}cat API_DOCUMENTATION.md${NC}"
echo ""
echo -e "📚 Documentação completa em: ${YELLOW}SETUP_GUIDE.md${NC}"
echo ""
