<?php

namespace App\Models;

use App\Core\Model;

class Licenca extends Model
{
    protected string $table = 'licencas';

    protected array $fillable = [
        'cliente_id',
        'projeto_id',
        'plano_id',
        'codigo_licenca',
        'chave_ativacao',
        'tipo_cobranca',
        'valor_cobrado',
        'data_inicio',
        'data_vencimento',
        'data_cancelamento',
        'status',
        'renovacao_automatica',
        'observacoes',
    ];

    /**
     * Busca licença por código
     */
    public function findByCodigo(string $codigo): ?array
    {
        return $this->where(['codigo_licenca' => $codigo])[0] ?? null;
    }

    /**
     * Busca licença por chave de ativação
     */
    public function findByChave(string $chave): ?array
    {
        return $this->where(['chave_ativacao' => $chave])[0] ?? null;
    }

    /**
     * Busca licenças de um cliente
     */
    public function getByCliente(int $clienteId, int $limit = 50, int $offset = 0): array
    {
        return $this->where(['cliente_id' => $clienteId], $limit, $offset);
    }

    /**
     * Busca licenças ativas
     */
    public function getAtivas(int $limit = 50, int $offset = 0): array
    {
        return $this->where(['status' => 'ATIVA'], $limit, $offset);
    }

    /**
     * Busca licenças vencidas
     */
    public function getVencidas(): array
    {
        $query = "SELECT * FROM {$this->table} WHERE status = 'ATIVA' AND data_vencimento < CURDATE()";
        return $this->query($query);
    }

    /**
     * Conta licenças por status
     */
    public function countByStatus(string $status): int
    {
        return $this->count(['status' => $status]);
    }
}
