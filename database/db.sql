CREATE DATABASE wr_eletronica;
USE wr_eletronica;

CREATE TABLE clientes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    endereco TEXT NOT NULL,
    email VARCHAR(100),
    cpf VARCHAR(14),
    observacoes TEXT,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE servicos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cliente_id INT,
    tipo_aparelho VARCHAR(100) NOT NULL,
    marca VARCHAR(50),
    modelo VARCHAR(50),
    numero_serie VARCHAR(50),
    descricao_problema TEXT NOT NULL,
    diagnostico TEXT,
    solucao TEXT,
    valor DECIMAL(10,2),
    status ENUM('pendente', 'em_andamento', 'concluido', 'abandonado') DEFAULT 'pendente',
    data_entrada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    prazo_entrega DATE NOT NULL,
    data_conclusao DATE,
    tecnico VARCHAR(100),
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);

CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    nome VARCHAR(100) NOT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Criar um usuário administrador padrão (senha: admin123)
INSERT INTO usuarios (usuario, senha, nome) VALUES 
('admin', '$2y$10$8TqHGqjhGRFHcQyPxnR9/uXuYaAcRXAB0lERoiwqU0/0ckZYYhni6', 'Administrador');