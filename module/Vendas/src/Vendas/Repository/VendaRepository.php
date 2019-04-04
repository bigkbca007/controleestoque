<?php

namespace Vendas\Repository;

use Doctrine\ORM\Query\Expr\Join;
use Vendas\Repository\AbstractRepository;
use Zend\Json\Json;

class VendaRepository extends AbstractRepository {

    public function getSelectOptions() {
        $result = $this->findBy(array('stAtivo' => '1'));

        $arrResult = array();
        if ($result) {
            foreach ($result as $item) {
                $arrResult[$item->getCodVenda()] = utf8_encode($item->getNome());
            }
        }

        return $arrResult;
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
    public function getDataGrid($filterRules = null, $orderby = null, $page = null, $rows = null, $tipoListagem = 1) {
        $t1 = 'Venda';
        $t2 = 'Cliente';

        $qb = $this->createQueryBuilder($t1);
        $qb
            ->select("
                        {$t1}.codVenda AS {$t1}_codVenda, 
                        {$t1}.dtVenda AS {$t1}_dtVenda, 
                        {$t1}.dtPagamento AS {$t1}_dtPagamento, 
                        {$t1}.stPago AS {$t1}_stPago, 
                        {$t1}.stAtivo AS {$t1}_stAtivo, 
                        {$t2}.nome AS {$t2}_nome 
                    ")
            ->innerJoin("Vendas\Entity\\$t2", $t2, Join::WITH, "{$t1}.codCliente = {$t2}.codCliente");
            if(2 != $tipoListagem){
                $qb
                    ->where('Venda.stAtivo = :filtroListagem')
                    ->setParameter('filtroListagem', $tipoListagem);
            }

        // Definindo campos de filtro
        if ($filterRules) {
            $rules = Json::decode($filterRules);
            $where = array();
            foreach ($rules as $key => $rule) {
                $field = str_replace('_', '.', $rule->field);
                $where[] = "$field LIKE :field{$key}";
            }
            if(!empty($where)){
                $qb->andWhere(implode(' AND ', $where));
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
        if($page && $rows){
            $qb
                ->setFirstResult(--$page*$rows)
                ->setMaxResults($rows);
        }

        $query = $qb->getQuery();
        $result = $query->getResult();

        return $result;
    }
/*
	public function getProdutosCliente($codVenda){
		$t1 = 'Venda';
		$t2 = 'Produto';
		$t3 = 'Categoria';
		$t4 = 'Fornecedor';
		$t5 = 'VendaHasProduto';

        $qb = $this->createQueryBuilder($t1);
        $qb
            ->select("
                        {$t2}.nome AS {$t2}_nome, 
                        {$t2}.precoVenda AS {$t2}_precoVenda, 
                        {$t2}.descricao AS {$t2}_descricao, 
                        {$t3}.nome AS {$t3}_nome, 
                        {$t4}.nome AS {$t4}_nome
                    ")
            ->innerJoin("Vendas\Entity\\$t2", $t2, Join::WITH, "{$t2}.codProduto = {$t2}.codProduto")
            ->innerJoin("Vendas\Entity\\$t3", $t3, Join::WITH, "{$t2}.codCategoria = {$t3}.codCategoria")
            ->innerJoin("Vendas\Entity\\$t4", $t4, Join::WITH, "{$t2}.codFornecedor = {$t4}.codFornecedor")
            ->innerJoin("Vendas\Entity\\$t5", $t5, Join::WITH, "{$t1}.codVenda = {$t5}.vendaCodVenda")
			->where("{$t5}.vendaCodVenda = {$codVenda}");

		$query = $qb->getQuery();
		echo $query->getSQL();die;
        $result = $query->getResult();
		
		return $result;
	}
	*/
}
