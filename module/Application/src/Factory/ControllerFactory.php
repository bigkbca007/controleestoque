<?php

namespace Application\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Description of ControllerFactory
 *
 * @author ederson.silva
 */

class ControllerFactory
{
    public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null) 
    {        
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $configs = $container->get('config');
        return new $requestedName( $container, $entityManager, $configs );
    }
}