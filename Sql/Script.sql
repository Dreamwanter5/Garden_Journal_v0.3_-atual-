create database gardenjournal;
Use gardenjournal;

Create table Configuracoes(
    idConfiguracoes int not null auto_increment primary key,
    descricao varchar(50000) not null
);

create table Usuario (
    id_usuario int not null auto_increment primary key,
    nome varchar (250) not null,
    senha varchar (250) not null,
    email varchar (250) not null,
    configuracoes int not null,
    constraint fk_usuario_configuracoes foreign key (configuracoes) references Configuracoes(idConfiguracoes)
);

create table acessoLogin (
    idAcessoLogin int not null auto_increment primary key,
    dt DATE,
    hora Time,
    id_usuario int not null,
    constraint fk_acessoLogin_usuario foreign key (id_usuario) references Usuario(id_usuario)  
);

Create table categoria (
    id_categoria int not null auto_increment primary key,
    nome varchar (250) not null, 
    data_criacao date not null,
    emoji varchar (45),
    imagem varchar (45),
    id_usuario int not null,
    constraint fk_categoria_usuario foreign key (id_usuario) references Usuario(id_usuario)  
);

create table nota (
    nome Varchar(45) not null primary key,
    texto varchar (5000),
    dt date,
    id_categoria int not null,
    constraint fk_nota_categoria foreign key (id_categoria) references Categoria(id_categoria)  
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
    id_nota VARCHAR(45) NOT NULL,
    id_categoria INT NOT NULL,
    PRIMARY KEY (id_nota, id_categoria),
    CONSTRAINT fk_nota_categoria_nota FOREIGN KEY (id_nota) REFERENCES nota(nome) ON DELETE CASCADE,
    CONSTRAINT fk_nota_categoria_categoria FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria) ON DELETE CASCADE
);