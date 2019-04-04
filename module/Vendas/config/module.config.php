<?php

namespace Vendas;

return array(
    'controllers' => [
        'factories' => [
            Controller\VendasController::class => \Application\Factory\ControllerFactory::class,
            Controller\ProdutosController::class => \Application\Factory\ControllerFactory::class,
            Controller\CategoriasController::class => \Application\Factory\ControllerFactory::class,
            Controller\ClientesController::class => \Application\Factory\ControllerFactory::class,
            Controller\FornecedoresController::class => \Application\Factory\ControllerFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
//            'home' => [
//                'type' => \Zend\Router\Http\Literal::class,
//                'options' => [
//                    'route' => '/',
//                    'defaults' => [
//                        'controller' => Controller\VendasController::class,
//                        'action' => 'index',
//                    ],
//                ],
//            ],
            'vendas' => [
                'type' => \Zend\Router\Http\Literal::class,
                'options' => [
                    'route' => '/vendas',
                    'defaults' => [
                        'controller' => Controller\VendasController::class,
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => array(
                    'getdata' => array(
                        'type' => \Zend\Router\Http\Literal::class,
                        'options' => array(
                            'route' => '/getdata',
                            'defaults' => array(
                                'controller' => Controller\VendasController::class,
                                'action' => 'getdata',
                            ),
                        ),
                    ),
                    'getdatagrid' => array(
                        'type' => \Zend\Router\Http\Literal::class,
                        'options' => array(
                            'route' => '/getdatagrid',
                            'defaults' => array(
                                'controller' => Controller\VendasController::class,
                                'action' => 'getdatagrid',
                            ),
                        ),
                    ),
                    'produtos' => array(
                        'type' => \Zend\Router\Http\Segment::class,
                        'options' => array(
                            'route' => '/produtos[/:action]',
                            'defaults' => array(
                                'controller' => Controller\ProdutosController::class,
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'salvarajax' => array(
                        'type' => \Zend\Router\Http\Segment::class,
                        'options' => array(
                            'route' => '/salvarajax',
                            'defaults' => array(
                                'controller' => Controller\VendasController::class,
                                'action' => 'salvarajax',
                            ),
                        ),
                    ),
                    'getdataform' => array(
                        'type' => \Zend\Router\Http\Segment::class,
                        'options' => array(
                            'route' => '/getdataform[/:id]',
                            'defaults' => array(
                                'controller' => Controller\VendasController::class,
                                'action' => 'getdataform',
                            ),
                        ),
                    ),
                    'removerajax' => array(
                        'type' => \Zend\Router\Http\Segment::class,
                        'options' => array(
                            'route' => '/removerajax',
                            'defaults' => array(
                                'controller' => Controller\VendasController::class,
                                'action' => 'removerajax',
                            ),
                        ),
                    ),
                    'getdadosprodutovenda' => array(
                        'type' => \Zend\Router\Http\Segment::class,
                        'options' => array(
                            'route' => '/getDadosProdutoVenda',
                            'defaults' => array(
                                'controller' => Controller\VendasController::class,
                                'action' => 'getdadosprodutovenda',
                            ),
                        ),
                    ),
                    'getdesconto' => array(
                        'type' => \Zend\Router\Http\Segment::class,
                        'options' => array(
                            'route' => '/getDesconto',
                            'defaults' => array(
                                'controller' => Controller\VendasController::class,
                                'action' => 'getdesconto',
                            ),
                        ),
                    ),
                    'categorias' => array(
                        'type' => \Zend\Router\Http\Segment::class,
                        'options' => array(
                            'route' => '/categorias[/:action]',
                            'defaults' => array(
                                'controller' => Controller\CategoriasController::class,
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'clientes' => array(
                        'type' => \Zend\Router\Http\Segment::class,
                        'options' => array(
                            'route' => '/clientes[/:action]',
                            'defaults' => array(
                                'controller' => Controller\ClientesController::class,
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'fornecedores' => array(
                        'type' => \Zend\Router\Http\Segment::class,
                        'options' => array(
                            'route' => '/fornecedores[/:action]',
                            'defaults' => array(
                                'controller' => Controller\FornecedoresController::class,
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'getprodutoscliente' => array(
                        'type' => \Zend\Router\Http\Segment::class,
                        'options' => array(
                            'route' => '/getprodutoscliente',
                            'defaults' => array(
                                'controller' => Controller\VendasController::class,
                                'action' => 'getprodutoscliente',
                            ),
                        ),
                    ),
                    'getNumProdutosDisponiveis' => array(
                        'type' => \Zend\Router\Http\Segment::class,
                        'options' => array(
                            'route' => '/getNumProdutosDisponiveis',
                            'defaults' => array(
                                'controller' => Controller\VendasController::class,
                                'action' => 'getNumProdutosDisponiveis',
                            ),
                        ),
                    ),
                    'devolverProduto' => array(
                        'type' => \Zend\Router\Http\Segment::class,
                        'options' => array(
                            'route' => '/devolverProduto',
                            'defaults' => array(
                                'controller' => Controller\VendasController::class,
                                'action' => 'devolverProduto',
                            ),
                        ),
                    ),
                    'ativarajax' => array(
                        'type' => \Zend\Router\Http\Segment::class,
                        'options' => array(
                            'route' => '/ativarajax',
                            'defaults' => array(
                                'controller' => Controller\VendasController::class,
                                'action' => 'ativarAjax',
                            ),
                        ),
                    ),
		),
            ],
        ],
    ],

    'service_manager' => array(
        'factories' => array(
            'Vendas\Service\ControleEstoqueService' => 'Application\Factory\ControllerFactory',
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'vendas' => __DIR__ . '/../view',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                )
            )
        ),
    ),
    'view_helpers' => [
        'invokables' => [
            'gridEasyuiHelper' => View\Helper\GridEasyuiHelper::class
        ]
    ]
);
