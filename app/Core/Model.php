<?php

namespace App\Core;

use Config\Database;
use PDO;
use PDOException;

abstract class Model
{
    protected PDO $db;
    protected string $table;
    protected array $fillable = [];
    protected array $hidden = [];

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Obtém todos os registros
     */
    public function all(int $limit = 50, int $offset = 0): array
    {
        try {
            $query = "SELECT * FROM {$this->table} LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new PDOException("Erro ao buscar registros: " . $e->getMessage());
        }
    }

    /**
     * Busca um registro por ID
     */
    public function find(int $id): ?array
    {
        try {
            $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (PDOException $e) {
            throw new PDOException("Erro ao buscar registro: " . $e->getMessage());
        }
    }

    /**
     * Busca registros com filtros
     */
    public function where(array $conditions, int $limit = 50, int $offset = 0): array
    {
        try {
            $whereClause = [];
            $params = [];

            foreach ($conditions as $column => $value) {
                $whereClause[] = "{$column} = :{$column}";
                $params[$column] = $value;
            }

            $query = "SELECT * FROM {$this->table}";
            if (!empty($whereClause)) {
                $query .= " WHERE " . implode(' AND ', $whereClause);
            }
            $query .= " LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($query);

            foreach ($params as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }

            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new PDOException("Erro ao buscar registros com filtros: " . $e->getMessage());
        }
    }

    /**
     * Cria um novo registro
     */
    public function create(array $data): int
    {
        try {
            $data = $this->filterFillable($data);
            
            $columns = array_keys($data);
            $placeholders = array_map(fn($col) => ":{$col}", $columns);

            $query = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") 
                      VALUES (" . implode(', ', $placeholders) . ")";

            $stmt = $this->db->prepare($query);

            foreach ($data as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }

            $stmt->execute();
            return (int)$this->db->lastInsertId();
        } catch (PDOException $e) {
            throw new PDOException("Erro ao criar registro: " . $e->getMessage());
        }
    }

    /**
     * Atualiza um registro
     */
    public function update(int $id, array $data): bool
    {
        try {
            $data = $this->filterFillable($data);
            
            if (empty($data)) {
                return false;
            }

            $columns = array_keys($data);
            $setClause = array_map(fn($col) => "{$col} = :{$col}", $columns);

            $query = "UPDATE {$this->table} SET " . implode(', ', $setClause) . " WHERE id = :id";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            foreach ($data as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException("Erro ao atualizar registro: " . $e->getMessage());
        }
    }

    /**
     * Deleta um registro
     */
    public function delete(int $id): bool
    {
        try {
            $query = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException("Erro ao deletar registro: " . $e->getMessage());
        }
    }

    /**
     * Conta registros com filtros
     */
    public function count(array $conditions = []): int
    {
        try {
            $query = "SELECT COUNT(*) as total FROM {$this->table}";

            if (!empty($conditions)) {
                $whereClause = [];
                foreach ($conditions as $column => $value) {
                    $whereClause[] = "{$column} = :{$column}";
                }
                $query .= " WHERE " . implode(' AND ', $whereClause);
            }

            $stmt = $this->db->prepare($query);

            foreach ($conditions as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }

            $stmt->execute();
            $result = $stmt->fetch();
            return (int)($result['total'] ?? 0);
        } catch (PDOException $e) {
            throw new PDOException("Erro ao contar registros: " . $e->getMessage());
        }
    }

    /**
     * Filtra dados para aceitar apenas campos permitidos
     */
    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_filter($data, fn($key) => in_array($key, $this->fillable), ARRAY_FILTER_USE_KEY);
    }

    /**
     * Executa uma query customizada
     */
    protected function query(string $sql, array $params = []): ?array
    {
        try {
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new PDOException("Erro ao executar query: " . $e->getMessage());
        }
    }

    /**
     * Inicia uma transação
     */
    protected function beginTransaction(): void
    {
        $this->db->beginTransaction();
    }

    /**
     * Confirma uma transação
     */
    protected function commit(): void
    {
        $this->db->commit();
    }

    /**
     * Desfaz uma transação
     */
    protected function rollback(): void
    {
        $this->db->rollBack();
    }
}
