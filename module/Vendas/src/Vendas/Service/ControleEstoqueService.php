<?php

namespace Vendas\Service;

use Application\Controller\AbstractController;
use DateTime;
use DateTimeZone;
use Exception;
use Vendas\Entity\Produto;
use Vendas\Entity\Venda;
use Vendas\Entity\VendaHasProduto;
use Vendas\Service\AbstractService;

class ControleEstoqueService extends AbstractService {

    const TIMEZONE = 'America/Bahia';

    public function salvarVenda($data, $entityClass) {

        $vendaProdutos = [];
        $i = 1;
        $sair = false;

        do {
            // Se houver codProduto-x, então há precoPago-x e quantidade-x. Pode ou não haver desconto-x
            if(isset($data['codProduto-'.$i])){
                $p = $this->getEntityManagerReference('Vendas\Entity\Produto', $data['codProduto-'.$i]);
                $vendaProdutos[$i]['produtoCodProduto'] = $this->getEntityManagerReference('Vendas\Entity\Produto', $data['codProduto-'.$i]);
                $vendaProdutos[$i]['precoPago'] =  str_replace('.', '', $data['precoPago-'.$i]);
                $vendaProdutos[$i]['precoPago'] =  str_replace(',', '.', $data['precoPago-'.$i]);
                $vendaProdutos[$i]['quantidade'] =  $data['quantidade-'.$i];
                if(isset($data['desconto-'.$i]) && !empty($data['desconto-'.$i])){
                    $vendaProdutos[$i]['desconto'] =  $data['desconto-'.$i];                    
                }
            } else {
                $sair = true;
            }
            ++$i;
        } while (!$sair);

        // Pegando os dados da venda
        $vendaArray = [];

        // Data da venda
        $dtVenda = AbstractController::formatarData($data['dtVenda'], 'Y-m-d h:i:s');
        $dt = new DateTime($dtVenda, new DateTimeZone(self::TIMEZONE));
        $vendaArray['dtVenda'] = $dt;

        // Data do pagamento
        if (!empty($data['dtPagamento'])) {
            $dtPagamento = AbstractController::formatarData($data['dtPagamento'], 'Y-m-d');
            $dt = new DateTime($dtPagamento, new DateTimeZone(self::TIMEZONE));
            $vendaArray['dtPagamento'] = $dt;
        } else {
            $vendaArray['dtPagamento'] = null;
        }

        // Cliente
        $vendaArray['codCliente'] = $this->getEntityManagerReference('Vendas\Entity\Cliente', $data['codCliente']);

        // Pago?
        $vendaArray['stPago'] = $data['stPago'];

        $em = $this->entitymanager;
        try{
            $em->beginTransaction();
            // Salvando a venda

            if (0 == $data['id']) {
                
                $venda = new $entityClass($vendaArray);
                $this->entitymanager->persist($venda);
                $this->entitymanager->flush($venda);

                //Atualizando as quantidades dos produtos. Se não houver quantidade no estoque então não atualiza.
                foreach($vendaProdutos as $key => $vp){
                    // Atualiza quantidade na tb produtos no banco quantidade de produtos na venda
                    $quantidade = $vp['produtoCodProduto']->getQuantidade() - $vp['quantidade'];
                    if($quantidade < 0){
                        throw new Exception('A qauantidade de produtos é maior do que a disponível.<br>Produto: '.$vp['produtoCodProduto']->getNome().'<br> Quantidade no estoque: '.$vp['produtoCodProduto']->getQuantidade());
                    }
                    $vp['produtoCodProduto']->setQuantidade($quantidade);
                    $em->persist($vp['produtoCodProduto']);
                    $em->flush($vp['produtoCodProduto']);

                    $vp['vendaCodVenda'] = $venda;
                    $produtoHasVenda = new VendaHasProduto($vp);

                    $em->persist($produtoHasVenda);
                    $em->flush($produtoHasVenda);
                }

            } else {
                $venda = $this->update($vendaArray, $entityClass, $data['codVenda']);
            }

            $em->commit();

            return ['error' => false];
        } catch (Exception $e){
            $em->rollback();     
            return array(
                'error' => true,
                'msg' => $e->getMessage(),
            );
        }
    }

    public function salvarProduto($data, $entityClass) {

        if('UTF-8' == mb_detect_encoding($data['nome'])){
            $data['nome'] = utf8_decode($data['nome']);
        }
        if('UTF-8' == mb_detect_encoding($data['descricao'])){
            $data['descricao'] = utf8_decode($data['descricao']);
        }
        $data['codCategoria'] = $this->getEntityManagerReference('Vendas\Entity\Categoria', $data['codCategoria']);
        $data['codFornecedor'] = $this->getEntityManagerReference('Vendas\Entity\Fornecedor', $data['codFornecedor']);

        if (!empty($data['dtFabricacao'])) {
            if(!($data['dtFabricacao'] instanceof DateTime)){
                $data['dtFabricacao'] = AbstractController::formatarData($data['dtFabricacao'], 'Y-m-d');
                $dt = new DateTime($data['dtFabricacao'], new DateTimeZone(self::TIMEZONE));
                $data['dtFabricacao'] = $dt;
            }
        } else {
            $data['dtFabricacao'] = null;
        }
        if (!empty($data['dtValidade'])) {
            if(!($data['dtValidade'] instanceof DateTime)){
                $data['dtValidade'] = AbstractController::formatarData($data['dtValidade'], 'Y-m-d');
                $dt = new DateTime($data['dtValidade'], new DateTimeZone(self::TIMEZONE));
                $data['dtValidade'] = $dt;
            }
        } else {
            $data['dtValidade'] = null;
        }

        $data['precoVenda'] =  str_replace(',', '.', $data['precoVenda']);

        $data['stAtivo'] =  1;
        
        if (0 == $data['id']) {
            return $this->insert($data, $entityClass);
        } else {
            return $this->update($data, $entityClass, $data['codProduto']);
        }
    }

    public function salvarCategoria($data, $entityClass) {
        if('UTF-8' == mb_detect_encoding($data['nome'])){
            $data['nome'] = utf8_decode($data['nome']);
        }

        if (0 == $data['id']) {
            return $this->insert($data, $entityClass);
        } else {
            return $this->update($data, $entityClass, $data['codCategoria']);
        }
    }

    /**
     * Método para salvar cliente
     * 
     * @param type $data
     * @param type $entityClass
     */
    public function salvarCliente($data, $entityClass) {
        //Removendo caracteres de máscara para telefone
        if('UTF-8' == mb_detect_encoding($data['nome'])){
            $data['nome'] = utf8_decode($data['nome']);
        }

        if (!empty($data['telefone'])) {
            $data['telefone'] = str_replace(array('(', ')', '-', ' '), '', $data['telefone']);
        } else {
            $data['telefone'] = null;
        }

        //Removendo caracteres de máscara para cpf
        if (!empty($data['cpf'])) {
            $data['cpf'] = str_replace(array('.', '-', ' '), '', $data['cpf']);
        } else {
            $data['cpf'] = null;
        }

        //Removendo caracteres de máscara para rg
        if (!empty($data['rg'])) {
            $data['rg'] = str_replace(array(' '), '', $data['rg']);
        } else {
            $data['rg'] = null;
        }

        //Setando a data de cadastro
        $data['dtCadastro'] = new DateTime('now', new DateTimeZone(self::TIMEZONE));

        if (0 == $data['id']) {
            return $this->insert($data, $entityClass);
        } else {
            return $this->update($data, $entityClass, $data['codCliente']);
        }
    }

    /**
     * Método para salvar fornecedor
     * 
     * @param type $data
     * @param type $entityClass
     */
    public function salvarFornecedor($data, $entityClass) {

        if('UTF-8' == mb_detect_encoding($data['nome'])){
            $data['nome'] = utf8_decode($data['nome']);
        }
        if (!empty($data['cnpj'])) {
            $data['cnpj'] = str_replace(array('.', '/', '-'), '', $data['cnpj']);
        } else {
            $data['cnpj'] = null;
        }

        //Removendo caracteres de máscara para telefone
        if (!empty($data['telefone'])) {
            $data['telefone'] = str_replace(array('(', ')', '-', ' '), '', $data['telefone']);
        } else {
            $data['telefone'] = null;
        }

        if (0 == $data['id']) {
            return $this->insert($data, $entityClass);
        } else {
            return $this->update($data, $entityClass, $data['codFornecedor']);
        }
    }

    public function remover($data, $entityClass, $id, $devolver = null) {
        try {
            $this->entitymanager->beginTransaction();
            
            // Devolve os produtos para o estoque.
            if($devolver){
                $repository = $this->entitymanager->getRepository(VendaHasProduto::class);
                $produtosVenda = $repository->findBy(['vendaCodVenda' => $id]);

                foreach($produtosVenda as $pv){
                    $qtdProduto = $pv->getProdutoCodProduto()->getQuantidade() + $pv->getQuantidade();
                    $codVenda = $pv->getVendaCodVenda()->getCodVenda();
                    $codProduto = $pv->getProdutoCodProduto()->getCodProduto();
                    $this->update(['quantidade' => 0, 'stAtivo' => 0], VendaHasProduto::class, ['vendaCodVenda' => $codVenda, 'produtoCodProduto' => $codProduto]);
                    $this->update(['quantidade' => $qtdProduto], Produto::class, $pv->getProdutoCodProduto()->getCodProduto());
                }
            }

            $data['stAtivo'] = 0;
            $ret = $this->update($data, $entityClass, $id);
            $this->entitymanager->commit();
            
            return $ret;
        } catch(\Exception $e){
            $this->entitymanager->rollback();
            return false;
        }
    }

    public function ativar($entityClass, $id) {
        $data['stAtivo'] = 1;
        $ret = $this->update($data, $entityClass, $id);

        return $ret;
    }

    public function devolverProduto($codVenda, $codProduto, $qtdRemover){
        $repository = $this->entitymanager->getRepository(VendaHasProduto::class);
        $vp = $repository->find(['vendaCodVenda' => $codVenda, 'produtoCodProduto' => $codProduto]);
        $produto = $vp->getProdutoCodProduto();
        $qtdVenda = $vp->getQuantidade() - $qtdRemover;
        $qtdProduto = $produto->getQuantidade() + $qtdRemover;
        $ret['removeuVenda'] = false;
        $ret['success'] = false;
        
        try{
            $this->entitymanager->beginTransaction();
            
            $this->update(['quantidade' => $qtdVenda], VendaHasProduto::class, ['vendaCodVenda' => $codVenda, 'produtoCodProduto' => $codProduto]);
            $this->update(['quantidade' => $qtdProduto], Produto::class, $codProduto);
            
            // Se a quantidade de produtos da venda ficar zerada, então remove este produto.
            if(0 == $qtdVenda){
                $this->update(['stAtivo' => 0], VendaHasProduto::class, ['vendaCodVenda' => $codVenda, 'produtoCodProduto' => $codProduto]);
            }
            
            // Verifica so ainda resta produtos (ativos) na venda. Se não houver então remove (desativa) a venda.
            $produtosVenda = $repository->findBy(['vendaCodVenda' => $codVenda, 'stAtivo' => 1]);
            // Se não resta produtos ativos na venda, então remove (desativa) a venda.

            if(0 == count($produtosVenda)){
                $this->update(['stAtivo' => 0], Venda::class, $codVenda);
                $ret['removeuVenda'] = true;
            }
            $this->entitymanager->commit();
            $ret['success'] = true;

            return $ret;
        } catch(\Exception $e){
            $this->entitymanager->rollback();
            return $ret;
        }
        
    }
}
