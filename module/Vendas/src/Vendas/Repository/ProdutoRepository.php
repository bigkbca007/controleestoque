<?php

namespace Vendas\Repository;

use Doctrine\ORM\Query\Expr\Join;
use Vendas\Entity\Produto;
use Vendas\Repository\AbstractRepository;
use Zend\Json\Json;

class ProdutoRepository extends AbstractRepository {

    public function getSelectOptions($form = false) {
        $result = $this->findBy(array('stAtivo' => '1'), array('nome' => 'ASC'));
        $arrResult = array();
        if ($result) {
            foreach ($result as $item) {
                if (0 < $item->getQuantidade()) {
                    if ($form) {
                        $arrResult[$item->getCodProduto()] = str_pad($item->getCodProduto(), 4, '0', STR_PAD_LEFT) . ' - ' . utf8_encode($item->getNome()) . ' (' . $item->getQuantidade() . ' em estoque)';
                    } else {
                        $arrResult[] = [
                                    'id' => $item->getCodProduto(),
                                    'text' => str_pad($item->getCodProduto(), 4, '0', STR_PAD_LEFT) . ' - ' . utf8_encode($item->getNome()) . ' (' . $item->getQuantidade() . ' em estoque)',
                        ];
                    }
                }
            }
        }
        return $arrResult;
    }

    /**
     * Fetch data to populate datatables
     * 
     * @param type $order_column
     * @param type $order_dir
     * @param type $search_value
     * @param type $search_column
     * @param type $start
     * @param type $length
     * @return type
     */
    public function getData($order_column = 'produto.nome', $order_dir = 'asc', $search_value = null, $search_column = null, $start = null, $length = null) {
        $qb = $this->createQueryBuilder('produto');
        $qb
                ->select('produto.codProduto, produto.nome AS produtoNome,'
                        . ' categoria.nome AS categoriaNome, fornecedor.nome AS fornecedorNome,'
                        . ' produto.precoVenda, produto.precoFabrica, produto.desconto,'
                        . ' produto.quantidade, produto.dtFabricacao, produto.dtValidade, produto.numDiasAlertarVencimento,'
                        . ' CASE WHEN (produto.codProduto IN(
                                SELECT IDENTITY(vendahasproduto.codProduto)
                                FROM Vendas\Entity\VendaHasProduto AS vendahasproduto
								INNER JOIN Vendas\Entity\Venda venda
									ON vendahasproduto.codVenda = venda.codVenda
                                WHERE vendahasproduto.produtoCodProduto = produto.codProduto AND venda.stAtivo = 1
                            )) THEN 0 ELSE 1 END as podeRemover')
                ->innerJoin('Vendas\Entity\Categoria', 'categoria', Join::WITH, 'produto.codCategoria = categoria.codCategoria')
                ->innerJoin('Vendas\Entity\Fornecedor', 'fornecedor', Join::WITH, 'produto.codFornecedor = fornecedor.codFornecedor')
                ->where('produto.stAtivo = 1');
        //->orderBy($order_column, $order_dir);

        if ($start !== null && $length) {
            $qb
                    ->setMaxResults($length)
                    ->setFirstResult($start);
        }

        if ($search_value && $search_column) {
            $qb
                    ->where("$search_column LIKE :search_value")
                    ->setParameter('search_value', "%$search_value%");
        }

        $query = $qb->getQuery();
        $result = $query->getResult();

        return $result;
    }

    /**
     * Fetch data to populate datagrid
     * 
     * @param type $filterRules
     * @param type $orderby
     * @param type $page
     * @param type $rows
     * @return type
     */
    public function getDataGrid($filterRules = null, $orderby = null, $page = null, $rows = null) {
        $t1 = 'Produto';
        $t2 = 'Categoria';
        $t3 = 'Fornecedor';

        $qb = $this->createQueryBuilder($t1);
        $qb
                ->select("
                        {$t1}.codProduto AS {$t1}_codProduto, 
                        {$t1}.nome AS {$t1}_nome, 
                        {$t1}.precoVenda AS {$t1}_precoVenda, 
                        {$t1}.precoFabrica AS {$t1}_precoFabrica, 
                        {$t1}.desconto AS {$t1}_desconto, 
                        {$t1}.quantidade AS {$t1}_quantidade, 
                        {$t1}.dtFabricacao AS {$t1}_dtFabricacao, 
                        {$t1}.dtValidade AS {$t1}_dtValidade, 
                        {$t1}.numDiasAlertarVencimento AS {$t1}_numDiasAlertarVencimento,                             
                        {$t2}.nome AS {$t2}_nome, 
                        {$t3}.nome AS {$t3}_nome,
                        CASE WHEN (Produto.codProduto IN(
							SELECT IDENTITY(vendahasproduto.produtoCodProduto)
							FROM Vendas\Entity\VendaHasProduto AS vendahasproduto, Vendas\Entity\Venda venda
							WHERE vendahasproduto.vendaCodVenda = venda.codVenda AND venda.stAtivo = 1
                        )) THEN 0 ELSE 1 END as podeRemover
                    ")
                ->innerJoin("Vendas\Entity\\$t2", $t2, Join::WITH, "{$t1}.codCategoria = {$t2}.codCategoria")
                ->innerJoin("Vendas\Entity\\$t3", $t3, Join::WITH, "{$t1}.codFornecedor = {$t3}.codFornecedor");

        // Definindo campos de filtro
        if ($filterRules) {
            $rules = Json::decode($filterRules);
            $where = array();
            foreach ($rules as $key => $rule) {
                $field = str_replace('_', '.', $rule->field);
                $where[] = "$field LIKE :field{$key}";
            }
            if (!empty($where)) {
                $qb->where(implode(' AND ', $where));
            }
        }

        // Atribuindo os parâmetros
        if ($filterRules) {
            $rules = Json::decode($filterRules);
            foreach ($rules as $key => $rule) {
                $qb->setParameter("field{$key}", "%{$rule->value}%");
            }
        }

        // Definindo ordenação
        if ($orderby) {
            $field = str_replace('_', '.', $orderby['sort']);
            $order = $orderby['order'];
            $qb->orderBy($field, $order);
        }

        // Definindo início da lista e o limite de linhas
        if ($page && $rows) {
            $qb
                    ->setFirstResult( --$page * $rows)
                    ->setMaxResults($rows);
        }

        $query = $qb->getQuery();
        $result = $query->getResult();

        return $result;
    }

    /**
     * Método para clonar um produto
     * 
     * @return string
     */
    public function clonar(Produto $produto) {
        $produto->setCodProduto(null);
    }

}
