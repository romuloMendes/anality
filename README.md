# 🔍 Anality - Análise de Ataques Hackers e Notícias

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-12.55.1-red.svg)
![PHP](https://img.shields.io/badge/PHP-8.2.30-blue.svg)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3.0-purple.svg)
![Chart.js](https://img.shields.io/badge/Chart.js-4.4.0-orange.svg)

**Plataforma inteligente para cruzar dados de ataques hackers com notícias e identificar correlações**

[🚀 Demonstração](#-demonstração) • [📋 Funcionalidades](#-funcionalidades) • [⚡ Instalação](#-instalação) • [📊 Dashboard](#-dashboard) • [🔧 API](#-api)

</div>

---

## 📋 Sobre o Projeto

**Anality** é uma aplicação web desenvolvida em Laravel que automatiza a coleta e análise de dados relacionados à segurança cibernética. O sistema cruza informações de ataques hackers com notícias relevantes, identificando correlações e padrões através de algoritmos inteligentes.

### 🎯 Objetivo Principal
- **Cruzar dados** de ataques hackers com notícias
- **Identificar correlações** entre eventos de segurança
- **Gerar insights** através de dashboards interativos
- **Automatizar análise** com web scraping

---

## 🚀 Demonstração

<div align="center">

### Dashboard Principal
![Dashboard Preview](https://via.placeholder.com/800x400/1a1a1a/ffffff?text=Dashboard+Anality)

### Análise de Correlações
![Correlações Preview](https://via.placeholder.com/800x400/2d3748/ffffff?text=An%C3%A1lise+de+Correla%C3%A7%C3%B5es)

</div>

---

## ✨ Funcionalidades

### 🔍 Web Scraping Inteligente
- ✅ **CISA Alerts** - Alertas oficiais de segurança
- ✅ **SecurityAffairs** - Notícias especializadas
- ✅ **TechCrunch** - Cobertura tecnológica
- ✅ **ZDNet** - Análises de segurança

### 📊 Análise de Correlações
- ✅ **Algoritmo Multi-critério** (4 fatores ponderados)
- ✅ **Score de Correlação** (0-100%)
- ✅ **Tipos de Correlação**: Direct, Temporal, Entity-based
- ✅ **Análise Temporal** até 7 dias

### 🎨 Dashboards Interativos
- ✅ **Estatísticas em Tempo Real**
- ✅ **Gráficos com Chart.js**
- ✅ **Timeline Visual**
- ✅ **Filtros Avançados**

### 🔧 API REST Completa
- ✅ **Endpoints JSON**
- ✅ **Status do Sistema**
- ✅ **Scraping Automatizado**
- ✅ **Análise Programática**

### 📱 Interface Moderna
- ✅ **Bootstrap 5** - Design responsivo
- ✅ **AJAX** - Atualizações dinâmicas
- ✅ **Mobile-friendly**
- ✅ **Dark Theme Ready**

---

## ⚡ Instalação

### Pré-requisitos
- **PHP 8.2+**
- **Composer**
- **Laravel 12.x**
- **MySQL/SQLite**

### 🚀 Setup Rápido (3 passos)

```bash
# 1. Clonar repositório
git clone https://github.com/SEU_USUARIO/anality.git
cd anality

# 2. Instalar dependências
composer install

# 3. Configurar aplicação
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed --class=SampleDataSeeder

# 4. Iniciar servidor
php artisan serve
```

Acesse: **http://localhost:8000**

### 📋 Setup Detalhado

Para instruções completas, consulte:
- **[QUICKSTART.md](QUICKSTART.md)** - Guia de início rápido
- **[README_ANALITY.md](README_ANALITY.md)** - Documentação técnica completa

---

## 📊 Dashboard

### 🏠 Página Principal
- **Estatísticas Gerais**: Ataques críticos, total de correlações
- **Dados Recentes**: Últimos ataques e notícias
- **Correlações Fortes**: Score > 70%
- **Ações Rápidas**: Botões para scraping e análise

### 📈 Estatísticas Detalhadas
- **Gráficos de Pizza**: Ataques por tipo
- **Gráficos de Barras**: Distribuição por severidade
- **Métricas**: Score médio de correlação

### 🔍 Filtros Avançados
- **Por Severidade**: Critical, High, Medium, Low
- **Por Tipo**: Ransomware, DDoS, Phishing, etc.
- **Por Fonte**: CISA, SecurityAffairs, etc.
- **Busca por Texto**: Título, descrição, entidade

### 📅 Timeline Visual
- **Cronograma Cronológico**
- **Eventos Ordenados** (mais recentes primeiro)
- **Indicadores Visuais** por severidade

---

## 🔧 API REST

### Endpoints Principais

```bash
# Status do sistema
GET  /api/status

# Scraping de ataques
POST /api/scrape/attacks

# Scraping de notícias
POST /api/scrape/news

# Análise de correlações
POST /api/analyze/correlations

# Análise completa
POST /api/analyze/full
```

### Exemplo de Uso

```bash
# Verificar status
curl http://localhost:8000/api/status

# Executar análise completa
curl -X POST http://localhost:8000/api/analyze/full
```

---

## 🛠️ CLI Commands

### Análise Automatizada

```bash
# Executar tudo
php artisan analysis:run --all

# Apenas ataques
php artisan analysis:run --attacks

# Apenas notícias
php artisan analysis:run --news

# Apenas correlações
php artisan analysis:run --correlations
```

### Agendamento (Cron)

```bash
# Adicionar ao crontab
* * * * * cd /path/to/anality && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📊 Algoritmo de Correlação

O sistema utiliza um **algoritmo multi-critério** que pondera 4 fatores:

| Fator | Peso | Descrição |
|-------|------|-----------|
| **Palavras-chave** | 30% | Similaridade de termos entre ataque e notícia |
| **Temporal** | 20% | Proximidade até 7 dias |
| **Entidades** | 25% | Menção da mesma empresa/entidade |
| **Tipo** | 25% | Classificação de ataque |

**Score Final**: 0-100%
- **>70%**: Correlação forte
- **40-70%**: Correlação média
- **<40%**: Sem correlação

---

## 🗄️ Estrutura do Banco

### Tabelas Principais

```sql
-- Ataques hackers
hacker_attacks (
  id, title, description, attack_type, severity,
  affected_entity, attack_date, source_name, tags, metadata
)

-- Notícias
news (
  id, title, content, source_name, published_date,
  category, keywords, summary, relevance_score, metadata
)

-- Correlações
correlation_analyses (
  id, hacker_attack_id, news_id, correlation_score,
  analysis_reason, correlation_type, analysis_date, is_validated
)
```

---

## 🎨 Tecnologias Utilizadas

### Backend
- **Laravel 12** - Framework PHP
- **Symfony DomCrawler** - Web scraping
- **Eloquent ORM** - Database layer

### Frontend
- **Bootstrap 5** - CSS Framework
- **Chart.js** - Data visualization
- **Bootstrap Icons** - Icon library
- **Blade Templates** - Template engine

### DevOps
- **Composer** - PHP dependency manager
- **Git** - Version control
- **Laravel Artisan** - CLI tools

---

## 📈 Roadmap

### Próximas Funcionalidades
- [ ] **Integração VirusTotal** - API de análise de malware
- [ ] **Machine Learning** - Classificação automática
- [ ] **Export PDF/Excel** - Relatórios avançados
- [ ] **Webhooks** - Alertas em tempo real
- [ ] **Multi-tenancy** - Suporte a múltiplos usuários
- [ ] **GraphQL API** - Consultas avançadas
- [ ] **WebSocket** - Updates em tempo real

---

## 🤝 Contribuindo

1. **Fork** o projeto
2. Crie uma **feature branch** (`git checkout -b feature/AmazingFeature`)
3. **Commit** suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. **Push** para a branch (`git push origin feature/AmazingFeature`)
5. Abra um **Pull Request**

### Como Testar
```bash
# Executar testes
php artisan test

# Verificar linting
./vendor/bin/phpcs

# Verificar qualidade
./vendor/bin/phpmd app text codesize,unusedcode,naming
```

---

## 📝 Licença

Este projeto está sob a licença **MIT**. Veja o arquivo [LICENSE](LICENSE) para detalhes.

---

## 👥 Autores

- **Romulo** - *Desenvolvimento inicial* - [GitHub](https://github.com/romulo)

---

## 🙏 Agradecimentos

- **Laravel Framework** - Base sólida para desenvolvimento
- **Bootstrap** - Interface moderna e responsiva
- **Chart.js** - Visualizações incríveis
- **Comunidade Open Source** - Inspiração e ferramentas

---

## 📞 Suporte

Para dúvidas ou sugestões:

- 📧 **Email**: dev@anality.local
- 🐛 **Issues**: [GitHub Issues](https://github.com/SEU_USUARIO/anality/issues)
- 📖 **Documentação**: [README_ANALITY.md](README_ANALITY.md)

---

<div align="center">

**Desenvolvido com ❤️ usando Laravel 12 + Bootstrap 5 + Chart.js**

⭐ **Star este repositório se gostou do projeto!**

[⬆ Voltar ao topo](#-anality---análise-de-ataques-hackers-e-notícias)

</div>

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
