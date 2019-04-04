<?php

namespace Vendas\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Hydrator\ClassMethods;

/**
 * Fornecedor
 *
 * @ORM\Table(name="fornecedor")
 * @ORM\Entity(repositoryClass="Vendas\Repository\FornecedorRepository")
 */
class Fornecedor
{
    /**
     * @var int
     *
     * @ORM\Column(name="cod_fornecedor", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $codFornecedor;

    /**
     * @var string
     *
     * @ORM\Column(name="cnpj", type="string", length=20, nullable=false)
     */
    private $cnpj;

    /**
     * @var string
     *
     * @ORM\Column(name="nome", type="string", length=150, nullable=false)
     */
    private $nome;

    /**
     * @var string|null
     *
     * @ORM\Column(name="telefone", type="string", length=15, nullable=true)
     */
    private $telefone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=100, nullable=true)
     */
    private $email;

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
     * Get codFornecedor.
     *
     * @return int
     */
    public function getCodFornecedor()
    {
        return $this->codFornecedor;
    }

    /**
     * Set cnpj.
     *
     * @param string $cnpj
     *
     * @return Fornecedor
     */
    public function setCnpj($cnpj)
    {
        $this->cnpj = $cnpj;

        return $this;
    }

    /**
     * Get cnpj.
     *
     * @return string
     */
    public function getCnpj()
    {
        return $this->cnpj;
    }

    /**
     * Set nome.
     *
     * @param string $nome
     *
     * @return Fornecedor
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
     * Set telefone.
     *
     * @param string|null $telefone
     *
     * @return Fornecedor
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
     * Set email.
     *
     * @param string|null $email
     *
     * @return Fornecedor
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
     * Set stAtivo.
     *
     * @param bool $stAtivo
     *
     * @return Fornecedor
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
