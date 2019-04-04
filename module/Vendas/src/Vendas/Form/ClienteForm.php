<?php

namespace Vendas\Form;

use Zend\Form\Form;

class ClienteForm extends Form {

    public function __construct(\Doctrine\ORM\EntityManager $entitymanager) {
        parent::__construct('clientes_form');

        $clientes = array('' => 'Selecione');

        $clientes += $entitymanager->getRepository('Vendas\Entity\Cliente')->getSelectOptions();

        $this->add(array(
            'name' => 'codCliente',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => array(
                'id' => 'codCliente',
            ),
        ));

        $this->add(array(
            'name' => 'nome',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'nome',
                'class' => 'form-control',
                'required' => 'required',
                'placeholder' => 'Nome do cliente'
            ),
            'options' => array(
                'label' => 'Nome*',
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
            'name' => 'cpf',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'cpf',
                'class' => 'form-control',
                'required' => 'required',
                'placeholder' => 'CPF'
            ),
            'options' => array(
                'label' => 'CPF',
                'label_attributes' => array(
                    'for' => 'cpf'
                ),
            ),
        ));

        $this->add(array(
            'name' => 'rg',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'rg',
                'class' => 'form-control',
                'required' => 'required',
                'placeholder' => 'RG'
            ),
            'options' => array(
                'label' => 'RG',
                'label_attributes' => array(
                    'for' => 'rg'
                ),
            ),
        ));

        $this->add(array(
            'name' => 'sexo',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'sexo',
                'class' => 'form-control',
                'required' => 'required',
                'placeholder' => 'Sexo'
            ),
            'options' => array(
                'label' => 'Sexo',
                'label_attributes' => array(
                    'for' => 'sexo'
                ),
                'value_options' => array('M' => 'Masculino', 'F' => 'Feminino')
            ),
        ));

        $this->add(array(
            'name' => 'btAdicionar',
            'type' => 'Zend\Form\Element\Button',
            'value' => 'Adicionar',
            'attributes' => array(
                'id' => 'btAdicionar',
                'class' => 'btn btn-primary',
                'onclick' => '$("#clientes_form").salvar("clientes");'
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
                'onclick' => '$("#clientes_form").cancelar("clientes");'
            ),
            'options' => array(
                'label' => 'Cancelar'
            )
        ));

    }

}
