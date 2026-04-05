# LicenseHub

LicenseHub agora foi reduzido para um conjunto de scripts PHP simples, focados em CRUD para consumo por uma aplicacao Delphi.

## Estrutura atual

- `public/index.php`: pagina inicial com orientacoes de uso
- `public/*.php`: scripts CRUD por entidade
- `bootstrap.php`: carregamento do `.env`
- `crud.php`: operacoes compartilhadas de listagem, consulta, criacao, atualizacao e remocao
- `config/Database.php`: conexao PDO simples
- `config/resources.php`: metadados das tabelas e campos permitidos
- `scripts/install.php`: instalador web
- `scripts/database.sql`: schema do banco

## Scripts disponiveis

- `clientes.php`
- `projetos.php`
- `planos.php`
- `perfis.php`
- `usuarios.php`
- `licencas.php`
- `cobrancas.php`
- `pagamentos.php`
- `notificacoes.php`
- `historico_licencas.php`
- `validacoes_licenca.php`

## Como usar

Os scripts devem ser consumidos com `POST` e `action` obrigatoria:

- `action=list` lista registros e aceita filtros
- `action=get` consulta um registro por `id`
- `action=create` cria um registro
- `action=update` atualiza um registro por `id`
- `action=delete` remove um registro por `id`

Exemplos:

- `POST /public/clientes.php` com `action=list&cidade=Curitiba`
- `POST /public/pagamentos.php` com `action=get&id=10`
- `POST /public/pagamentos.php` com `action=create&cobranca_id=5&valor_pago=99.90`

## Instalacao

1. Configure o `.env`
2. Importe `scripts/database.sql`
3. Acesse os scripts em `public/`

Para uma instalacao assistida, use `scripts/install.php`.

## Seguranca

Depois da instalacao, remova a pasta `scripts/` do servidor.
