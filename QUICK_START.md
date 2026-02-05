# 🎓 Tutorial Rápido - LicenseHub API

## ⚡ 5 Minutos para Começar

### 1️⃣ Instalação Rápida (2 min)

```bash
# Clonar/Navegar para pasta do projeto
cd LicenseHub

# Copiar arquivo de configuração
cp .env.example .env

# Editar .env com suas credenciais MySQL
# (Abra .env em um editor e atualize DB_USER, DB_PASSWORD, etc)

# Instalar dependências
composer install

# Criar banco de dados
mysql -u root -p < database.sql

# Iniciar servidor
php -S localhost:8000 -t public/
```

### 2️⃣ Testar a API (1 min)

```bash
# Em outro terminal, teste:

# Health Check
curl http://localhost:8000/health

# Listar clientes (vazio no início)
curl http://localhost:8000/clientes
```

### 3️⃣ Criar seu Primeiro Cliente (1 min)

```bash
curl -X POST "http://localhost:8000/clientes" \
  -H "Content-Type: application/json" \
  -d '{
    "razao_social": "Minha Empresa LTDA",
    "nome_fantasia": "Minha Empresa",
    "cnpj": "12.345.678/0001-99",
    "email": "contato@minha-empresa.com",
    "telefone": "11 3000-0000",
    "cidade": "São Paulo",
    "estado": "SP",
    "ativo": 1
  }'
```

### 4️⃣ Criar um Projeto (1 min)

```bash
curl -X POST "http://localhost:8000/projetos" \
  -H "Content-Type: application/json" \
  -d '{
    "nome": "Sistema de Gestão",
    "codigo": "SGEST",
    "sigla": "SG",
    "descricao": "Meu sistema de gestão",
    "versao_atual": "1.0.0",
    "ativo": 1
  }'
```

---

## 📋 Fluxo Típico de Uso

### Cenário: Gerenciar uma Licença

```
1. CRIAR CLIENTE
   └─ POST /clientes
   └─ Resposta: { "data": { "id": 1, ... } }

2. CRIAR PROJETO
   └─ POST /projetos
   └─ Resposta: { "data": { "id": 1, ... } }

3. CRIAR PLANO
   └─ POST /planos (com projeto_id=1)
   └─ Resposta: { "data": { "id": 1, ... } }

4. CRIAR LICENÇA
   └─ POST /licencas (com cliente_id=1, projeto_id=1, plano_id=1)
   └─ Resposta: { "data": { "id": 1, "codigo_licenca": "LIC-..." } }

5. CRIAR COBRANÇA
   └─ POST /cobrancas (com licenca_id=1, cliente_id=1)
   └─ Resposta: { "data": { "id": 1, "numero_fatura": "FT-..." } }

6. REGISTRAR PAGAMENTO
   └─ POST /pagamentos (com cobranca_id=1)
   └─ Resposta: { "data": { "id": 1, ... } }

7. VALIDAR LICENÇA (do cliente)
   └─ POST /licencas/validar (com codigo_licenca)
   └─ Resposta: { "data": { "ativa": true } }
```

---

## 🔑 Principais Endpoints

### Clientes
```bash
# Listar
GET /clientes

# Buscar um
GET /clientes/1

# Criar
POST /clientes

# Atualizar
PUT /clientes/1

# Deletar
DELETE /clientes/1

# Buscar por critérios
GET /clientes/search?razao_social=...&cnpj=...
```

### Projetos
```bash
# Listar todos
GET /projetos

# Listar ativos
GET /projetos/ativos

# Criar
POST /projetos

# Atualizar
PUT /projetos/1

# Deletar
DELETE /projetos/1
```

### Licenças
```bash
# Listar todas
GET /licencas

# Listar ativas
GET /licencas/ativas

# Listar de um cliente
GET /clientes/1/licencas

# Validar (IMPORTANTE - Acesso Público)
POST /licencas/validar
{
  "codigo_licenca": "LIC-2026-001"
}

# Criar
POST /licencas

# Atualizar
PUT /licencas/1

# Deletar
DELETE /licencas/1
```

### Cobranças
```bash
# Listar
GET /cobrancas

# Listar pendentes
GET /cobrancas/pendentes

# De um cliente
GET /clientes/1/cobrancas

# Criar
POST /cobrancas

# Atualizar
PUT /cobrancas/1
```

### Pagamentos
```bash
# Listar
GET /pagamentos

# De uma cobrança
GET /cobrancas/1/pagamentos

# Criar
POST /pagamentos

# Atualizar
PUT /pagamentos/1
```

---

## 🎯 Casos de Uso Práticos

### Caso 1: Cliente Quer Validar sua Licença

**Requisição:**
```bash
POST /licencas/validar
Content-Type: application/json

{
  "codigo_licenca": "LIC-2026-001"
}
```

**Resposta (Válida):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "codigo": "LIC-2026-001",
    "status": "ATIVA",
    "data_vencimento": "2027-02-05",
    "ativa": true
  },
  "message": "Licença válida"
}
```

**Resposta (Inválida):**
```json
{
  "success": false,
  "message": "Licença expirada"
}
```

---

### Caso 2: Listar Cobranças Vencidas

**Requisição:**
```bash
GET /cobrancas/pendentes?page=1&per_page=10
```

**Resposta:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "numero_fatura": "FT-2026-001",
      "valor_final": 299.90,
      "status": "PENDENTE",
      "data_vencimento": "2026-02-10",
      "cliente": { "razao_social": "Empresa ABC" }
    }
  ],
  "pagination": {
    "page": 1,
    "per_page": 10,
    "total": 3,
    "total_pages": 1
  }
}
```

---

### Caso 3: Registrar Pagamento de uma Cobrança

**Passo 1: Atualizar status da cobrança**
```bash
PUT /cobrancas/1
Content-Type: application/json

{
  "status": "PAGO",
  "data_pagamento": "2026-02-10"
}
```

**Passo 2: Registrar o pagamento**
```bash
POST /pagamentos
Content-Type: application/json

{
  "cobranca_id": 1,
  "valor_pago": 299.90,
  "data_pagamento": "2026-02-10 15:30:00",
  "forma_pagamento": "PIX",
  "referencia_externa": "PIX-ABC123"
}
```

---

## 📱 Usando com Insomnia ou Postman

### Importar Collection

1. **Crie uma nova Collection** chamada "LicenseHub"
2. **Adicione requests** para cada endpoint
3. **Use variáveis de ambiente**:

```
{{base_url}} = http://localhost:8000
{{cliente_id}} = 1
{{projeto_id}} = 1
```

4. **Exemplo de Request em Insomnia:**

```
Method: POST
URL: {{base_url}}/clientes
Body (JSON):
{
  "razao_social": "Teste",
  "cnpj": "12.345.678/0001-99",
  "email": "teste@exemplo.com"
}
```

---

## ⚠️ Erros Comuns

### Erro: "SQLSTATE[HY000]: General error: 2006 MySQL server has gone away"
**Solução:** Verifique as credenciais no arquivo `.env`

### Erro: "Call to undefined function json_encode"
**Solução:** Instale a extensão json do PHP

### Erro: "Class not found"
**Solução:** Execute `composer dump-autoload`

### Erro: "404 - Rota não encontrada"
**Solução:** Verifique a URL e HTTP method (GET, POST, etc)

### Erro: "Campo obrigatório"
**Solução:** Verifique se todos os campos required foram enviados

---

## 🔐 Segurança

### ✅ Já Implementado
- Prepared Statements (previne SQL Injection)
- Hash bcrypt para senhas
- Validação de entrada
- CORS headers

### 🔲 Adicione Depois
- Autenticação JWT
- Rate limiting
- Logging de requisições
- Validação mais rigorosa

---

## 📞 Suporte e Documentação

1. **API_DOCUMENTATION.md** - Todos os endpoints com exemplos
2. **HTTP_EXAMPLES.md** - Exemplos de requisições HTTP
3. **SETUP_GUIDE.md** - Guia de configuração completo

---

## 🚀 Próximos Passos

1. ✅ Leia a documentação completa
2. ✅ Configure seu `.env`
3. ✅ Instale as dependências
4. ✅ Crie seu primeiro cliente
5. ✅ Teste a validação de licenças
6. ✅ Implemente em sua aplicação

---

**Parabéns! Você está pronto para usar o LicenseHub!** 🎉

Qualquer dúvida, consulte a documentação ou adicione logging para debug.
