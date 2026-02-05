<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Models\Notificacao;
use App\Models\Cliente;
use App\Models\Licenca;

class NotificacaoController extends Controller
{
    private Notificacao $model;
    private Cliente $clienteModel;
    private Licenca $licencaModel;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Notificacao();
        $this->clienteModel = new Cliente();
        $this->licencaModel = new Licenca();
    }

    /**
     * Lista todas as notificações
     */
    public function index(): void
    {
        try {
            $pagination = $this->getPagination();
            $notificacoes = $this->model->all($pagination['per_page'], $pagination['offset']);
            $total = $this->model->count();

            Response::paginated($notificacoes, $pagination['page'], $pagination['per_page'], $total);
        } catch (\Exception $e) {
            Response::error('Erro ao listar notificações: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Obtém uma notificação específica
     */
    public function show(int $id): void
    {
        try {
            $notificacao = $this->model->find($id);

            if (!$notificacao) {
                Response::error('Notificação não encontrada', 404);
            }

            Response::success($notificacao);
        } catch (\Exception $e) {
            Response::error('Erro ao buscar notificação: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Cria uma nova notificação
     */
    public function store(): void
    {
        try {
            $this->validate(['cliente_id', 'tipo', 'titulo']);

            if (!$this->clienteModel->find($this->request('cliente_id'))) {
                Response::error('Cliente não encontrado', 404);
            }

            if ($this->request('licenca_id') && !$this->licencaModel->find($this->request('licenca_id'))) {
                Response::error('Licença não encontrada', 404);
            }

            $id = $this->model->create($this->request());
            $notificacao = $this->model->find($id);

            Response::success($notificacao, 'Notificação criada com sucesso', 201);
        } catch (\Exception $e) {
            Response::error('Erro ao criar notificação: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Atualiza uma notificação
     */
    public function update(int $id): void
    {
        try {
            if (!$this->model->find($id)) {
                Response::error('Notificação não encontrada', 404);
            }

            if ($this->request('cliente_id') && !$this->clienteModel->find($this->request('cliente_id'))) {
                Response::error('Cliente não encontrado', 404);
            }

            $this->model->update($id, $this->request());
            $notificacao = $this->model->find($id);

            Response::success($notificacao, 'Notificação atualizada com sucesso');
        } catch (\Exception $e) {
            Response::error('Erro ao atualizar notificação: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Deleta uma notificação
     */
    public function destroy(int $id): void
    {
        try {
            if (!$this->model->find($id)) {
                Response::error('Notificação não encontrada', 404);
            }

            $this->model->delete($id);
            Response::success(null, 'Notificação deletada com sucesso');
        } catch (\Exception $e) {
            Response::error('Erro ao deletar notificação: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Lista notificações não lidas
     */
    public function naoLidas(): void
    {
        try {
            $pagination = $this->getPagination();
            $notificacoes = $this->model->getNaoLidas($pagination['per_page'], $pagination['offset']);
            $total = $this->model->countNaoLidas();

            Response::paginated($notificacoes, $pagination['page'], $pagination['per_page'], $total);
        } catch (\Exception $e) {
            Response::error('Erro ao listar notificações: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Marca notificação como lida
     */
    public function marcarComoLida(int $id): void
    {
        try {
            if (!$this->model->find($id)) {
                Response::error('Notificação não encontrada', 404);
            }

            $this->model->marcarComoLida($id);
            $notificacao = $this->model->find($id);

            Response::success($notificacao, 'Notificação marcada como lida');
        } catch (\Exception $e) {
            Response::error('Erro ao atualizar notificação: ' . $e->getMessage(), 500);
        }
    }
}
