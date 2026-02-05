<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Models\Cobranca;
use App\Models\Licenca;
use App\Models\Cliente;

class CobrancaController extends Controller
{
    private Cobranca $model;
    private Licenca $licencaModel;
    private Cliente $clienteModel;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Cobranca();
        $this->licencaModel = new Licenca();
        $this->clienteModel = new Cliente();
    }

    /**
     * Lista todas as cobranças
     */
    public function index(): void
    {
        try {
            $pagination = $this->getPagination();
            $cobrancas = $this->model->all($pagination['per_page'], $pagination['offset']);
            $total = $this->model->count();

            Response::paginated($cobrancas, $pagination['page'], $pagination['per_page'], $total);
        } catch (\Exception $e) {
            Response::error('Erro ao listar cobranças: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Obtém uma cobrança específica
     */
    public function show(int $id): void
    {
        try {
            $cobranca = $this->model->find($id);

            if (!$cobranca) {
                Response::error('Cobrança não encontrada', 404);
            }

            Response::success($cobranca);
        } catch (\Exception $e) {
            Response::error('Erro ao buscar cobrança: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Cria uma nova cobrança
     */
    public function store(): void
    {
        try {
            $this->validate(['licenca_id', 'cliente_id', 'numero_fatura', 'valor_final']);

            if (!$this->licencaModel->find($this->request('licenca_id'))) {
                Response::error('Licença não encontrada', 404);
            }

            if (!$this->clienteModel->find($this->request('cliente_id'))) {
                Response::error('Cliente não encontrado', 404);
            }

            if ($this->model->findByFatura($this->request('numero_fatura'))) {
                Response::error('Número de fatura já existe', 409);
            }

            $id = $this->model->create($this->request());
            $cobranca = $this->model->find($id);

            Response::success($cobranca, 'Cobrança criada com sucesso', 201);
        } catch (\Exception $e) {
            Response::error('Erro ao criar cobrança: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Atualiza uma cobrança
     */
    public function update(int $id): void
    {
        try {
            if (!$this->model->find($id)) {
                Response::error('Cobrança não encontrada', 404);
            }

            if ($this->request('licenca_id') && !$this->licencaModel->find($this->request('licenca_id'))) {
                Response::error('Licença não encontrada', 404);
            }

            if ($this->request('cliente_id') && !$this->clienteModel->find($this->request('cliente_id'))) {
                Response::error('Cliente não encontrado', 404);
            }

            $this->model->update($id, $this->request());
            $cobranca = $this->model->find($id);

            Response::success($cobranca, 'Cobrança atualizada com sucesso');
        } catch (\Exception $e) {
            Response::error('Erro ao atualizar cobrança: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Deleta uma cobrança
     */
    public function destroy(int $id): void
    {
        try {
            if (!$this->model->find($id)) {
                Response::error('Cobrança não encontrada', 404);
            }

            $this->model->delete($id);
            Response::success(null, 'Cobrança deletada com sucesso');
        } catch (\Exception $e) {
            Response::error('Erro ao deletar cobrança: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Lista cobranças pendentes
     */
    public function pendentes(): void
    {
        try {
            $pagination = $this->getPagination();
            $cobrancas = $this->model->getPendentes($pagination['per_page'], $pagination['offset']);
            $total = $this->model->countByStatus('PENDENTE');

            Response::paginated($cobrancas, $pagination['page'], $pagination['per_page'], $total);
        } catch (\Exception $e) {
            Response::error('Erro ao listar cobranças pendentes: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Lista cobranças de um cliente
     */
    public function byCliente(int $clienteId): void
    {
        try {
            if (!$this->clienteModel->find($clienteId)) {
                Response::error('Cliente não encontrado', 404);
            }

            $pagination = $this->getPagination();
            $cobrancas = $this->model->getByCliente($clienteId, $pagination['per_page'], $pagination['offset']);

            Response::paginated($cobrancas, $pagination['page'], $pagination['per_page'], count($cobrancas));
        } catch (\Exception $e) {
            Response::error('Erro ao listar cobranças: ' . $e->getMessage(), 500);
        }
    }
}
