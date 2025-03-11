ALTER TABLE ordem_servico
ADD COLUMN tipo_aparelho VARCHAR(100) AFTER cliente_id,
ADD COLUMN marca VARCHAR(100) AFTER tipo_aparelho,
ADD COLUMN modelo VARCHAR(100) AFTER marca,
ADD COLUMN numero_serie VARCHAR(100) AFTER modelo,
ADD COLUMN observacoes TEXT AFTER defeito_relatado;