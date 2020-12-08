<?php

namespace Vendas\Controller;

use Application\Controller\AbstractController;
use Interop\Container\ContainerInterface;
use Vendas\Entity\Venda;
use Vendas\Entity\VendaHasProduto;
use Vendas\Form\VendaForm;
use Vendas\Service\ControleEstoqueService;
use Zend\Json\Json;
use Zend\View\Model\ViewModel;

class VendasController extends AbstractController {

    public function __construct(ContainerInterface $container = null, $entitymanager = null) {
        $this->container = $container;
        $this->entitymanager = $entitymanager;
    }

    public function indexAction() {
        $form = new VendaForm($this->entitymanager);
        $view = new ViewModel(array(
            'form' => $form
        ));
        return $view;
    }

    /**
     * Método para busca de dados para listagem no datatable
     */
//    public function getDataAction2() {
//
//        $post = $this->getRequest()->getPost();
//        $start = $post->get('start', null);
//        $length = $post->get('length', null);
//        $columns = $post->get('columns');
//        $order = $post->get('order')[0];
//
//        $search_value = $post->get('search')['value'] ? $post->get('search')['value'] : '';
//        $search_column_num = $post->get('search')['regex']; //Este campo será usado para passar o número da coluna da busca
//        $search_column_name = str_replace('_', '.', @$columns[$search_column_num]['data']);
//
//        $order_column_name = str_replace('_', '.', $columns[$order['column']]['data']);
//        $order_dir = $order['dir'];
//
//        $repository = $this->entitymanager->getRepository('Vendas\Entity\Venda');
//
//        $vendas = $repository->getData($order_column_name, $order_dir, $search_value, $search_column_name, $start, $length);
//        $vendas_total = $repository->getData($order_column_name, $order_dir, $search_value, $search_column_name);
//
//        $data = array();
//        foreach ($vendas as $key => $v) {
//            //As chaves para $data[$key] devem ser iguais aos nomes das colunas na definição do datatable
//            $data[$key]['numLinha'] = $key + 1 + $start;
//            $data[$key]['produto_nome'] = utf8_encode($v['produtoNome']);
//            $data[$key]['venda_precoPago'] = "R$ " . number_format($v['precoPago'], 2, ',', '.');
//            $data[$key]['produto_precoVenda'] = "R$ " . number_format($v['precoVenda'], 2, ',', '.');
//            $data[$key]['cliente_nome'] = utf8_encode($v['clienteNome']);
//            $data[$key]['venda_dtPagamento'] = ($v['dtPagamento'] instanceof \DateTime) ? $v['dtPagamento']->format('d/m/Y') : '';
//            $data[$key]['venda_dtVenda'] = $v['dtVenda']->format('d/m/Y h:i');
//            $data[$key]['fornecedor_nome'] = utf8_encode($v['fornecedorNome']);
//            $data[$key]['venda_quantidade'] = utf8_encode($v['quantidade']);
//            $data[$key]['venda_desconto'] = utf8_encode($v['desconto']);
//            $data[$key]['venda_stPago'] = $v['stPago'] ? '<span class="glyphicon glyphicon-ok" style="color:#0F0"></span>' : '<span class="glyphicon glyphicon-remove" style="color:#F00"></span>';
//            $data[$key]['acao'] = '
//                                    <button class="btn btn-link" data-url="" onclick="$(\'#vendas-table\').carregarFormEditar(' . $v['codVenda'] . ', \'vendas_form\', \'vendas/\');"><span class="glyphicon glyphicon-pencil"></span></button>
//                                    <button class="btn btn-link" data-url="" onclick="$(\'#vendas-table\').modalRemover(' . $v['codVenda'] . ', \'vendas/\');"><span class="glyphicon glyphicon-trash" style="color:#FF0000;"></span></button>
//                                   ';
//        }
//        $resp = array(
//            'data' => $data,
//            'recordsFiltered' => count($vendas_total),
//            'recordsTotal' => count($vendas_total),
//        );
//
//        ob_clean();
//        return $this->getResponse()->setContent(Json::encode($resp));
//    }

    /**
     * Método para busca de dados para listagem no grid easyui
     * @return type
     */
    public function getDataGridAction() {
        $qs = $this->getRequest()->getQuery();
        $page = $qs->page;
        $rows = $qs->rows;
        $order = null;
        $filterRules = null;

        if ($qs->sort) {
            $order['sort'] = $qs->sort;
            $order['order'] = $qs->order ? $qs->order : null;
        }

        if ($qs->filterRules) {
            $filterRules = $qs->filterRules;
        }

        $tipoListagem = isset($qs['tipoListagem']) ? $qs['tipoListagem'] : 1;
        $repository = $this->entitymanager->getRepository('Vendas\Entity\Venda');

        $vendas = $repository->getDataGrid($filterRules, $order, $page, $rows, $tipoListagem);
        $vendasTotal = $repository->getDataGrid($filterRules, null, null, null, $tipoListagem);
        $total = count($vendasTotal);

        foreach ($vendas as &$v) {
            $v['Venda_dtVenda'] = $v['Venda_dtVenda'] instanceof \DateTime ? $v['Venda_dtVenda']->format('d/m/Y') : '';
            $v['Venda_dtPagamento'] = $v['Venda_dtPagamento'] instanceof \DateTime ? $v['Venda_dtPagamento']->format('d/m/Y') : '';
            $v['Venda_stPago'] = $v['Venda_stPago'] ? '<strong class="text-success"> Sim</strong>' : '<strong class="text-danger">Não</strong>';
            $v['Cliente_nome'] = utf8_encode($v['Cliente_nome']);
            if(1 == $v['Venda_stAtivo']){
                $v['acao'] = '
                            <button class="btn btn-link" data-url="" onclick="$(\'#vendas-table\').carregarFormEditar(' . $v['Venda_codVenda'] . ', \'vendas_form\', \'vendas/\');"><span class="glyphicon glyphicon-pencil"></span></button>
                            <button class="btn btn-link" data-url="" onclick="$(\'#vendas-table\').modalRemover(' . $v['Venda_codVenda'] . ', \'vendas/\', \'grid-estoque-vendas\');" title="Desativar venda."><span class="glyphicon glyphicon-trash text-danger"></span></button>
                        ';
            } else {
                $vp = $this->entitymanager->getRepository(VendaHasProduto::class)->findBy(['vendaCodVenda' => $v['Venda_codVenda'], 'stAtivo' => 1]);
                if(count($vp) > 0){
                    $v['acao'] = '
                            <button class="btn btn-link" data-url="" onclick="$(\'#vendas-table\').modalAtivar(' . $v['Venda_codVenda'] . ', \'vendas/\', \'grid-estoque-vendas\');" title="Ativar venda."><span class="glyphicon glyphicon-ok text-success"></span></button>
                        ';
                }
            }
        }

        $resp = array(
            'rows' => $vendas,
            'total' => $total
        );

        ob_clean();
        return $this->getResponse()->setContent(Json::encode($resp));
    }

    /**
     * Método para salvar dados via ajax
     */
    public function salvarajaxAction() {

        $data = $this->getRequest()->getPost()->toArray();
//        $data = [
//            'id'=> 0,
//            'codVenda' => '',
//            'codProduto-1' => 37,
//            'precoPago-1' => '46,99',
//            'quantidade-1' => 1,
//            'desconto-1' => '',
//            'codProduto-2' => 40,
//            'precoPago-2' => 40,
//            'quantidade-2' => 1,
//            'desconto-2' => '',
//            'codProduto-3' => 15,
//            'precoPago-3' => 20,
//            'quantidade-3' => 3,
//            'desconto-3' => '',
//            'dtVenda' => '2018/11/12 22 =>46',
//            'dtPagamento' => '',
//            'stPago' => 1,
//            'codCliente' => 8,
//            'numProdutosAdicionais' => 2
//        ];

        $resp = array();
        $produto = null;
        $service = $this->container->get('Vendas\Service\ControleEstoqueService');

//        if(!is_null($data['editProdSemQtde']) && $data['editProdSemQtde'] == '1'){
//            $produto = $service->getEntityManagerReference('Vendas\Entity\Produto', $data['codProduto']);
//        }

        $entityClass = 'Vendas\Entity\Venda';
        $inserir = $editar = 'salvarVenda';
        $form = new VendaForm($this->entitymanager, $produto);
        
        // Adiciona campos no form para os produtos adicionais para a validação.
        for($i = 0; $i < $data['numProdutosAdicionais']; $i++){
            // Adiciona no form
            $codProduto = clone $form->get('codProduto-1');
            $codProduto->setName('codProduto-'.($i+2))->setAttribute('name', 'codProduto-'.($i+2));
            $precoPago = clone $form->get('precoPago-1');
            $precoPago->setName('precoPago-'.($i+2))->setAttribute('name', 'precoPago-'.($i+2));
            $quantidade = clone $form->get('quantidade-1');
            $quantidade->setName('quantidade-'.($i+2))->setAttribute('name', 'quantidade-'.($i+2));
            $desconto = clone $form->get('desconto-1');
            $desconto->setName('desconto-'.($i+2))->setAttribute('name', 'desconto-'.($i+2));
            
            $form->add($codProduto);
            $form->add($precoPago);
            $form->add($quantidade);
            $form->add($desconto);

        }        
        $filter = $this->getFilter('\Vendas\Filter\VendaFilter', $data);
        $form->setInputFilter($filter);
        $item = 'venda';
        $genero = 'a';

        $form->remove('codProduto-0');
        $form->setData($data);

        if ($form->isValid()) {

            if (0 == $this->getRequest()->getPost()->get('id')) {
                //Insere
                $result = $service->$inserir($data, $entityClass);
                if (0 == $result['error']) {
                    $resp['success'] = 1;
                    $resp['msg'] = ucfirst($item) . " adicionad$genero";
                    $resp['type'] = "success";
                    //GRAVAR LOG
                } else {
                    $resp['success'] = 0;
                    $resp['msg'] = !empty($result['msg']) ? $result['msg'] : "Ocorreu um erro ao tentar adicionar $item";
                    $resp['type'] = "danger";
                    //GRAVAR LOG
                }
            } else {
                //Edita
                $result = $service->$editar($data, $entityClass);
                if (0 == $result['error']) {
                    $resp['success'] = 1;
                    $resp['msg'] = ucfirst($item) . " alterad$genero";
                    $resp['type'] = "success";
                    //GRAVAR LOG
                } else {
                    $resp['success'] = 0;
                    $resp['msg'] = !empty($result['msg']) ? $result['msg'] : "Ocorreu um erro ao tentar alterar $item";
                    $resp['type'] = "danger";
                    //GRAVAR LOG
                }
            }
        } else {
            $msg = '';
            $msgs = $form->getMessages();
            array_walk_recursive($msgs, function($m) use (&$msg) {
                $msg .= $m . '<br />';
            });
            $resp = array(
                'success' => 0,
                'msg' => $msg,
                'type' => 'danger'
            );
        }

        ob_clean();
        return $this->getResponse()->setContent(Json::encode($resp));
    }

    public function getDataFormAction() {
        $id = $this->params()->fromPost('id');
        $resp = array();

        if ($id) {
            $venda = $this->entitymanager->getRepository('Vendas\Entity\Venda')->find($id);
            if ($venda) {
                $data['codVenda'] = $venda->getCodVenda();
                $data['dtVenda'] = $venda->getDtVenda()->format('d/m/Y h:i');
                $data['dtPagamento'] = $venda->getDtPagamento() ? $venda->getDtPagamento()->format('d/m/Y') : '';
                $data['stPago'] = $venda->getStPago();
                $data['codCliente'] = $venda->getCodCliente()->getCodCliente();

                $produtosVendidos = $this->entitymanager->getRepository('Vendas\Entity\VendaHasProduto')->findBy(['vendaCodVenda' => $id]);
                if (!empty($produtosVendidos)) {
                    foreach ($produtosVendidos as $key => $pv) {
                        //As chaves para $data devem ser iguais aos names dos inputs
                        $data['produtos'][$key]['codProduto'] = $pv->getProdutoCodProduto()->getCodProduto();
                        $data['produtos'][$key]['nome'] = str_pad($data['produtos'][$key]['codProduto'], 4, '0', STR_PAD_LEFT)." - ".utf8_encode($pv->getProdutoCodProduto()->getNome()).' ('.$pv->getProdutoCodProduto()->getQuantidade().' em estoque)';
                        $data['produtos'][$key]['quantidade'] = $pv->getProdutoCodProduto()->getQuantidade();
                        $data['produtos'][$key]['precoPago'] = number_format($pv->getPrecoPago(), 2, ',', '.');
                        $data['produtos'][$key]['desconto'] = $pv->getDesconto();
                    }
                }
                $resp = array(
                    'success' => 1,
                    'data' => $data
                );
            } else {
                $resp = array(
                    'success' => 0,
                    'msg' => 'Item não encontrado',
                    'type' => 'info'
                );
            }
        } else {
            $resp = array(
                'success' => 0,
                'msg' => 'ID inválido',
                'type' => 'danger'
            );
        }

        ob_clean();
        return $this->getResponse()->setContent(Json::encode($resp));
    }

    public function removerAjaxAction() {

        $resp = $this->remover('Vendas\Entity\Venda');

        ob_clean();
        return $this->getResponse()->setContent(Json::encode($resp));
    }

    /**
     * Método para remoção (desativação) de item
     * 
     * @param string $entity
     * @pamra string $service
     */
    public function remover($entity, $service = 'Vendas\Service\ControleEstoqueService') {
        $id = $this->params()->fromPost('id');
        
        if (!$id) {
            $resp = array(
                'success' => 0,
                'msg' => 'ID inválido',
                'type' => 'danger'
            );
        } else {
            //Verificar permissões
            $repository = $this->entitymanager->getRepository(Venda::class);
            $item = $repository->find($id);
            if (!empty($item)) {
                $svc = $this->container->get(ControleEstoqueService::class);
                
                $devolver = $this->params()->fromPost('devolver', null);
                
                $result = $svc->remover($item->toArray(), $entity, $id, $devolver);
                if (0 == $result['error']) {
                    $resp['success'] = 1;
                    $resp['msg'] = "Registro removido.";
                    $resp['type'] = "success";
                    //GRAVAR LOG
                } else {
                    $resp['success'] = 0;
                    $resp['msg'] = !empty($result['msg']) ? $result['msg'] : "Ocorreu um erro ao tentar remover o registro.";
                    $resp['type'] = "danger";
                    //GRAVAR LOG
                }
            } else {
                $resp = array(
                    'success' => 0,
                    'msg' => 'Item não encontrado',
                    'type' => 'info'
                );
            }
        }

        return $resp;
    }

    public function getDadosProdutoVendaAction() {
        $codProduto = $this->params()->fromPost('codProduto');

        $produto = $this->entitymanager->getRepository('Vendas\Entity\Produto')->find($codProduto);

        $resp = array('desconto' => $produto->getDesconto(), 'preco_pago' => $produto->getPrecoVenda(), 'quantidade' => $produto->getQuantidade());
        ob_clean();
        return $this->getResponse()->setContent(Json::encode($resp));
    }

    public function getDescontoAction() {
        $codProduto = $this->params()->fromPost('codProduto');
        $desconto = $this->params()->fromPost('desconto');
        $produto = $this->entitymanager->getRepository('Vendas\Entity\Produto')->find($codProduto);

        $pv = $produto->getPrecoVenda();
        $preco_pago = $pv - ($pv * ($desconto / 100));
        $preco_pago_desconto = number_format($preco_pago, 2, ',', '.');

        ob_clean();
        return $this->getResponse()->setContent($preco_pago_desconto);
    }

    /**
     * Método para gerar filtros para campos adicionados dinâmicamente na view
     * OBS.: Para que este método seja eficaz, é preciso que os nomes dos filtros
     *       sejam iguais aos definidos para os elementos do formulário
     * 
     * @param type $classFilter
     * @param type $data
     */
    public function getFilter($classFilter, $data) {
        $filter = new $classFilter();

        foreach ($data as $key => $d) {
            // Adciona filtros para produtos
            if (strpos($key, 'codProduto') !== false) {
                if (!$filter->has($key)) {
                    $filter->add(array(
                        'name' => $key,
                        'required' => true,
                        'validators' => array(
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'messages' => array(
                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'Informe o produto ' . substr(strstr($key, '-'), 1) . '.'
                                    ),
                                ),
                            ),
                        )
                    ));
                }
            }

            // Adciona filtros para preco pago
            if (strpos($key, 'precoPago') !== false) {
                if (!$filter->has($key)) {
                    $filter->add(array(
                        'name' => $key,
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'messages' => array(
                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'Informe o proço do produto ' . substr(strstr($key, '-'), 1) . '.'
                                    ),
                                ),
                            ),
                        )
                    ));
                }
            }

            // Adciona filtros para quantidade
            if (strpos($key, 'quantidade') !== false) {
                if (!$filter->has($key)) {
                    $filter->add(array(
                        'name' => $key,
                        'required' => true,
                        'validators' => array(
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'messages' => array(
                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'Informe a quantidade do produto ' . substr(strstr($key, '-'), 1) . '.'
                                    ),
                                ),
                            ),
                        )
                    ));
                }
            }
        }

        return $filter;
    }

    /**
     * Método para buscar produtos vendidos a um determinado cliente.
     * Estes dados populam o subgrid de vendas.
     */
    public function getProdutosClienteAction() {
        $resp = [];
        $qs = $this->getRequest()->getQuery();
        $codVenda = $qs->id;
        $vendaRepository = $this->entitymanager->getRepository(VendaHasProduto::class);
        $produtosCliente = $vendaRepository->findBy(['vendaCodVenda' => $codVenda, 'stAtivo' => 1]);
        $linha = $qs['linha'];

        foreach ($produtosCliente as $key => $p) {
            $codProduto = $p->getProdutoCodProduto()->getCodProduto();
            $resp[$key]['Produto_codProduto'] = $codProduto;
            $resp[$key]['Produto_nome'] = utf8_encode($p->getProdutoCodProduto()->getNome());
            $resp[$key]['Produto_precoPago'] = "R$ " . number_format($p->getPrecoPago(), 2, ',', '.');
            $resp[$key]['Produto_precoPagoRaw'] = number_format($p->getPrecoPago(), 2, ',', '.');
            $resp[$key]['Produto_descricao'] = utf8_encode($p->getProdutoCodProduto()->getDescricao());
            $resp[$key]['VendaHasProduto_quantidade'] = $p->getQuantidade();
            $resp[$key]['Categoria_nome'] = utf8_encode($p->getProdutoCodProduto()->getCodCategoria()->getNome());
            $resp[$key]['Fornecedor_nome'] = utf8_encode($p->getProdutoCodProduto()->getCodFornecedor()->getNome());
            $resp[$key]['acao'] = '
                                    <button class="btn btn-link" onclick="modalDevolverProduto(' . $codVenda . ', ' . $codProduto . ', ' . $linha . ');"><span class="glyphicon glyphicon-arrow-left" style="color:#FF0000;" title="Devolver produto."></span></button>
                                   ';
        }

        ob_clean();
        return $this->getResponse()->setContent(Json::encode($resp));
    }

    public function devolverProdutoAction(){
        $quantidadeRemover = $this->params()->fromPost('quantidadeRemover', null);
        //$justificativa = $this->params()->fromPost('justificativa', '');

        //if(empty($justificativa)){
        //    $resp['success'] = 0;
        //    $resp['msg'] = "Para devolver um produto é necessário fornecer uma justificativa.";
        //} else {
        $codVenda = $this->params()->fromPost('codVenda');
        $codProduto = $this->params()->fromPost('codProduto');

        $resp = ['success' => 0];

        if($codVenda && $codProduto && $quantidadeRemover){
            $repository = $this->entitymanager->getRepository(VendaHasProduto::class);
            $vp = $repository->find(['vendaCodVenda' => $codVenda, 'produtoCodProduto' => $codProduto]);

            // A quantidade de produtos a remover deve ser menor ou igual à quantidade da venda.
            if($quantidadeRemover <= $vp->getQuantidade()){
                $service = $this->container->get('Vendas\Service\ControleEstoqueService');
                $resp = $service->devolverProduto($codVenda, $codProduto, $quantidadeRemover);
            } else {
                $resp['success'] = 0;
                $resp['msg'] = "Você está tentando devolver <strong>$quantidadeRemover</strong> produto(s), mas consta(m) <strong>{$vp->getQuantidade()}</strong> produto(s) nesta venda.";
            }

        }
        
        ob_clean();
        return $this->getResponse()->setContent(Json::encode($resp));

    }
    
    public function getNumProdutosDisponiveisAction(){
        $codVenda = $this->params()->fromPost('codVenda');
        $codProduto = $this->params()->fromPost('codProduto');
        $resp = ['success' => 0];
        if($codVenda && $codProduto){
            $repository = $this->entitymanager->getRepository(VendaHasProduto::class);
            //$vp = $repository->findBy(['vendaCodVenda' => $codVenda, 'produtoCodProduto' => $codProduto]);
            $vp = $repository->find(['vendaCodVenda' => $codVenda, 'produtoCodProduto' => $codProduto]);

            $resp = [
                'success' => 1,
                'quantidadeProdutos' => $vp->getProdutoCodProduto()->getQuantidade(),
                'quantidadeProdutosVenda' => $vp->getQuantidade()
            ];
        }
        
        ob_clean();
        return $this->getResponse()->setContent(Json::encode($resp));
    }
    
    public function ativarAjaxAction(){
        $id = $this->params()->fromPost('id');
        
        if (!$id) {
            $resp = array(
                'success' => 0,
                'msg' => 'ID inválido',
                'type' => 'danger'
            );
        } else {
            //Verificar permissões
            $repository = $this->entitymanager->getRepository(Venda::class);
            $item = $repository->find($id);
            if (!empty($item)) {
                $svc = $this->container->get(ControleEstoqueService::class);
                
                $result = $svc->ativar(Venda::class, $id);
                if (0 == $result['error']) {
                    $resp['success'] = 1;
                    $resp['msg'] = "Registro ativado.";
                    $resp['type'] = "success";
                    //GRAVAR LOG
                } else {
                    $resp['success'] = 0;
                    $resp['msg'] = !empty($result['msg']) ? $result['msg'] : "Ocorreu um erro ao tentar ativar o registro.";
                    $resp['type'] = "danger";
                    //GRAVAR LOG
                }
            } else {
                $resp = array(
                    'success' => 0,
                    'msg' => 'Item não encontrado',
                    'type' => 'info'
                );
            }
        }

        ob_clean();
        return $this->getResponse()->setContent(Json::encode($resp));
    }
}
