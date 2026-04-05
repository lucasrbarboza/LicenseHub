<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$envPath = $root . DIRECTORY_SEPARATOR . '.env';
$envExamplePath = $root . DIRECTORY_SEPARATOR . '.env.example';
$sqlPath = __DIR__ . DIRECTORY_SEPARATOR . 'database.sql';

function env_quote(string $value): string
{
    if ($value === '') {
        return '';
    }

    if (preg_match('/[\s#"\\\\]/', $value)) {
        $escaped = str_replace(["\\", '"'], ["\\\\", '\\"'], $value);
        return '"' . $escaped . '"';
    }

    return $value;
}

function set_env_value(string $content, string $key, string $value): string
{
    $quoted = env_quote($value);
    $pattern = '/^' . preg_quote($key, '/') . '=.*/m';

    if (preg_match($pattern, $content)) {
        return (string) preg_replace($pattern, $key . '=' . $quoted, $content);
    }

    $separator = str_ends_with($content, "\n") ? '' : "\n";
    return $content . $separator . $key . '=' . $quoted . "\n";
}

function import_sql(PDO $pdo, string $sqlPath): void
{
    $sql = file_get_contents($sqlPath);
    if ($sql === false) {
        throw new RuntimeException('Falha ao ler o arquivo database.sql.');
    }

    $sql = preg_replace('/^\s*--.*$/m', '', $sql);
    $statements = preg_split('/;\s*(\r?\n|$)/', (string) $sql);

    foreach ($statements as $statement) {
        $statement = trim($statement);
        if ($statement === '') {
            continue;
        }

        $pdo->exec($statement);
    }
}

$message = null;
$success = false;
$wasSubmitted = ($_SERVER['REQUEST_METHOD'] === 'POST');

if ($wasSubmitted) {
    $dbHost = trim($_POST['db_host'] ?? 'localhost');
    $dbPort = trim($_POST['db_port'] ?? '3306');
    $dbUser = trim($_POST['db_user'] ?? 'root');
    $dbPassword = $_POST['db_password'] ?? '';
    $dbName = trim($_POST['db_name'] ?? 'licensehub');
    $clearDb = isset($_POST['clear_db']);

    try {
        $messages = [];

        if (!file_exists($envPath)) {
            if (!file_exists($envExamplePath)) {
                throw new RuntimeException('Arquivo .env.example nao encontrado.');
            }

            if (!copy($envExamplePath, $envPath)) {
                throw new RuntimeException('Falha ao criar o arquivo .env.');
            }

            $messages[] = 'Arquivo .env criado com sucesso.';
        }

        $envContent = file_get_contents($envPath);
        if ($envContent === false) {
            throw new RuntimeException('Falha ao ler o arquivo .env.');
        }

        $envContent = set_env_value($envContent, 'DB_HOST', $dbHost);
        $envContent = set_env_value($envContent, 'DB_PORT', $dbPort);
        $envContent = set_env_value($envContent, 'DB_USER', $dbUser);
        $envContent = set_env_value($envContent, 'DB_PASSWORD', $dbPassword);
        $envContent = set_env_value($envContent, 'DB_NAME', $dbName);

        if (file_put_contents($envPath, $envContent) === false) {
            throw new RuntimeException('Falha ao atualizar o arquivo .env.');
        }

        $messages[] = 'Arquivo .env atualizado com sucesso.';

        $serverPdo = new PDO(
            "mysql:host={$dbHost};port={$dbPort};charset=utf8mb4",
            $dbUser,
            $dbPassword,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );

        if ($clearDb) {
            $serverPdo->exec("DROP DATABASE IF EXISTS `{$dbName}`");
            $messages[] = 'Banco de dados anterior removido.';
        }

        $serverPdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $messages[] = 'Banco de dados criado com sucesso.';

        $databasePdo = new PDO(
            "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4",
            $dbUser,
            $dbPassword,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );

        import_sql($databasePdo, $sqlPath);
        $messages[] = 'Estrutura importada com sucesso.';
        $messages[] = 'Projeto pronto para uso.';
        $messages[] = 'Por seguranca, remova a pasta scripts/ apos a instalacao.';

        $message = implode('<br>', array_map('htmlspecialchars', $messages));
        $success = true;
    } catch (Throwable $e) {
        $message = 'Erro durante a instalacao: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador LicenseHub</title>
    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, sans-serif;
            background: #f4f1ea;
            color: #1f2937;
        }

        main {
            max-width: 720px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .card {
            background: #fffdf8;
            border: 1px solid #ddd3c4;
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 18px 35px rgba(31, 41, 55, 0.08);
        }

        h1 {
            margin-top: 0;
            font-size: 2rem;
        }

        p {
            color: #5b6471;
            line-height: 1.6;
        }

        .field {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #cfc5b6;
            border-radius: 10px;
            font-size: 1rem;
            box-sizing: border-box;
        }

        .checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 20px 0;
        }

        button {
            width: 100%;
            border: 0;
            border-radius: 12px;
            padding: 14px;
            font-size: 1rem;
            font-weight: 700;
            background: #0f766e;
            color: #fff;
            cursor: pointer;
        }

        .message {
            margin-bottom: 20px;
            padding: 14px 16px;
            border-radius: 12px;
            line-height: 1.6;
        }

        .message.success {
            background: #dff3ef;
            color: #0f5132;
        }

        .message.error {
            background: #fde2e1;
            color: #8a1c1c;
        }
    </style>
</head>
<body>
    <main>
        <section class="card">
            <h1>Instalador LicenseHub</h1>
            <p>Este instalador prepara o arquivo <code>.env</code> e importa o banco para a versao simplificada do projeto.</p>

            <?php if ($wasSubmitted && $message !== null): ?>
                <div class="message <?= $success ? 'success' : 'error' ?>">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <?php if (!$success): ?>
                <form method="post">
                    <div class="field">
                        <label for="db_host">Host do banco</label>
                        <input type="text" id="db_host" name="db_host" value="localhost" required>
                    </div>

                    <div class="field">
                        <label for="db_port">Porta</label>
                        <input type="text" id="db_port" name="db_port" value="3306" required>
                    </div>

                    <div class="field">
                        <label for="db_user">Usuario</label>
                        <input type="text" id="db_user" name="db_user" value="root" required>
                    </div>

                    <div class="field">
                        <label for="db_password">Senha</label>
                        <input type="password" id="db_password" name="db_password">
                    </div>

                    <div class="field">
                        <label for="db_name">Banco</label>
                        <input type="text" id="db_name" name="db_name" value="licensehub" required>
                    </div>

                    <label class="checkbox" for="clear_db">
                        <input type="checkbox" id="clear_db" name="clear_db" checked>
                        <span>Apagar o banco atual antes de importar</span>
                    </label>

                    <button type="submit">Instalar</button>
                </form>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
