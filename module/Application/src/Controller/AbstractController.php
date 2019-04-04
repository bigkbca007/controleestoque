<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class AbstractController extends AbstractActionController {

    protected $container;
    protected $entitymanager;

    /**
     *
     * @param string $entity
     * @return type
     */
//    public function getEntityManager() {
//        return @$this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
//    }

    /**
     *
     * @param string $entity
     * @param int $id
     * @return type
     */
//    public function getEntityManagerReference($entity, $id) {
//        return @$this->getEntityManager()->getReference($entity, $id);
//    }

    /**
     *
     * @param string $entity
     * @return type
     */
//    public function getEntityRepository($entity) {
//        $em = @$this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
//        return $em->getRepository($entity);
//    }

    /**
     * Método para verificar se o usuário ainda está logado
     *
     * @return boolean
     */
    public function isLoggedIn() {
        $container = new \Zend\Session\Container('Auth');
        if (count($container->user) == 0) {
            $this->redirect()->toRoute(null, array('controller' => 'Login', 'action' => 'index'));
            exit;
        }
        return true;
    }

    /**
     * Método para formatar data
     * 
     * @param mixed $dt
     * $return string
     */
    public static function formatarData($dt, $formato) {

        if ('Y-m-d' == $formato) {
            $parts = explode('/', $dt);
            $dtFormatada = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
        } elseif ('d/m/Y' == $formato) {
            $parts = explode('-', $dt);
            $dtFormatada = $parts[2] . '/' . $parts[1] . '/' . $parts[0];
        } elseif ('Y-m-d h:i:s' == $formato) {
            if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})\s(\d{2}):(\d{2}):(\d{2})$/', $dt)) {
                $dtFormatada = preg_replace('/^(\d{2})\/(\d{2})\/(\d{4})\s(\d{2}):(\d{2}):(\d{2})$/', '$3-$2-$1 $4:$5:$6', $dt);
            } elseif (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})\s(\d{2}):(\d{2})$/', $dt)) {
                $dtFormatada = preg_replace('/^(\d{2})\/(\d{2})\/(\d{4})\s(\d{2}):(\d{2})$/', '$3-$2-$1 $4:$5:00', $dt);
            }
        }

        return $dtFormatada;
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
            $repository = $this->entitymanager->getRepository($entity);
            $item = $repository->find($id);
            if (!empty($item)) {
                $svc = $this->container->get($service);

                $result = $svc->remover($item->toArray(), $entity, $id);
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

}
