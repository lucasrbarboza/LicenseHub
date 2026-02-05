
-- =====================================================
-- MySQL Schema converted from Firebird
-- Charset and Engine
-- =====================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';

-- =====================
-- TABLE: CLIENTES
-- =====================
CREATE TABLE clientes (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    razao_social VARCHAR(255) NOT NULL,
    nome_fantasia VARCHAR(255),
    cnpj VARCHAR(18) NOT NULL,
    inscricao_estadual VARCHAR(20),
    email VARCHAR(255),
    telefone VARCHAR(20),
    celular VARCHAR(20),
    endereco VARCHAR(255),
    numero VARCHAR(20),
    complemento VARCHAR(100),
    bairro VARCHAR(100),
    cidade VARCHAR(100),
    estado VARCHAR(2),
    cep VARCHAR(10),
    responsavel_nome VARCHAR(255),
    responsavel_email VARCHAR(255),
    responsavel_telefone VARCHAR(20),
    observacoes TEXT,
    ativo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unq_clientes_cnpj (cnpj),
    INDEX idx_clientes_razao (razao_social)
) ENGINE=InnoDB;

-- =====================
-- TABLE: PROJETOS
-- =====================
CREATE TABLE projetos (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    codigo VARCHAR(50) NOT NULL,
    sigla VARCHAR(10) NOT NULL,
    descricao VARCHAR(500),
    versao_atual VARCHAR(20),
    ativo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unq_projetos_codigo (codigo),
    UNIQUE KEY unq_projetos_sigla (sigla)
) ENGINE=InnoDB;

-- =====================
-- TABLE: PLANOS
-- =====================
CREATE TABLE planos (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    projeto_id BIGINT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    descricao VARCHAR(500),
    valor_mensal DECIMAL(15,2) DEFAULT 0,
    valor_anual DECIMAL(15,2) DEFAULT 0,
    max_usuarios INT,
    max_dispositivos INT,
    recursos JSON,
    ativo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_planos_projeto (projeto_id),
    CONSTRAINT fk_planos_projeto FOREIGN KEY (projeto_id) REFERENCES projetos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================
-- TABLE: PERFIS
-- =====================
CREATE TABLE perfis (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao VARCHAR(255),
    permissoes JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================
-- TABLE: USUARIOS
-- =====================
CREATE TABLE usuarios (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    perfil_id BIGINT,
    ativo TINYINT(1) DEFAULT 1,
    ultimo_acesso TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unq_usuarios_email (email),
    INDEX idx_usuarios_perfil (perfil_id),
    CONSTRAINT fk_usuarios_perfil FOREIGN KEY (perfil_id) REFERENCES perfis(id)
) ENGINE=InnoDB;

-- =====================
-- TABLE: LICENCAS
-- =====================
CREATE TABLE licencas (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    cliente_id BIGINT NOT NULL,
    projeto_id BIGINT NOT NULL,
    plano_id BIGINT NOT NULL,
    codigo_licenca VARCHAR(50) NOT NULL,
    chave_ativacao VARCHAR(255) NOT NULL,
    tipo_cobranca ENUM('MENSAL','TRIMESTRAL','SEMESTRAL','ANUAL') DEFAULT 'MENSAL',
    valor_cobrado DECIMAL(15,2) DEFAULT 0,
    data_inicio DATE,
    data_vencimento DATE,
    data_cancelamento DATE,
    status ENUM('ATIVA','VENCIDA','CANCELADA','SUSPENSA','TRIAL') DEFAULT 'ATIVA',
    renovacao_automatica TINYINT(1) DEFAULT 0,
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unq_licencas_codigo (codigo_licenca),
    UNIQUE KEY unq_licencas_chave (chave_ativacao),
    INDEX idx_licencas_cliente (cliente_id),
    INDEX idx_licencas_projeto (projeto_id),
    INDEX idx_licencas_status (status),
    CONSTRAINT fk_licencas_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    CONSTRAINT fk_licencas_projeto FOREIGN KEY (projeto_id) REFERENCES projetos(id),
    CONSTRAINT fk_licencas_plano FOREIGN KEY (plano_id) REFERENCES planos(id)
) ENGINE=InnoDB;

-- =====================
-- TABLE: COBRANCAS
-- =====================
CREATE TABLE cobrancas (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    licenca_id BIGINT NOT NULL,
    cliente_id BIGINT NOT NULL,
    numero_fatura VARCHAR(50) NOT NULL,
    descricao VARCHAR(500),
    valor DECIMAL(15,2) DEFAULT 0,
    desconto DECIMAL(15,2) DEFAULT 0,
    valor_final DECIMAL(15,2) DEFAULT 0,
    data_vencimento DATE,
    data_pagamento DATE,
    status ENUM('PENDENTE','PAGO','VENCIDO','CANCELADO') DEFAULT 'PENDENTE',
    forma_pagamento ENUM('BOLETO','PIX','CARTAO','TRANSFERENCIA','DINHEIRO'),
    comprovante_url VARCHAR(500),
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unq_cobrancas_fatura (numero_fatura),
    INDEX idx_cobrancas_cliente (cliente_id),
    INDEX idx_cobrancas_status (status),
    CONSTRAINT fk_cobrancas_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    CONSTRAINT fk_cobrancas_licenca FOREIGN KEY (licenca_id) REFERENCES licencas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================
-- TABLE: PAGAMENTOS
-- =====================
CREATE TABLE pagamentos (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    cobranca_id BIGINT NOT NULL,
    valor_pago DECIMAL(15,2) DEFAULT 0,
    data_pagamento TIMESTAMP NOT NULL,
    forma_pagamento ENUM('BOLETO','PIX','CARTAO','TRANSFERENCIA','DINHEIRO') NOT NULL,
    referencia_externa VARCHAR(255),
    comprovante_url VARCHAR(500),
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_pagamentos_cobranca (cobranca_id),
    CONSTRAINT fk_pagamentos_cobranca FOREIGN KEY (cobranca_id) REFERENCES cobrancas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================
-- TABLE: NOTIFICACOES
-- =====================
CREATE TABLE notificacoes (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    licenca_id BIGINT,
    cliente_id BIGINT,
    tipo ENUM('VENCIMENTO_PROXIMO','LICENCA_VENCIDA','PAGAMENTO_RECEBIDO','COBRANCA_VENCIDA','LICENCA_SUSPENSA') NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    mensagem TEXT,
    enviado_email TINYINT(1) DEFAULT 0,
    data_envio_email TIMESTAMP NULL,
    lido TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_notificacoes_tipo (tipo),
    CONSTRAINT fk_notificacoes_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    CONSTRAINT fk_notificacoes_licenca FOREIGN KEY (licenca_id) REFERENCES licencas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================
-- TABLE: HISTORICO_LICENCAS
-- =====================
CREATE TABLE historico_licencas (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    licenca_id BIGINT NOT NULL,
    usuario_id BIGINT,
    acao ENUM('CRIACAO','RENOVACAO','CANCELAMENTO','SUSPENSAO','REATIVACAO','ALTERACAO') NOT NULL,
    status_anterior VARCHAR(20),
    status_novo VARCHAR(20),
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_historico_licenca (licenca_id),
    CONSTRAINT fk_historico_licenca FOREIGN KEY (licenca_id) REFERENCES licencas(id) ON DELETE CASCADE,
    CONSTRAINT fk_historico_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =====================
-- TABLE: VALIDACOES_LICENCA
-- =====================
CREATE TABLE validacoes_licenca (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    licenca_id BIGINT,
    cliente_id BIGINT,
    projeto_id BIGINT,
    cnpj VARCHAR(18),
    sigla VARCHAR(10),
    ip_origem VARCHAR(45),
    status_retornado VARCHAR(20),
    mensagem_retorno VARCHAR(500),
    dados_request JSON,
    dados_response JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_validacoes_cnpj (cnpj),
    INDEX idx_validacoes_data (created_at),
    CONSTRAINT fk_validacoes_licenca FOREIGN KEY (licenca_id) REFERENCES licencas(id) ON DELETE SET NULL,
    CONSTRAINT fk_validacoes_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL,
    CONSTRAINT fk_validacoes_projeto FOREIGN KEY (projeto_id) REFERENCES projetos(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =====================
-- VIEWS
-- =====================
CREATE VIEW vw_cobrancas_pendentes AS
SELECT
    c.id,
    c.numero_fatura,
    c.data_vencimento,
    c.valor_final,
    c.status,
    DATEDIFF(c.data_vencimento, CURDATE()) AS dias_para_vencer,
    cl.razao_social,
    cl.cnpj,
    cl.email,
    l.codigo_licenca,
    p.nome AS projeto_nome
FROM cobrancas c
JOIN licencas l ON l.id = c.licenca_id
JOIN clientes cl ON cl.id = c.cliente_id
JOIN projetos p ON p.id = l.projeto_id
WHERE c.status IN ('PENDENTE','VENCIDO');

CREATE VIEW vw_licencas_completas AS
SELECT
    l.id,
    l.codigo_licenca,
    l.status,
    l.data_inicio,
    l.data_vencimento,
    l.valor_cobrado,
    l.tipo_cobranca,
    c.id AS cliente_id,
    c.razao_social,
    c.cnpj,
    c.email AS cliente_email,
    p.id AS projeto_id,
    p.nome AS projeto_nome,
    p.codigo AS projeto_codigo,
    p.sigla AS projeto_sigla,
    pl.id AS plano_id,
    pl.nome AS plano_nome,
    DATEDIFF(l.data_vencimento, CURDATE()) AS dias_para_vencer
FROM licencas l
JOIN clientes c ON c.id = l.cliente_id
JOIN projetos p ON p.id = l.projeto_id
JOIN planos pl ON pl.id = l.plano_id;
