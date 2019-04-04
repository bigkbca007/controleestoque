<?php

namespace Vendas\Repository;

use Vendas\Repository\AbstractRepository;

class ClienteRepository extends AbstractRepository
{

    public function getSelectOptions() {
        $result = $this->findBy(array('stAtivo' => '1'));

        $arrResult = array();
        if ($result) {
            foreach ($result as $item) {
                $arrResult[$item->getCodCliente()] = utf8_encode($item->getNome());
            }
        }

        return $arrResult;
    }

    public function getData($order_dir = 'asc', $search_value = null, $search_column = null, $start = null, $length = null) {
        $qb = $this->createQueryBuilder('cliente');
        $qb
                ->select('cliente.codCliente, cliente.nome AS clienteNome, cliente.email, '
                        . ' cliente.telefone, cliente.cpf, cliente.rg, cliente.sexo, '
                        . ' CASE WHEN (cliente.codCliente IN(
                                SELECT IDENTITY(venda.codCliente)
                                FROM Vendas\Entity\Venda AS venda
                            )) THEN 0 ELSE 1 END as podeRemover')
                ->where('cliente.stAtivo = 1')
                ->orderBy('cliente.nome', $order_dir);

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