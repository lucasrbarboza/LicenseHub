<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Models\Plano;
use App\Models\Projeto;

class PlanoController extends Controller
{
    private Plano $model;
    private Projeto $projetoModel;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Plano();
        $this->projetoModel = new Projeto();
    }

    /**
     * Lista todos os planos
     */
    public function index(): void
    {
        try {
            $pagination = $this->getPagination();
            $planos = $this->model->all($pagination['per_page'], $pagination['offset']);
            $total = $this->model->count();

            Response::paginated($planos, $pagination['page'], $pagination['per_page'], $total);
        } catch (\Exception $e) {
            Response::error('Erro ao listar planos: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Obtém um plano específico
     */
    public function show(int $id): void
    {
        try {
            $plano = $this->model->find($id);

            if (!$plano) {
                Response::error('Plano não encontrado', 404);
            }

            Response::success($plano);
        } catch (\Exception $e) {
            Response::error('Erro ao buscar plano: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Cria um novo plano
     */
    public function store(): void
    {
        try {
            $this->validate(['projeto_id', 'nome']);

            if (!$this->projetoModel->find($this->request('projeto_id'))) {
                Response::error('Projeto não encontrado', 404);
            }

            $id = $this->model->create($this->request());
            $plano = $this->model->find($id);

            Response::success($plano, 'Plano criado com sucesso', 201);
        } catch (\Exception $e) {
            Response::error('Erro ao criar plano: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Atualiza um plano
     */
    public function update(int $id): void
    {
        try {
            if (!$this->model->find($id)) {
                Response::error('Plano não encontrado', 404);
            }

            if ($this->request('projeto_id')) {
                if (!$this->projetoModel->find($this->request('projeto_id'))) {
                    Response::error('Projeto não encontrado', 404);
                }
            }

            $this->model->update($id, $this->request());
            $plano = $this->model->find($id);

            Response::success($plano, 'Plano atualizado com sucesso');
        } catch (\Exception $e) {
            Response::error('Erro ao atualizar plano: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Deleta um plano
     */
    public function destroy(int $id): void
    {
        try {
            if (!$this->model->find($id)) {
                Response::error('Plano não encontrado', 404);
            }

            $this->model->delete($id);
            Response::success(null, 'Plano deletado com sucesso');
        } catch (\Exception $e) {
            Response::error('Erro ao deletar plano: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Lista planos de um projeto
     */
    public function byProjeto(int $projetoId): void
    {
        try {
            if (!$this->projetoModel->find($projetoId)) {
                Response::error('Projeto não encontrado', 404);
            }

            $pagination = $this->getPagination();
            $planos = $this->model->getByProjeto($projetoId, $pagination['per_page'], $pagination['offset']);

            Response::paginated($planos, $pagination['page'], $pagination['per_page'], count($planos));
        } catch (\Exception $e) {
            Response::error('Erro ao listar planos: ' . $e->getMessage(), 500);
        }
    }
}
