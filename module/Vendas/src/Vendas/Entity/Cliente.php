<?php

namespace Vendas\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Hydrator\ClassMethods;

/**
 * Cliente
 *
 * @ORM\Table(name="cliente")
 * @ORM\Entity(repositoryClass="Vendas\Repository\ClienteRepository")
 */
class Cliente
{
    /**
     * @var int
     *
     * @ORM\Column(name="cod_cliente", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $codCliente;

    /**
     * @var string
     *
     * @ORM\Column(name="nome", type="string", length=200, nullable=false)
     */
    private $nome;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=100, nullable=true)
     */
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(name="telefone", type="string", length=15, nullable=true)
     */
    private $telefone;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt_cadastro", type="datetime", nullable=false)
     */
    private $dtCadastro;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cpf", type="string", length=15, nullable=true)
     */
    private $cpf;

    /**
     * @var string|null
     *
     * @ORM\Column(name="rg", type="string", length=12, nullable=true)
     */
    private $rg;

    /**
     * @var string|null
     *
     * @ORM\Column(name="sexo", type="string", length=1, nullable=true)
     */
    private $sexo;

    /**
     * @var bool
     *
     * @ORM\Column(name="st_ativo", type="boolean", nullable=false, options={"default"="1"})
     */
    private $stAtivo = '1';


    public function __construct($data = array()) {
        $hydrator = new ClassMethods();
        $hydrator->hydrate($data, $this);
    }

    public function toArray(){
        $hydrator = new ClassMethods();
        return $hydrator->extract($this);
    }

    /**
     * Get codCliente.
     *
     * @return int
     */
    public function getCodCliente()
    {
        return $this->codCliente;
    }

    /**
     * Set nome.
     *
     * @param string $nome
     *
     * @return Cliente
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get nome.
     *
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set email.
     *
     * @param string|null $email
     *
     * @return Cliente
     */
    public function setEmail($email = null)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string|null
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set telefone.
     *
     * @param string|null $telefone
     *
     * @return Cliente
     */
    public function setTelefone($telefone = null)
    {
        $this->telefone = $telefone;

        return $this;
    }

    /**
     * Get telefone.
     *
     * @return string|null
     */
    public function getTelefone()
    {
        return $this->telefone;
    }

    /**
     * Set dtCadastro.
     *
     * @param \DateTime $dtCadastro
     *
     * @return Cliente
     */
    public function setDtCadastro($dtCadastro)
    {
        $this->dtCadastro = $dtCadastro;

        return $this;
    }

    /**
     * Get dtCadastro.
     *
     * @return \DateTime
     */
    public function getDtCadastro()
    {
        return $this->dtCadastro;
    }

    /**
     * Set cpf.
     *
     * @param string|null $cpf
     *
     * @return Cliente
     */
    public function setCpf($cpf = null)
    {
        $this->cpf = $cpf;

        return $this;
    }

    /**
     * Get cpf.
     *
     * @return string|null
     */
    public function getCpf()
    {
        return $this->cpf;
    }

    /**
     * Set rg.
     *
     * @param string|null $rg
     *
     * @return Cliente
     */
    public function setRg($rg = null)
    {
        $this->rg = $rg;

        return $this;
    }

    /**
     * Get rg.
     *
     * @return string|null
     */
    public function getRg()
    {
        return $this->rg;
    }

    /**
     * Set sexo.
     *
     * @param string|null $sexo
     *
     * @return Cliente
     */
    public function setSexo($sexo = null)
    {
        $this->sexo = $sexo;

        return $this;
    }

    /**
     * Get sexo.
     *
     * @return string|null
     */
    public function getSexo()
    {
        return $this->sexo;
    }

    /**
     * Set stAtivo.
     *
     * @param bool $stAtivo
     *
     * @return Cliente
     */
    public function setStAtivo($stAtivo)
    {
        $this->stAtivo = $stAtivo;

        return $this;
    }

    /**
     * Get stAtivo.
     *
     * @return bool
     */
    public function getStAtivo()
    {
        return $this->stAtivo;
    }
}
