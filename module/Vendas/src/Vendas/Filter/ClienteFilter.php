<?php

namespace Vendas\Filter;

use Zend\InputFilter\InputFilter;

class ClienteFilter extends InputFilter {

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
                        'max' => 200,
                        'messages' => array(
                            \Zend\Validator\StringLength::TOO_LONG => 'O nome do cliente deve ter de 1 a 200 caracteres.',
                            \Zend\Validator\StringLength::TOO_SHORT => 'O nome do cliente deve ter de 1 a 200 caracteres.'
                        ),
                    ),
                ),
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
                        'pattern' => '/^\(\d{2}\)\s\d{5}\-\d{4}/',
                        'messages' => array(
                            \Zend\Validator\Regex::NOT_MATCH => 'Telefone inválido. Deve estar no formado (99) 99999-9999.'
                        ),
                    )

                )
            )
        ));

        $this->add(array(
            'name' => 'cpf',
            'required' => false,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'Regex',
                    'options' => array(
                        'pattern' => '/^\d{3}\.\d{3}\.\d{3}\-\d{2}/',
                        'messages' => array(
                            \Zend\Validator\Regex::NOT_MATCH => 'CPF inválido. Deve estar no formado 999.999.999-99.'
                        ),
                    )

                )
            )
        ));

        $this->add(array(
            'name' => 'rg',
            'required' => false,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'Regex',
                    'options' => array(
                        'pattern' => '/^\d{8}\s\d{2}$/',
                        'messages' => array(
                            \Zend\Validator\Regex::NOT_MATCH => 'RG inválido. Deve estar no formado 99999999 99.'
                        ),
                    )

                )
            )
        ));

        $this->add(array(
            'name' => 'sexo',
            'required' => false,
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            \Zend\Validator\NotEmpty::IS_EMPTY => 'Informe o sexo.'
                        ),
                    ),
                ),
            )
        ));
    }

}
