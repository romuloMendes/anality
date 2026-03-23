# Anality - Análise de Ataques Hackers e Notícias

Uma aplicação Laravel moderna para **cruzar dados de ataques hackers com notícias**, identificar correlações e gerar relatórios e dashboards analíticos.

## 🎯 Funcionalidades

✅ **Web Scraping Automático**: Coleta dados de ataques hackers e notícias de múltiplas fontes  
✅ **Análise de Correlações**: Cruza dados identificando relações entre ataques e notícias  
✅ **Dashboards Interativos**: Visualiza estatísticas e padrões em tempo real  
✅ **Timeline Visual**: Acompanha a evolução dos ataques ao longo do tempo  
✅ **Relatórios Detalhados**: Filtra e analisa ataques por tipo, severidade e fonte  
✅ **API REST**: Endpoints para integração com sistemas externos

## 📋 Pré-requisitos

- PHP 8.2+
- Laravel 12.x
- MySQL/MariaDB ou SQLite
- Composer
- Node.js (opcional, para compilação de assets)

## 🚀 Instalação

### 1. Clonar o repositório

```bash
git clone https://github.com/seu-usuario/anality.git
cd anality
```

### 2. Instalar dependências

```bash
composer install
```

### 3. Configurar ambiente

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Criar banco de dados

```bash
php artisan migrate:fresh
```

### 5. Iniciar servidor

```bash
php artisan serve
```

Acesse: `http://localhost:8000`

## 📚 Uso

### Dashboard Principal

Acesse a página inicial para ver um resumo estatístico de:

- Total de ataques críticos
- Total de ataques registrados
- Total de notícias
- Total de correlações identificadas

### Executar Análise Completa

#### Via Interface Web

1. Clique em "Atualizar Ataques"
2. Clique em "Atualizar Notícias"
3. Clique em "Executar Análise Completa"

#### Via CLI (Recomendado)

```bash
# Scraping de ataques
php artisan analysis:run --attacks

# Scraping de notícias
php artisan analysis:run --news

# Análise de correlações
php artisan analysis:run --correlations

# Tudo junto
php artisan analysis:run --all
```

### Filtrar Ataques

1. Acesse "Ataques"
2. Use os filtros por:
    - Severidade (Critical, High, Medium, Low)
    - Tipo de Ataque (Ransomware, DDoS, Phishing, etc.)
    - Fonte
    - Busca por texto livre

### Visualizar Correlações

1. Acesse "Correlações"
2. Veja quais ataques estão correlacionados com notícias
3. Verifique o score de correlação (0-100%)

### Estatísticas

Visualize gráficos de:

- Distribuição de ataques por tipo
- Distribuição por severidade
- Tipos de correlação encontradas
- Score médio de correlação

## 🔌 API Endpoints

### Scraping

```bash
POST /api/scrape/attacks      # Scraping de ataques
POST /api/scrape/news         # Scraping de notícias
POST /api/analyze/correlations # Análise de correlações
POST /api/analyze/full        # Executa tudo (scraping + análise)
GET  /api/status              # Status dos dados
```

### Ejemplos

```bash
curl -X POST http://localhost:8000/api/scrape/attacks
curl -X POST http://localhost:8000/api/analyze/full
curl http://localhost:8000/api/status
```

## 📊 Estrutura de Dados

### Tabelas Principais

#### hacker_attacks

- `id` - ID único
- `title` - Título do ataque
- `description` - Descrição detalhada
- `attack_type` - Tipo (DDoS, Ransomware, etc.)
- `severity` - Nível (critical, high, medium, low)
- `affected_entity` - Empresa/entidade afetada
- `attack_date` - Data do ataque
- `source_name` - Fonte (CISA, SecurityAffairs, etc.)
- `tags` - Array de tags

#### news

- `id` - ID único
- `title` - Título da notícia
- `content` - Conteúdo completo
- `source_name` - Porta de notícia
- `published_date` - Data de publicação
- `category` - Categoria da notícia
- `keywords` - Array de palavras-chave
- `relevance_score` - Score de relevância (1-10)

#### correlation_analyses

- `id` - ID único
- `hacker_attack_id` - FK para ataques
- `news_id` - FK para notícias
- `correlation_score` - Score de correlação (0-100)
- `analysis_reason` - Motivo da correlação
- `correlation_type` - Tipo (direct, temporal, entity_based)

## 🔍 Algoritmo de Correlação

A análise de correlação considera:

1. **Palavras-chave (30%)** - Similaridade de termos entre ataque e notícia
2. **Temporal (20%)** - Proximidade de datas (até 7 dias)
3. **Entidades (25%)** - Empresas/entidades mencionadas
4. **Tipo de Ataque (25%)** - Classificação de ataque

**Score de Correlação**: 0-100%

- **>70%**: Correlação forte (muito provável relacionamento)
- **40-70%**: Correlação média
- **<40%**: Sem correlação identificada

## 📂 Estrutura do Projeto

```
anality/
├── app/
│   ├── Console/Commands/
│   │   └── AnalyzeHackerAttacks.php      # Comando Artisan
│   ├── Http/Controllers/
│   │   ├── DashboardController.php       # Dashboard e views
│   │   └── AnalysisController.php        # Scraping e análise
│   ├── Models/
│   │   ├── HackerAttack.php              # Modelo de ataques
│   │   ├── News.php                      # Modelo de notícias
│   │   └── CorrelationAnalysis.php       # Modelo de correlações
│   └── Services/
│       ├── HackerAttackScraperService.php    # Scraper de ataques
│       ├── NewsScraperService.php            # Scraper de notícias
│       └── CorrelationAnalysisService.php    # Análise de correlações
├── database/
│   └── migrations/
│       ├── *_create_hacker_attacks_table.php
│       ├── *_create_news_table.php
│       └── *_create_correlation_analyses_table.php
├── resources/
│   └── views/
│       ├── layouts/app.blade.php             # Layout principal
│       ├── dashboard.blade.php               # Dashboard
│       ├── statistics.blade.php              # Estatísticas
│       ├── correlations.blade.php            # Correlações
│       ├── attacks.blade.php                 # Lista de ataques
│       ├── attack-detail.blade.php           # Detalhes de ataque
│       └── timeline.blade.php                # Timeline
└── routes/
    └── web.php                              # Rotas web
```

## 🛡️ Segurança

- Inputs validados e sanitizados
- Proteção contra CSRF
- Rate limiting nas APIs
- Consultas parametrizadas contra SQL injection

## 🐛 Troubleshooting

### Erro: "Class 'App\Services\HackerAttackScraperService' not found"

Verifique se o autoload do Composer está configurado:

```bash
composer dump-autoload
```

### Erro: "SQLSTATE[HY000]: General error"

Execute as migrações:

```bash
php artisan migrate:fresh
```

### Scraping não retorna dados

- Verifique conexão de internet
- As fontes podem ter mudado de estrutura HTML
- Verifique logs em `storage/logs/laravel.log`

## 🔄 Agendamento Automático (Opcional)

Para executar análises automaticamente, configure o scheduler do Laravel:

```bash
# Adicione ao crontab
* * * * * cd /path/to/anality && php artisan schedule:run >> /dev/null 2>&1
```

Edite `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('analysis:run --all')
             ->daily()
             ->at('02:00'); // 2 AM
}
```

## 📈 Próximas Melhorias

- [ ] Integração com APIs de segurança (VirusTotal, AlienVault)
- [ ] Machine Learning para melhor classificação
- [ ] Exportação de relatórios (PDF, Excel)
- [ ] Alertas em tempo real via Webhooks
- [ ] Autenticação e multi-tenancy
- [ ] GraphQL API
- [ ] Análise de redes de ataque

## 📝 Licença

MIT License - veja LICENSE.md

## 🤝 Contribuindo

1. Fork o projeto
2. Crie uma feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📧 Suporte

Para dúvidas ou sugestões, abra uma issue no repositório.

---

**Desenvolvido com ❤️ usando Laravel e Laravel Boost**
