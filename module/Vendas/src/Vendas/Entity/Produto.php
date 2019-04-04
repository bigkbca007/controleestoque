<?php

namespace Vendas\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Hydrator\ClassMethods;

/**
 * Produto
 *
 * @ORM\Table(name="produto", indexes={@ORM\Index(name="fk_produto_fornecedor1_idx", columns={"cod_fornecedor"}), @ORM\Index(name="fk_produto_categoria1_idx", columns={"cod_categoria"})})
 * @ORM\Entity(repositoryClass="Vendas\Repository\ProdutoRepository")
 */
class Produto
{
    /**
     * @var int
     *
     * @ORM\Column(name="cod_produto", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $codProduto;

    /**
     * @var string
     *
     * @ORM\Column(name="nome", type="string", length=100, nullable=false)
     */
    private $nome;

    /**
     * @var string|null
     *
     * @ORM\Column(name="descricao", type="text", length=65535, nullable=true)
     */
    private $descricao;

    /**
     * @var float
     *
     * @ORM\Column(name="preco_venda", type="float", precision=10, scale=0, nullable=false)
     */
    private $precoVenda;

    /**
     * @var float|null
     *
     * @ORM\Column(name="preco_fabrica", type="float", precision=10, scale=0, nullable=true)
     */
    private $precoFabrica;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dt_fabricacao", type="date", nullable=true)
     */
    private $dtFabricacao;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dt_validade", type="date", nullable=true)
     */
    private $dtValidade;

    /**
     * @var int
     *
     * @ORM\Column(name="quantidade", type="integer", nullable=false)
     */
    private $quantidade;

    /**
     * @var float|null
     *
     * @ORM\Column(name="desconto", type="float", precision=10, scale=0, nullable=true)
     */
    private $desconto;

    /**
     * @var bool
     *
     * @ORM\Column(name="st_ativo", type="boolean", nullable=false)
     */
    private $stAtivo;

    /**
     * @var int|null
     *
     * @ORM\Column(name="num_dias_alertar_vencimento", type="integer", nullable=true)
     */
    private $numDiasAlertarVencimento;

    /**
     * @var \Vendas\Entity\Categoria
     *
     * @ORM\ManyToOne(targetEntity="Vendas\Entity\Categoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cod_categoria", referencedColumnName="cod_categoria")
     * })
     */
    private $codCategoria;

    /**
     * @var \Vendas\Entity\Fornecedor
     *
     * @ORM\ManyToOne(targetEntity="Vendas\Entity\Fornecedor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cod_fornecedor", referencedColumnName="cod_fornecedor")
     * })
     */
    private $codFornecedor;

    /*
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Vendas\Entity\Venda", mappedBy="produtoCodProduto")
     */
    //private $vendaCodVenda;

    /*
     * Constructor
     */
    /*
    public function __construct()
    {
        $this->vendaCodVenda = new \Doctrine\Common\Collections\ArrayCollection();
    }
    */


    public function __construct($data = array()) {
        $hydrator = new ClassMethods();
        $hydrator->hydrate($data, $this);
    }

    public function toArray(){
        $hydrator = new ClassMethods();
        return $hydrator->extract($this);
    }

	
    /**
     * Set codProduto.
     *
     * @param string $codProduto
     *
     * @return Produto
     */
	 public function setCodProduto($codProduto)
    {
        $this->codProduto = $codProduto;
		
		return $this;
    }

    /**
     * Get codProduto.
     *
     * @return int
     */
    public function getCodProduto()
    {
        return $this->codProduto;
    }

    /**
     * Set nome.
     *
     * @param string $nome
     *
     * @return Produto
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
     * Set descricao.
     *
     * @param string|null $descricao
     *
     * @return Produto
     */
    public function setDescricao($descricao = null)
    {
        $this->descricao = $descricao;

        return $this;
    }

    /**
     * Get descricao.
     *
     * @return string|null
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * Set precoVenda.
     *
     * @param float $precoVenda
     *
     * @return Produto
     */
    public function setPrecoVenda($precoVenda)
    {
        $this->precoVenda = $precoVenda;

        return $this;
    }

    /**
     * Get precoVenda.
     *
     * @return float
     */
    public function getPrecoVenda()
    {
        return $this->precoVenda;
    }

    /**
     * Set precoFabrica.
     *
     * @param float|null $precoFabrica
     *
     * @return Produto
     */
    public function setPrecoFabrica($precoFabrica = null)
    {
        $this->precoFabrica = $precoFabrica;

        return $this;
    }

    /**
     * Get precoFabrica.
     *
     * @return float|null
     */
    public function getPrecoFabrica()
    {
        return $this->precoFabrica;
    }

    /**
     * Set dtFabricacao.
     *
     * @param \DateTime|null $dtFabricacao
     *
     * @return Produto
     */
    public function setDtFabricacao($dtFabricacao = null)
    {
        $this->dtFabricacao = $dtFabricacao;

        return $this;
    }

    /**
     * Get dtFabricacao.
     *
     * @return \DateTime|null
     */
    public function getDtFabricacao()
    {
        return $this->dtFabricacao;
    }

    /**
     * Set dtValidade.
     *
     * @param \DateTime|null $dtValidade
     *
     * @return Produto
     */
    public function setDtValidade($dtValidade = null)
    {
        $this->dtValidade = $dtValidade;

        return $this;
    }

    /**
     * Get dtValidade.
     *
     * @return \DateTime|null
     */
    public function getDtValidade()
    {
        return $this->dtValidade;
    }

    /**
     * Set quantidade.
     *
     * @param int $quantidade
     *
     * @return Produto
     */
    public function setQuantidade($quantidade)
    {
        $this->quantidade = $quantidade;

        return $this;
    }

    /**
     * Get quantidade.
     *
     * @return int
     */
    public function getQuantidade()
    {
        return $this->quantidade;
    }

    /**
     * Set desconto.
     *
     * @param float|null $desconto
     *
     * @return Produto
     */
    public function setDesconto($desconto = null)
    {
        $this->desconto = $desconto;

        return $this;
    }

    /**
     * Get desconto.
     *
     * @return float|null
     */
    public function getDesconto()
    {
        return $this->desconto;
    }

    /**
     * Set stAtivo.
     *
     * @param bool $stAtivo
     *
     * @return Produto
     */
    public function setStAtivo($stAtivo = 1)
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
     * Set numDiasAlertarVencimento.
     *
     * @param int|null $numDiasAlertarVencimento
     *
     * @return Produto
     */
    public function setNumDiasAlertarVencimento($numDiasAlertarVencimento = null)
    {
        $this->numDiasAlertarVencimento = $numDiasAlertarVencimento;

        return $this;
    }

    /**
     * Get numDiasAlertarVencimento.
     *
     * @return int|null
     */
    public function getNumDiasAlertarVencimento()
    {
        return $this->numDiasAlertarVencimento;
    }

    /**
     * Set codCategoria.
     *
     * @param \Vendas\Entity\Categoria|null $codCategoria
     *
     * @return Produto
     */
    public function setCodCategoria(\Vendas\Entity\Categoria $codCategoria = null)
    {
        $this->codCategoria = $codCategoria;

        return $this;
    }

    /**
     * Get codCategoria.
     *
     * @return \Vendas\Entity\Categoria|null
     */
    public function getCodCategoria()
    {
        return $this->codCategoria;
    }

    /**
     * Set codFornecedor.
     *
     * @param \Vendas\Entity\Fornecedor|null $codFornecedor
     *
     * @return Produto
     */
    public function setCodFornecedor(\Vendas\Entity\Fornecedor $codFornecedor = null)
    {
        $this->codFornecedor = $codFornecedor;

        return $this;
    }

    /**
     * Get codFornecedor.
     *
     * @return \Vendas\Entity\Fornecedor|null
     */
    public function getCodFornecedor()
    {
        return $this->codFornecedor;
    }

    /**
     * Add vendaCodVenda.
     *
     * @param \Vendas\Entity\Venda $vendaCodVenda
     *
     * @return Produto
     */
    public function addVendaCodVenda(\Vendas\Entity\Venda $vendaCodVenda)
    {
        $this->vendaCodVenda[] = $vendaCodVenda;

        return $this;
    }

    /**
     * Remove vendaCodVenda.
     *
     * @param \Vendas\Entity\Venda $vendaCodVenda
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeVendaCodVenda(\Vendas\Entity\Venda $vendaCodVenda)
    {
        return $this->vendaCodVenda->removeElement($vendaCodVenda);
    }

    /**
     * Get vendaCodVenda.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVendaCodVenda()
    {
        return $this->vendaCodVenda;
    }
}
