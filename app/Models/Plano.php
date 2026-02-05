<?php

namespace App\Models;

use App\Core\Model;

class Plano extends Model
{
    protected string $table = 'planos';

    protected array $fillable = [
        'projeto_id',
        'nome',
        'descricao',
        'valor_mensal',
        'valor_anual',
        'max_usuarios',
        'max_dispositivos',
        'recursos',
        'ativo',
    ];

    /**
     * Busca planos de um projeto
     */
    public function getByProjeto(int $projetoId, int $limit = 50, int $offset = 0): array
    {
        return $this->where(['projeto_id' => $projetoId, 'ativo' => 1], $limit, $offset);
    }

    /**
     * Conta planos ativos
     */
    public function countAtivos(): int
    {
        return $this->count(['ativo' => 1]);
    }
}
