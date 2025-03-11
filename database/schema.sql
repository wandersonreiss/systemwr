DROP TABLE IF EXISTS servicos;
DROP TABLE IF EXISTS ordem_servico;

CREATE TABLE ordem_servico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    tipo_aparelho VARCHAR(100),
    marca VARCHAR(100),
    modelo VARCHAR(100),
    numero_serie VARCHAR(100),
    descricao TEXT,
    status ENUM('Aguardando', 'Em Andamento', 'Concluído', 'Cancelado') DEFAULT 'Aguardando',
    data_entrada DATE,
    data_previsao DATE,
    data_conclusao DATE,
    valor DECIMAL(10,2),
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);