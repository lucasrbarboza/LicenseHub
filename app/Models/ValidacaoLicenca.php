<?php

namespace App\Models;

use App\Core\Model;

class ValidacaoLicenca extends Model
{
    protected string $table = 'validacoes_licenca';

    protected array $fillable = [
        'licenca_id',
        'cliente_id',
        'projeto_id',
        'cnpj',
        'sigla',
        'ip_origem',
        'status_retornado',
        'mensagem_retorno',
        'dados_request',
        'dados_response',
    ];

    /**
     * Busca validações de uma licença
     */
    public function getByLicenca(int $licencaId, int $limit = 50, int $offset = 0): array
    {
        return $this->where(['licenca_id' => $licencaId], $limit, $offset);
    }

    /**
     * Busca validações por CNPJ
     */
    public function getByCnpj(string $cnpj, int $limit = 50, int $offset = 0): array
    {
        return $this->where(['cnpj' => $cnpj], $limit, $offset);
    }

    /**
     * Busca validações por IP
     */
    public function getByIp(string $ip, int $limit = 50, int $offset = 0): array
    {
        return $this->where(['ip_origem' => $ip], $limit, $offset);
    }

    /**
     * Registra uma tentativa de validação
     */
    public function registrarValidacao(array $dados): int
    {
        return $this->create($dados);
    }
}
