# 🚀 Quick Start - Anality

Comece em 5 minutos!

## 1️⃣ Setup Inicial

```bash
cd /home/romulo/project/anality

# Instalar dependências
composer install

# Gerar chave de aplicação
php artisan key:generate

# Criar banco de dados
php artisan migrate:fresh

# (Opcional) Popular com dados de exemplo
php artisan seed:SampleDataSeeder
```

## 2️⃣ Iniciar Servidor

```bash
php artisan serve
```

Acesse: **http://localhost:8000**

## 3️⃣ Executar Análise

### Opção A: Via Web (Interface Gráfica)

1. Abra o dashboard em http://localhost:8000
2. Clique em "AtualAtualizar Ataques"
3. Clique em "Atualizar Notícias"
4. Clique em "Executar Análise Completa"

### Opção B: Via Terminal (Recomendado)

```bash
# Ejecutar tudo de uma vez
php artisan analysis:run --all

# Ou separado:
php artisan analysis:run --attacks
php artisan analysis:run --news
php artisan analysis:run --correlations
```

## 4️⃣ Explorar Resultados

- **Dashboard**: http://localhost:8000/
- **Ataques**: http://localhost:8000/attacks
- **Correlações**: http://localhost:8000/correlations
- **Estatísticas**: http://localhost:8000/statistics
- **Timeline**: http://localhost:8000/timeline

## 📊 Visualizar Status

```bash
# Verificar quantidade de dados
curl http://localhost:8000/api/status
```

## 🎯 Próximos Passos

1. Customizar fontes de scraping em `app/Services/`
2. Ajustar threshold de correlação em `CorrelationAnalysisService.php`
3. Configurar banco de dados em `.env`
4. Agendar execução automática com Cron

## 🔧 Configurações (.env)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=anality
DB_USERNAME=root
DB_PASSWORD=

APP_URL=http://localhost:8000
```

## 📞 Suporte

Dúvidas? Veja a documentação completa em `README_ANALITY.md`

---

**Pronto para analisar dados! 🎉**
