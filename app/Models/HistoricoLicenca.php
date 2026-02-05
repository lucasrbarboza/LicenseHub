<?php

namespace App\Models;

use App\Core\Model;

class HistoricoLicenca extends Model
{
    protected string $table = 'historico_licencas';

    protected array $fillable = [
        'licenca_id',
        'usuario_id',
        'acao',
        'status_anterior',
        'status_novo',
        'observacoes',
    ];

    /**
     * Busca histórico de uma licença
     */
    public function getByLicenca(int $licencaId, int $limit = 50, int $offset = 0): array
    {
        return $this->where(['licenca_id' => $licencaId], $limit, $offset);
    }

    /**
     * Registra uma ação no histórico
     */
    public function registrarAcao(int $licencaId, string $acao, ?int $usuarioId = null, ?string $statusAnterior = null, ?string $statusNovo = null, ?string $observacoes = null): int
    {
        return $this->create([
            'licenca_id' => $licencaId,
            'usuario_id' => $usuarioId,
            'acao' => $acao,
            'status_anterior' => $statusAnterior,
            'status_novo' => $statusNovo,
            'observacoes' => $observacoes,
        ]);
    }

    /**
     * Conta ações por tipo
     */
    public function countByAcao(string $acao): int
    {
        return $this->count(['acao' => $acao]);
    }
}
