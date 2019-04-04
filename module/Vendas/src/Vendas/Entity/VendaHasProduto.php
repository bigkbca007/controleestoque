<?php

namespace Vendas\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Hydrator\ClassMethods;

/**
 * Description of VendaHasProduto
 *
 * @ORM\Table(name="venda_has_produto", indexes={@ORM\Index(name="fk_venda_has_produto_produto1", columns={"produto_cod_poduto"}), @ORM\Index(name="fk_venda_has_produto_venda1", columns={"venda_cod_venda"})})
 * @ORM\Entity
 */
class VendaHasProduto {

    /**
     * @var Vendas\Entity\Produto
     * 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Vendas\Entity\Produto")
     * @ORM\JoinColumns({
     *    @ORM\JoinColumn(name="produto_cod_produto", referencedColumnName="cod_produto")
     * })
     */
    private $produtoCodProduto;

    /**
     * @var \Vendas\Entity\Venda
     * 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Vendas\Entity\Venda")
     * @ORM\JoinColumns({
     *    @ORM\JoinColumn(name="venda_cod_venda", referencedColumnName="cod_venda")
     * })
     */
    private $vendaCodVenda;

    /**
     * @var float
     * @ORM\Column(name="preco_pago", type="float", nullable=false)
     */
    private $precoPago;

    /**
     * @var integer
     * @ORM\Column(name="quantidade", type="integer", nullable=false)
     */
    private $quantidade;

    /**
     * @var integer 
     * @ORM\Column(name="desconto", type="integer", nullable=true)
     */
    private $desconto;

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

    public function toArray() {
        $hydrator = new ClassMethods();
        return $hydrator->extract($this);
    }

    public function getProdutoCodProduto() {
        return $this->produtoCodProduto;
    }

    public function getVendaCodVenda() {
        return $this->vendaCodVenda;
    }

    public function getPrecoPago() {
        return $this->precoPago;
    }

    public function getQuantidade() {
        return $this->quantidade;
    }

    public function getDesconto() {
        return $this->desconto;
    }

    public function setProdutoCodProduto(\Vendas\Entity\Produto $produtoCodProduto) {
        $this->produtoCodProduto = $produtoCodProduto;
        return $this;
    }

    public function setVendaCodVenda(\Vendas\Entity\Venda $vendaCodVenda) {
        $this->vendaCodVenda = $vendaCodVenda;
        return $this;
    }

    public function setPrecoPago($precoPago) {
        $this->precoPago = $precoPago;
        return $this;
    }

    public function setQuantidade($quantidade) {
        $this->quantidade = $quantidade;
        return $this;
    }

    public function setDesconto($desconto) {
        $this->desconto = $desconto;
        return $this;
    }

    /**
     * Set stAtivo.
     *
     * @param bool $stAtivo
     *
     * @return Venda
     */
    public function setStAtivo($stAtivo) {
        $this->stAtivo = $stAtivo;

        return $this;
    }

    /**
     * Get stAtivo.
     *
     * @return bool
     */
    public function getStAtivo() {
        return $this->stAtivo;
    }

}
