<?php

namespace Vendas\Filter;

use Zend\InputFilter\InputFilter;

class ProdutoFilter extends InputFilter {

    public function __construct() {

        $this->add(array(
            'name' => 'nome',
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
                            \Zend\Validator\NotEmpty::IS_EMPTY => 'Informe o nome.'
                        ),
                    ),
                ),
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'min' => 1,
                        'max' => 100,
                        'messages' => array(
                            \Zend\Validator\StringLength::TOO_LONG => 'O nome do produto deve ter de 1 a 100 caracteres.',
                            \Zend\Validator\StringLength::TOO_SHORT => 'O nome do produto deve ter de 1 a 100 caracteres.'
                        ),
                    ),
                ),
            )
        ));

        $this->add(array(
            'name' => 'codCategoria',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            \Zend\Validator\NotEmpty::IS_EMPTY => 'Informe a categoria.'
                        ),
                    ),
                ),
            )
        ));

        $this->add(array(
            'name' => 'codFornecedor',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            \Zend\Validator\NotEmpty::IS_EMPTY => 'Informe o fornecedor.'
                        ),
                    ),
                ),
            )
        ));

        $this->add(array(
            'name' => 'precoVenda',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            \Zend\Validator\NotEmpty::IS_EMPTY => 'Informe a preço de venda.'
                        ),
                    ),
                ),
            )
        ));

        $this->add(array(
            'name' => 'precoFabrica',
            'required' => false,
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            \Zend\Validator\NotEmpty::IS_EMPTY => 'Informe a preço de fábrica.'
                        ),
                    ),
                ),
            )
        ));

        $this->add(array(
            'name' => 'desconto',
            'required' => false,
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            \Zend\Validator\NotEmpty::IS_EMPTY => 'Informe o desconto.'
                        ),
                    ),
                ),
            )
        ));

        $this->add(array(
            'name' => 'quantidade',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            \Zend\Validator\NotEmpty::IS_EMPTY => 'Informe a quantidade.'
                        ),
                    ),
                ),
            )
        ));

    }

}
