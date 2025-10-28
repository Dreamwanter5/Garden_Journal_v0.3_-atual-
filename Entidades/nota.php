<?php
class Nota implements JsonSerializable
{
    private $titulo;
    private $descricao;
    private $conteudo;       // markdown
    private $conteudo_html;  // html gerado (opcional)
    private $id_usuario;
    private $dt;             // DATE string
    private $categorias = []; // array de ids

    public function __construct(
        string $titulo = '',
        string $descricao = '',
        string $conteudo = '',
        int $id_usuario = 0,
        array $categorias = [],
        string $conteudo_html = '',
        string $dt = null
    ) {
        $this->titulo = $titulo;
        $this->descricao = $descricao;
        $this->conteudo = $conteudo;
        $this->conteudo_html = $conteudo_html;
        $this->id_usuario = $id_usuario;
        $this->categorias = $categorias;
        $this->dt = $dt ?? date('Y-m-d');
    }

    public function jsonSerialize(): mixed
    {
        return [
            'titulo' => $this->titulo,
            'descricao' => $this->descricao,
            'conteudo' => $this->conteudo,
            'conteudo_html' => $this->conteudo_html,
            'id_usuario' => $this->id_usuario,
            'dt' => $this->dt,
            'categorias' => $this->categorias
        ];
    }

    public function getTitulo(): string
    {
        return $this->titulo;
    }
    public function getDescricao(): string
    {
        return $this->descricao;
    }
    public function getConteudo(): string
    {
        return $this->conteudo;
    }
    public function getConteudoHtml(): string
    {
        return $this->conteudo_html;
    }
    public function getIdUsuario(): int
    {
        return $this->id_usuario;
    }
    public function getDt(): string
    {
        return $this->dt;
    }
    public function getCategorias(): array
    {
        return $this->categorias;
    }

    public function setCategorias(array $v)
    {
        $this->categorias = $v;
    }
    public function setIdUsuario(int $v)
    {
        $this->id_usuario = $v;
    }
}