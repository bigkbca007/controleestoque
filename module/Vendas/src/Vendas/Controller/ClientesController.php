<?php

namespace Vendas\Controller;

use Application\Controller\AbstractController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;
use \Interop\Container\ContainerInterface;

class ClientesController extends AbstractController {

    public function __construct(ContainerInterface $container = null, $entitymanager = null) {
        $this->container = $container;
        $this->entitymanager = $entitymanager;
    }

    public function indexAction() {
        $form = new \Vendas\Form\ClienteForm($this->entitymanager);
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

        $repository = $this->entitymanager->getRepository('Vendas\Entity\Cliente');

        $clientes = $repository->getData($order_dir, $search_value, $search_column_name, $start, $length);
        $clientes_total = $repository->getData($order_dir, $search_value, $search_column_name);

        $data = array();
        foreach ($clientes as $key => $c) {
            $data[$key]['numLinha'] = $key + 1 + $start;
            $data[$key]['cliente_nome'] = utf8_encode($c['clienteNome']);
            $data[$key]['cliente_email'] = utf8_encode($c['email']);
            $data[$key]['cliente_telefone'] = preg_match('/^(\d{2})(\d{4})(\d{4})$/',$c['telefone'],$matches) ? "({$matches[1]}) {$matches[2]}-{$matches[3]}" : '';
            $data[$key]['cliente_cpf'] = preg_match('/^(\d{3})(\d{3})(\d{3})(\d{2})$/',$c['cpf'],$matches) ? "{$matches[1]}.{$matches[2]}.{$matches[3]}-{$matches[4]}" : '';
            $data[$key]['cliente_rg'] = preg_match('/^(\d{8})(\d{2})$/',$c['rg'],$matches) ? "{$matches[1]} {$matches[2]}" : '';
            $data[$key]['cliente_sexo'] = 'M' == $c['sexo'] ? '<span style="color:#1369ff;">Masculino</span>' : '<span style="color:#f000ff;">Feminino</span>';
            $disabled = !$c['podeRemover'] ? 'disabled': '';
            $color = !$c['podeRemover'] ? '#eaeaea': '#ff0000';
            $title = !$c['podeRemover'] ? 'Este cliente não pode ser removido por que ele consta em pelo menos uma venda.': '';
            $data[$key]['acao'] = '
                                    <button class="btn btn-link" data-url="" onclick="$.fn.carregarFormEditar('.$c['codCliente'].', \'clientes_form\', \'vendas/clientes/\');"><span class="glyphicon glyphicon-pencil"></span></button>
                                    <button class="btn btn-link" data-url="" onclick="$(\'#clientes-table\').modalRemover('.$c['codCliente'].', \'vendas/clientes/\');" '.$disabled.' title="'.$title.'"><span class="glyphicon glyphicon-trash" style="color:'.$color.';"></span></button>
                                   ';
        }

        $resp = array(
            'data' => $data,
            'recordsFiltered' => count($clientes_total),
            'recordsTotal' => count($clientes_total),
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

        $entityClass = 'Vendas\Entity\Cliente';
        $form = new \Vendas\Form\ClienteForm($this->entitymanager);
        $form->setInputFilter(new \Vendas\Filter\ClienteFilter());
        $item = 'cliente';
        $genero = 'o';

        $form->setData($data);

        if ($form->isValid()) {
            $service = $this->container->get('Vendas\Service\ControleEstoqueService');

            if (0 == $this->getRequest()->getPost()->get('id')) {
                //Insere
                $result = $service->salvarCliente($data, $entityClass);
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
                $result = $service->salvarCliente($data, $entityClass);
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
            $repository = $this->entitymanager->getRepository('Vendas\Entity\Cliente');
            $cliente = $repository->find($id);

            if(!empty($cliente)){
                $data['codCliente'] = $cliente->getCodCliente();
                $data['nome'] = utf8_encode($cliente->getNome());
                $data['email'] = $cliente->getEmail();
                $data['telefone'] = preg_replace('/^(\d{2})(\d{5})(\d{4})$/','($1) $2-$3', $cliente->getTelefone());
                $data['cpf'] = preg_replace('/^(\d{3})(\d{3})(\d{3})(\d{2})$/','$1.$2.$3-$4', $cliente->getCpf());
                $data['rg'] = preg_replace('/^(\d{8})(\d{2})$/','$1 $2', $cliente->getRg());
                $data['sexo'] = $cliente->getSexo();

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

        $resp = $this->remover('Vendas\Entity\Cliente');
       
        ob_clean();
        return $this->getResponse()->setContent(Json::encode($resp));
    }    
}