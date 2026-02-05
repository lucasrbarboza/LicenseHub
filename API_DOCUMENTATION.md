# LicenseHub - API REST

Estrutura completa do web service PHP para gestão de licenças de software com operações CRUD.

## 🚀 Início Rápido

### Instalação

1. **Clone o repositório** (ou navegue para a pasta do projeto)

2. **Copie o arquivo de configuração**:
```bash
cp .env.example .env
```

3. **Edite o `.env`** com suas credenciais de banco de dados:
```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=licensehub
DB_USER=root
DB_PASSWORD=sua_senha
```

4. **Instale as dependências**:
```bash
composer install
```

5. **Configure o banco de dados**:
```bash
# Execute o arquivo database.sql no seu MySQL
mysql -u root -p licensehub < database.sql
```

6. **Inicie o servidor PHP**:
```bash
php -S localhost:8000 -t public/
```

A API estará disponível em: `http://localhost:8000`

---

## 📁 Estrutura do Projeto

```
LicenseHub/
├── app/
│   ├── Controllers/          # Controllers com operações CRUD
│   ├── Models/               # Models das entidades
│   └── Core/                 # Classes base (Model, Controller, Router, Response)
├── config/                   # Configuração (Database, Config)
├── routes/                   # Definição de rotas (api.php)
├── public/                   # Documento raiz (index.php)
├── database.sql              # Schema do banco de dados
├── .env.example              # Arquivo de configuração exemplo
├── composer.json             # Dependências do projeto
└── readme.md                 # Esta documentação
```

---

## 🔌 API Endpoints

### Health Check
- **GET** `/health` - Verifica se a API está funcionando

### Clientes
- **GET** `/clientes` - Lista todos os clientes (com paginação)
- **GET** `/clientes/search?razao_social=...` - Busca clientes por critérios
- **GET** `/clientes/{id}` - Obtém um cliente específico
- **POST** `/clientes` - Cria um novo cliente
- **PUT** `/clientes/{id}` - Atualiza um cliente
- **DELETE** `/clientes/{id}` - Deleta um cliente

### Projetos
- **GET** `/projetos` - Lista todos os projetos
- **GET** `/projetos/ativos` - Lista projetos ativos
- **GET** `/projetos/{id}` - Obtém um projeto específico
- **POST** `/projetos` - Cria um novo projeto
- **PUT** `/projetos/{id}` - Atualiza um projeto
- **DELETE** `/projetos/{id}` - Deleta um projeto

### Planos
- **GET** `/planos` - Lista todos os planos
- **GET** `/planos/{id}` - Obtém um plano específico
- **GET** `/projetos/{projetoId}/planos` - Lista planos de um projeto
- **POST** `/planos` - Cria um novo plano
- **PUT** `/planos/{id}` - Atualiza um plano
- **DELETE** `/planos/{id}` - Deleta um plano

### Licenças
- **GET** `/licencas` - Lista todas as licenças
- **GET** `/licencas/ativas` - Lista licenças ativas
- **POST** `/licencas/validar` - Valida uma licença (API pública)
- **GET** `/licencas/{id}` - Obtém uma licença específica
- **GET** `/clientes/{clienteId}/licencas` - Lista licenças de um cliente
- **POST** `/licencas` - Cria uma nova licença
- **PUT** `/licencas/{id}` - Atualiza uma licença
- **DELETE** `/licencas/{id}` - Deleta uma licença

### Cobranças
- **GET** `/cobrancas` - Lista todas as cobranças
- **GET** `/cobrancas/pendentes` - Lista cobranças pendentes
- **GET** `/cobrancas/{id}` - Obtém uma cobrança específica
- **GET** `/clientes/{clienteId}/cobrancas` - Lista cobranças de um cliente
- **POST** `/cobrancas` - Cria uma nova cobrança
- **PUT** `/cobrancas/{id}` - Atualiza uma cobrança
- **DELETE** `/cobrancas/{id}` - Deleta uma cobrança

### Pagamentos
- **GET** `/pagamentos` - Lista todos os pagamentos
- **GET** `/pagamentos/{id}` - Obtém um pagamento específico
- **GET** `/cobrancas/{cobrancaId}/pagamentos` - Lista pagamentos de uma cobrança
- **POST** `/pagamentos` - Cria um novo pagamento
- **PUT** `/pagamentos/{id}` - Atualiza um pagamento
- **DELETE** `/pagamentos/{id}` - Deleta um pagamento

### Notificações
- **GET** `/notificacoes` - Lista todas as notificações
- **GET** `/notificacoes/nao-lidas` - Lista notificações não lidas
- **GET** `/notificacoes/{id}` - Obtém uma notificação específica
- **POST** `/notificacoes` - Cria uma nova notificação
- **PUT** `/notificacoes/{id}` - Atualiza uma notificação
- **DELETE** `/notificacoes/{id}` - Deleta uma notificação
- **PUT** `/notificacoes/{id}/marcar-como-lida` - Marca como lida

### Usuários
- **GET** `/usuarios` - Lista todos os usuários
- **GET** `/usuarios/{id}` - Obtém um usuário específico
- **POST** `/usuarios` - Cria um novo usuário
- **PUT** `/usuarios/{id}` - Atualiza um usuário
- **DELETE** `/usuarios/{id}` - Deleta um usuário

---

## 📊 Exemplos de Requisições

### Criar um Cliente
```bash
POST /clientes
Content-Type: application/json

{
  "razao_social": "Empresa XYZ LTDA",
  "nome_fantasia": "Empresa XYZ",
  "cnpj": "12.345.678/0001-99",
  "email": "contato@empresa.com",
  "telefone": "11 3000-0000",
  "endereco": "Rua Principal, 100",
  "cidade": "São Paulo",
  "estado": "SP",
  "cep": "01310-100"
}
```

### Listar Clientes com Paginação
```bash
GET /clientes?page=1&per_page=10
```

### Validar uma Licença (API Pública)
```bash
POST /licencas/validar
Content-Type: application/json

{
  "codigo_licenca": "LIC-2026-001"
}
```

ou

```bash
POST /licencas/validar
Content-Type: application/json

{
  "chave_ativacao": "ABC123XYZ789..."
}
```

### Criar um Projeto
```bash
POST /projetos
Content-Type: application/json

{
  "nome": "Sistema de Gestão",
  "codigo": "SGEST",
  "sigla": "SG",
  "descricao": "Sistema de gestão administrativo",
  "versao_atual": "1.0.0"
}
```

### Criar um Plano
```bash
POST /planos
Content-Type: application/json

{
  "projeto_id": 1,
  "nome": "Plano Profissional",
  "descricao": "Acesso completo ao sistema",
  "valor_mensal": 299.90,
  "valor_anual": 2999.00,
  "max_usuarios": 10,
  "max_dispositivos": 5,
  "recursos": "{\"api_access\": true, \"support\": true}"
}
```

---

## 🔒 Segurança

### Boas Práticas Implementadas

- ✅ **Prepared Statements** - Prevenção contra SQL Injection
- ✅ **Hash de Senhas** - Uso de bcrypt para senhas
- ✅ **Tratamento de Erros** - Errors sanitizados em produção
- ✅ **CORS Headers** - Configurados por padrão
- ✅ **Validação de Entrada** - Verificação de campos obrigatórios
- ✅ **Constraints no BD** - Chaves estrangeiras e índices

### Próximos Passos (Implementar)

- 🔲 Autenticação JWT
- 🔲 Rate Limiting
- 🔲 Logging de requisições
- 🔲 Validação de permissões
- 🔲 Criptografia de dados sensíveis

---

## 📝 Resposta Padrão

### Sucesso (200)
```json
{
  "success": true,
  "data": { ... },
  "message": "Operação realizada com sucesso"
}
```

### Paginado (200)
```json
{
  "success": true,
  "data": [ ... ],
  "pagination": {
    "page": 1,
    "per_page": 10,
    "total": 50,
    "total_pages": 5
  }
}
```

### Erro (400+)
```json
{
  "success": false,
  "message": "Descrição do erro"
}
```

---

## 🛠️ Configuração do Servidor

### Apache (.htaccess)
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ public/index.php?url=$1 [QSA,L]
</IfModule>
```

### Nginx
```nginx
location / {
    try_files $uri $uri/ /public/index.php?$query_string;
}
```

### Desenvolvimento (PHP built-in)
```bash
php -S localhost:8000 -t public/
```

---

## 📦 Dependências

- PHP 8.0+
- MySQL 8.0+
- Composer

## 📄 Licença

MIT License - Sinta-se livre para usar e modificar.

---

## 📧 Suporte

Para dúvidas ou sugestões, entre em contato com o desenvolvedor.
