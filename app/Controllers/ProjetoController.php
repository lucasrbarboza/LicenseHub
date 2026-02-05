<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Models\Projeto;

class ProjetoController extends Controller
{
    private Projeto $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Projeto();
    }

    /**
     * Lista todos os projetos
     */
    public function index(): void
    {
        try {
            $pagination = $this->getPagination();
            $projetos = $this->model->all($pagination['per_page'], $pagination['offset']);
            $total = $this->model->count();

            Response::paginated($projetos, $pagination['page'], $pagination['per_page'], $total);
        } catch (\Exception $e) {
            Response::error('Erro ao listar projetos: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Obtém um projeto específico
     */
    public function show(int $id): void
    {
        try {
            $projeto = $this->model->find($id);

            if (!$projeto) {
                Response::error('Projeto não encontrado', 404);
            }

            Response::success($projeto);
        } catch (\Exception $e) {
            Response::error('Erro ao buscar projeto: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Cria um novo projeto
     */
    public function store(): void
    {
        try {
            $this->validate(['nome', 'codigo', 'sigla']);

            if ($this->model->findByCodigo($this->request('codigo'))) {
                Response::error('Código de projeto já existe', 409);
            }

            if ($this->model->findBySigla($this->request('sigla'))) {
                Response::error('Sigla de projeto já existe', 409);
            }

            $id = $this->model->create($this->request());
            $projeto = $this->model->find($id);

            Response::success($projeto, 'Projeto criado com sucesso', 201);
        } catch (\Exception $e) {
            Response::error('Erro ao criar projeto: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Atualiza um projeto
     */
    public function update(int $id): void
    {
        try {
            if (!$this->model->find($id)) {
                Response::error('Projeto não encontrado', 404);
            }

            if ($this->request('codigo')) {
                $existente = $this->model->findByCodigo($this->request('codigo'));
                if ($existente && $existente['id'] != $id) {
                    Response::error('Código já existe', 409);
                }
            }

            if ($this->request('sigla')) {
                $existente = $this->model->findBySigla($this->request('sigla'));
                if ($existente && $existente['id'] != $id) {
                    Response::error('Sigla já existe', 409);
                }
            }

            $this->model->update($id, $this->request());
            $projeto = $this->model->find($id);

            Response::success($projeto, 'Projeto atualizado com sucesso');
        } catch (\Exception $e) {
            Response::error('Erro ao atualizar projeto: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Deleta um projeto
     */
    public function destroy(int $id): void
    {
        try {
            if (!$this->model->find($id)) {
                Response::error('Projeto não encontrado', 404);
            }

            $this->model->delete($id);
            Response::success(null, 'Projeto deletado com sucesso');
        } catch (\Exception $e) {
            Response::error('Erro ao deletar projeto: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Lista projetos ativos
     */
    public function ativos(): void
    {
        try {
            $pagination = $this->getPagination();
            $projetos = $this->model->getAtivos($pagination['per_page'], $pagination['offset']);
            $total = $this->model->countAtivos();

            Response::paginated($projetos, $pagination['page'], $pagination['per_page'], $total);
        } catch (\Exception $e) {
            Response::error('Erro ao listar projetos ativos: ' . $e->getMessage(), 500);
        }
    }
}
