<?php

namespace Vendas\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Hydrator\ClassMethods;

/**
 * Venda
 *
 * @ORM\Table(name="venda", indexes={@ORM\Index(name="fk_venda_cliente1_idx", columns={"cod_cliente"})})
 * @ORM\Entity(repositoryClass="Vendas\Repository\VendaRepository")
 */
class Venda
{
    /**
     * @var int
     *
     * @ORM\Column(name="cod_venda", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $codVenda;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dt_venda", type="datetime", nullable=true)
     */
    private $dtVenda;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dt_pagamento", type="datetime", nullable=true)
     */
    private $dtPagamento;

    /**
     * @var bool
     *
     * @ORM\Column(name="st_pago", type="boolean", nullable=false)
     */
    private $stPago = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="st_ativo", type="boolean", nullable=false, options={"default"="1"})
     */
    private $stAtivo = '1';

    /**
     * @var \Vendas\Entity\Cliente
     *
     * @ORM\ManyToOne(targetEntity="Vendas\Entity\Cliente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cod_cliente", referencedColumnName="cod_cliente")
     * })
     */
    private $codCliente;

    /*
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Vendas\Entity\Produto", inversedBy="vendaCodVenda")
     * @ORM\JoinTable(name="venda_has_produto",
     *   joinColumns={
     *     @ORM\JoinColumn(name="venda_cod_venda", referencedColumnName="cod_venda")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="produto_cod_produto", referencedColumnName="cod_produto")
     *   }
     * )
     */
    //private $produtoCodProduto;

    /*
     * Constructor
     */
//    public function __construct()
//    {
//        $this->produtoCodProduto = new \Doctrine\Common\Collections\ArrayCollection();
//    }

    public function __construct($data = array()) {
        $hydrator = new ClassMethods();
        $hydrator->hydrate($data, $this);
    }

    public function toArray(){
        $hydrator = new ClassMethods();
        return $hydrator->extract($this);
    }

    /**
     * Get codVenda.
     *
     * @return int
     */
    public function getCodVenda()
    {
        return $this->codVenda;
    }

    /**
     * Set dtVenda.
     *
     * @param \DateTime|null $dtVenda
     *
     * @return Venda
     */
    public function setDtVenda($dtVenda = null)
    {
        $this->dtVenda = $dtVenda;

        return $this;
    }

    /**
     * Get dtVenda.
     *
     * @return \DateTime|null
     */
    public function getDtVenda()
    {
        return $this->dtVenda;
    }

    /**
     * Set dtPagamento.
     *
     * @param \DateTime|null $dtPagamento
     *
     * @return Venda
     */
    public function setDtPagamento($dtPagamento = null)
    {
        $this->dtPagamento = $dtPagamento;

        return $this;
    }

    /**
     * Get dtPagamento.
     *
     * @return \DateTime|null
     */
    public function getDtPagamento()
    {
        return $this->dtPagamento;
    }

    /**
     * Set stPago.
     *
     * @param bool $stPago
     *
     * @return Venda
     */
    public function setStPago($stPago)
    {
        $this->stPago = $stPago;

        return $this;
    }

    /**
     * Get stPago.
     *
     * @return bool
     */
    public function getStPago()
    {
        return $this->stPago;
    }

    /**
     * Set stAtivo.
     *
     * @param bool $stAtivo
     *
     * @return Venda
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

    /**
     * Set codCliente.
     *
     * @param \Vendas\Entity\Cliente|null $codCliente
     *
     * @return Venda
     */
    public function setCodCliente(\Vendas\Entity\Cliente $codCliente = null)
    {
        $this->codCliente = $codCliente;

        return $this;
    }

    /**
     * Get codCliente.
     *
     * @return \Vendas\Entity\Cliente|null
     */
    public function getCodCliente()
    {
        return $this->codCliente;
    }

    /**
     * Add produtoCodProduto.
     *
     * @param \Vendas\Entity\Produto $produtoCodProduto
     *
     * @return Venda
     */
    public function addProdutoCodProduto(\Vendas\Entity\Produto $produtoCodProduto)
    {
        $this->produtoCodProduto[] = $produtoCodProduto;

        return $this;
    }

    /**
     * Remove produtoCodProduto.
     *
     * @param \Vendas\Entity\Produto $produtoCodProduto
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeProdutoCodProduto(\Vendas\Entity\Produto $produtoCodProduto)
    {
        return $this->produtoCodProduto->removeElement($produtoCodProduto);
    }

    /**
     * Get produtoCodProduto.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProdutoCodProduto()
    {
        return $this->produtoCodProduto;
    }
}
