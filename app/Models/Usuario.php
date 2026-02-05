<?php

namespace App\Models;

use App\Core\Model;

class Usuario extends Model
{
    protected string $table = 'usuarios';

    protected array $fillable = [
        'nome',
        'email',
        'senha',
        'perfil_id',
        'ativo',
    ];

    protected array $hidden = ['senha'];

    /**
     * Busca usuário por email
     */
    public function findByEmail(string $email): ?array
    {
        return $this->where(['email' => $email])[0] ?? null;
    }

    /**
     * Busca usuários ativos
     */
    public function getAtivos(int $limit = 50, int $offset = 0): array
    {
        return $this->where(['ativo' => 1], $limit, $offset);
    }

    /**
     * Valida credenciais do usuário
     */
    public function validarCredenciais(string $email, string $senha): ?array
    {
        $usuario = $this->findByEmail($email);

        if (!$usuario) {
            return null;
        }

        if (!password_verify($senha, $usuario['senha'])) {
            return null;
        }

        unset($usuario['senha']);
        return $usuario;
    }

    /**
     * Conta usuários ativos
     */
    public function countAtivos(): int
    {
        return $this->count(['ativo' => 1]);
    }
}
