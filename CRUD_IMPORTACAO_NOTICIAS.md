# 📚 Documentação - CRUD de Importação de Notícias

## Visão Geral

O sistema Anality possui um **CRUD completo** para importar notícias de arquivos CSV. Isso permite que você alimente a base de dados com notícias de forma rápida e automatizada.

## 🚀 Como Usar

### 1. Via Interface Web

#### Passo 1: Acessar a página de importação

- Acesse a aplicação em: `http://localhost:8000`
- Clique no menu **Admin** na barra de navegação
- Selecione **Importar Notícias**

#### Passo 2: Preparar o arquivo CSV

Seu arquivo CSV deve ter a seguinte estrutura:

```
title;summary;date
A surpreendente recuperação do emprego no Brasil;Adicionalmente, os efeitos defasados...;31.dez.2022 às 23h15
Entenda o mundo em 2022 com livros;Crítico literário e autor irlandês...;31.dez.2022 às 23h15
```

**Colunas obrigatórias:**

- `title` - Título da notícia (obrigatório)
- `summary` - Resumo ou conteúdo da notícia
- `date` - Data de publicação no formato `DD.mês.YYYY às HHhMM`

**Formato de data aceito:**

```
31.dez.2022 às 23h15    ✓ Correto
31.12.2022 23:15        ✗ Não funciona
2022-12-31              ✗ Não funciona
```

#### Passo 3: Fazer upload

- Clique no campo "Selecione o arquivo CSV"
- Escolha seu arquivo (máximo 10MB)
- Clique em **Importar Notícias**
- Aguarde o processamento

#### Passo 4: Verificar resultado

O sistema exibirá:

- ✓ Quantidade de notícias importadas
- ✗ Quantidade de erros
- 📊 Detalhes de cada erro encontrado

---

### 2. Via Linha de Comando (Artisan)

Você pode importar notícias usando o comando Artisan:

```bash
php artisan news:import caminho/para/arquivo.csv
```

#### Exemplo:

```bash
# Importar arquivo específico
php artisan news:import public/log_das_noticias.csv

# Importar com caminho absoluto
php artisan news:import /home/user/downloads/news.csv
```

#### Saída do comando:

```
Iniciando importação de notícias...

 ████████████████████████████████████████ 100%

✓ Importação concluída com sucesso!

┌────────────────┬────────┐
│ Métrica        │ Valor  │
├────────────────┼────────┤
│ Total          │ 50     │
│ Importadas     │ 48     │
│ Falhadas       │ 2      │
└────────────────┴────────┘

Erros encontrados:
  • Linha 12: Título é obrigatório
  • Linha 25: Data inválida: 25-12-2022
```

---

### 3. Via API REST

```bash
curl -X POST http://localhost:8000/admin/news/import/api \
  -F "csv_file=@caminho/para/arquivo.csv"
```

#### Resposta de sucesso (HTTP 200):

```json
{
    "success": true,
    "imported": 48,
    "failed": 2,
    "total": 50,
    "errors": [
        "Linha 12: Título é obrigatório",
        "Linha 25: Data inválida: 25-12-2022"
    ]
}
```

#### Resposta de erro (HTTP 422):

```json
{
    "success": false,
    "error": "O arquivo deve ser um CSV",
    "imported": 0,
    "failed": 0
}
```

---

## 📋 Validações Aplicadas

O sistema realiza as seguintes validações:

| Validação               | Descrição                       |
| ----------------------- | ------------------------------- |
| **Arquivo obrigatório** | Arquivo CSV é necessário        |
| **Tipo de arquivo**     | Aceita apenas .csv e .txt       |
| **Tamanho máximo**      | 10MB por arquivo                |
| **Título obrigatório**  | Cada notícia deve ter um título |
| **Data válida**         | Data no formato correto         |
| **Duplicatas**          | Notícias iguais são puladas     |
| **Codificação**         | Arquivo deve estar em UTF-8     |

---

## 🔄 Campo Mapping (CSV → Banco de Dados)

| Campo CSV | Campo DB              | Descrição                          |
| --------- | --------------------- | ---------------------------------- |
| `title`   | `title`               | Título da notícia                  |
| `summary` | `summary` + `content` | Resumo/conteúdo                    |
| `date`    | `published_date`      | Data de publicação                 |
| -         | `source_name`         | Definido como "Folha de S.Paulo"   |
| -         | `keywords`            | Extraído automaticamente do título |
| -         | `relevance_score`     | Calculado baseado no comprimento   |
| -         | `metadata`            | Informações de importação          |

---

## 🎯 Exemplo Completo

### Arquivo CSV de entrada:

```csv
title;summary;date
A surpreendente recuperação do emprego no Brasil;Adicionalmente, os efeitos defasados da política monetária, estimulativa até meados de 2022, ajudaram a impulsionar a atividade econômica e, por consequência, o mercado de trabalho. A economia cresceu 2,1%;31.dez.2022 às 23h15
Entenda o mundo em 2022 com livros; séries; filmes e podcasts indicados pela Folha;Crítico literário e autor irlandês, O'Toole, 64, é um dos meus comentaristas favoritos sobre política e cultura americanas. ...; o contexto político é crucial;31.dez.2022 às 23h15
A direita explosiva quis voltar;A tortura tomou-se política de Estado e foi seguida por uma diretriz de extermínio. É importante entender este contexto;31.dez.2022 às 23h15
```

### Resultado no banco:

Após a importação, a tabela `news` conterá:

```
id | title | summary | source_name | published_date | relevance_score | keywords
---|-------|---------|-------------|-----------------|-----------------|----------
1  | A surpreendente... | Adicionalmente... | Folha de S.Paulo | 2022-12-31 | 28.5 | [economia, mercado, política]
2  | Entenda o mundo... | Crítico literário... | Folha de S.Paulo | 2022-12-31 | 15.2 | [crítico, política, cultura]
3  | A direita explosiva... | A tortura tomou... | Folha de S.Paulo | 2022-12-31 | 18.7 | [política, estado, extermínio]
```

---

## ⚠️ Tratamento de Erros

### Erros Comuns

#### 1. Arquivo não encontrado

```
❌ Arquivo não encontrado: /caminho/errado/arquivo.csv
```

**Solução:** Verifique o caminho do arquivo

#### 2. Formato incorreto

```
❌ O arquivo deve ser um CSV
```

**Solução:** Use extensão `.csv` ou `.txt`

#### 3. Arquivo muito grande

```
❌ O arquivo não pode exceder 10MB
```

**Solução:** Divida o arquivo em partes menores

#### 4. Data inválida

```
❌ Linha 15: Data inválida: 31-12-2022
```

**Solução:** Use o formato `DD.mês.YYYY às HHhMM` (ex: `31.dez.2022 às 23h15`)

#### 5. Título vazio

```
❌ Linha 8: Título é obrigatório
```

**Solução:** Verifique se todas as notícias têm título

---

## 🔍 Monitoramento

### Ver logs de importação

```bash
# Ver últimos logs
tail -f storage/logs/laravel.log

# Filtrar apenas importações
grep "importação" storage/logs/laravel.log
```

### Verificar dados no banco

```bash
# Contar notícias importadas
sqlite3 database/data.db "SELECT COUNT(*) FROM news;"

# Ver últimas notícias importadas
sqlite3 database/data.db "SELECT title, published_date FROM news ORDER BY created_at DESC LIMIT 10;"
```

---

## 💡 Dicas Práticas

1. **Valide seu CSV antes de importar**
    - Use ferramentas online ou Excel para verificar o formato
    - Garanta que o separador é ponto-e-vírgula (;)

2. **Use UTF-8**
    - Salve o arquivo em codificação UTF-8
    - Evite caracteres especiais problemáticos

3. **Faça backup antes de importar grandes volumes**

    ```bash
    php artisan migrate --seed
    ```

4. **Processe em lotes**
    - Para arquivos muito grandes (1GB+), divida em múltiplos CSVs
    - Processe um de cada vez

5. **Automatize com agendador (Cron)**
    - Crie um script que importa automaticamente
    - Execute via agendador do Linux

---

## 📞 Suporte

Para problemas ou dúvidas:

- Verifique a seção [⚠️ Tratamento de Erros](#-tratamento-de-erros)
- Consulte o arquivo `README_ANALITY.md`
- Abra uma issue no GitHub
