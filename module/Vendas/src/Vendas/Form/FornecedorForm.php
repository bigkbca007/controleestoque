<?php

namespace Vendas\Form;

use Zend\Form\Form;

class FornecedorForm extends Form {

    public function __construct(\Doctrine\ORM\EntityManager $entitymanager) {
        parent::__construct('fornecedores_form');

        $this->add(array(
            'name' => 'codFornecedor',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => array(
                'id' => 'codFornecedor',
            ),
        ));
        
        $this->add(array(
            'name' => 'cnpj',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'cnpj',
                'class' => 'form-control',
                'required' => 'required',
                'placeholder' => 'CNPJ'
            ),
            'options' => array(
                'label' => 'CNPJ*',
                'label_attributes' => array(
                    'for' => 'cnpj'
                ),
            ),
        ));

        $this->add(array(
            'name' => 'nome',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'nome',
                'class' => 'form-control',
                'required' => 'required',
                'placeholder' => 'Nome do fornecedor'
            ),
            'options' => array(
                'label' => 'Fornecedor*',
                'label_attributes' => array(
                    'for' => 'nome'
                ),
            ),
        ));

        $this->add(array(
            'name' => 'email',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'email',
                'class' => 'form-control',
                'required' => 'required',
                'placeholder' => 'E-mail'
            ),
            'options' => array(
                'label' => 'E-mail',
                'label_attributes' => array(
                    'for' => 'email'
                ),
            ),
        ));

        $this->add(array(
            'name' => 'telefone',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'telefone',
                'class' => 'form-control',
                'required' => 'required',
                'placeholder' => 'Telefone'
            ),
            'options' => array(
                'label' => 'Telefone',
                'label_attributes' => array(
                    'for' => 'telefone'
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
                'onclick' => '$("#fornecedores_form").salvar("fornecedores");'
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
                'onclick' => '$("#fornecedores_form").cancelar("fornecedores");'
            ),
            'options' => array(
                'label' => 'Cancelar'
            )
        ));

    }

}
