<?php

namespace App\Models;

use App\Core\Model;

class Projeto extends Model
{
    protected string $table = 'projetos';

    protected array $fillable = [
        'nome',
        'codigo',
        'sigla',
        'descricao',
        'versao_atual',
        'ativo',
    ];

    /**
     * Busca projeto por código
     */
    public function findByCodigo(string $codigo): ?array
    {
        return $this->where(['codigo' => $codigo])[0] ?? null;
    }

    /**
     * Busca projeto por sigla
     */
    public function findBySigla(string $sigla): ?array
    {
        return $this->where(['sigla' => $sigla])[0] ?? null;
    }

    /**
     * Busca projetos ativos
     */
    public function getAtivos(int $limit = 50, int $offset = 0): array
    {
        return $this->where(['ativo' => 1], $limit, $offset);
    }

    /**
     * Conta projetos ativos
     */
    public function countAtivos(): int
    {
        return $this->count(['ativo' => 1]);
    }
}
