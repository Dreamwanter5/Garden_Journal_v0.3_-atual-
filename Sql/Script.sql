-- Create and use database
CREATE DATABASE IF NOT EXISTS gardenjournal;
USE gardenjournal;

-- Create base tables
CREATE TABLE Configuracoes (
    idConfiguracoes INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    descricao VARCHAR(50000) NOT NULL
);

CREATE TABLE Usuario (
    id_usuario INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(250) NOT NULL,
    senha VARCHAR(250) NOT NULL,
    email VARCHAR(250) NOT NULL,
    configuracoes INT NOT NULL DEFAULT 1,
    CONSTRAINT fk_usuario_configuracoes 
        FOREIGN KEY (configuracoes) 
        REFERENCES Configuracoes(idConfiguracoes)
);

CREATE TABLE categoria (
    id_categoria INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(250) NOT NULL, 
    data_criacao DATE NOT NULL,
    emoji VARCHAR(45),
    imagem VARCHAR(45),
    id_usuario INT NOT NULL,
    CONSTRAINT fk_categoria_usuario 
        FOREIGN KEY (id_usuario) 
        REFERENCES Usuario(id_usuario)  
);

CREATE TABLE nota (
    nome VARCHAR(45) NOT NULL PRIMARY KEY,
    texto VARCHAR(5000),
    dt DATE,
    id_usuario INT NOT NULL,
    CONSTRAINT fk_nota_usuario 
        FOREIGN KEY (id_usuario) 
        REFERENCES usuario(id_usuario)
);

CREATE TABLE tags (
    idTags INT NOT NULL AUTO_INCREMENT PRIMARY KEY,  
    Descricao VARCHAR(250)
);

CREATE TABLE nota_historico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_nota VARCHAR(45),
    conteudo TEXT,
    data_alteracao DATETIME
);

CREATE TABLE nota_categoria (
    id_nota VARCHAR(45) NOT NULL,
    id_categoria INT NOT NULL,
    PRIMARY KEY (id_nota, id_categoria),
    CONSTRAINT fk_nota_categoria_nota 
        FOREIGN KEY (id_nota) 
        REFERENCES nota(nome) 
        ON DELETE CASCADE,
    CONSTRAINT fk_nota_categoria_categoria 
        FOREIGN KEY (id_categoria) 
        REFERENCES categoria(id_categoria) 
        ON DELETE CASCADE
);

-- Insert default configuration
INSERT INTO Configuracoes (descricao) 
VALUES ('Configuração padrão')
ON DUPLICATE KEY UPDATE descricao = VALUES(descricao);

-- Adiciona coluna descricao (execute uma vez)
ALTER TABLE nota
  ADD COLUMN IF NOT EXISTS descricao VARCHAR(500) DEFAULT NULL;