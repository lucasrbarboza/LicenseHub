<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Models\Licenca;
use App\Models\Cliente;
use App\Models\Projeto;
use App\Models\Plano;

class LicencaController extends Controller
{
    private Licenca $model;
    private Cliente $clienteModel;
    private Projeto $projetoModel;
    private Plano $planoModel;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Licenca();
        $this->clienteModel = new Cliente();
        $this->projetoModel = new Projeto();
        $this->planoModel = new Plano();
    }

    /**
     * Lista todas as licenças
     */
    public function index(): void
    {
        try {
            $pagination = $this->getPagination();
            $licencas = $this->model->all($pagination['per_page'], $pagination['offset']);
            $total = $this->model->count();

            Response::paginated($licencas, $pagination['page'], $pagination['per_page'], $total);
        } catch (\Exception $e) {
            Response::error('Erro ao listar licenças: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Obtém uma licença específica
     */
    public function show(int $id): void
    {
        try {
            $licenca = $this->model->find($id);

            if (!$licenca) {
                Response::error('Licença não encontrada', 404);
            }

            Response::success($licenca);
        } catch (\Exception $e) {
            Response::error('Erro ao buscar licença: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Cria uma nova licença
     */
    public function store(): void
    {
        try {
            $this->validate(['cliente_id', 'projeto_id', 'plano_id', 'codigo_licenca', 'chave_ativacao']);

            if (!$this->clienteModel->find($this->request('cliente_id'))) {
                Response::error('Cliente não encontrado', 404);
            }

            if (!$this->projetoModel->find($this->request('projeto_id'))) {
                Response::error('Projeto não encontrado', 404);
            }

            if (!$this->planoModel->find($this->request('plano_id'))) {
                Response::error('Plano não encontrado', 404);
            }

            if ($this->model->findByCodigo($this->request('codigo_licenca'))) {
                Response::error('Código de licença já existe', 409);
            }

            if ($this->model->findByChave($this->request('chave_ativacao'))) {
                Response::error('Chave de ativação já existe', 409);
            }

            $id = $this->model->create($this->request());
            $licenca = $this->model->find($id);

            Response::success($licenca, 'Licença criada com sucesso', 201);
        } catch (\Exception $e) {
            Response::error('Erro ao criar licença: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Atualiza uma licença
     */
    public function update(int $id): void
    {
        try {
            if (!$this->model->find($id)) {
                Response::error('Licença não encontrado', 404);
            }

            if ($this->request('cliente_id') && !$this->clienteModel->find($this->request('cliente_id'))) {
                Response::error('Cliente não encontrado', 404);
            }

            if ($this->request('projeto_id') && !$this->projetoModel->find($this->request('projeto_id'))) {
                Response::error('Projeto não encontrado', 404);
            }

            if ($this->request('plano_id') && !$this->planoModel->find($this->request('plano_id'))) {
                Response::error('Plano não encontrado', 404);
            }

            $this->model->update($id, $this->request());
            $licenca = $this->model->find($id);

            Response::success($licenca, 'Licença atualizada com sucesso');
        } catch (\Exception $e) {
            Response::error('Erro ao atualizar licença: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Deleta uma licença
     */
    public function destroy(int $id): void
    {
        try {
            if (!$this->model->find($id)) {
                Response::error('Licença não encontrada', 404);
            }

            $this->model->delete($id);
            Response::success(null, 'Licença deletada com sucesso');
        } catch (\Exception $e) {
            Response::error('Erro ao deletar licença: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Lista licenças de um cliente
     */
    public function byCliente(int $clienteId): void
    {
        try {
            if (!$this->clienteModel->find($clienteId)) {
                Response::error('Cliente não encontrado', 404);
            }

            $pagination = $this->getPagination();
            $licencas = $this->model->getByCliente($clienteId, $pagination['per_page'], $pagination['offset']);

            Response::paginated($licencas, $pagination['page'], $pagination['per_page'], count($licencas));
        } catch (\Exception $e) {
            Response::error('Erro ao listar licenças: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Lista licenças ativas
     */
    public function ativas(): void
    {
        try {
            $pagination = $this->getPagination();
            $licencas = $this->model->getAtivas($pagination['per_page'], $pagination['offset']);
            $total = $this->model->countByStatus('ATIVA');

            Response::paginated($licencas, $pagination['page'], $pagination['per_page'], $total);
        } catch (\Exception $e) {
            Response::error('Erro ao listar licenças ativas: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Valida uma licença (API)
     */
    public function validar(): void
    {
        try {
            $codigo = $this->request('codigo_licenca');
            $chave = $this->request('chave_ativacao');

            if (!$codigo && !$chave) {
                Response::error('Código de licença ou chave de ativação é obrigatório', 400);
            }

            $licenca = null;

            if ($codigo) {
                $licenca = $this->model->findByCodigo($codigo);
            } else {
                $licenca = $this->model->findByChave($chave);
            }

            if (!$licenca) {
                Response::error('Licença não encontrada', 404);
            }

            if ($licenca['status'] !== 'ATIVA') {
                Response::error('Licença não está ativa. Status: ' . $licenca['status'], 400);
            }

            if (strtotime($licenca['data_vencimento']) < time()) {
                Response::error('Licença expirada', 400);
            }

            Response::success([
                'id' => $licenca['id'],
                'codigo' => $licenca['codigo_licenca'],
                'status' => $licenca['status'],
                'data_vencimento' => $licenca['data_vencimento'],
                'ativa' => true,
            ], 'Licença válida');
        } catch (\Exception $e) {
            Response::error('Erro ao validar licença: ' . $e->getMessage(), 500);
        }
    }
}
