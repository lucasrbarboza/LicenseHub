<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Models\Cliente;

class ClienteController extends Controller
{
    private Cliente $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Cliente();
    }

    /**
     * Lista todos os clientes com paginação
     */
    public function index(): void
    {
        try {
            $pagination = $this->getPagination();
            $clientes = $this->model->all($pagination['per_page'], $pagination['offset']);
            $total = $this->model->count();

            Response::paginated($clientes, $pagination['page'], $pagination['per_page'], $total);
        } catch (\Exception $e) {
            Response::error('Erro ao listar clientes: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Obtém um cliente específico
     */
    public function show(int $id): void
    {
        try {
            $cliente = $this->model->find($id);

            if (!$cliente) {
                Response::error('Cliente não encontrado', 404);
            }

            Response::success($cliente);
        } catch (\Exception $e) {
            Response::error('Erro ao buscar cliente: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Cria um novo cliente
     */
    public function store(): void
    {
        try {
            $this->validate(['razao_social', 'cnpj', 'email']);

            // Verifica se CNPJ já existe
            if ($this->model->findByCnpj($this->request('cnpj'))) {
                Response::error('CNPJ já cadastrado', 409);
            }

            $id = $this->model->create($this->request());

            $cliente = $this->model->find($id);
            Response::success($cliente, 'Cliente criado com sucesso', 201);
        } catch (\Exception $e) {
            Response::error('Erro ao criar cliente: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Atualiza um cliente
     */
    public function update(int $id): void
    {
        try {
            if (!$this->model->find($id)) {
                Response::error('Cliente não encontrado', 404);
            }

            // Se CNPJ está sendo alterado, verifica duplicação
            if ($this->request('cnpj')) {
                $existente = $this->model->findByCnpj($this->request('cnpj'));
                if ($existente && $existente['id'] != $id) {
                    Response::error('CNPJ já cadastrado', 409);
                }
            }

            $this->model->update($id, $this->request());

            $cliente = $this->model->find($id);
            Response::success($cliente, 'Cliente atualizado com sucesso');
        } catch (\Exception $e) {
            Response::error('Erro ao atualizar cliente: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Deleta um cliente
     */
    public function destroy(int $id): void
    {
        try {
            if (!$this->model->find($id)) {
                Response::error('Cliente não encontrado', 404);
            }

            $this->model->delete($id);
            Response::success(null, 'Cliente deletado com sucesso');
        } catch (\Exception $e) {
            Response::error('Erro ao deletar cliente: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Busca clientes por critérios
     */
    public function search(): void
    {
        try {
            $pagination = $this->getPagination();
            $filtros = [];

            if ($this->query('razao_social')) {
                $filtros['razao_social'] = $this->query('razao_social');
            }
            if ($this->query('cnpj')) {
                $filtros['cnpj'] = $this->query('cnpj');
            }
            if ($this->query('email')) {
                $filtros['email'] = $this->query('email');
            }

            $clientes = $this->model->where($filtros, $pagination['per_page'], $pagination['offset']);
            $total = $this->model->count($filtros);

            Response::paginated($clientes, $pagination['page'], $pagination['per_page'], $total);
        } catch (\Exception $e) {
            Response::error('Erro ao buscar clientes: ' . $e->getMessage(), 500);
        }
    }
}
