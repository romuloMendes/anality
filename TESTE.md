# 🧪 Guia de Teste - Anality

Teste a aplicação em 2 minutos!

## ✅ Verificação Inicial

```bash
cd /home/romulo/project/anality

# Verificar banco de dados
php artisan tinker
>>> HackerAttack::count()
5
>>> News::count()
5
>>> CorrelationAnalysis::count()
5
>>> exit

# Sucesso! Os dados estão carregados
```

## 🚀 Iniciar Servidor

```bash
php artisan serve
```

Acesse: http://localhost:8000

## 📊 Teste das Funcionalidades

### 1. Dashboard Principal

- URL: http://localhost:8000
- ✅ Estatísticas aparecem (5 ataques, 5 notícias, 5 correlações)
- ✅ Tabela "Ataques Recentes" mostra 5 ataques
- ✅ Tabela "Notícias Recentes" mostra 5 notícias
- ✅ "Correlações Mais Fortes" mostra 5 correlações

### 2. Página de Ataques

- URL: http://localhost:8000/attacks
- ✅ Lista todos os 5 ataques
- ✅ Filtros funcionam (severidade, tipo, fonte)
- ✅ Paginação funciona (se houver muitos)

### 3. Detalhes de Ataque

- URL: http://localhost:8000/attacks/1
- ✅ Título, tipo, severidade aparecem
- ✅ Data e entidade afetada mostradas
- ✅ Notícias correlacionadas aparecem no lado direito

### 4. Correlações

- URL: http://localhost:8000/correlations
- ✅ Lista as 5 correlações com score em % (95%, 92%, etc)
- ✅ Mostra tipo de correlação: direct, entity_based, etc
- ✅ Barra de progresso com o score

### 5. Estatísticas

- URL: http://localhost:8000/statistics
- ✅ Gráfico de pizza: Tipos de ataque
- ✅ Gráfico de barras: Severidade
- ✅ Gráfico de os: Tipos de correlação
- ✅ Score médio de correlação: ~92.6%

### 6. Timeline

- URL: http://localhost:8000/timeline
- ✅ Mostra 5 ataques em ordem cronológica reversa
- ✅ Cada item com data, tipo e severidade

## 🔌 APIs REST

### Testar com cURL

```bash
# Status dos dados
curl http://localhost:8000/api/status
# Response: {"attacks":5,"news":5,"correlations":5,...}

# Scraping de ataques (POST)
curl -X POST http://localhost:8000/api/scrape/attacks
# Response: {"success":true,"message":"...","count":0}

# Scraping de notícias (POST)
curl -X POST http://localhost:8000/api/scrape/news
# Response: {"success":true,"message":"...","count":0}

# Análise de correlações (POST)
curl -X POST http://localhost:8000/api/analyze/correlations
# Response: {"success":true,"data":{...}}

# Análise completa (POST)
curl -X POST http://localhost:8000/api/analyze/full
# Response: {"success":true,"data":{"attacks_processed":...}}
```

## 📱 Testes via Interface

### Teste 1: Atualizar Ataques

1. Dashboard → Clique "Atualizar Ataques"
2. ✅ Spinner aparece
3. ✅ Alert com mensagem aparece
4. ✅ Página recarrega (dados aumentam)

### Teste 2: Buscar Ataque

1. Ataques → Campo "Buscar..." → Digite "ransomware"
2. ✅ Filtra ataques com "ransomware"
3. ✅ Reset button funciona

### Teste 3: Filtrar por Severidade

1. Ataques → Dropdown Severidade → Selecione "critical"
2. ✅ Mostra apenas ataques críticos
3. ✅ Clique "Filtrar"

## 🗄️ Testes do Banco de Dados

```bash
php artisan tinker

# Verificar ataques
>>> $attacks = HackerAttack::all();
>>> $attacks->count()
5
>>> $attacks->first()->title
"Critical Ransomware Attack Hits Major Healthcare Provider"
>>> $attacks->first()->severity
"critical"

# Verificar notícias
>>> $news = News::all();
>>> $news->count()
5
>>> $news->first()->keywords
["ransomware", "healthcare", "cybersecurity"]

# Verificar correlações
>>> $corr = CorrelationAnalysis::first();
>>> $corr->correlation_score
95
>>> $corr->hackerAttack->title
>>> $corr->news->title

# Teste de relacionamentos
>>> $attack = HackerAttack::find(1);
>>> $attack->correlationAnalyses->count()
1
>>> $attack->correlationAnalyses->first()->news->title

exit
```

## 📊 Teste de Análise CLI

```bash
# Executar análise com dados de exemplo (se houver dados reais)
php artisan analysis:run --correlations

# Verifica se novas correlações foram criadas
php artisan tinker
>>> CorrelationAnalysis::count()
# Deve mostrar um número >= 5
```

## 🔍 Checklist de Testes

- [ ] Dashboard carrega sem erros
- [ ] Card de estatísticas mostra números corretos
- [ ] Tabela de ataques recentes exibe 5 ataques
- [ ] Tabela de notícias recentes exibe 5 notícias
- [ ] Correlações mostram scores 90%+
- [ ] Página /attacks filtra corretamente
- [ ] Detalhes de ataque carregam notícias correlacionadas
- [ ] Gráficos em statistics renderizam
- [ ] Timeline mostra eventos cronologicamente
- [ ] API endpoints retornam JSON válido
- [ ] Botões de atualização funcionam via AJAX
- [ ] Paginação funciona (se houver muitos registros)
- [ ] Banco de dados tem 15 registros (5+5+5)

## 🐛 Troubleshooting

### Erro: "Class not found"

```bash
composer dump-autoload
php artisan serve
```

### Erro: "SQLSTATE[HY000]"

```bash
php artisan migrate:fresh --seed --class=SampleDataSeeder
```

### Dashboard em branco

```bash
# Verificar storage
chmod -R 777 storage logs

# Verificar erros
tail -f storage/logs/laravel.log
```

### Gráficos não aparecem

- Abrir DevTools (F12)
- Verificar Console por erros
- Verificar se Chart.js carregou corretamente

---

**Todos os testes passando? 🎉 Parabéns! Anality está funcionando perfeitamente!**
