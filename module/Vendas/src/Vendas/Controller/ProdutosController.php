<?php

namespace Vendas\Controller;

use Application\Controller\AbstractController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;
use \Interop\Container\ContainerInterface;

class ProdutosController extends AbstractController {

    protected $container;
    protected $entitymanager;
    protected $configs;
    
    public function __construct(ContainerInterface $container = null, $entitymanager = null, Array $configs = []) {
        $this->container = $container;
        $this->entitymanager = $entitymanager;
        $this->configs = $configs;
    }

    public function indexAction() {
        $form = new \Vendas\Form\ProdutoForm($this->entitymanager);
//        $form->setInputFilter(new \Vendas\Filter\VendaFilter());
        $view = new ViewModel(array(
            'form' => $form
        ));
        return $view;
    }

    /**
     * Método para busca de dados para listagem no datatable
     */
    public function getDataAction() {

        $post = $this->getRequest()->getPost();
        $start = $post->get('start', null);
        $length = $post->get('length', null);
        $columns = $post->get('columns');
        $order = $post->get('order')[0];

        //Dados para busca
        $search_value = $post->get('search')['value'] ? $post->get('search')['value'] : '';
        $search_column_num = $post->get('search')['regex']; //Este campo será usado para passar o número da coluna da busca
        $search_column_name = str_replace('_', '.', $columns[$search_column_num]['data']);

        //Dados par ordenação
        $order_column_name = $columns[$order['column']]['data'] ? str_replace('_', '.', $columns[$order['column']]['data']) : 'nome';
        $order_dir = $order['dir'];

        $repository = $this->entitymanager->getRepository('Vendas\Entity\Produto');

        $produtos = $repository->getData($order_column_name, $order_dir, $search_value, $search_column_name, $start, $length);
        $produtos_total = $repository->getData($order_column_name, $order_dir, $search_value, $search_column_name);

        $data = array();
        foreach ($produtos as $key => $p) {
            $data[$key]['Produto_codProduto'] = str_pad($p['codProduto'], 4, '0', STR_PAD_LEFT);
            $data[$key]['Produto_nome'] = utf8_encode($p['produtoNome']);
            $data[$key]['Categoria_nome'] = utf8_encode($p['categoriaNome']);
            $data[$key]['Fornecedor_nome'] = utf8_encode($p['fornecedorNome']);
            $data[$key]['Produto_precoVenda'] = 'R$ ' . number_format($p['precoVenda'], 2, ',', '.');
            $data[$key]['Produto_precoFabrica'] = 'R$ ' . number_format($p['precoFabrica'], 2, ',', '.');
            $data[$key]['Produto_dtFabricacao'] = $p['dtFabricacao'] instanceof \DateTime ? $p['dtFabricacao']->format('d/m/Y') : '';
            //Verifica se o sistema deve alertar que a data de vencimento está próxima
            if($p['dtValidade'] instanceof \DateTime){
                $corAlertValidade = '#000000';

                //Se o produto não possuir um número de dias espescífico para alertar do vencimento, então usa a configuração global
                if(!$p['numDiasAlertarVencimento']){
                    $dias = $this->configs['params_sist']['tmp_alertar_venciento_produto']['dias'];
                } else {
                    $dias = $p['numDiasAlertarVencimento'];
                }
                $now = new \DateTime('now 00:00:00', new \DateTimeZone('America/Bahia'));
                $diffInDays = $now->diff($p['dtValidade'])->days;
                $invert = $now->diff($p['dtValidade'])->invert;

                if(1 == $invert and $diffInDays > 0){
                    //Produtos vencidos
                    $data[$key]['Produto_dtValidade'] = '<span class="glyphicon glyphicon-remove" style="color:#ff0000"></span> <strong style="color:#ff0000" title="Produto vencido.">'.$p['dtValidade']->format('d/m/Y').'</strong>';
                } elseif($diffInDays == 0){
                    //Produtos que vencem hoje
                    $data[$key]['Produto_dtValidade'] = '<span class="glyphicon glyphicon-exclamation-sign" style="color:#ffd400"></span> <strong style="color:#ffd400" title="ESTE PRODUTO VENCE HOJE!">'.$p['dtValidade']->format('d/m/Y').'</strong>';
                } elseif($diffInDays > 0 && $diffInDays <= $dias){
                    //Produtos perto de vencer, de acordo com o parâmento
                    $data[$key]['Produto_dtValidade'] = '<strong style="color:#ffd400">'.$p['dtValidade']->format('d/m/Y').'</strong>';
                } else {
                    //Produtos longe devencer, de acordo com o parâmetro
                    $data[$key]['Produto_dtValidade'] = $p['dtValidade']->format('d/m/Y');
                }

            } else {
                $data[$key]['Produto_dtValidade'] =  '';
            }
            //$data[$key]['produto_desconto'] = $p['desconto'] . '%';
            $data[$key]['Produto_quantidade'] = $p['quantidade'] == 0 ? "<strong style='color:#ff0000;'>{$p['quantidade']}</strong>" : "<strong style='color:#00ff00;'>{$p['quantidade']}</strong>";
            $disabled = !$p['podeRemover'] ? 'disabled': '';
            $color = !$p['podeRemover'] ? '#eaeaea': '#ff0000';
            $title = !$p['podeRemover'] ? 'Este produto não pode ser removido por que ele consta em pelo menos uma venda.': 'Remover este produto.';
            $data[$key]['acao'] = '
                                    <button class="btn btn-link" onclick="$.fn.carregarFormEditar(' . $p['codProduto'] . ', \'produtos_form\', \'vendas/produtos/\');" title="Alterar informações do produto."><span class="glyphicon glyphicon-pencil"></span></button>
                                    <button class="btn btn-link" onclick="$(\'#produtos-table\').modalRemover('.$p['codProduto'].', \'vendas/produtos/\');" '.$disabled.' title="'.$title.'"><span class="glyphicon glyphicon-trash" style="color:'.$color.';"></span></button>
                                    <button class="btn btn-link" onclick="$(\'#produtos-table\').modalClonar('.$p['codProduto'].', \'vendas/produtos/\');" title="Duplicar este produto."><span class="glyphicon glyphicon-duplicate" style="color:#ffb812;"></span></button>
                                   ';
        }

        $resp = array(
            'rows' => $data,
            'total' => count($produtos_total),
            //'recordsTotal' => count($produtos_total),
        );

        ob_clean();
        return $this->getResponse()->setContent(Json::encode($resp));
    }

    /**
     * 
     * @return type
     *      [page] => 1
            [rows] => 50
            [sort] => Produto_nome
            [order] => asc
            [filterRules] => [{"field":"Produto_nome","op":"contains","value":"f"}]
     */
    public function getDataGridAction() {

        $qs = $this->getRequest()->getQuery();
        $page = $qs->page;
        $rows = $qs->rows;
        $order = null;
        $filterRules = null;
        
        if($qs->sort){
            $order['sort'] = $qs->sort;
            $order['order'] = $qs->order?$qs->order:null;
        }
        
        if($qs->filterRules){
            $filterRules = $qs->filterRules;
        }

        $repository = $this->entitymanager->getRepository('Vendas\Entity\Produto');

        $produtos = $repository->getDataGrid($filterRules, $order, $page, $rows);
        $produtosTotal = $repository->getDataGrid($filterRules);
        $total = count($produtosTotal);
        //echo '<pre>';print_r($produtos);die();

        $data = array();
        $totalPreco = 0;
        $totalQuantidade = 0;
        foreach ($produtos as $key => $p) {
            $data[$key]['Produto_codProduto'] = str_pad($p['Produto_codProduto'], 4, '0', STR_PAD_LEFT);
            $data[$key]['Produto_nome'] = utf8_encode($p['Produto_nome']);
            $data[$key]['Categoria_nome'] = utf8_encode($p['Categoria_nome']);
            $data[$key]['Fornecedor_nome'] = utf8_encode($p['Fornecedor_nome']);
            $data[$key]['Produto_precoVenda'] = 'R$ ' . number_format($p['Produto_precoVenda'], 2, ',', '.');
            $data[$key]['Produto_precoFabrica'] = 'R$ ' . number_format($p['Produto_precoFabrica'], 2, ',', '.');
            $data[$key]['Produto_dtFabricacao'] = $p['Produto_dtFabricacao'] instanceof \DateTime ? $p['Produto_dtFabricacao']->format('d/m/Y') : '';
            //Verifica se o sistema deve alertar que a data de vencimento está próxima
            if($p['Produto_dtValidade'] instanceof \DateTime){
                $corAlertValidade = '#000000';

                //Se o produto não possuir um número de dias espescífico para alertar do vencimento, então usa a configuração global
                if(!$p['Produto_numDiasAlertarVencimento']){
                    $dias = $this->configs['params_sist']['tmp_alertar_venciento_produto']['dias'];
                } else {
                    $dias = $p['Produto_numDiasAlertarVencimento'];
                }
                $now = new \DateTime('now 00:00:00', new \DateTimeZone('America/Bahia'));
                $diffInDays = $now->diff($p['Produto_dtValidade'])->days;
                $invert = $now->diff($p['Produto_dtValidade'])->invert;

                if(1 == $invert and $diffInDays > 0){
                    //Produtos vencidos
                    $data[$key]['Produto_dtValidade'] = '<span class="glyphicon glyphicon-remove" style="color:#ff0000"></span> <strong style="color:#ff0000" title="Produto vencido.">'.$p['Produto_dtValidade']->format('d/m/Y').'</strong>';
                } elseif($diffInDays == 0){
                    //Produtos que vencem hoje
                    $data[$key]['Produto_dtValidade'] = '<span class="glyphicon glyphicon-exclamation-sign" style="color:#ffd400"></span> <strong style="color:#ffd400" title="ESTE PRODUTO VENCE HOJE!">'.$p['Produto_dtValidade']->format('d/m/Y').'</strong>';
                } elseif($diffInDays > 0 && $diffInDays <= $dias){
                    //Produtos perto de vencer, de acordo com o parâmento
                    $data[$key]['Produto_dtValidade'] = '<strong style="color:#ffd400">'.$p['Produto_dtValidade']->format('d/m/Y').'</strong>';
                } else {
                    //Produtos longe devencer, de acordo com o parâmetro
                    $data[$key]['Produto_dtValidade'] = $p['Produto_dtValidade']->format('d/m/Y');
                }

            } else {
                $data[$key]['Produto_dtValidade'] =  '';
            }
            //$data[$key]['produto_desconto'] = $p['desconto'] . '%';
            $data[$key]['Produto_quantidade'] = $p['Produto_quantidade'] == 0 ? "<strong style='color:#ff0000;'>{$p['Produto_quantidade']}</strong>" : "<strong style='color:#00ff00;'>{$p['Produto_quantidade']}</strong>";
            $disabled = !$p['podeRemover'] ? 'disabled': '';
            $color = !$p['podeRemover'] ? '#eaeaea': '#ff0000';
            $title = !$p['podeRemover'] ? 'Este produto não pode ser removido por que ele consta em pelo menos uma venda.': 'Remover este produto.';
            $data[$key]['acao'] = '
                                    <button class="btn btn-link" onclick="$.fn.carregarFormEditar(' . $p['Produto_codProduto'] . ', \'produtos_form\', \'vendas/produtos/\');" title="Alterar informações do produto."><span class="glyphicon glyphicon-pencil"></span></button>
                                    <button class="btn btn-link" onclick="$(\'#produtos-table\').modalRemover('.$p['Produto_codProduto'].', \'vendas/produtos/\');" '.$disabled.' title="'.$title.'"><span class="glyphicon glyphicon-trash" style="color:'.$color.';"></span></button>
                                   ';
                                    //<button class="btn btn-link" onclick="$(\'#produtos-table\').modalClonar('.$p['Produto_codProduto'].', \'vendas/produtos/\');" title="Duplicar este produto."><span class="glyphicon glyphicon-duplicate" style="color:#ffb812;"></span></button>
            
            $totalPreco += $p['Produto_precoVenda'] * $p['Produto_quantidade'];
            $totalQuantidade += $p['Produto_quantidade'];
        }

        $totalPreco = number_format($totalPreco, 2, ',','.');
        $resp = array(
            'rows' => $data,
            'total' => $total,
            'footer' => [
                [
                    'Produto_precoVenda' => "<strong class='text-success'>Total: R$ $totalPreco</strong>",
                    'Produto_quantidade' => "<strong class='text-success'>Total: $totalQuantidade</strong>",
                ]
            ]            //'recordsTotal' => count($produtos_total),
        );

        ob_clean();
        return $this->getResponse()->setContent(Json::encode($resp));
    }

    /**
     * Método para salvar dados via ajax
     */
    public function salvarAjaxAction() {

        $data = $this->getRequest()->getPost()->toArray();
        $resp = array();

        $entityClass = 'Vendas\Entity\Produto';
        $form = new \Vendas\Form\ProdutoForm($this->entitymanager);
        $form->setInputFilter(new \Vendas\Filter\ProdutoFilter());
        $item = 'produto';
        $genero = 'o';

        $form->setData($data);

        if ($form->isValid()) {
            $service = $this->container->get('Vendas\Service\ControleEstoqueService');

            if (0 == $this->getRequest()->getPost()->get('id')) {
                //Insere
                $result = $service->salvarProduto($data, $entityClass);
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
                $result = $service->salvarProduto($data, $entityClass);
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
            $repository = $this->entitymanager->getRepository('Vendas\Entity\Produto');
            $produto = $repository->find($id);
            if (!empty($produto)) {
                $data['codProduto'] = $produto->getCodProduto();
                $data['nome'] = utf8_encode($produto->getNome());
                $data['codCategoria'] = $produto->getCodCategoria()->getCodCategoria();
                $data['codFornecedor'] = $produto->getCodFornecedor()->getCodFornecedor();
                $data['dtFabricacao'] = $produto->getDtFabricacao() instanceof \DateTime ? $produto->getDtFabricacao()->format('d/m/Y') : '';
                $data['dtValidade'] = $produto->getDtValidade() instanceof \DateTime ? $produto->getDtValidade()->format('d/m/Y') : '';
                $data['descricao'] = utf8_encode($produto->getDescricao());
                $data['precoVenda'] = number_format($produto->getPrecoVenda(), 2, ',', '.');
                //$data['precoFabrica'] = number_format($produto->getPrecoFabrica(), 2, ',', '.');
                //$data['desconto'] = $produto->getDesconto();
                $data['quantidade'] = $produto->getQuantidade();
                $data['numDiasAlertarVencimento'] = $produto->getNumDiasAlertarVencimento();

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

    /**
     * Método para remover item
     * 
     * @return type string
     */
    public function removerAjaxAction() {

        //Verificando se este produto consta em uma venda
        $id = $this->params()->fromPost('id');
        $repository = $this->entitymanager->getRepository('Vendas\Entity\Venda');
        $vendas = $repository->findBy(array('codProduto' => $id, 'stAtivo' => 1));
        
        if(empty($vendas)) {
            $resp = $this->remover('Vendas\Entity\Produto');
        } else {
            $resp = array(
                'success' => 0,
                'msg' => 'Este produto não pode ser removido por que ele consta em pelo menos uma venda.',
                'type' => 'warning'
            );
        }

        ob_clean();
        return $this->getResponse()->setContent(Json::encode($resp));
    }

    /**
     * Método para clonar item
     * 
     * @return string
     */
    public function clonarAjaxAction() {

        //Buscando item a ser clonado
        $id = $this->params()->fromPost('id', 0);

        $repository = $this->entitymanager->getRepository('Vendas\Entity\Produto');
        $produto = $repository->find($id);

        if(!empty($produto)) {
            //Atribui '0' ao código do produto para que se crie um novo
            $produto->setCodProduto(0);
            $data = [];
            $data['nome'] = utf8_encode($produto->getNome());
            $data['descricao'] = utf8_encode($produto->getDescricao());
            $data['codCategoria'] = $produto->getCodCategoria();
            $data['codFornecedor'] = $produto->getCodFornecedor();

            $service = $this->container->get('Vendas\Service\ControleEstoqueService');
            $result = $service->salvarProduto($data, 'Vendas\Entity\Produto');
            if($result){
                $resp = array(
                    'success' => 1,
                    'msg' => 'Produto duplicado com sucesso.',
                    'type' => 'success'
                );
            } else {                
                $resp = array(
                    'success' => 0,
                    'msg' => 'Ocorreu um erro ao tentar duplicar o produto.',
                    'type' => 'danger'
                );
            }
        } else {
            $resp = array(
                'success' => 0,
                'msg' => 'O sistema não conseguiu encontrar este produto para ser duplicado.',
                'type' => 'warning'
            );
        }

        ob_clean();
        return $this->getResponse()->setContent(Json::encode($resp));
    }

    /**
     * Método para busca de lista de produtos
     * 
     * @return mixed
     */
    public function getSelectOptionsAction(){
        $repository = $this->entitymanager->getRepository('Vendas\Entity\Produto');
        $resp = $repository->getSelectOptions();
        
        ob_clean();
        return $this->getResponse()->setContent(Json::encode($resp));
    }

    /**
     * Método para busca de lista de produtos
     * 
     * @return mixed
     */
    public function getSelectOptionAction(){
        $codProduto = $this->params()->fromQuery('codProduto');
        $repository = $this->entitymanager->getRepository('Vendas\Entity\Produto');
        $produto = $repository->find($codProduto);

        $resp = array(
            'codProduto' => $produto->getCodProduto(),
            'nome' => str_pad($produto->getCodProduto(),4,'0',STR_PAD_LEFT).' - '.utf8_encode($produto->getNome()) . ' ('.$produto->getQuantidade().' em estoque)'
        );
        
        ob_clean();
        return $this->getResponse()->setContent(Json::encode($resp));
    }
}
