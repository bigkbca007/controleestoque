<?php

namespace Vendas\Filter;

use Zend\InputFilter\InputFilter;

class VendaFilter extends InputFilter {

    public function __construct() {

        $this->add(array(
            'name' => 'codProduto-1',
            'required' => false,
        ));
        $this->add(array(
            'name' => 'codProduto-1',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            \Zend\Validator\NotEmpty::IS_EMPTY => 'Informe o produto 1.'
                        ),
                    ),
                ),
            )
        ));

        $this->add(array(
            'name' => 'precoPago-1',
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
                            \Zend\Validator\NotEmpty::IS_EMPTY => 'Informe o proço do produto 1.'
                        ),
                    ),
                ),
            )            
        ));

        $this->add(array(
            'name' => 'quantidade-1',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            \Zend\Validator\NotEmpty::IS_EMPTY => 'Informe a quantidade do produto 1.'
                        ),
                    ),
                ),
            )            
        ));

        $this->add(array(
            'name' => 'dtVenda',
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
                            \Zend\Validator\NotEmpty::IS_EMPTY => 'Informe a data da venda.'
                        ),
                    ),
                ),
            )
        ));

        $this->add(array(
            'name' => 'stPago',
            'required' => true,
        ));

        $this->add(array(
            'name' => 'codCliente',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            \Zend\Validator\NotEmpty::IS_EMPTY => 'Informe o cliente.'
                        ),
                    ),
                ),
            )            
        ));

    }

}
