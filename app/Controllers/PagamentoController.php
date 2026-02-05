<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Models\Pagamento;
use App\Models\Cobranca;

class PagamentoController extends Controller
{
    private Pagamento $model;
    private Cobranca $cobrancaModel;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Pagamento();
        $this->cobrancaModel = new Cobranca();
    }

    /**
     * Lista todos os pagamentos
     */
    public function index(): void
    {
        try {
            $pagination = $this->getPagination();
            $pagamentos = $this->model->all($pagination['per_page'], $pagination['offset']);
            $total = $this->model->count();

            Response::paginated($pagamentos, $pagination['page'], $pagination['per_page'], $total);
        } catch (\Exception $e) {
            Response::error('Erro ao listar pagamentos: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Obtém um pagamento específico
     */
    public function show(int $id): void
    {
        try {
            $pagamento = $this->model->find($id);

            if (!$pagamento) {
                Response::error('Pagamento não encontrado', 404);
            }

            Response::success($pagamento);
        } catch (\Exception $e) {
            Response::error('Erro ao buscar pagamento: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Cria um novo pagamento
     */
    public function store(): void
    {
        try {
            $this->validate(['cobranca_id', 'valor_pago', 'forma_pagamento']);

            if (!$this->cobrancaModel->find($this->request('cobranca_id'))) {
                Response::error('Cobrança não encontrada', 404);
            }

            $id = $this->model->create($this->request());
            $pagamento = $this->model->find($id);

            Response::success($pagamento, 'Pagamento criado com sucesso', 201);
        } catch (\Exception $e) {
            Response::error('Erro ao criar pagamento: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Atualiza um pagamento
     */
    public function update(int $id): void
    {
        try {
            if (!$this->model->find($id)) {
                Response::error('Pagamento não encontrado', 404);
            }

            if ($this->request('cobranca_id') && !$this->cobrancaModel->find($this->request('cobranca_id'))) {
                Response::error('Cobrança não encontrada', 404);
            }

            $this->model->update($id, $this->request());
            $pagamento = $this->model->find($id);

            Response::success($pagamento, 'Pagamento atualizado com sucesso');
        } catch (\Exception $e) {
            Response::error('Erro ao atualizar pagamento: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Deleta um pagamento
     */
    public function destroy(int $id): void
    {
        try {
            if (!$this->model->find($id)) {
                Response::error('Pagamento não encontrado', 404);
            }

            $this->model->delete($id);
            Response::success(null, 'Pagamento deletado com sucesso');
        } catch (\Exception $e) {
            Response::error('Erro ao deletar pagamento: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Lista pagamentos de uma cobrança
     */
    public function byCobranca(int $cobrancaId): void
    {
        try {
            if (!$this->cobrancaModel->find($cobrancaId)) {
                Response::error('Cobrança não encontrada', 404);
            }

            $pagination = $this->getPagination();
            $pagamentos = $this->model->getByCobranca($cobrancaId, $pagination['per_page'], $pagination['offset']);

            Response::paginated($pagamentos, $pagination['page'], $pagination['per_page'], count($pagamentos));
        } catch (\Exception $e) {
            Response::error('Erro ao listar pagamentos: ' . $e->getMessage(), 500);
        }
    }
}
