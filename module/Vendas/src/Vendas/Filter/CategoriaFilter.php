<?php

namespace Vendas\Filter;

use Zend\InputFilter\InputFilter;

class CategoriaFilter extends InputFilter {

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
                        'max' => 45,
                        'messages' => array(
                            \Zend\Validator\StringLength::TOO_LONG => 'O nome da categoria deve ter de 1 a 45 caracteres.',
                            \Zend\Validator\StringLength::TOO_SHORT => 'O nome da categoria deve ter de 1 a 45 caracteres.'
                        ),
                    ),
                ),
            )
        ));
    }

}
