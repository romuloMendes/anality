#!/bin/bash

# ANALITY - Setup Script
# Script para configuração rápida da aplicação

set -e

echo "🚀 Iniciando setup do Anality..."
echo ""

# Cores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Diretório do projeto
PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$PROJECT_DIR"

# 1. Verificar dependências
echo -e "${YELLOW}[1/5]${NC} Verificando dependências..."
if ! command -v php &> /dev/null; then
    echo -e "${RED}❌ PHP não encontrado${NC}"
    exit 1
fi

if ! command -v composer &> /dev/null; then
    echo -e "${RED}❌ Composer não encontrado${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Dependências verificadas${NC}"
echo ""

# 2. Instalar pacotes PHP
echo -e "${YELLOW}[2/5]${NC} Instalando pacotes PHP..."
composer install --no-interaction
echo -e "${GREEN}✓ Pacotes instalados${NC}"
echo ""

# 3. Configurar .env
echo -e "${YELLOW}[3/5]${NC} Configurando arquivo .env..."
if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate
    echo -e "${GREEN}✓ Arquivo .env criado${NC}"
else
    echo -e "${YELLOW}⚠ Arquivo .env já existe${NC}"
fi
echo ""

# 4. Setup do banco de dados
echo -e "${YELLOW}[4/5]${NC} Configurando banco de dados..."
php artisan migrate:fresh --seed --class=SampleDataSeeder
echo -e "${GREEN}✓ Banco de dados pronto com dados de exemplo${NC}"
echo ""

# 5. Permissões
echo -e "${YELLOW}[5/5]${NC} Configurando permissões..."
chmod -R 755 storage bootstrap/cache
chmod -R 777 storage logs 2>/dev/null || true
echo -e "${GREEN}✓ Permissões configuradas${NC}"
echo ""

echo -e "${GREEN}✅ Setup concluído com sucesso!${NC}"
echo ""
echo -e "${YELLOW}Para iniciar o servidor:${NC}"
echo "  php artisan serve"
echo ""
echo -e "${YELLOW}Acesse:${NC}"
echo "  http://localhost:8000"
echo ""
echo -e "${YELLOW}Comandos úteis:${NC}"
echo "  php artisan analysis:run --all     (Executar análise completa)"
echo "  php artisan tinker                  (Console interativo)"
echo ""
