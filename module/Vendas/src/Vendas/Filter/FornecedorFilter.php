<?php

namespace Vendas\Filter;

use Zend\InputFilter\InputFilter;

class FornecedorFilter extends InputFilter {

    public function __construct() {

        $this->add(array(
            'name' => 'cnpj',
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
                            \Zend\Validator\NotEmpty::IS_EMPTY => 'Informe o CNPJ.'
                        ),
                    ),
                ),
                array(
                    'name' => 'Regex',
                    'options' => array(
                        'pattern' => '/^\d{2}\.\d{3}\.\d{3}\/\d{4}\-\d{2}/',
                        'messages' => array(
                            \Zend\Validator\Regex::NOT_MATCH => 'CNPJ inválido. Deve estar no formado 99.999.999/9999-99.'
                        ),
                    )

                )
            )
        ));

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
                            \Zend\Validator\NotEmpty::IS_EMPTY => 'Informe o nome do fornecedor.'
                        ),
                    ),
                ),
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'min' => 1,
                        'max' => 150,
                        'messages' => array(
                            \Zend\Validator\StringLength::TOO_LONG => 'O nome do fornecedor deve ter de 1 a 100 caracteres.',
                            \Zend\Validator\StringLength::TOO_SHORT => 'O nome do fornecedor deve ter de 1 a 100 caracteres.'
                        ),
                    ),
                ),
            )
        ));

        $this->add(array(
            'name' => 'telefone',
            'required' => false,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'Regex',
                    'options' => array(
                        'pattern' => '/^\(\d{2}\)\s\d{4}\-\d{4}/',
                        'messages' => array(
                            \Zend\Validator\Regex::NOT_MATCH => 'Telefone inválido. Deve estar no formado (99) 9999-9999.'
                        ),
                    )

                )
            )
        ));

        $this->add(array(
            'name' => 'email',
            'required' => false,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'Regex',
                    'options' => array(
                        'pattern' => '/^[a-zA-Z0-9][a-zA-Z0-9\._-]+@([a-zA-Z0-9\._-]+\.)[a-zA-Z-0-9]{2,3}/',
                        'messages' => array(
                            \Zend\Validator\Regex::NOT_MATCH => 'E-mail inválido.'
                        ),
                    )

                )
            )
        ));
    }
}
