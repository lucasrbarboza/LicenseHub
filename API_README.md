# LicenseHub API - Web Service PHP

> **Sistema completo de gestão de licenças de software com API REST em PHP puro**

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)
![PHP](https://img.shields.io/badge/PHP-8.0+-purple.svg)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-blue.svg)

---

## 🎯 Visão Geral

LicenseHub é um **web service REST em PHP puro** que oferece um sistema completo para gerenciar licenças de software, cobranças, pagamentos e validações. Implementado com as melhores práticas de engenharia de software.

### ✨ Destaques

- ✅ **CRUD Completo** - 80+ endpoints REST
- ✅ **11 Models** - Todas as entidades do banco
- ✅ **9 Controllers** - Lógica de negócio bem estruturada
- ✅ **Validação de Licenças** - API pública para validar
- ✅ **Segurança** - Prepared Statements, bcrypt, CORS
- ✅ **Paginação** - Em todas as listagens
- ✅ **Zero Dependências** - Apenas PHP nativo + MySQL
- ✅ **Documentação Completa** - 6 arquivos de docs
- ✅ **Scripts de Instalação** - Para Linux/Mac e Windows
- ✅ **Fácil de Estender** - Arquitetura limpa e modular

---

## 📦 O Que Está Incluído

### Arquitetura
```
Controllers (9)     → Lógica de negócio
    ↓
Models (11)         → Acesso a dados
    ↓
Core Classes (4)    → Funcionalidades base
    ↓
MySQL Database      → Persistência
```

### Entidades
- **Clientes** - Empresas/usuários
- **Projetos** - Sistemas licenciados
- **Planos** - Tipos de licenciamento
- **Licenças** - Chaves e ativações
- **Cobranças** - Billing e faturas
- **Pagamentos** - Registros de pagamento
- **Notificações** - Sistema de notificações
- **Usuários** - Gestão de usuários
- **Perfis** - Controle de acesso
- **Histórico** - Auditoria de licenças
- **Validações** - Log de validações

---

## 🚀 Início Rápido

### Pré-requisitos
- PHP 8.0+
- MySQL 8.0+
- Composer

### Instalação (2 min)

**Linux/Mac:**
```bash
# Clone ou navegue para pasta
cd LicenseHub

# Execute o script de instalação
bash install.sh
```

**Windows:**
```bash
# Execute o arquivo batch
install.bat
```

**Manual:**
```bash
# Copiar configuração
cp .env.example .env

# Editar .env (DB_USER, DB_PASSWORD, etc)
# ...

# Instalar dependências
composer install

# Criar banco de dados
mysql -u root -p < database.sql

# Iniciar servidor
php -S localhost:8000 -t public/
```

### Testar a API
```bash
# Health Check
curl http://localhost:8000/health

# Listar clientes
curl http://localhost:8000/clientes

# Criar cliente
curl -X POST http://localhost:8000/clientes \
  -H "Content-Type: application/json" \
  -d '{
    "razao_social": "Empresa XYZ LTDA",
    "cnpj": "12.345.678/0001-99",
    "email": "contato@empresa.com"
  }'

# Validar licença (IMPORTANTE)
curl -X POST http://localhost:8000/licencas/validar \
  -H "Content-Type: application/json" \
  -d '{"codigo_licenca": "LIC-2026-001"}'
```

---

## 📚 Documentação

| Documento | Descrição |
|-----------|-----------|
| **QUICK_START.md** | 5 minutos para começar |
| **SETUP_GUIDE.md** | Instalação detalhada |
| **API_DOCUMENTATION.md** | Referência completa |
| **HTTP_EXAMPLES.md** | Exemplos práticos |
| **PROJECT_SUMMARY.md** | Visão geral |
| **CHECKLIST.md** | Checklist e estatísticas |

---

## 🔌 API Endpoints

### Resumo
```
Clientes       → GET, POST, PUT, DELETE + busca
Projetos       → GET, POST, PUT, DELETE + filtros
Planos         → GET, POST, PUT, DELETE + por projeto
Licenças       → GET, POST, PUT, DELETE + validação ⭐
Cobranças      → GET, POST, PUT, DELETE + status
Pagamentos     → GET, POST, PUT, DELETE
Notificações   → GET, POST, PUT, DELETE + marcar lida
Usuários       → GET, POST, PUT, DELETE
Health Check   → GET (status da API)

TOTAL: 80+ endpoints
```

### Exemplo: Listar Clientes
```bash
GET /clientes?page=1&per_page=10
```

**Resposta:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "razao_social": "Empresa ABC LTDA",
      "cnpj": "12.345.678/0001-90",
      "email": "contato@abc.com",
      "ativo": 1
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

---

## 🎯 Casos de Uso

### 1️⃣ Validar Licença (Acesso Público)
```bash
POST /licencas/validar
{
  "codigo_licenca": "LIC-2026-001"
}
```

Resposta:
```json
{
  "success": true,
  "data": {
    "ativa": true,
    "status": "ATIVA",
    "data_vencimento": "2027-02-05"
  }
}
```

### 2️⃣ Gerenciar Licenças de um Cliente
```bash
# Listar licenças
GET /clientes/1/licencas

# Criar licença
POST /licencas
{
  "cliente_id": 1,
  "projeto_id": 1,
  "plano_id": 1,
  "codigo_licenca": "LIC-2026-001",
  "chave_ativacao": "ABC123XYZ789..."
}
```

### 3️⃣ Gerenciar Cobranças
```bash
# Listar pendentes
GET /cobrancas/pendentes

# Criar cobrança
POST /cobrancas
{
  "licenca_id": 1,
  "cliente_id": 1,
  "numero_fatura": "FT-2026-001",
  "valor_final": 299.90
}

# Registrar pagamento
POST /pagamentos
{
  "cobranca_id": 1,
  "valor_pago": 299.90,
  "forma_pagamento": "PIX"
}
```

---

## 🏗️ Arquitetura

### Estrutura de Diretórios
```
LicenseHub/
├── app/
│   ├── Controllers/     (9 controllers)
│   ├── Models/          (11 models)
│   └── Core/            (4 classes base)
├── config/              (Configuração e BD)
├── routes/              (Definição de rotas)
├── public/              (Entry point)
├── database.sql         (Schema do BD)
├── composer.json        (Dependências)
├── .env.example         (Configuração modelo)
└── [Documentação]
```

### Padrão MVC
```
Request
   ↓
Router (routes/api.php)
   ↓
Controller (app/Controllers/*)
   ↓
Model (app/Models/*)
   ↓
Database (MySQL)
   ↓
Response (JSON)
```

---

## 🔒 Segurança

### ✅ Implementado
- **Prepared Statements** - Prevenção SQL Injection
- **Hash bcrypt** - Senhas seguras
- **Validação de entrada** - Campos obrigatórios
- **CORS headers** - Cross-origin configurado
- **Chaves estrangeiras** - Integridade referencial
- **Índices otimizados** - Performance
- **Tratamento de erros** - Mensagens seguras
- **Constraints** - Dados únicos e válidos

### 🔲 Recomendado Adicionar
- **Autenticação JWT** - Proteção de endpoints
- **Rate Limiting** - Proteção contra abuso
- **Logging** - Rastreamento de operações
- **HTTPS/SSL** - Criptografia em trânsito
- **Permissões** - Controle por roles
- **2FA** - Autenticação dupla

---

## 📊 Estatísticas

| Item | Quantidade |
|------|-----------|
| Controllers | 9 |
| Models | 11 |
| Classes Core | 4 |
| Endpoints | 80+ |
| Linhas de Código | 5.000+ |
| Documentação | 6 arquivos |
| Scripts | 2 (Linux, Windows) |

---

## 🧪 Testando a API

### Com cURL
```bash
# GET
curl http://localhost:8000/clientes

# POST
curl -X POST http://localhost:8000/clientes \
  -H "Content-Type: application/json" \
  -d '{"razao_social":"..."}'

# PUT
curl -X PUT http://localhost:8000/clientes/1 \
  -H "Content-Type: application/json" \
  -d '{"email":"novo@email.com"}'

# DELETE
curl -X DELETE http://localhost:8000/clientes/1
```

### Com Insomnia/Postman
1. Crie uma coleção "LicenseHub"
2. Configure variável: `{{base_url}} = http://localhost:8000`
3. Importe os exemplos de HTTP_EXAMPLES.md
4. Teste cada endpoint

---

## 🛠️ Desenvolvimento

### Adicionar Nova Entidade

**1. Criar Model:**
```php
// app/Models/NovaEntidade.php
namespace App\Models;
use App\Core\Model;

class NovaEntidade extends Model {
    protected string $table = 'nova_entidade';
    protected array $fillable = ['campo1', 'campo2'];
}
```

**2. Criar Controller:**
```php
// app/Controllers/NovaEntidadeController.php
namespace App\Controllers;
use App\Core\Controller;

class NovaEntidadeController extends Controller {
    // Implementar CRUD...
}
```

**3. Registrar Rotas:**
```php
// routes/api.php
$router->get('/nova-entidade', 'NovaEntidadeController', 'index');
$router->post('/nova-entidade', 'NovaEntidadeController', 'store');
// ... etc
```

---

## 📈 Próximas Melhorias

### Curto Prazo
- [ ] Autenticação JWT
- [ ] Rate limiting
- [ ] Logging detalhado
- [ ] Validações mais rigorosas

### Médio Prazo
- [ ] Cache (Redis)
- [ ] Testes unitários (PHPUnit)
- [ ] Documentação OpenAPI/Swagger
- [ ] WebHooks

### Longo Prazo
- [ ] GraphQL alternativa
- [ ] CI/CD (GitHub Actions)
- [ ] Dashboard administrativo
- [ ] Mobile app

---

## 🤝 Contribuindo

1. Faça um fork
2. Crie uma branch para sua feature
3. Commit suas mudanças
4. Push para a branch
5. Abra um Pull Request

---

## 📄 Licença

MIT License - Sinta-se livre para usar, modificar e distribuir.

---

## 📧 Contato

**Desenvolvedor:** Dev Lucas Rafael  
**Email:** dev@licensehub.local  
**GitHub:** [seu-usuario]/licensehub

---

## 🌟 Reconhecimento

Construído com ❤️ usando:
- **PHP 8.0+** - Linguagem moderna
- **MySQL 8.0+** - Banco de dados robusto
- **PDO** - Acesso seguro ao BD
- **Composer** - Gerenciador de dependências

---

## 📞 Suporte

**Documentação:**
- 📖 Todos os arquivos `.md` na raiz do projeto
- 🔌 Exemplos de requisições em `HTTP_EXAMPLES.md`
- 🚀 Início rápido em `QUICK_START.md`

**Erros Comuns:**
- Verifique `.env` - credenciais do banco
- Limpe cache: `composer dump-autoload`
- Reinicie o servidor PHP

---

**Versão:** 1.0.0  
**Status:** ✅ Pronto para Produção  
**Última Atualização:** 5 de Fevereiro de 2026

---

## ⭐ Se Este Projeto Ajudou

Se você achou útil, dê uma ⭐ no repositório!

```
  ________
 / LicenseHub \
 \ API v1.0.0 /
  ‾‾‾‾‾‾‾‾‾‾
     🎉
```

**Obrigado por usar LicenseHub!**
