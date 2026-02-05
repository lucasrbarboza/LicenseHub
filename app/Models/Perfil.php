<?php

namespace App\Models;

use App\Core\Model;

class Perfil extends Model
{
    protected string $table = 'perfis';

    protected array $fillable = [
        'nome',
        'descricao',
        'permissoes',
    ];

    /**
     * Busca perfil por nome
     */
    public function findByNome(string $nome): ?array
    {
        return $this->where(['nome' => $nome])[0] ?? null;
    }
}
