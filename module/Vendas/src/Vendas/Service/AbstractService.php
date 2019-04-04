<?php

namespace Vendas\Service;

use Doctrine\ORM\EntityManager;
use Exception;
use Zend\Hydrator\ClassMethods;
use Zend\ServiceManager\ServiceManager;

abstract class AbstractService {

    /**
     * @var ServiceManager
     */
    protected $servicemanager;

    /**
     * @var EntityManager
     */
    protected $entitymanager;

    public function __construct(ServiceManager $servicemanager, EntityManager $entitymanager) {
        $this->servicemanager = $servicemanager;
        $this->entitymanager = $entitymanager;
    }

    /**
     * Get EntityManager
     * 
     * @return EntityManager
     */
    public function getEntityManager() {
        return $this->entitymanager;
    }

    public function getEntityManagerReference($entity, $id) {

        return $this->getEntityManager()->getReference($entity, $id);
    }

    public function insert($data, $entityClass) {
        try {
            $entity = new $entityClass($data);

            $em = $this->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return array(
                'error' => 0,
                'entity' => $entity,
            );
        } catch (Exception $e) {
            return array(
                'error' => 1,
                'msg' => $e->getMessage(),
            );
        }
    }

    public function update($data, $entityClass, $id) {
        try {
            $entity = $this->getEntityManagerReference($entityClass, $id);
            $hydrator = new ClassMethods();
            $hydrator->hydrate($data, $entity);

            $em = $this->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return array(
                'error' => 0,
                'entity' => $entity,
            );
        } catch (Exception $e) {
            return array(
                'error' => 1,
                'msg' => $e->getMessage(),
            );
        }
    }

    public function delete($entity, $id) {
        try {
            $repository = $this->getEntityManager($entity);
            $FindEntity = $repository->find($id);

            $retorno = false;

            if ($FindEntity) {
                $entity = $this->getEntityManagerReference($entity, $id);
                $em = $this->getEntityManager();
                $em->remove($entity);
                $em->flush();
                $retorno = true;
            }

            return array(
                'error' => 0,
                'entity' => $entity,
            );
        } catch (Exception $e) {
            return array(
                'error' => 1,
                'msg' => $e->getMessage(),
            );
        }
    }

}
