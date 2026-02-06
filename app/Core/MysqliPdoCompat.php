<?php

namespace App\Core;

use PDOException;

/**
 * Adapter that mimics a subset of PDO API on top of mysqli.
 * Methods implemented: prepare, lastInsertId, beginTransaction, commit, rollBack
 */
class MysqliPdoCompat
{
    private \mysqli $mysqli;

    public function __construct(string $host, int $port, string $db, string $user, string $pass)
    {
        $this->mysqli = new \mysqli($host, $user, $pass, $db, $port);
        if ($this->mysqli->connect_errno) {
            throw new PDOException('MySQLi connect error: ' . $this->mysqli->connect_error);
        }
        // ensure utf8mb4
        $this->mysqli->set_charset('utf8mb4');
    }

    public function prepare(string $sql): MysqliStatementCompat
    {
        return new MysqliStatementCompat($this->mysqli, $sql);
    }

    public function lastInsertId(): string
    {
        return (string) $this->mysqli->insert_id;
    }

    public function beginTransaction(): bool
    {
        return $this->mysqli->begin_transaction();
    }

    public function commit(): bool
    {
        return $this->mysqli->commit();
    }

    public function rollBack(): bool
    {
        return $this->mysqli->rollback();
    }

    // Additional helper to execute raw queries (not used widely)
    public function query(string $sql)
    {
        $res = $this->mysqli->query($sql);
        if ($res === false) {
            throw new PDOException('MySQLi query error: ' . $this->mysqli->error);
        }
        return $res;
    }
}

class MysqliStatementCompat
{
    private \mysqli $mysqli;
    private string $originalSql;
    private string $preparedSql;
    private array $params = [];
    private ?\mysqli_stmt $stmt = null;
    private array $paramOrder = [];

    public function __construct(\mysqli $mysqli, string $sql)
    {
        $this->mysqli = $mysqli;
        $this->originalSql = $sql;
        // Convert named params (:name) to ? and record order
        $this->preparedSql = preg_replace_callback('/:([a-zA-Z0-9_]+)/', function ($m) {
            $this->paramOrder[] = $m[1];
            return '?';
        }, $sql);
        $this->stmt = $this->mysqli->prepare($this->preparedSql);
        if ($this->stmt === false) {
            throw new PDOException('MySQLi prepare error: ' . $this->mysqli->error);
        }
    }

    /**
     * Bind value by name or position
     */
    public function bindValue(string $param, $value, $type = null): void
    {
        // accept :param or param
        $name = ltrim($param, ':');
        $this->params[$name] = $value;
    }

    public function execute(): bool
    {
        if (!empty($this->paramOrder)) {
            $types = '';
            $values = [];
            foreach ($this->paramOrder as $name) {
                $val = $this->params[$name] ?? null;
                $values[] = $val;
                if (is_int($val)) {
                    $types .= 'i';
                } elseif (is_double($val) || is_float($val)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
            }
            if (!empty($values)) {
                // bind params dynamically
                $refs = [];
                foreach ($values as $k => $v) {
                    $refs[$k] = &$values[$k];
                }
                array_unshift($refs, $types);
                // call_user_func_array on mysqli_stmt::bind_param
                if (!call_user_func_array([$this->stmt, 'bind_param'], $refs)) {
                    throw new PDOException('MySQLi bind_param error: ' . $this->stmt->error);
                }
            }
        }

        if (!$this->stmt->execute()) {
            throw new PDOException('MySQLi execute error: ' . $this->stmt->error);
        }

        return true;
    }

    private function resultFetchAssocAll(\mysqli_result $result): array
    {
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function fetchAll(): array
    {
        $result = $this->stmt->get_result();
        if ($result === false) {
            // fallback: bind result vars
            $meta = $this->stmt->result_metadata();
            if ($meta === false) {
                return [];
            }
            $fields = [];
            $row = [];
            while ($field = $meta->fetch_field()) {
                $fields[] = $field->name;
                $row[$field->name] = null;
            }
            $bind = [];
            foreach ($fields as $k => $name) {
                $bind[$k] = &$row[$name];
            }
            call_user_func_array([$this->stmt, 'bind_result'], $bind);
            $rows = [];
            while ($this->stmt->fetch()) {
                $copy = [];
                foreach ($fields as $name) {
                    $copy[$name] = $row[$name];
                }
                $rows[] = $copy;
            }
            return $rows;
        }
        return $this->resultFetchAssocAll($result);
    }

    public function fetch(): ?array
    {
        $all = $this->fetchAll();
        return $all[0] ?? null;
    }

    public function rowCount(): int
    {
        $result = $this->stmt->get_result();
        if ($result === false) {
            return $this->stmt->num_rows;
        }
        return $result->num_rows;
    }

    public function close(): void
    {
        $this->stmt->close();
    }
}
