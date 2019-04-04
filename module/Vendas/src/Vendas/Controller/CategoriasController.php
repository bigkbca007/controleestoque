<?php

namespace Vendas\Controller;

use Application\Controller\AbstractController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;
use \Interop\Container\ContainerInterface;

class CategoriasController extends AbstractController {

    public function __construct(ContainerInterface $container = null, $entitymanager = null) {
        $this->container = $container;
        $this->entitymanager = $entitymanager;
    }

    public function indexAction() {
        $form = new \Vendas\Form\CategoriaForm($this->entitymanager);
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
        $length = $post->get('length') == -1 ? null : $post->get('start');

        $columns = $post->get('columns');
        $order = $post->get('order')[0];

        //Dados para busca
        $search_value = $post->get('search')['value'] ? $post->get('search')['value'] : '';
        $search_column_num = $post->get('search')['regex']; //Este campo será usado para passar o número da coluna da busca
        $search_column_name = str_replace('_', '.', $columns[$search_column_num]['data']);

        //Dados para ordenação
        $order_dir = $order['dir'];

        $repository = $this->entitymanager->getRepository('Vendas\Entity\Categoria');

        $categorias = $repository->getData($order_dir, $search_value, $search_column_name, $start, $length);
        $categorias_total = $repository->getData($order_dir, $search_value, $search_column_name);

        $data = array();
        foreach ($categorias as $key => $c) {
            $data[$key]['numLinha'] = $key + 1 + $start;
            $data[$key]['categoria_nome'] = utf8_encode($c['categoriaNome']);
            $disabled = !$c['podeRemover'] ? 'disabled': '';
            $color = !$c['podeRemover'] ? '#eaeaea': '#ff0000';
            $title = !$c['podeRemover'] ? 'Esta categoria não pode ser removida por que ela consta em pelo menos um produto ou possui categoria filha.': '';
            $data[$key]['acao'] = '
                                    <button class="btn btn-link" onclick="$.fn.carregarFormEditar(' . $c['codCategoria'] . ', \'categorias_form\', \'vendas/categorias/\');"><span class="glyphicon glyphicon-pencil"></span></button>
                                    <button class="btn btn-link" onclick="$(\'#categorias-table\').modalRemover(' . $c['codCategoria'] . ', \'vendas/categorias/\');" '.$disabled.' title="'.$title.'"><span class="glyphicon glyphicon-trash" style="color:'.$color.';"></span></button>
                                   ';
        }

        $resp = array(
            'data' => $data,
            'recordsFiltered' => count($categorias_total),
            'recordsTotal' => count($categorias_total),
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

        $entityClass = 'Vendas\Entity\Categoria';
        $form = new \Vendas\Form\CategoriaForm($this->entitymanager);
        $form->setInputFilter(new \Vendas\Filter\CategoriaFilter());
        $item = 'categoria';
        $genero = 'o';

        $form->setData($data);

        if ($form->isValid()) {
            $service = $this->container->get('Vendas\Service\ControleEstoqueService');

            if (0 == $this->getRequest()->getPost()->get('id')) {
                //Insere
                $result = $service->salvarCategoria($data, $entityClass);
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
                $result = $service->salvarCategoria($data, $entityClass);
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
            $repository = $this->entitymanager->getRepository('Vendas\Entity\Categoria');
            $categoria = $repository->find($id);

            if (!empty($categoria)) {
                $data['codCategoria'] = $categoria->getCodCategoria();
                $data['nome'] = utf8_encode($categoria->getNome());

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
     * Método para busaca de opções para combo
     */
    public function getcombooptionsAction() {

        $categorias = array('' => 'Selecione') + $this->entitymanager->getRepository('Vendas\Entity\Categoria')->getSelectOptions();
        $options = '';

        foreach ($categorias as $key => $c) {
            $options .= '<option value="' . $key . '">' . $c . '</option>';
        }

        ob_clean();
        return $this->getResponse()->setContent($options);
    }

    public function removerAjaxAction() {

        $resp = $this->remover('Vendas\Entity\Categoria');

        ob_clean();
        return $this->getResponse()->setContent(Json::encode($resp));
    }

}
