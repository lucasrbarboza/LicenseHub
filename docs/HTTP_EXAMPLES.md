# 🔌 Exemplos de Requisições HTTP

## 📑 Índice

- [Cliente](#cliente)
- [Projeto](#projeto)
- [Plano](#plano)
- [Licença](#licença)
- [Cobrança](#cobrança)
- [Pagamento](#pagamento)
- [Notificação](#notificação)
- [Usuário](#usuário)

---

## Cliente

### Listar Clientes
```bash
GET /clientes?page=1&per_page=10

curl -X GET "http://localhost:8000/clientes?page=1&per_page=10" \
  -H "Content-Type: application/json"
```

**Resposta (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "razao_social": "Empresa ABC LTDA",
      "nome_fantasia": "ABC",
      "cnpj": "12.345.678/0001-90",
      "email": "contato@abc.com",
      "telefone": "11 3000-0000",
      "ativo": 1,
      "created_at": "2026-02-05 10:30:00"
    }
  ],
  "pagination": {
    "page": 1,
    "per_page": 10,
    "total": 5,
    "total_pages": 1
  }
}
```

### Criar Cliente
```bash
POST /clientes
Content-Type: application/json

curl -X POST "http://localhost:8000/clientes" \
  -H "Content-Type: application/json" \
  -d '{
    "razao_social": "Empresa Nova LTDA",
    "nome_fantasia": "Empresa Nova",
    "cnpj": "98.765.432/0001-10",
    "inscricao_estadual": "123.456.789.012",
    "email": "contato@empresanova.com",
    "telefone": "11 3001-0000",
    "celular": "11 98765-4321",
    "endereco": "Avenida Paulista, 1000",
    "numero": "1000",
    "complemento": "Sala 100",
    "bairro": "Bela Vista",
    "cidade": "São Paulo",
    "estado": "SP",
    "cep": "01311-100",
    "responsavel_nome": "João da Silva",
    "responsavel_email": "joao@empresanova.com",
    "responsavel_telefone": "11 98765-4322",
    "observacoes": "Cliente VIP",
    "ativo": 1
  }'
```

**Resposta (201):**
```json
{
  "success": true,
  "data": {
    "id": 2,
    "razao_social": "Empresa Nova LTDA",
    "cnpj": "98.765.432/0001-10",
    "email": "contato@empresanova.com",
    "created_at": "2026-02-05 11:00:00"
  },
  "message": "Cliente criado com sucesso"
}
```

### Buscar Cliente por ID
```bash
GET /clientes/1

curl -X GET "http://localhost:8000/clientes/1"
```

### Atualizar Cliente
```bash
PUT /clientes/1
Content-Type: application/json

curl -X PUT "http://localhost:8000/clientes/1" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "novo-email@empresa.com",
    "telefone": "11 3001-5000",
    "ativo": 1
  }'
```

### Deletar Cliente
```bash
DELETE /clientes/1

curl -X DELETE "http://localhost:8000/clientes/1"
```

### Buscar Clientes
```bash
GET /clientes/search?razao_social=Empresa&cnpj=12.345

curl -X GET "http://localhost:8000/clientes/search?razao_social=Empresa&email=contato@empresa.com"
```

---

## Projeto

### Criar Projeto
```bash
POST /projetos
Content-Type: application/json

curl -X POST "http://localhost:8000/projetos" \
  -H "Content-Type: application/json" \
  -d '{
    "nome": "Sistema de Gestão",
    "codigo": "SGEST",
    "sigla": "SG",
    "descricao": "Sistema de gestão administrativa",
    "versao_atual": "1.0.0",
    "ativo": 1
  }'
```

### Listar Projetos
```bash
GET /projetos?page=1&per_page=10

curl -X GET "http://localhost:8000/projetos"
```

### Listar Projetos Ativos
```bash
GET /projetos/ativos?page=1&per_page=10

curl -X GET "http://localhost:8000/projetos/ativos"
```

### Obter Projeto
```bash
GET /projetos/1

curl -X GET "http://localhost:8000/projetos/1"
```

### Atualizar Projeto
```bash
PUT /projetos/1
Content-Type: application/json

curl -X PUT "http://localhost:8000/projetos/1" \
  -H "Content-Type: application/json" \
  -d '{
    "versao_atual": "2.0.0"
  }'
```

### Deletar Projeto
```bash
DELETE /projetos/1

curl -X DELETE "http://localhost:8000/projetos/1"
```

---

## Plano

### Criar Plano
```bash
POST /planos
Content-Type: application/json

curl -X POST "http://localhost:8000/planos" \
  -H "Content-Type: application/json" \
  -d '{
    "projeto_id": 1,
    "nome": "Plano Profissional",
    "descricao": "Acesso completo com suporte",
    "valor_mensal": 299.90,
    "valor_anual": 2999.00,
    "max_usuarios": 10,
    "max_dispositivos": 5,
    "recursos": "{\"api_access\": true, \"support\": true, \"sso\": true}",
    "ativo": 1
  }'
```

### Listar Planos
```bash
GET /planos?page=1&per_page=10

curl -X GET "http://localhost:8000/planos"
```

### Listar Planos de um Projeto
```bash
GET /projetos/1/planos?page=1&per_page=10

curl -X GET "http://localhost:8000/projetos/1/planos"
```

### Obter Plano
```bash
GET /planos/1

curl -X GET "http://localhost:8000/planos/1"
```

### Atualizar Plano
```bash
PUT /planos/1
Content-Type: application/json

curl -X PUT "http://localhost:8000/planos/1" \
  -H "Content-Type: application/json" \
  -d '{
    "valor_mensal": 349.90
  }'
```

### Deletar Plano
```bash
DELETE /planos/1

curl -X DELETE "http://localhost:8000/planos/1"
```

---

## Licença

### Criar Licença
```bash
POST /licencas
Content-Type: application/json

curl -X POST "http://localhost:8000/licencas" \
  -H "Content-Type: application/json" \
  -d '{
    "cliente_id": 1,
    "projeto_id": 1,
    "plano_id": 1,
    "codigo_licenca": "LIC-2026-001",
    "chave_ativacao": "ABC123XYZ789ABC123XYZ789ABC123XYZ789",
    "tipo_cobranca": "MENSAL",
    "valor_cobrado": 299.90,
    "data_inicio": "2026-02-05",
    "data_vencimento": "2027-02-05",
    "status": "ATIVA",
    "renovacao_automatica": 1
  }'
```

### Listar Licenças
```bash
GET /licencas?page=1&per_page=10

curl -X GET "http://localhost:8000/licencas"
```

### Listar Licenças Ativas
```bash
GET /licencas/ativas?page=1&per_page=10

curl -X GET "http://localhost:8000/licencas/ativas"
```

### Listar Licenças de um Cliente
```bash
GET /clientes/1/licencas?page=1&per_page=10

curl -X GET "http://localhost:8000/clientes/1/licencas"
```

### Validar Licença (API Pública) ⭐
```bash
POST /licencas/validar
Content-Type: application/json

curl -X POST "http://localhost:8000/licencas/validar" \
  -H "Content-Type: application/json" \
  -d '{
    "codigo_licenca": "LIC-2026-001"
  }'
```

**Resposta de sucesso:**
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

**Resposta de erro (licença vencida):**
```json
{
  "success": false,
  "message": "Licença expirada"
}
```

### Obter Licença
```bash
GET /licencas/1

curl -X GET "http://localhost:8000/licencas/1"
```

### Atualizar Licença
```bash
PUT /licencas/1
Content-Type: application/json

curl -X PUT "http://localhost:8000/licencas/1" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "ATIVA",
    "data_vencimento": "2028-02-05"
  }'
```

### Deletar Licença
```bash
DELETE /licencas/1

curl -X DELETE "http://localhost:8000/licencas/1"
```

---

## Cobrança

### Criar Cobrança
```bash
POST /cobrancas
Content-Type: application/json

curl -X POST "http://localhost:8000/cobrancas" \
  -H "Content-Type: application/json" \
  -d '{
    "licenca_id": 1,
    "cliente_id": 1,
    "numero_fatura": "FT-2026-001",
    "descricao": "Cobrança mensal - Licença SG-2026-001",
    "valor": 299.90,
    "desconto": 0.00,
    "valor_final": 299.90,
    "data_vencimento": "2026-03-05",
    "status": "PENDENTE",
    "forma_pagamento": "PIX"
  }'
```

### Listar Cobranças
```bash
GET /cobrancas?page=1&per_page=10

curl -X GET "http://localhost:8000/cobrancas"
```

### Listar Cobranças Pendentes
```bash
GET /cobrancas/pendentes?page=1&per_page=10

curl -X GET "http://localhost:8000/cobrancas/pendentes"
```

### Listar Cobranças de um Cliente
```bash
GET /clientes/1/cobrancas?page=1&per_page=10

curl -X GET "http://localhost:8000/clientes/1/cobrancas"
```

### Obter Cobrança
```bash
GET /cobrancas/1

curl -X GET "http://localhost:8000/cobrancas/1"
```

### Atualizar Cobrança
```bash
PUT /cobrancas/1
Content-Type: application/json

curl -X PUT "http://localhost:8000/cobrancas/1" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "PAGO",
    "data_pagamento": "2026-02-10"
  }'
```

### Deletar Cobrança
```bash
DELETE /cobrancas/1

curl -X DELETE "http://localhost:8000/cobrancas/1"
```

---

## Pagamento

### Criar Pagamento
```bash
POST /pagamentos
Content-Type: application/json

curl -X POST "http://localhost:8000/pagamentos" \
  -H "Content-Type: application/json" \
  -d '{
    "cobranca_id": 1,
    "valor_pago": 299.90,
    "data_pagamento": "2026-02-10 15:30:00",
    "forma_pagamento": "PIX",
    "referencia_externa": "PIX-ABC123",
    "comprovante_url": "https://exemplo.com/comprovante.pdf",
    "observacoes": "Pagamento recebido com sucesso"
  }'
```

### Listar Pagamentos
```bash
GET /pagamentos?page=1&per_page=10

curl -X GET "http://localhost:8000/pagamentos"
```

### Listar Pagamentos de uma Cobrança
```bash
GET /cobrancas/1/pagamentos?page=1&per_page=10

curl -X GET "http://localhost:8000/cobrancas/1/pagamentos"
```

### Obter Pagamento
```bash
GET /pagamentos/1

curl -X GET "http://localhost:8000/pagamentos/1"
```

### Atualizar Pagamento
```bash
PUT /pagamentos/1
Content-Type: application/json

curl -X PUT "http://localhost:8000/pagamentos/1" \
  -H "Content-Type: application/json" \
  -d '{
    "observacoes": "Pagamento verificado"
  }'
```

### Deletar Pagamento
```bash
DELETE /pagamentos/1

curl -X DELETE "http://localhost:8000/pagamentos/1"
```

---

## Notificação

### Criar Notificação
```bash
POST /notificacoes
Content-Type: application/json

curl -X POST "http://localhost:8000/notificacoes" \
  -H "Content-Type: application/json" \
  -d '{
    "licenca_id": 1,
    "cliente_id": 1,
    "tipo": "VENCIMENTO_PROXIMO",
    "titulo": "Sua licença vence em 30 dias",
    "mensagem": "A licença LIC-2026-001 vencerá em 05 de março de 2026. Renove agora.",
    "enviado_email": 0,
    "lido": 0
  }'
```

### Listar Notificações
```bash
GET /notificacoes?page=1&per_page=10

curl -X GET "http://localhost:8000/notificacoes"
```

### Listar Notificações Não Lidas
```bash
GET /notificacoes/nao-lidas?page=1&per_page=10

curl -X GET "http://localhost:8000/notificacoes/nao-lidas"
```

### Obter Notificação
```bash
GET /notificacoes/1

curl -X GET "http://localhost:8000/notificacoes/1"
```

### Marcar como Lida
```bash
PUT /notificacoes/1/marcar-como-lida

curl -X PUT "http://localhost:8000/notificacoes/1/marcar-como-lida"
```

### Atualizar Notificação
```bash
PUT /notificacoes/1
Content-Type: application/json

curl -X PUT "http://localhost:8000/notificacoes/1" \
  -H "Content-Type: application/json" \
  -d '{
    "lido": 1,
    "enviado_email": 1
  }'
```

### Deletar Notificação
```bash
DELETE /notificacoes/1

curl -X DELETE "http://localhost:8000/notificacoes/1"
```

---

## Usuário

### Criar Usuário
```bash
POST /usuarios
Content-Type: application/json

curl -X POST "http://localhost:8000/usuarios" \
  -H "Content-Type: application/json" \
  -d '{
    "nome": "João Silva",
    "email": "joao.silva@exemplo.com",
    "senha": "SenhaSegura123!",
    "perfil_id": 1,
    "ativo": 1
  }'
```

### Listar Usuários
```bash
GET /usuarios?page=1&per_page=10

curl -X GET "http://localhost:8000/usuarios"
```

### Obter Usuário
```bash
GET /usuarios/1

curl -X GET "http://localhost:8000/usuarios/1"
```

### Atualizar Usuário
```bash
PUT /usuarios/1
Content-Type: application/json

curl -X PUT "http://localhost:8000/usuarios/1" \
  -H "Content-Type: application/json" \
  -d '{
    "nome": "João Silva Updated",
    "senha": "NovaSenha456!"
  }'
```

### Deletar Usuário
```bash
DELETE /usuarios/1

curl -X DELETE "http://localhost:8000/usuarios/1"
```

---

## 🧪 Health Check

### Verificar Status da API
```bash
GET /health

curl -X GET "http://localhost:8000/health"
```

**Resposta:**
```json
{
  "success": true,
  "data": {
    "status": "ok",
    "timestamp": "2026-02-05 11:00:00",
    "version": "1.0.0"
  },
  "message": "API is running"
}
```

---

## 💡 Dicas

1. **Use Postman ou Insomnia** para testar as requisições facilmente
2. **Todas as listagens suportam** `?page=1&per_page=10`
3. **Sempre envie** `Content-Type: application/json` em POST/PUT
4. **Respostas de erro** terão `success: false` e status HTTP apropriado
5. **Senhas são sempre hashadas** com bcrypt
6. **Validação de licença é pública** (não requer autenticação)

---

**Criado em:** 5 de Fevereiro de 2026
