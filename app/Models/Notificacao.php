<?php

namespace App\Models;

use App\Core\Model;

class Notificacao extends Model
{
    protected string $table = 'notificacoes';

    protected array $fillable = [
        'licenca_id',
        'cliente_id',
        'tipo',
        'titulo',
        'mensagem',
        'enviado_email',
        'data_envio_email',
        'lido',
    ];

    /**
     * Busca notificações de um cliente
     */
    public function getByCliente(int $clienteId, int $limit = 50, int $offset = 0): array
    {
        return $this->where(['cliente_id' => $clienteId], $limit, $offset);
    }

    /**
     * Busca notificações não lidas
     */
    public function getNaoLidas(int $limit = 50, int $offset = 0): array
    {
        return $this->where(['lido' => 0], $limit, $offset);
    }

    /**
     * Busca notificações não enviadas por email
     */
    public function getNaoEnviadas(): array
    {
        return $this->where(['enviado_email' => 0]);
    }

    /**
     * Marca notificação como lida
     */
    public function marcarComoLida(int $id): bool
    {
        return $this->update($id, ['lido' => 1]);
    }

    /**
     * Conta notificações não lidas
     */
    public function countNaoLidas(): int
    {
        return $this->count(['lido' => 0]);
    }
}
