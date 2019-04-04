<?php

namespace Vendas\Repository;

use Vendas\Repository\AbstractRepository;

class FornecedorRepository extends AbstractRepository
{

    public function getSelectOptions() {
        $result = $this->findBy(array('stAtivo' => '1'), array('nome'=>'ASC'));
        $arrResult = array();
        if ($result) {
            foreach ($result as $item) {
                $arrResult[$item->getCodFornecedor()] = utf8_encode($item->getNome());
            }
        }

        return $arrResult;
    }

    public function getData($order_dir = 'asc', $search_value = null, $search_column = null, $start = null, $length = null) {
        $qb = $this->createQueryBuilder('fornecedor');
        $qb
                ->select('fornecedor.codFornecedor, fornecedor.cnpj, fornecedor.nome, '
                        . ' fornecedor.email, fornecedor.telefone, fornecedor.cnpj, '
                        . ' CASE WHEN (fornecedor.codFornecedor IN(
                                SELECT IDENTITY(produto.codFornecedor)
                                FROM Vendas\Entity\Produto AS produto
                            )) THEN 0 ELSE 1 END as podeRemover')
                ->where('fornecedor.stAtivo = 1')
                ->orderBy('fornecedor.nome', $order_dir);

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
}
