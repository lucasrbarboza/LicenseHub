<?php

namespace App\Models;

use App\Core\Model;

class Pagamento extends Model
{
    protected string $table = 'pagamentos';

    protected array $fillable = [
        'cobranca_id',
        'valor_pago',
        'data_pagamento',
        'forma_pagamento',
        'referencia_externa',
        'comprovante_url',
        'observacoes',
    ];

    /**
     * Busca pagamentos de uma cobrança
     */
    public function getByCobranca(int $cobrancaId, int $limit = 50, int $offset = 0): array
    {
        return $this->where(['cobranca_id' => $cobrancaId], $limit, $offset);
    }

    /**
     * Busca pagamentos por forma de pagamento
     */
    public function getByForma(string $forma, int $limit = 50, int $offset = 0): array
    {
        return $this->where(['forma_pagamento' => $forma], $limit, $offset);
    }

    /**
     * Soma de pagamentos de uma cobrança
     */
    public function getTotalByCobranca(int $cobrancaId): float
    {
        $query = "SELECT COALESCE(SUM(valor_pago), 0) as total FROM {$this->table} WHERE cobranca_id = :cobranca_id";
        $result = $this->query($query, ['cobranca_id' => $cobrancaId]);
        return (float)($result[0]['total'] ?? 0);
    }
}
