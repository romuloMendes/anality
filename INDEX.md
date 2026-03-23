# 🎯 Anality - Índice de Documentação

## 📚 Documentação Disponível

### 🚀 Comece Aqui

1. **[QUICKSTART.md](QUICKSTART.md)** - Setup em 5 minutos ⭐ COMECE AQUI
2. **[README_ANALITY.md](README_ANALITY.md)** - Documentação Completa (detalhada)
3. **[TESTE.md](TESTE.md)** - Guia de Testes e Verificação

### 📖 Referência

- **RESUMO.js** - Arquitetura e estrutura técnica
- **setup.sh** - Script automático de instalação

---

## 🎯 Resumo Executivo

**Anality** é uma plataforma Laravel que:

```
┌─────────────────────────────────────────┐
│  DADOS DE ATAQUES HACKERS               │
│  + NOTÍCIAS RELACIONADAS                │
└──────────────────┬──────────────────────┘
                   │
                   ▼
        ┌──────────────────────┐
        │  ANÁLISE INTEGRADA   │
        │  • Correlações       │
        │  • Padrões           │
        │  • Estatísticas      │
        └──────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────┐
│  DASHBOARDS INTERATIVOS                 │
│  • Gráficos                             │
│  • Filtros Avançados                    │
│  • Timeline Visual                      │
└─────────────────────────────────────────┘
```

---

## 🚀 Iniciar em 3 Passos

```bash
# 1. Instalar
cd /home/romulo/project/anality
composer install && php artisan key:generate

# 2. Setup banco
php artisan migrate:fresh --seed --class=SampleDataSeeder

# 3. Servidor
php artisan serve
# → http://localhost:8000
```

---

## 📊 O Que Foi Criado

### 🔧 Backend

- ✅ **3 Models**: HackerAttack, News, CorrelationAnalysis
- ✅ **3 Serviços**: HackerAttackScraper, NewsScraper, CorrelationAnalysis
- ✅ **2 Controllers**: Dashboard, Analysis API
- ✅ **1 Command CLI**: analysis:run com opções
- ✅ **3 Migrations**: Tabelas com schemas otimizados

### 🎨 Frontend

- ✅ **7 Views**: Dashboard, Statistics, Correlations, Attacks, etc
- ✅ **Bootstrap 5**: Interface profissional
- ✅ **Chart.js**: Gráficos interativos
- ✅ **AJAX**: Atualizações em tempo real

### 🔌 API

- ✅ **4 Endpoints de Scraping**: /api/scrape/\*
- ✅ **2 Endpoints de Análise**: /api/analyze/\*
- ✅ **1 Endpoint de Status**: /api/status

### 📚 Dados

- ✅ **5 Ataques de Exemplo** (Ransomware, DDoS, Phishing, etc)
- ✅ **5 Notícias de Exemplo** (Correlacionadas)
- ✅ **5 Correlações Pré-análise** (95%, 92%, etc)

---

## 🔍 Estrutura do Projeto

```
anality/
├── app/
│   ├── Console/Commands/
│   │   └── AnalyzeHackerAttacks.php
│   ├── Http/Controllers/
│   │   ├── DashboardController.php
│   │   └── AnalysisController.php
│   ├── Models/
│   │   ├── HackerAttack.php
│   │   ├── News.php
│   │   └── CorrelationAnalysis.php
│   └── Services/
│       ├── HackerAttackScraperService.php
│       ├── NewsScraperService.php
│       └── CorrelationAnalysisService.php
│
├── database/
│   ├── migrations/          (3 tabelas)
│   └── seeders/
│       └── SampleDataSeeder.php
│
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php
│   ├── dashboard.blade.php
│   ├── statistics.blade.php
│   ├── correlations.blade.php
│   ├── attacks.blade.php
│   ├── attack-detail.blade.php
│   └── timeline.blade.php
│
├── routes/
│   └── web.php              (11 rotas)
│
├── QUICKSTART.md            ⭐ COMECE AQUI
├── README_ANALITY.md
├── TESTE.md
├── RESUMO.js
├── setup.sh
└── INDEX.md                 (este arquivo)
```

---

## 📊 Dados Inclusos

### Exemplos de Ataques

1. **Ransomware** - Healthcare (Critical)
2. **Exploit** - Windows CVE (High)
3. **DDoS** - E-commerce (High)
4. **Phishing** - Bank (High)
5. **Data Breach** - Tech Company (Critical)

### Exemplos de Notícias

- Healthcare Ransomware Attacks (9/10 relevância)
- Microsoft Emergency Patch (8/10)
- E-commerce DDoS (8/10)
- Banking Phishing (9/10)
- Data Breach (10/10)

### Correlações Pré-analisadas

- 5 correlações com scores: 95%, 92%, 88%, 91%, 97%
- Tipos: direct, entity_based, contextual

---

## 🎯 Funcionalidades Principais

### 📊 Dashboard

- Visão geral de ataques críticos
- Estatísticas de correlações
- Tabelas de dados recentes
- Botões de ação rápida

### 🔍 Filtros

- Por severidade (critical, high, medium, low)
- Por tipo de ataque (Ransomware, DDoS, etc)
- Por fonte (CISA, SecurityAffairs)
- Busca por texto livre

### 📈 Análise

- Algoritmo de correlação multi-critério
- Score 0-100% com explicação
- Padrões identificados
- Timeline visual

### 💾 API REST

- Endpoints JSON para integração
- Status do sistema
- Webhook-ready

### 📱 CLI

- Execução agendada
- Logging detalhado
- Opções de filtro

---

## 🔐 Segurança

- ✅ CSRF Protection
- ✅ Input Validation
- ✅ SQL Injection Prevention
- ✅ Rate Limiting Ready

---

## 🚀 Próximas Fases (Roadmap)

- [ ] Integração VirusTotal/AlienVault
- [ ] Machine Learning
- [ ] Export PDF/Excel
- [ ] Real-time Webhooks
- [ ] Multi-tenant
- [ ] GraphQL API
- [ ] WebSocket Dashboard

---

## ❓ FAQ

**P: Como rodar a aplicação?**
R: `php artisan serve` e acesse http://localhost:8000

**P: Como atualizar dados?**
R: Via dashboard ou `php artisan analysis:run --all`

**P: Posso customizar as fontes de scraping?**
R: Sim! Edite `app/Services/HackerAttackScraperService.php`

**P: Como mudar o threshold de correlação?**
R: Mude valor em `CorrelationAnalysisService.php` linha ~40

---

## 📞 Suporte

- 📖 Leia [README_ANALITY.md](README_ANALITY.md)
- 🧪 Teste com [TESTE.md](TESTE.md)
- 📝 Veja [RESUMO.js](RESUMO.js)

---

**Desenvolvido com ❤️ usando Laravel 12 + Bootstrap 5 + Chart.js**

**Status: ✅ Pronto para usar!**
