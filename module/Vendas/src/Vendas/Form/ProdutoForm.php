<?php

namespace Vendas\Form;

use Zend\Form\Form;

class ProdutoForm extends Form {

    public function __construct(\Doctrine\ORM\EntityManager $entitymanager) {
        parent::__construct('produtos_form');

        $categorias = array('' => 'Selecione');
        $fornecedores = array('' => 'Selecione');

        $categorias += $entitymanager->getRepository('Vendas\Entity\Categoria')->getSelectOptions();
        $fornecedores += $entitymanager->getRepository('Vendas\Entity\Fornecedor')->getSelectOptions();

        $this->add(array(
            'name' => 'codProduto',
            'type' => 'Zend\Form\Element\Hidden'
        ));
        
        $this->add(array(
            'name' => 'nome',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'nome',
                'class' => 'form-control',
                'required' => 'required',
                'placeholder' => 'Nome do produto'
            ),
            'options' => array(
                'label' => 'Nome do Produto*',
                'label_attributes' => array(
                    'for' => 'nome'
                ),
            ),
        ));

        $this->add(array(
            'name' => 'descricao',
            'type' => 'Zend\Form\Element\Textarea',
            'attributes' => array(
                'id' => 'descricao',
                'class' => 'form-control',
                'placeholder' => 'Descrição do Produto',
                'rows' => 4
            ),
            'options' => array(
                'label' => 'Descrição do Produto',
                'label_attributes' => array(
                    'for' => 'descricao'
                ),
            ),
        ));

        $this->add(array(
            'name' => 'precoVenda',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'precoVenda',
                'class' => 'form-control campo-preco',
                'required' => 'required',
                'placeholder' => 'Preço de Venda'
            ),
            'options' => array(
                'label' => 'Preço de Venda*',
                'label_attributes' => array(
                    'for' => 'precoVenda'
                ),
            ),
        ));

        $this->add(array(
            'name' => 'precoFabrica',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'precoFabrica',
                'class' => 'form-control campo-preco',
                'required' => 'required',
                'placeholder' => 'Preço de Fábrica'
            ),
            'options' => array(
                'label' => 'Preço de Fábrica',
                'label_attributes' => array(
                    'for' => 'precoFabrica'
                ),
            ),
        ));

        $this->add(array(
            'name' => 'dtFabricacao',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'dtFabricacao',
                'class' => 'form-control campo-data',
                'placeholder' => 'Data de Fabricação'
            ),
            'options' => array(
                'label' => 'Data de Fabricação',
                'label_attributes' => array(
                    'for' => 'dtFabricacao'
                ),
            ),
        ));

        $this->add(array(
            'name' => 'dtValidade',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'dtValidade',
                'class' => 'form-control campo-data',
                'placeholder' => 'Data de Validade'
            ),
            'options' => array(
                'label' => 'Data de Validade',
                'label_attributes' => array(
                    'for' => 'dtValidade'
                ),
            ),
        ));

        $this->add(array(
            'name' => 'quantidade',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'quantidade',
                'class' => 'form-control',
                'required' => 'required',
                'placeholder' => 'Quantidade'
            ),
            'options' => array(
                'label' => 'Quantidade*',
                'label_attributes' => array(
                    'for' => 'quantidade'
                ),
            ),
        ));

        $this->add(array(
            'name' => 'desconto',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'desconto',
                'class' => 'form-control',
                'required' => 'required',
                'placeholder' => 'Desconto'
            ),
            'options' => array(
                'label' => 'Desconto',
                'label_attributes' => array(
                    'for' => 'desconto'
                ),
            ),
        ));

        $this->add(array(
            'name' => 'codCategoria',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'codCategoria',
                'class' => 'form-control',
                'required' => 'required'
            ),
            'options' => array(
                'label' => 'Categoria*',
                'label_attributes' => array(
                    'for' => 'codCategoria'
                ),
                'value_options' => $categorias
            ),
        ));

        $this->add(array(
            'name' => 'codFornecedor',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'codFornecedor',
                'class' => 'form-control',
                'required' => 'required'
            ),
            'options' => array(
                'label' => 'Fornecedor*',
                'label_attributes' => array(
                    'for' => 'codFornecedor'
                ),
                'value_options' => $fornecedores
            ),
        ));

        $this->add(array(
            'name' => 'numDiasAlertarVencimento',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'numDiasAlertarVencimento',
                'class' => 'form-control',
                'placeholder' => 'Dias antes do vencimento para começar a alertar.',
                'title' => 'Define o número de dias antes do vencimento para começar a alertar.'
            ),
            'options' => array(
                'label' => 'Dias alertar vencimento',
                'label_attributes' => array(
                    'for' => 'numDiasAlertarVencimento'
                )
            ),
        ));

        $this->add(array(
            'name' => 'btAdicionar',
            'type' => 'Zend\Form\Element\Button',
            'value' => 'Adicionar',
            'attributes' => array(
                'id' => 'btAdicionar',
                'class' => 'btn btn-primary',
                'onclick' => '$("#produtos_form").salvar("produtos");'
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
                'onclick' => '$("#produtos_form").cancelar("produtos");'
            ),
            'options' => array(
                'label' => 'Cancelar'
            )
        ));
    }

}
