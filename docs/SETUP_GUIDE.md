# Guia de Configuração - LicenseHub API

## 1️⃣ Instalação Inicial

### Pré-requisitos
- PHP 8.0 ou superior
- MySQL 8.0 ou superior
- Composer (opcional — o instalador web tentará usá-lo se disponível)

### Passos

**1. Preparar o arquivo de configuração:**
```bash
cp .env.example .env
```

**2. Editar o arquivo `.env`:**
```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=licensehub
DB_USER=root
DB_PASSWORD=sua_senha_aqui

APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8000

JWT_SECRET=uma_chave_muito_secreta_aqui_no_minimo_32_caracteres
```

**3. Instalar dependências PHP (opcional):**
```bash
# Se preferir instalar manualmente:
composer install
# Observação: o instalador web tentará executar o Composer automaticamente se estiver disponível, mas não interromperá a instalação em caso de falha.
```

**4. Usar o instalador web (recomendado):**
```bash
# Inicie o servidor embutido do PHP (para testes locais):
php -S localhost:8000 -t public/
# Abra no navegador:
# http://localhost:8000/scripts/install.php
# Siga o formulário: o instalador verificará/usar/criará o banco de dados e importará `database.sql`.
# Após a conclusão, por segurança, remova a pasta `scripts/` do servidor.
```

**(Alternativa) 4b. Criar o banco manualmente (opcional):**
```bash
# Abra o MySQL
mysql -u root -p

# Na shell do MySQL, execute:
CREATE DATABASE licensehub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE licensehub;
source database.sql;

# Verifique as tabelas criadas:
SHOW TABLES;
```

**5. Inicie o servidor (se não iniciou anteriormente):**
```bash
php -S localhost:8000 -t public/
```

Teste a API:
```bash
curl http://localhost:8000/health
```

---

## 2️⃣ Estrutura de Diretórios

```
LicenseHub/
├── app/                          # Código da aplicação
│   ├── Controllers/              # 8 Controllers com CRUD
│   │   ├── ClienteController.php
│   │   ├── ProjetoController.php
│   │   ├── PlanoController.php
│   │   ├── LicencaController.php
│   │   ├── CobrancaController.php
│   │   ├── PagamentoController.php
│   │   ├── NotificacaoController.php
│   │   ├── UsuarioController.php
│   │   └── HealthController.php
│   ├── Models/                   # 11 Models para as entidades
│   │   ├── Cliente.php
│   │   ├── Projeto.php
│   │   ├── Plano.php
│   │   ├── Licenca.php
│   │   ├── Cobranca.php
│   │   ├── Pagamento.php
│   │   ├── Notificacao.php
│   │   ├── Usuario.php
│   │   ├── Perfil.php
│   │   ├── HistoricoLicenca.php
│   │   └── ValidacaoLicenca.php
│   └── Core/                     # Classes base
│       ├── Model.php             # Base para todos os Models
│       ├── Controller.php        # Base para todos os Controllers
│       ├── Router.php            # Sistema de roteamento
│       └── Response.php          # Classe para retornar respostas JSON
├── config/                       # Configurações
│   ├── Config.php               # Gerenciador de configs
│   └── Database.php             # Conexão com BD
├── routes/                       # Definição de rotas
│   └── api.php                  # Todas as rotas da API
├── public/                       # Diretório raiz da web
│   └── index.php                # Ponto de entrada da aplicação
├── database.sql                  # Schema do banco de dados
├── composer.json                 # Dependências PHP
├── .env.example                  # Arquivo de exemplo para configuração
├── API_DOCUMENTATION.md          # Documentação completa da API
├── SETUP_GUIDE.md               # Este guia
└── readme.md                     # Documentação do projeto
```

---

## 3️⃣ Entidades e Relacionamentos

### Diagrama Conceitual

```
CLIENTES (1) ──→ (N) LICENCAS
   │
   ├→ COBRANCAS
   └→ NOTIFICACOES

PROJETOS (1) ──→ (N) PLANOS
   │
   ├→ LICENCAS
   └→ VALIDACOES_LICENCA

PLANOS (1) ──→ (N) LICENCAS

LICENCAS (1) ──→ (N) COBRANCAS
   │
   ├→ NOTIFICACOES
   ├→ HISTORICO_LICENCAS
   └→ VALIDACOES_LICENCA

COBRANCAS (1) ──→ (N) PAGAMENTOS

USUARIOS (N) ──→ (1) PERFIS

PERFIS (1) ──→ (N) USUARIOS
```

---

## 4️⃣ Funcionalidades Implementadas

### ✅ CRUD Completo
Cada entidade possui operações completas:
- **Create** - POST `/recurso` - Criar novo registro
- **Read** - GET `/recurso/{id}` - Obter um registro
- **Update** - PUT `/recurso/{id}` - Atualizar registro
- **Delete** - DELETE `/recurso/{id}` - Deletar registro
- **List** - GET `/recurso` - Listar com paginação

### ✅ Funcionalidades Especiais

**Clientes:**
- Busca por CNPJ, razão social, email
- Listagem de ativos/inativos

**Projetos:**
- Filtragem por código e sigla
- Listagem de ativos

**Licenças:**
- Validação de licenças (endpoint público)
- Listagem por status (ATIVA, VENCIDA, CANCELADA, SUSPENSA, TRIAL)
- Busca de vencidas
- Histórico completo

**Cobranças:**
- Listagem de pendentes
- Listagem de vencidas
- Rastreamento por status

**Pagamentos:**
- Múltiplos pagamentos por cobrança
- Formas de pagamento (BOLETO, PIX, CARTAO, TRANSFERENCIA, DINHEIRO)

**Notificações:**
- Tipos: VENCIMENTO_PROXIMO, LICENCA_VENCIDA, PAGAMENTO_RECEBIDO, etc
- Marcação como lida
- Email integrado (pronto para envio)

**Usuários:**
- Hash de senhas com bcrypt
- Perfis e permissões JSON
- Validação de credenciais

---

## 5️⃣ Paginação

Todas as listagens suportam paginação:

```bash
GET /clientes?page=1&per_page=10
```

**Resposta:**
```json
{
  "success": true,
  "data": [ ... ],
  "pagination": {
    "page": 1,
    "per_page": 10,
    "total": 100,
    "total_pages": 10
  }
}
```

**Limites:**
- Mínimo: 1
- Máximo: 100 por página
- Padrão: 50

---

## 6️⃣ Validação de Licenças (API Pública)

Endpoint especial para validar licenças remotamente:

```bash
POST /licencas/validar
Content-Type: application/json

{
  "codigo_licenca": "LIC-2026-001"
}
```

**Verifica:**
- ✅ Existência da licença
- ✅ Status ativo
- ✅ Data de vencimento
- ✅ Integridade da chave

**Resposta de Sucesso:**
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

---

## 7️⃣ Tratamento de Erros

Todos os erros retornam status HTTP apropriado e mensagem JSON:

```json
{
  "success": false,
  "message": "Descrição do erro"
}
```

**Códigos HTTP:**
- `200` - OK
- `201` - Created
- `400` - Bad Request
- `404` - Not Found
- `409` - Conflict
- `500` - Internal Server Error

---

## 8️⃣ Boas Práticas Implementadas

✅ **Segurança:**
- Prepared Statements (prevenção SQL Injection)
- Hash bcrypt para senhas
- CORS headers configurados
- Validação de entrada

✅ **Performance:**
- Índices no banco de dados
- Paginação obrigatória
- Prepared statements

✅ **Manutenibilidade:**
- PSR-4 Autoloading
- Separação de concerns (MVC)
- Reutilização de código
- Documentação completa

✅ **Escalabilidade:**
- Camada de Models reutilizável
- Controllers simples e focados
- Router flexível
- Fácil adicionar novas entidades

---

## 9️⃣ Como Adicionar uma Nova Entidade

Exemplo: Adicionar tabela "Configuracoes"

**1. Criar o Model:**
```php
// app/Models/Configuracao.php
namespace App\Models;
use App\Core\Model;

class Configuracao extends Model {
    protected string $table = 'configuracoes';
    protected array $fillable = ['chave', 'valor', 'tipo'];
}
```

**2. Criar o Controller:**
```php
// app/Controllers/ConfiguracaoController.php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\Configuracao;

class ConfiguracaoController extends Controller {
    private Configuracao $model;
    
    public function __construct() {
        parent::__construct();
        $this->model = new Configuracao();
    }
    
    // Implementar: index, show, store, update, destroy...
}
```

**3. Registrar as rotas:**
```php
// routes/api.php
$router->get('/configuracoes', 'ConfiguracaoController', 'index');
$router->get('/configuracoes/{id}', 'ConfiguracaoController', 'show');
$router->post('/configuracoes', 'ConfiguracaoController', 'store');
$router->put('/configuracoes/{id}', 'ConfiguracaoController', 'update');
$router->delete('/configuracoes/{id}', 'ConfiguracaoController', 'destroy');
```

---

## 🔟 Troubleshooting

### Erro: "Call to undefined function json_encode"
**Solução:** Instale a extensão JSON do PHP
```bash
apt-get install php-json  # Ubuntu/Debian
yum install php-json      # CentOS/RHEL
```

### Erro: "SQLSTATE[HY000]: General error: 2006 MySQL server has gone away"
**Solução:** Verifique conexão com banco de dados e configurações em `.env`

### Erro: "Class not found: App\Models\..."
**Solução:** Execute `composer dump-autoload`

### Erro: "Fatal error: Cannot redeclare function"
**Solução:** Verifique se há namespaces conflitantes ou duplicação de classes

---

## 🎯 Próximos Passos Recomendados

1. ✅ Implementar autenticação JWT
2. ✅ Adicionar rate limiting
3. ✅ Criar sistema de logging
4. ✅ Implementar cache Redis
5. ✅ Adicionar testes unitários (PHPUnit)
6. ✅ Criar documentação OpenAPI/Swagger
7. ✅ Implementar validações mais rigorosas
8. ✅ Adicionar CI/CD (GitHub Actions)

---

**Criado em:** 5 de Fevereiro de 2026  
**Versão:** 1.0.0
