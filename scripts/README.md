# Scripts / Instalador

Este diretório contém o instalador do LicenseHub.

Atualmente o instalador disponível é:

- `install.php` — instalador web em PHP (formulário interativo).

Observações importantes:

- O instalador `install.php` verifica se o banco de dados pode ser usado; caso não exista, ele tenta criar o banco e em seguida importa o arquivo `database.sql`.
- O instalador valida a existência do arquivo `database.sql` antes de tentar a importação.
- O Composer é tratado como opcional: o instalador verifica `composer --version` e, se disponível, tenta executar `composer install`; caso a execução falhe, a instalação continua e um aviso é exibido.
- Por **segurança**, remova a pasta `scripts/` do servidor após concluir a instalação.

Como usar (local):

1. A partir do diretório do projeto, execute:

```bash
php -S localhost:8000 -t public/
```

2. Abra no navegador:

```
http://localhost:8000/scripts/install.php
```

3. Preencha o formulário com as credenciais do banco, nome do banco e outras opções (ex.: limpar banco existente) e clique em "Instalar LicenseHub".

4. Ao finalizar, siga as recomendações de segurança (remover `scripts/`).

Se preferir instalar manualmente, siga os passos do `docs/SETUP_GUIDE.md` (criação de DB e importação via MySQL CLI).
