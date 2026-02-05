<?php

namespace App\Models;

use App\Core\Model;

class Cliente extends Model
{
    protected string $table = 'clientes';

    protected array $fillable = [
        'razao_social',
        'nome_fantasia',
        'cnpj',
        'inscricao_estadual',
        'email',
        'telefone',
        'celular',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'responsavel_nome',
        'responsavel_email',
        'responsavel_telefone',
        'observacoes',
        'ativo',
    ];

    /**
     * Busca cliente por CNPJ
     */
    public function findByCnpj(string $cnpj): ?array
    {
        return $this->where(['cnpj' => $cnpj])[0] ?? null;
    }

    /**
     * Busca clientes ativos
     */
    public function getAtivos(int $limit = 50, int $offset = 0): array
    {
        return $this->where(['ativo' => 1], $limit, $offset);
    }

    /**
     * Conta clientes ativos
     */
    public function countAtivos(): int
    {
        return $this->count(['ativo' => 1]);
    }
}
