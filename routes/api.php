<?php

use App\Core\Router;

$router = new Router();

// ==================== ROTAS DE CLIENTES ====================
$router->get('/clientes', 'ClienteController', 'index');
$router->get('/clientes/search', 'ClienteController', 'search');
$router->get('/clientes/{id}', 'ClienteController', 'show');
$router->post('/clientes', 'ClienteController', 'store');
$router->put('/clientes/{id}', 'ClienteController', 'update');
$router->delete('/clientes/{id}', 'ClienteController', 'destroy');

// ==================== ROTAS DE PROJETOS ====================
$router->get('/projetos', 'ProjetoController', 'index');
$router->get('/projetos/ativos', 'ProjetoController', 'ativos');
$router->get('/projetos/{id}', 'ProjetoController', 'show');
$router->post('/projetos', 'ProjetoController', 'store');
$router->put('/projetos/{id}', 'ProjetoController', 'update');
$router->delete('/projetos/{id}', 'ProjetoController', 'destroy');

// ==================== ROTAS DE PLANOS ====================
$router->get('/planos', 'PlanoController', 'index');
$router->get('/planos/{id}', 'PlanoController', 'show');
$router->get('/projetos/{projetoId}/planos', 'PlanoController', 'byProjeto');
$router->post('/planos', 'PlanoController', 'store');
$router->put('/planos/{id}', 'PlanoController', 'update');
$router->delete('/planos/{id}', 'PlanoController', 'destroy');

// ==================== ROTAS DE LICENÇAS ====================
$router->get('/licencas', 'LicencaController', 'index');
$router->get('/licencas/ativas', 'LicencaController', 'ativas');
$router->post('/licencas/validar', 'LicencaController', 'validar');
$router->get('/licencas/{id}', 'LicencaController', 'show');
$router->get('/clientes/{clienteId}/licencas', 'LicencaController', 'byCliente');
$router->post('/licencas', 'LicencaController', 'store');
$router->put('/licencas/{id}', 'LicencaController', 'update');
$router->delete('/licencas/{id}', 'LicencaController', 'destroy');

// ==================== ROTAS DE COBRANÇAS ====================
$router->get('/cobrancas', 'CobrancaController', 'index');
$router->get('/cobrancas/pendentes', 'CobrancaController', 'pendentes');
$router->get('/cobrancas/{id}', 'CobrancaController', 'show');
$router->get('/clientes/{clienteId}/cobrancas', 'CobrancaController', 'byCliente');
$router->post('/cobrancas', 'CobrancaController', 'store');
$router->put('/cobrancas/{id}', 'CobrancaController', 'update');
$router->delete('/cobrancas/{id}', 'CobrancaController', 'destroy');

// ==================== ROTAS DE PAGAMENTOS ====================
$router->get('/pagamentos', 'PagamentoController', 'index');
$router->get('/pagamentos/{id}', 'PagamentoController', 'show');
$router->get('/cobrancas/{cobrancaId}/pagamentos', 'PagamentoController', 'byCobranca');
$router->post('/pagamentos', 'PagamentoController', 'store');
$router->put('/pagamentos/{id}', 'PagamentoController', 'update');
$router->delete('/pagamentos/{id}', 'PagamentoController', 'destroy');

// ==================== ROTAS DE NOTIFICAÇÕES ====================
$router->get('/notificacoes', 'NotificacaoController', 'index');
$router->get('/notificacoes/nao-lidas', 'NotificacaoController', 'naoLidas');
$router->get('/notificacoes/{id}', 'NotificacaoController', 'show');
$router->post('/notificacoes', 'NotificacaoController', 'store');
$router->put('/notificacoes/{id}', 'NotificacaoController', 'update');
$router->delete('/notificacoes/{id}', 'NotificacaoController', 'destroy');
$router->put('/notificacoes/{id}/marcar-como-lida', 'NotificacaoController', 'marcarComoLida');

// ==================== ROTAS DE USUÁRIOS ====================
$router->get('/usuarios', 'UsuarioController', 'index');
$router->get('/usuarios/{id}', 'UsuarioController', 'show');
$router->post('/usuarios', 'UsuarioController', 'store');
$router->put('/usuarios/{id}', 'UsuarioController', 'update');
$router->delete('/usuarios/{id}', 'UsuarioController', 'destroy');

// ==================== ROTAS DE HEALTH CHECK ====================
$router->get('/health', 'HealthController', 'check');

return $router;
