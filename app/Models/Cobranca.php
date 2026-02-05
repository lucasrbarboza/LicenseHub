<?php

namespace App\Models;

use App\Core\Model;

class Cobranca extends Model
{
    protected string $table = 'cobrancas';

    protected array $fillable = [
        'licenca_id',
        'cliente_id',
        'numero_fatura',
        'descricao',
        'valor',
        'desconto',
        'valor_final',
        'data_vencimento',
        'data_pagamento',
        'status',
        'forma_pagamento',
        'comprovante_url',
        'observacoes',
    ];

    /**
     * Busca cobrança por número de fatura
     */
    public function findByFatura(string $fatura): ?array
    {
        return $this->where(['numero_fatura' => $fatura])[0] ?? null;
    }

    /**
     * Busca cobranças de um cliente
     */
    public function getByCliente(int $clienteId, int $limit = 50, int $offset = 0): array
    {
        return $this->where(['cliente_id' => $clienteId], $limit, $offset);
    }

    /**
     * Busca cobranças pendentes
     */
    public function getPendentes(int $limit = 50, int $offset = 0): array
    {
        return $this->where(['status' => 'PENDENTE'], $limit, $offset);
    }

    /**
     * Busca cobranças vencidas
     */
    public function getVencidas(): array
    {
        $query = "SELECT * FROM {$this->table} WHERE status IN ('PENDENTE', 'VENCIDO') AND data_vencimento < CURDATE()";
        return $this->query($query);
    }

    /**
     * Conta cobranças por status
     */
    public function countByStatus(string $status): int
    {
        return $this->count(['status' => $status]);
    }
}
