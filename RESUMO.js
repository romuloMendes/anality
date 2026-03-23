// ANALITY - Resumo do Projeto

/**
 * 📊 ANALITY - Análise de Ataques Hackers e Notícias
 * Plataforma Laravel para correlacionar dados de ataques com notícias
 *
 * 🎯 Objetivo: Cruzar dados de ataques hackers com notícias para identificar
 *              padrões, correlações e gerar relatórios analíticos
 */

// ============================================================================
// 📁 ESTRUTURA DO PROJETO
// ============================================================================

/app
    / Console / Commands
    └─ AnalyzeHackerAttacks.php(Comando CLI para análise)

    / Http / Controllers
    ├─ DashboardController.php(Dashboard e visualizações)
    └─ AnalysisController.php(APIs de scraping e análise)

    / Models
    ├─ HackerAttack.php(Modelo de ataques)
    ├─ News.php(Modelo de notícias)
    └─ CorrelationAnalysis.php(Modelo de correlações)

    / Services
    ├─ HackerAttackScraperService.php(Web scraping de ataques)
    ├─ NewsScraperService.php(Web scraping de notícias)
    └─ CorrelationAnalysisService.php(Análise de correlações)

    / resources / views
    / layouts
    └─ app.blade.php(Layout base com Bootstrap)
  ├─ dashboard.blade.php(Dashboard principal)
  ├─ statistics.blade.php(Gráficos e estatísticas)
  ├─ correlations.blade.php(Lista de correlações)
  ├─ attacks.blade.php(Ataques com filtros)
  ├─ attack - detail.blade.php(Detalhes de ataque)
  └─ timeline.blade.php(Timeline visual)

    / database / migrations
  ├─ * _create_hacker_attacks_table.php
  ├─ * _create_news_table.php
  └─ * _create_correlation_analyses_table.php

    / database / seeders
  └─ SampleDataSeeder.php(Dados de exemplo)

// ============================================================================
// 🔧 FUNCIONALIDADES IMPLEMENTADAS
// ============================================================================

✅ WEB SCRAPING
    - CISA(Cybersecurity & Infrastructure Security Agency)
    - SecurityAffairs
    - TechCrunch
    - ZDNet

✅ ANÁLISE DE CORRELAÇÕES
    - Similaridade de palavras - chave(30 %)
        - Proximidade temporal(20 %)
            - Menção de entidades(25 %)
                - Tipo de ataque(25 %)
                    - Score correlação: 0 - 100 %

✅ DASHBOARDS INTERATIVOS
    - Estatísticas em tempo real
        - Gráficos com Chart.js
            - Atualizações via AJAX
                - Filtros avançados

✅ API REST
    - POST / api / scrape / attacks
    - POST / api / scrape / news
    - POST / api / analyze / correlations
    - POST / api / analyze / full
    - GET / api / status

✅ COMMAND CLI
    - php artisan analysis: run--attacks
        - php artisan analysis: run--news
            - php artisan analysis: run--correlations
                - php artisan analysis: run--all

// ============================================================================
// 🚀 COMO USAR
// ============================================================================

// 1. INSTALAÇÃO
cd / home / romulo / project / anality
composer install
php artisan key: generate
php artisan migrate: fresh--seed--class=SampleDataSeeder

// 2. INICIAR SERVIDOR
php artisan serve
// Acesse: http://localhost:8000

// 3. EXECUTAR ANÁLISE (CLI - Recomendado)
php artisan analysis: run--all

// 4. EXECUTAR ANÁLISE (WEB)
// Interface: Dashboard → [Atualizar Ataques] → [Atualizar Notícias] → [Análise]

// ============================================================================
// 📊 BANCO DE DADOS
// ============================================================================

TABELA: hacker_attacks
├─ id(int)
├─ title(string)
├─ description(text)
├─ attack_type(string) → DDoS, Ransomware, Phishing, etc
├─ severity(string) → critical, high, medium, low
├─ affected_entity(string)
├─ attack_date(date)
├─ source_name(string) → CISA, SecurityAffairs, etc
├─ source_url(string)
├─ tags(json) →["ransomware", "hospital"]
├─ metadata(json)
└─ timestamps

TABELA: news
├─ id(int)
├─ title(string)
├─ content(text)
├─ source_name(string) → TechCrunch, ZDNet, etc
├─ source_url(string)
├─ published_date(date)
├─ category(string)
├─ keywords(json)
├─ summary(text)
├─ relevance_score(int) 1 - 10
├─ metadata(json)
└─ timestamps

TABELA: correlation_analyses
├─ id(int)
├─ hacker_attack_id(int) FK
├─ news_id(int) FK
├─ correlation_score(float) 0 - 100
├─ analysis_reason(text)
├─ correlation_type(string) → direct, temporal, entity_based
├─ analysis_date(date)
├─ is_validated(boolean)
├─ pattern_data(json)
└─ timestamps

// ============================================================================
// 🔗 ROTAS
// ============================================================================

GET / dashboard         // Dashboard principal
GET / statistics          statistics        // Gráficos e stats
GET / correlations        correlations      // Lista correlações
GET / attacks             attacks           // Ataques com filtros
GET / attacks / { id }        attack - detail     // Detalhes ataque
GET / timeline            timeline          // Timeline visual

POST / api / scrape / attacks         scrapeAttacks        // Scraping de ataques
POST / api / scrape / news            scrapeNews           // Scraping de notícias
POST / api / analyze / correlations   runCorrelationAnalysis
POST / api / analyze / full           runFullAnalysis      // Tudo junto
GET / api / status                 status               // Status dos dados

// ============================================================================
// 📦 DEPENDÊNCIAS ADICIONADAS
// ============================================================================

composer require symfony / dom - crawler    // Web scraping
composer require symfony / http - client    // Requisições HTTP

    // ============================================================================
    // 🎨 INTERFACE
    // ============================================================================

    - Bootstrap 5(CSS Framework)
        - Bootstrap Icons(Ícones)
            - Chart.js(Gráficos)
            - Laravel Blade(Templates)

// ============================================================================
// 📈 DADOS DE EXEMPLO INCLUSOS
// ============================================================================

✓ 5 Ataques hackers de exemplo
✓ 5 Notícias de exemplo
✓ 5 Correlações validadas

Tipos de ataques: Ransomware, DDoS, Phishing, Exploit, Data Breach
Severidades: critical, high, medium, low

// ============================================================================
// 🔧 CUSTOMIZAÇÃO
// ============================================================================

ADICIONAR NOVA FONTE DE SCRAPING:
1. Editar: app / Services / HackerAttackScraperService.php
2. Criar novo método: private function scrapeNovaFonte()
3. Adicionar ao array em scrapeFromMultipleSources()

AJUSTAR THRESHOLD DE CORRELAÇÃO:
1. Editar: app / Services / CorrelationAnalysisService.php
2. Linha ~40: if ($score > 40) { ... }
3. Mudar valor de threshold

ADICIONAR NOVO CAMPO À ANÁLISE:
1. Editar: CorrelationAnalysisService.php
2. Novo método: private function compareFator()
3. Adicionar peso em calculateCorrelationScore()

// ============================================================================
// 📝 ARQUIVOS CRIADOS
// ============================================================================

DOCUMENTAÇÃO:
├─ README_ANALITY.md                    // Documentação completa
├─ QUICKSTART.md                         // Guia rápido
├─ setup.sh                              // Script de setup
└─ RESUMO.js                             // Este arquivo

CÓDIGO - FONTE:
├─ app / Services /                         // 3 serviços principais
├─ app / Http / Controllers /                 // 2 controllers
├─ app / Models /                           // 3 modelos
├─ app / Console / Commands /                 // 1 comando CLI
├─ database / migrations /                  // 3 migrations
├─ database / seeders /                     // 1 seeder
└─ resources / views /                      // 7 views

    // ============================================================================
    // 🚦 STATUS
    // ============================================================================

    [✅] Estrutura do projeto criada
    [✅] Modelos e migrations configurados
    [✅] Web scraping implementado
    [✅] Análise de correlações funcional
    [✅] Dashboard com gráficos
    [✅] API REST pronta
    [✅] CLI commands criados
    [✅] Dados de exemplo carregados

    // ============================================================================
    // 🎯 PRÓXIMAS FASES (Futuro)
    // ============================================================================

    [] Integração com APIs de segurança(VirusTotal, AlienVault OTX)
    [] Machine Learning para classificação automática
    [] Exportação de relatórios(PDF, Excel, CSV)
    [] Alertas em tempo real via Webhooks
    [] Autenticação e permissões de usuário
    [] Multi - tenancy
    [] GraphQL API
    [] Dashboard em tempo real com WebSockets
    [] Análise de rede de ataques(Graph)
    [] Trending topics e padrões temporais
    [] API de terceiros(Slack, Discord)

// ============================================================================
// 📞 SUPORTE RÁPIDO
// ============================================================================

❓ Como rodar a aplicação ?
→ php artisan serve

❓ Como executar análise completa ?
→ php artisan analysis: run--all

❓ Onde estão os logs ?
→ storage / logs / laravel.log

❓ Como limpar cache ?
→ php artisan cache: clear

❓ Como resetar banco ?
→ php artisan migrate: fresh--seed--class=SampleDataSeeder

❓ Como acessar console ?
→ php artisan tinker

// ============================================================================
// ✨ TECNOLOGIAS UTILIZADAS
// ============================================================================

Backend:
- Laravel 12
    - PHP 8.2 +
        - Symfony DomCrawler & HttpClient
            - MySQL / SQLite

Frontend:
- Bootstrap 5
    - Chart.js
    - Bootstrap Icons
        - Vue.js(opcional para futuros melhoramentos)

DevOps:
- Composer
    - Laravel Artisan
        - Git

// ============================================================================
