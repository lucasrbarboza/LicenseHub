<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Models\Usuario;
use App\Models\Perfil;

class UsuarioController extends Controller
{
    private Usuario $model;
    private Perfil $perfilModel;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Usuario();
        $this->perfilModel = new Perfil();
    }

    /**
     * Lista todos os usuários
     */
    public function index(): void
    {
        try {
            $pagination = $this->getPagination();
            $usuarios = $this->model->all($pagination['per_page'], $pagination['offset']);
            
            // Remove senhas da resposta
            $usuarios = array_map(fn($u) => $this->removeFieldsSensitivos($u), $usuarios);
            
            $total = $this->model->count();

            Response::paginated($usuarios, $pagination['page'], $pagination['per_page'], $total);
        } catch (\Exception $e) {
            Response::error('Erro ao listar usuários: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Obtém um usuário específico
     */
    public function show(int $id): void
    {
        try {
            $usuario = $this->model->find($id);

            if (!$usuario) {
                Response::error('Usuário não encontrado', 404);
            }

            $usuario = $this->removeFieldsSensitivos($usuario);
            Response::success($usuario);
        } catch (\Exception $e) {
            Response::error('Erro ao buscar usuário: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Cria um novo usuário
     */
    public function store(): void
    {
        try {
            $this->validate(['nome', 'email', 'senha']);

            if ($this->model->findByEmail($this->request('email'))) {
                Response::error('Email já cadastrado', 409);
            }

            if ($this->request('perfil_id') && !$this->perfilModel->find($this->request('perfil_id'))) {
                Response::error('Perfil não encontrado', 404);
            }

            $data = $this->request();
            $data['senha'] = password_hash($data['senha'], PASSWORD_BCRYPT);

            $id = $this->model->create($data);
            $usuario = $this->model->find($id);
            $usuario = $this->removeFieldsSensitivos($usuario);

            Response::success($usuario, 'Usuário criado com sucesso', 201);
        } catch (\Exception $e) {
            Response::error('Erro ao criar usuário: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Atualiza um usuário
     */
    public function update(int $id): void
    {
        try {
            if (!$this->model->find($id)) {
                Response::error('Usuário não encontrado', 404);
            }

            if ($this->request('email')) {
                $existente = $this->model->findByEmail($this->request('email'));
                if ($existente && $existente['id'] != $id) {
                    Response::error('Email já cadastrado', 409);
                }
            }

            if ($this->request('perfil_id') && !$this->perfilModel->find($this->request('perfil_id'))) {
                Response::error('Perfil não encontrado', 404);
            }

            $data = $this->request();
            
            // Se senha foi enviada, hash
            if (!empty($data['senha'])) {
                $data['senha'] = password_hash($data['senha'], PASSWORD_BCRYPT);
            } else {
                unset($data['senha']);
            }

            $this->model->update($id, $data);
            $usuario = $this->model->find($id);
            $usuario = $this->removeFieldsSensitivos($usuario);

            Response::success($usuario, 'Usuário atualizado com sucesso');
        } catch (\Exception $e) {
            Response::error('Erro ao atualizar usuário: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Deleta um usuário
     */
    public function destroy(int $id): void
    {
        try {
            if (!$this->model->find($id)) {
                Response::error('Usuário não encontrado', 404);
            }

            $this->model->delete($id);
            Response::success(null, 'Usuário deletado com sucesso');
        } catch (\Exception $e) {
            Response::error('Erro ao deletar usuário: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove campos sensíveis da resposta
     */
    private function removeFieldsSensitivos(array $usuario): array
    {
        unset($usuario['senha']);
        return $usuario;
    }
}
