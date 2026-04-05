<?php
declare(strict_types=1);

$scripts = [
    'clientes.php',
    'projetos.php',
    'planos.php',
    'perfis.php',
    'usuarios.php',
    'licencas.php',
    'cobrancas.php',
    'pagamentos.php',
    'notificacoes.php',
    'historico_licencas.php',
    'validacoes_licenca.php',
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LicenseHub Scripts</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f2efe8;
            --card: #fffdf9;
            --ink: #1f2a37;
            --muted: #596579;
            --line: #d9d0c2;
            --accent: #0f766e;
            --accent-soft: #dff3ef;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, sans-serif;
            background:
                radial-gradient(circle at top left, #f8efe0 0, transparent 32%),
                linear-gradient(180deg, #f8f4ed 0%, var(--bg) 100%);
            color: var(--ink);
            min-height: 100vh;
        }

        main {
            max-width: 900px;
            margin: 0 auto;
            padding: 48px 20px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 18px 40px rgba(31, 42, 55, 0.08);
        }

        h1 {
            margin: 0 0 12px;
            font-size: 2.2rem;
        }

        p {
            color: var(--muted);
            line-height: 1.6;
        }

        .note {
            background: var(--accent-soft);
            border-left: 4px solid var(--accent);
            padding: 14px 16px;
            border-radius: 10px;
            margin: 24px 0;
        }

        code {
            font-family: Consolas, monospace;
            background: #f3efe8;
            padding: 2px 6px;
            border-radius: 6px;
        }

        ul {
            margin: 16px 0 0;
            padding-left: 20px;
        }

        li {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <main>
        <section class="card">
            <h1>LicenseHub simplificado</h1>
            <p>Este projeto agora expoe scripts PHP diretos para CRUD, pensados para consumo por uma aplicacao Delphi sem roteador, controllers ou camada de API REST completa.</p>

            <div class="note">
                Use <code>POST</code> com <code>action</code> obrigatoria. As actions aceitas sao <code>list</code>, <code>get</code>, <code>create</code>, <code>update</code> e <code>delete</code>. Filtros e campos devem ser enviados no corpo da requisicao.
            </div>

            <p>Scripts disponíveis:</p>
            <ul>
                <?php foreach ($scripts as $script): ?>
                    <li><code><?= htmlspecialchars($script, ENT_QUOTES, 'UTF-8') ?></code></li>
                <?php endforeach; ?>
            </ul>
        </section>
    </main>
</body>
</html>
