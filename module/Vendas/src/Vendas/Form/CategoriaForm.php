<?php

namespace Vendas\Form;

use Zend\Form\Form;

class CategoriaForm extends Form {

    public function __construct(\Doctrine\ORM\EntityManager $entitymanager) {
        parent::__construct('categorias_form');

        $this->add(array(
            'name' => 'codCategoria',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => array(
                'id' => 'codCategoria',
            ),
        ));

        $this->add(array(
            'name' => 'nome',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'nome',
                'class' => 'form-control',
                'required' => 'required',
                'placeholder' => 'Nome da categoria'
            ),
            'options' => array(
                'label' => 'Nome*',
                'label_attributes' => array(
                    'for' => 'nome'
                ),
            ),
        ));

        $this->add(array(
            'name' => 'btAdicionar',
            'type' => 'Zend\Form\Element\Button',
            'value' => 'Adicionar',
            'attributes' => array(
                'id' => 'btAdicionar',
                'class' => 'btn btn-primary',
                'onclick' => '$("#categorias_form").salvar("categorias");'
            ),
            'options' => array(
                'label' => 'Salvar'
            )
        ));

        $this->add(array(
            'name' => 'btCancelar',
            'type' => 'Zend\Form\Element\Button',
            'value' => 'Adicionar',
            'attributes' => array(
                'id' => 'btCancelar',
                'class' => 'btn btn-deafult hide',
                'onclick' => '$("#categorias_form").cancelar("categorias");'
            ),
            'options' => array(
                'label' => 'Cancelar'
            )
        ));

    }

}
