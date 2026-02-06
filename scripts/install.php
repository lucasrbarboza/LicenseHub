<?php
declare(strict_types=1);

// Configurações iniciais
$root = dirname(__DIR__);
$envPath = $root . DIRECTORY_SEPARATOR . ".env";
$envExamplePath = $root . DIRECTORY_SEPARATOR . ".env.example";
$sqlPath = __DIR__ . DIRECTORY_SEPARATOR . "database.sql";

// Funções auxiliares
function envQuote(string $value): string
{
    if ($value === "") {
        return "";
    }

    if (preg_match("/[\s#\"\\\\]/", $value)) {
        $escaped = str_replace(["\\", "\""], ["\\\\", "\\\""], $value);
        return "\"" . $escaped . "\"";
    }

    return $value;
}

function setEnvValue(string $content, string $key, string $value): string
{
    $quoted = envQuote($value);
    $pattern = "/^" . preg_quote($key, "/") . "=.*/m";

    if (preg_match($pattern, $content)) {
        return preg_replace($pattern, $key . "=" . $quoted, $content);
    }

    $separator = str_ends_with($content, "\n") ? "" : "\n";
    return $content . $separator . $key . "=" . $quoted . "\n";
}

// Verificar se o formulário foi submetido

$message = null;
$success = false;
$wasSubmitted = ($_SERVER["REQUEST_METHOD"] === "POST");

if ($wasSubmitted) {
    $dbHost = $_POST["db_host"] ?? "localhost";
    $dbUser = $_POST["db_user"] ?? "root";
    $dbPassword = $_POST["db_password"] ?? "";
    $dbName = $_POST["db_name"] ?? "licensehub";
    $clearDb = isset($_POST["clear_db"]);

        // Ambiente e autenticação da API
        $appEnv = $_POST["app_env"] ?? "development"; // development | homologation | production
        $apiAuthEnabled = isset($_POST["api_auth_enabled"]);
        $apiAuthToken = trim($_POST["api_auth_token"] ?? "");
        $msgArr = [];
        // Criar arquivo .env
        if (!file_exists($envPath)) {
            if (file_exists($envExamplePath)) {
                copy($envExamplePath, $envPath);
                $msgArr[] = "Arquivo .env criado com sucesso.";
            } else {
                throw new Exception("Arquivo .env.example não encontrado.");
            }
        } else {
            $msgArr[] = "Arquivo .env já existe, pulando criação.";
        }

        // Atualizar arquivo .env
        $envContent = file_get_contents($envPath);
        $envContent = setEnvValue($envContent, "DB_HOST", $dbHost);
        $envContent = setEnvValue($envContent, "DB_USER", $dbUser);
        $envContent = setEnvValue($envContent, "DB_PASSWORD", $dbPassword);
        $envContent = setEnvValue($envContent, "DB_NAME", $dbName);

        // Definir ambiente e configurações de autenticação
        $envContent = setEnvValue($envContent, "APP_ENV", $appEnv);

        // Se em production, a autenticação é obrigatória
        if ($appEnv === 'production') {
            $apiAuthEnabled = true;
            if ($apiAuthToken === '') {
                // Gera token seguro automaticamente
                $apiAuthToken = bin2hex(random_bytes(16));
                $msgArr[] = "Token da API gerado automaticamente: <code>$apiAuthToken</code> (salve em local seguro).";
            }
        }

        $envContent = setEnvValue($envContent, "API_AUTH_ENABLED", $apiAuthEnabled ? 'true' : 'false');
        if ($apiAuthToken !== '') {
            $envContent = setEnvValue($envContent, "API_AUTH_TOKEN", $apiAuthToken);
        }

        file_put_contents($envPath, $envContent);
        $msgArr[] = "Arquivo .env atualizado com sucesso.";

        // Função auxiliar para executar comandos shell com verificação de retorno
        $runCommand = function(string $cmd): array {
            exec($cmd . " 2>&1", $out, $ret);
            return ['output' => implode("\n", $out), 'return' => $ret];
        };

        // Instalar dependências do Composer (opcional)
        $checkComposer = $runCommand("composer --version");
        if ($checkComposer['return'] === 0) {
            // Composer disponível, tenta instalar dependências, mas não falha o processo se der erro
            $res = $runCommand("composer install --no-interaction --prefer-dist");
            if ($res['return'] === 0) {
                $msgArr[] = "Dependências do Composer instaladas.";
            } else {
                // Registrar aviso mas continuar
                $msgArr[] = "Aviso: Falha ao executar 'composer install' (continua a instalação): <pre style=\"white-space:pre-wrap;\">" . htmlspecialchars($res['output']) . "</pre>";
            }
        } else {
            $msgArr[] = "Composer não encontrado no PATH; pulando instalação de dependências (execução manual é opcional).";
        }

        // Limpar banco de dados se solicitado
        if ($clearDb) {
            $dropCommand = "mysql -h" . escapeshellarg($dbHost) . " -u" . escapeshellarg($dbUser) . ($dbPassword !== "" ? " -p" . escapeshellarg($dbPassword) : "") . " -e " . escapeshellarg("DROP DATABASE IF EXISTS `$dbName`;");
            $res = $runCommand($dropCommand);
            if ($res['return'] !== 0) {
                throw new Exception("Falha ao limpar banco de dados: " . $res['output']);
            }
            $msgArr[] = "Banco de dados anterior removido.";
        }

        // Verificar se o arquivo SQL existe
        if (!file_exists($sqlPath)) {
            throw new Exception("Arquivo SQL não encontrado em: $sqlPath");
        }

        // Tentar usar o banco; se não for possível, tentar criar e usar
        $useCommand = "mysql -h" . escapeshellarg($dbHost) . " -u" . escapeshellarg($dbUser) . ($dbPassword !== "" ? " -p" . escapeshellarg($dbPassword) : "") . " -e " . escapeshellarg("USE `$dbName`;");
        $res = $runCommand($useCommand);
        if ($res['return'] !== 0) {
            // criar banco de dados
            $createCommand = "mysql -h" . escapeshellarg($dbHost) . " -u" . escapeshellarg($dbUser) . ($dbPassword !== "" ? " -p" . escapeshellarg($dbPassword) : "") . " -e " . escapeshellarg("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
            $resCreate = $runCommand($createCommand);
            if ($resCreate['return'] !== 0) {
                throw new Exception("Falha ao criar banco de dados: " . $resCreate['output']);
            }
            $msgArr[] = "Banco de dados criado.";

            // tentar usar novamente
            $res = $runCommand($useCommand);
            if ($res['return'] !== 0) {
                throw new Exception("Banco de dados '$dbName' não pôde ser usado: " . $res['output']);
            }
        } else {
            $msgArr[] = "Banco de dados existe e está acessível.";
        }

        // Importar dados (garantindo a codificação UTF-8)
        $sqlFullPath = realpath($sqlPath) ?: $sqlPath;
        $importCommand = "mysql --default-character-set=utf8mb4 -h" . escapeshellarg($dbHost) . " -u" . escapeshellarg($dbUser) . ($dbPassword !== "" ? " -p" . escapeshellarg($dbPassword) : "") . " " . escapeshellarg($dbName) . " < " . escapeshellarg($sqlFullPath);
        $res = $runCommand($importCommand);
        if ($res['return'] !== 0) {
            throw new Exception("Falha ao importar dados do banco: " . $res['output']);
        }
        $msgArr[] = "Dados importados com sucesso.";

        $success = true;
        $msgArr[] = "<strong>Instalação concluída com sucesso!</strong>";
        $msgArr[] = "<div style=\"background-color: #fff3cd; color: #856404; padding: 10px; margin: 10px 0; border-radius: 5px;\"><strong>ATENÇÃO:</strong> Por segurança, remova a pasta <code>scripts/</code> após a instalação.<br>Esta pasta contém ferramentas de instalação que não devem permanecer no servidor de produção.</div>";
        $msgArr[] = "Próximos passos:";
        $msgArr[] = "1. Inicie o servidor PHP: php -S localhost:8000 -t public/";
        $msgArr[] = "2. Teste a API: curl http://localhost:8000/health";
        $msgArr[] = "3. Consulte a documentação: API_DOCUMENTATION.md";

        $message = implode("<br>", $msgArr);
    } catch (Exception $e) {
        $success = false;
        $message = "Erro durante a instalação: " . htmlspecialchars($e->getMessage());
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
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            margin: 20px 0;
        }
        .checkbox-group input[type="checkbox"] {
            margin-right: 10px;
            transform: scale(1.2);
        }
        .checkbox-group label {
            margin: 0;
            font-weight: normal;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            margin-top: 20px;
            padding: 15px;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Instalador LicenseHub</h1>


        <?php if ($wasSubmitted && !is_null($message)): ?>
            <div class="message <?php echo $success ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if (!$success): ?>
            <form method="POST">
                <div class="form-group">
                    <label for="db_host">Host do Banco de Dados:</label>
                    <input type="text" id="db_host" name="db_host" value="localhost" required>
                </div>

                <div class="form-group">
                    <label for="db_user">Usuário do Banco de Dados:</label>
                    <input type="text" id="db_user" name="db_user" value="root" required>
                </div>

                <div class="form-group">
                    <label for="db_password">Senha do Banco de Dados:</label>
                    <input type="password" id="db_password" value="root" name="db_password">
                </div>

                <div class="form-group">
                    <label for="db_name">Nome do Banco de Dados:</label>
                    <input type="text" id="db_name" name="db_name" value="licensehub" required>
                </div>

                <div class="form-group">
                    <label for="app_env">Ambiente:</label>
                    <select id="app_env" name="app_env">
                        <option value="development">development</option>
                        <option value="homologation">homologation</option>
                        <option value="production">production</option>
                    </select>
                    <small>Escolha <code>production</code> para ativar autenticação obrigatória.</small>
                </div>

                <div class="form-group">
                    <label for="api_auth_token">Token de Autenticação da API (opcional):</label>
                    <input type="text" id="api_auth_token" name="api_auth_token" placeholder="Deixe vazio para gerar um token automático em production">
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="api_auth_enabled" name="api_auth_enabled" checked>
                    <label for="api_auth_enabled">Habilitar autenticação da API (obrigatório em production)</label>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="clear_db" name="clear_db" checked>
                    <label for="clear_db">Limpar banco de dados existente antes da instalação</label>
                </div>

                <button type="submit">Instalar LicenseHub</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>


