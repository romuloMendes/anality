# 🚀 Enviando para GitHub

Seu projeto Anality foi preparado localmente e está pronto para ser enviado ao GitHub!

## 📋 Passos para Enviar

### 1️⃣ Criar Repositório no GitHub

1. Acesse [github.com/new](https://github.com/new)
2. Digite o nome: **`anality`**
3. Descrição: _"Análise de Ataques Hackers e Notícias com Laravel"_
4. Escolha se quer público ou privado
5. **NÃO** marque "Initialize this repository with..."
6. Clique em **"Create repository"**

### 2️⃣ Copiar a URL do Repositório

Após criar, você verá uma página como esta:

```bash
git remote add origin https://github.com/SEU_USUARIO/anality.git
git branch -M main
git push -u origin main
```

### 3️⃣ Executar os Comandos

Abra o terminal e execute:

```bash
cd /home/romulo/project/anality

# Adicionar o repositório remoto
git remote add origin https://github.com/SEU_USUARIO/anality.git

# Renomear branch para 'main'
git branch -M main

# Fazer push para GitHub
git push -u origin main
```

**Substitua `SEU_USUARIO` pelo seu username do GitHub**

### 4️⃣ Autenticar (se necessário)

Se pedir autenticação:

- **Via HTTPS**: Use seu username + token de acesso (crie em Settings → Developer settings → Personal access tokens)
- **Via SSH**: Use sua chave SSH pública

## ✅ Pronto!

Seu projeto estará em: `https://github.com/SEU_USUARIO/anality`

---

## 📌 Próximas Atualizações

Quando quiser enviar novas alterações:

```bash
git add .
git commit -m "Sua mensagem aqui"
git push origin main
```

## 🔧 Alterar Autor Git (Opcional)

Para usar seu email e nome real:

```bash
git config --global user.email seu_email@example.com
git config --global user.name "Seu Nome"
```

---

**💡 Dica:** Guarde o link do seu repositório!
