<?php

namespace Vendas\Controller;

use Application\Controller\AbstractController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;
use \Interop\Container\ContainerInterface;

class FornecedoresController extends AbstractController {

    public function __construct(ContainerInterface $container = null, $entitymanager = null) {
        $this->container = $container;
        $this->entitymanager = $entitymanager;
    }

    public function indexAction() {
        $form = new \Vendas\Form\FornecedorForm($this->entitymanager);
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
        $start = $post->get('start');
        $length = $post->get('length') == -1 ? null: $post->get('start');

        $columns = $post->get('columns');
        $order = $post->get('order')[0];

        //Dados para busca
        $search_value = $post->get('search')['value'] ? $post->get('search')['value'] : '';
        $search_column_num = $post->get('search')['regex'];//Este campo será usado para passar o número da coluna da busca
        $search_column_name = str_replace('_','.',$columns[$search_column_num]['data']);

        //Dados para ordenação
        $order_dir = $order['dir'];

        $repository = $this->entitymanager->getRepository('Vendas\Entity\Fornecedor');

        $fornecedores = $repository->getData($order_dir, $search_value, $search_column_name, $start, $length);
        $fornecedores_total = $repository->getData($order_dir, $search_value, $search_column_name);

        $data = array();
        foreach ($fornecedores as $key => $f) {
            $data[$key]['numLinha'] = $key + 1 + $start;
            $data[$key]['fornecedor_nome'] = utf8_encode($f['nome']);
            $data[$key]['fornecedor_telefone'] = preg_match('/^(\d{2})(\d{4})(\d{4})$/',$f['telefone'],$matches) ? "({$matches[1]}) {$matches[2]}-{$matches[3]}" : '';
            $data[$key]['fornecedor_cnpj'] = preg_match('/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/',$f['cnpj'],$matches) ? "{$matches[1]}.{$matches[2]}.{$matches[3]}/{$matches[4]}-{$matches[5]}" : '';
            $data[$key]['fornecedor_email'] = $f['email'];
            $disabled = !$f['podeRemover'] ? 'disabled': '';
            $color = !$f['podeRemover'] ? '#eaeaea': '#ff0000';
            $title = !$f['podeRemover'] ? 'Este fornecedor não pode ser removido por que ele está relacionado a pelo menos um produto cadastrado.': '';
            $data[$key]['acao'] = '
                                    <button class="btn btn-link" data-url="" onclick="$(\'#fornecedores-table\').carregarFormEditar('.$f['codFornecedor'].', \'fornecedores_form\', \'vendas/fornecedores/\');"><span class="glyphicon glyphicon-pencil"></span></button>
                                    <button class="btn btn-link" data-url="" onclick="$(\'#fornecedores-table\').modalRemover('.$f['codFornecedor'].', \'vendas/fornecedores/\');" '.$disabled.' title="'.$title.'"><span class="glyphicon glyphicon-trash" style="color:'.$color.';"></span></button>
                                   ';
        }

        $resp = array(
            'data' => $data,
            'recordsFiltered' => count($fornecedores_total),
            'recordsTotal' => count($fornecedores_total),
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

        $entityClass = 'Vendas\Entity\Fornecedor';
        $form = new \Vendas\Form\FornecedorForm($this->entitymanager);
        $form->setInputFilter(new \Vendas\Filter\FornecedorFilter());
        $item = 'fornecedor';
        $genero = 'o';

        $form->setData($data);

        if ($form->isValid()) {
            $service = $this->container->get('Vendas\Service\ControleEstoqueService');

            if (0 == $this->getRequest()->getPost()->get('id')) {
                //Insere
                $result = $service->salvarFornecedor($data, $entityClass);
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
                $result = $service->salvarFornecedor($data, $entityClass);
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

    public function getDataFormAction(){
        $id = $this->params()->fromPost('id');
        $resp = array();

        if($id){
            $repository = $this->entitymanager->getRepository('Vendas\Entity\Fornecedor');
            $fornecedor = $repository->find($id);

            if(!empty($fornecedor)){
                $data['codFornecedor'] = $fornecedor->getCodFornecedor();
                $data['cnpj'] = preg_replace('/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/','$1.$2.$3/$4-$5', $fornecedor->getCnpj());
                $data['nome'] = utf8_encode($fornecedor->getNome());
                $data['telefone'] = preg_replace('/^(\d{2})(\d{4})(\d{4})$/','($1) $2-$3', $fornecedor->getTelefone());
                $data['email'] = $fornecedor->getEmail();

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

        $resp = $this->remover('Vendas\Entity\Fornecedor');
       
        ob_clean();
        return $this->getResponse()->setContent(Json::encode($resp));
    }    

    public function getCidadesAction(){
        $sigla = $this->params()->fromPost('sigla');
        $cidades = '';
        if(!empty($sigla)){
            $repository = $this->entitymanager->getRepository('Vendas\Entity\Cidade');
            $cidades = $repository->getCidades($sigla);
        }

        ob_clean();
        return $this->getResponse()->setContent(Json::encode($cidades));        
    }
}