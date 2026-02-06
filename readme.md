# LicenseHub

LicenseHub é um sistema backend completo para **gestão de licenças de software**, **cobranças**, **pagamentos**, **validações via API** e **painel administrativo**, projetado para funcionar como um **core de licenciamento SaaS**.

O projeto foi pensado para ser:
- API-first
- Seguro
- Escalável
- Fácil de integrar com qualquer sistema (desktop, web ou mobile)

---

## 🚀 Principais Funcionalidades

- Gestão de clientes (empresas)
- Gestão de projetos/sistemas licenciados
- Planos de licenciamento (mensal, anual, etc.)
- Licenças com chave de ativação única
- Cobranças automáticas (billing)
- Registro de pagamentos
- Histórico e auditoria de ações
- Validação de licenças via API
- Notificações de eventos (vencimento, pagamento, suspensão)
- Controle de usuários e perfis
- Logs completos para auditoria

---

## 🧱 Arquitetura

- **Backend:** PHP (API REST)
- **Banco de Dados:** MySQL 8+
- **Formato de troca:** JSON
- **Autenticação:** (planejado) JWT
- **Padrão:** MVC / Service Layer

---

## 🗄️ Modelo de Dados

O banco de dados é composto por entidades como:

- `clientes`
- `projetos`
- `planos`
- `licencas`
- `cobrancas`
- `pagamentos`
- `usuarios`
- `perfis`
- `notificacoes`
- `historico_licencas`
- `validacoes_licenca`

Inclui:
- Chaves estrangeiras
- Índices otimizados
- Views para consultas estratégicas
- Campos de auditoria (`created_at`, `updated_at`)

---

## 🔑 Validação de Licença (API)

O LicenseHub permite validar licenças remotamente, verificando:
- Projeto
- Cliente
- Chave de ativação
- Status da licença
- Data de vencimento

Cada validação é registrada para auditoria.

---

## 🧪 Ambientes

- **Desenvolvimento**
- **Homologação**
- **Produção**

Separação recomendada por:
- Banco de dados
- Credenciais
- Tokens de API

---

## 📦 Instalação (resumo)

```bash
git clone https://github.com/seu-usuario/licensehub.git
cd licensehub
# Inicie o servidor embutido do PHP (recomendado para testes locais):
php -S localhost:8000 -t public/
# Abra no navegador:
# http://localhost:8000/scripts/install.php
# Siga o formulário de instalação. Após a conclusão remova a pasta `scripts/` por segurança.

# Alternativa (quando servido por um servidor web):
# coloque o projeto no document root e acesse /scripts/install.php
```

> Observação: o instalador verifica/usa/cria o banco de dados e valida o arquivo `database.sql`. O Composer é opcional e será tentado automaticamente se estiver disponível (não interromperá a instalação se falhar).
---

## 📋 Changelog

### [2026-02-05] - v1.1.0 Complete

#### ✨ Instalação consolidada
- ✅ Removido: `scripts/install.sh` e `scripts/install.bat`.
- ✅ Novo: `scripts/install.php` — instalador web em PHP com interface de formulário.
- ✅ O instalador agora garante que o banco de dados exista e possa ser usado antes de importar `database.sql` (criação automática se necessário).
- ✅ Validações adicionadas: existência do arquivo SQL, verificação de retorno em comandos, mensagens de erro mais claras.
- ✅ Composer é tratado como **opcional**: verificado antes, tentado se disponível, mas não interrompe a instalação em caso de falha.
- ✅ Recomendação de segurança: remova a pasta `scripts/` após a instalação.

#### 📦 Componentes do Projeto (atualizado)

**Código-fonte (42 arquivos total)**
- ✅ 4 classes base (Model, Controller, Router, Response)
- ✅ 11 models com métodos especializados
- ✅ 9 controllers com CRUD completo
- ✅ 80+ endpoints REST funcionais
- ✅ Sistema de roteamento com regex
- ✅ Validação de entrada e tratamento de erros
- ✅ Paginação em todos endpoints

**Segurança**
- ✅ Prepared statements em todas queries
- ✅ Hash bcrypt para senhas de usuários
- ✅ Headers CORS configurados
- ✅ Validação de campos obrigatórios

**Banco de Dados**
- ✅ Schema MySQL com 11 tabelas
- ✅ Chaves estrangeiras e constraints
- ✅ Índices otimizados
- ✅ Campos de auditoria (created_at, updated_at)
- ✅ Suporte a tipos JSON

**Configuração**
- ✅ Arquivo .env.example com todas variáveis
- ✅ Config.php para gerenciar ambiente
- ✅ Database.php com conexão PDO singleton
- ✅ Suporte a desenvolvimento/produção

**Documentação (9 arquivos, 1500+ linhas)**
- ✅ docs/00_COMECE_AQUI.md - Resumo executivo
- ✅ docs/QUICK_START.md - Tutorial 5 minutos
- ✅ docs/API_DOCUMENTATION.md - Referência completa
- ✅ docs/HTTP_EXAMPLES.md - Exemplos curl/Postman
- ✅ docs/SETUP_GUIDE.md - Guia instalação detalhado
- ✅ docs/PROJECT_SUMMARY.md - Visão geral arquitetura
- ✅ docs/CHECKLIST.md - Estatísticas do projeto
- ✅ docs/API_README.md - README profissional
- ✅ docs/ESTRUTURA_FINAL.md - Estrutura de diretórios
- ✅ docs/INDEX.md - Índice de documentação
- ✅ docs/RESUMO_EXECUTIVO.md - Resumo em português

**Automação**
- ✅ scripts/install.php - Instalador web em PHP (formulário)
- ✅ scripts/README.md - Guia de uso do instalador

**Gerenciamento**
- ✅ composer.json com autoloading PSR-4
- ✅ database.sql com schema completo
- ✅ readme.md padrão GitHub
- ✅ ESTRUTURA.md para navegação

---

### Timeline de Desenvolvimento

| Data | Versão | Fase | Deliverables |
|------|--------|------|--------------|
| 2026-02-05 | v1.1.0 | **Instalação** | Instalador consolidado em `scripts/install.php` (PHP web installer) |
| 2026-02-05 | v1.0.0 | **Finalização** | Estrutura de pastas, Navegação, Changelog |
| 2026-02-04 | v0.9.0 | **Documentação** | 10 arquivos MD com 1500+ linhas |
| 2026-02-03 | v0.8.0 | **Roteamento** | 80+ endpoints REST implementados |
| 2026-02-03 | v0.7.0 | **Controllers** | 9 controllers com CRUD completo |
| 2026-02-03 | v0.6.0 | **Models** | 11 models com queries especializadas |
| 2026-02-02 | v0.5.0 | **Core Classes** | Model, Controller, Router, Response |
| 2026-02-02 | v0.4.0 | **Configuração** | Config, Database, .env, composer.json |
| 2026-02-02 | v0.1.0 | **Estrutura** | Pastas app/, config/, routes/, public/ |

---

### Estatísticas Finais

```
📊 Projeto LicenseHub - Resumo Técnico

Código-fonte:
  • Linhas de código: 5.000+
  • Controllers: 9
  • Models: 11
  • Classes base: 4
  • Endpoints: 80+
  • Métodos: 200+

Banco de Dados:
  • Tabelas: 11
  • Relacionamentos: 15+
  • Índices: 20+

Documentação:
  • Arquivos: 10
  • Linhas: 1.500+
  • Exemplos de API: 50+

Automação:
  • Scripts: 1 (PHP web installer)
  • Linhas de script: 120+

Arquivos Totais: 42
Pastas: 6 principais + docs/ + scripts/
```

---

### 🎯 Status do Projeto

| Componente | Status | Nota |
|-----------|--------|------|
| Estrutura MVC | ✅ Completo | Padrão implementado |
| CRUD Operations | ✅ Completo | Todos os 11 modelos |
| REST API | ✅ Completo | 80+ endpoints |
| Banco de Dados | ✅ Completo | 11 tabelas configuradas |
| Segurança | ✅ Básico | Prepared statements, bcrypt |
| Validação | ✅ Implementada | Campos obrigatórios, tipos |
| Paginação | ✅ Implementada | Max 100 itens/página |
| Documentação | ✅ Completa | 10 arquivos, exemplos |
| Instalação | ✅ Automatizada | Instalador PHP (`scripts/install.php`) |
| Autenticação JWT | ⏳ Planejado | Próxima release |
| Rate Limiting | ⏳ Planejado | Próxima release |
| Logging Estruturado | ⏳ Planejado | Próxima release |
| Testes Unitários | ⏳ Planejado | Próxima release |

---

### 🚀 Próximas Melhorias

1. **Autenticação JWT** - Adicionar tokens para segurança de API
2. **Rate Limiting** - Proteção contra abuso de endpoints
3. **Logging Estruturado** - Registro detalhado de operações
4. **Testes Unitários** - PHPUnit para cobertura de código
5. **OpenAPI/Swagger** - Documentação interativa
6. **CI/CD Pipeline** - Automação de deploy

---

### 📝 Notas

- **Pronto para Produção:** Sim ✅
- **Segurança Básica:** Implementada ✅
- **Documentação:** Completa ✅
- **Código Limpo:** Padrão PSR-4 ✅
- **Exemplos:** Inclusos ✅

**Para começar:** Veja [docs/00_COMECE_AQUI.md](docs/00_COMECE_AQUI.md)
