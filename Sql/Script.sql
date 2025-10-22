create database gardenjournal;
Use gardenjournal;

Create table Configuracoes(
    idConfiguracoes int not null auto_increment primary key,
    descricao varchar(50000) not null
);

CREATE TABLE usuario (
    id_usuario INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(250) NOT NULL,
    senha VARCHAR(250) NOT NULL,
    email VARCHAR(250) NOT NULL UNIQUE
);

create table acessoLogin (
    idAcessoLogin int not null auto_increment primary key,
    dt DATE,
    hora Time,
    id_usuario int not null,
    constraint fk_acessoLogin_usuario foreign key (id_usuario) references Usuario(id_usuario)  
);

CREATE TABLE categoria (
    id_categoria INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(250) NOT NULL,
    data_criacao DATE NOT NULL,
    id_usuario INT NOT NULL,
    CONSTRAINT fk_categoria_usuario FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);

CREATE TABLE nota (
    id_nota INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(250) NOT NULL,
    conteudo_markdown TEXT,
    conteudo_html TEXT,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    id_usuario INT NOT NULL,
    CONSTRAINT fk_nota_usuario FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);

create table tags (
    idTags int not null auto_increment primary key,  
    Descricao varchar (250)
);
select * from usuario;

CREATE TABLE nota_historico (
     id INT AUTO_INCREMENT PRIMARY KEY,
     id_nota VARCHAR(45),
     conteudo TEXT,
     data_alteracao DATETIME
);

CREATE TABLE nota_categoria (
    id_nota INT NOT NULL,
    id_categoria INT NOT NULL,
    PRIMARY KEY (id_nota, id_categoria),
    CONSTRAINT fk_notacategoria_nota FOREIGN KEY (id_nota) REFERENCES nota(id_nota) ON DELETE CASCADE,
    CONSTRAINT fk_notacategoria_categoria FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria) ON DELETE CASCADE
);