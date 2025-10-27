<?php
class Usuario implements JsonSerializable
{
    private $nome;
    private $email;
    private $senha;
    private $id_usuario;

    public function jsonSerialize(): mixed
    {
        return [
            'id_usuario' => $this->id_usuario,
            'nome' => $this->nome,
            'email' => $this->email,
        ];
    }

    function __construct(
        $nome,
        $email,
        $senha,
        $id_usuario = 0
    ) {
        $this->nome = $nome;
        $this->email = $email;
        $this->senha = $senha;
        $this->id_usuario = $id_usuario;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getSenha()
    {
        return $this->senha;
    }

    public function setSenha($senha)
    {
        $this->senha = $senha;
    }

    // original method kept for compatibility
    public function getid_usuario()
    {
        return $this->id_usuario;
    }

    public function setid_usuario($id_usuario)
    {
        $this->id_usuario = $id_usuario;
    }

    // added camelCase aliases to avoid issues with different call styles
    public function getIdUsuario()
    {
        return $this->getid_usuario();
    }

    public function setIdUsuario($id_usuario)
    {
        $this->setid_usuario($id_usuario);
    }
}
