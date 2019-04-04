<?php

namespace Vendas\Form;

use Zend\Form\Form;

class VendaForm extends Form {

    public function __construct(\Doctrine\ORM\EntityManager $entitymanager, \Vendas\Entity\Produto $produto = null) {
        parent::__construct('vendas_form');

        $produtos = array('' => '');
        $clientes = array('' => '');

        $produtos += $entitymanager->getRepository('Vendas\Entity\Produto')->getSelectOptions(true);
        $clientes += $entitymanager->getRepository('Vendas\Entity\Cliente')->getSelectOptions();

        if(!is_null($produto)){
            $produtos += array($produto->getCodProduto() => $produto->getNome());
        }

        $this->add(array(
            'name' => 'codVenda',
            'type' => 'Zend\Form\Element\Hidden'
        ));
        
        $this->add(array(
            'name' => 'codProduto-1',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'codProduto-1',
                'class' => 'form-control easyui-combobox',
                'onchange' => '$.fn.getDadosProdutoVenda(this);'
            ),
            'options' => array(
                'label' => 'Produto*',
                'label_attributes' => array(
                    'for' => 'codProduto-1'
                ),
                'value_options' => $produtos
            ),
        ));
                        
        $this->add(array(
            'name' => 'codProduto-0',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'codProduto-0',
                'class' => 'form-control',
                'onchange' => '$.fn.getDadosProdutoVenda(this);'
            ),
            'options' => array(
                'label' => 'Produto*',
                'label_attributes' => array(
                    'for' => 'codProduto-0'
                ),
                'value_options' => $produtos
            ),
        ));

        $this->add(array(
            'name' => 'precoPago-1',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'precoPago-1',
                'class' => 'form-control dinheiro',
                'placeholder' => 'Preço do Produto*'
            ),
            'options' => array(
                'label' => 'Preço do Produto*',
                'label_attributes' => array(
                    'for' => 'precoPago'
                ),
            ),
        ));

        $this->add(array(
            'name' => 'codCliente',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'codCliente',
                'class' => 'form-control easyui-combobox',                
            ),
            'options' => array(
                'label' => 'Cliente*',
                'label_attributes' => array(
                    'for' => 'codCliente'
                ),
                'value_options' => $clientes
            ),
        ));

        $this->add(array(
            'name' => 'dtVenda',
            'type' => '\Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'dtVenda',
                'class' => 'form-control campo-data-hora',
                'placeholder' => 'Data da venda',
            ),
            'options' => array(
                'label' => 'Data da Venda*',
                'label_attributes' => array('for' => 'dtVenda')
            ),
        ));

        $this->add(array(
            'name' => 'dtPagamento',
            'type' => '\Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'dtPagamento',
                'class' => 'form-control campo-data',
                'placeholder' => 'Data para pagamento',
            ),
            'options' => array(
                'label' => 'Data para pagamento',
                'label_attributes' => array('for' => 'dtPagamento')
            ),
        ));

        $this->add(array(
            'name' => 'quantidade-1',
            'type' => '\Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'quantidade-1',
                'class' => 'form-control',
                'placeholder' => 'Quantidade',
            ),
            'options' => array(
                'label' => 'Quantidade*',
                'label_attributes' => array('for' => 'quantidade')
            ),
        ));

        $this->add(array(
            'name' => 'desconto-1',
            'type' => '\Zend\Form\Element\Text',
            'attributes' => array(
                'id' => 'desconto-1',
                'class' => 'form-control',
                'placeholder' => 'Desconto',
                'title' => 'Desconto aplicado sobro o preço do produto cadastrado.',
                'onkeyup' => '$.fn.aplicarDesconto(this)'
            ),
            'options' => array(
                'label' => 'Desconto',
                'label_attributes' => array('for' => 'desconto'),
            ),
        ));

        $this->add(array(
            'name' => 'stPago',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'stPago',
                'class' => 'form-control',                
            ),
            'options' => array(
                'label' => 'Produto pago?*',
                'label_attributes' => array(
                    'for' => 'stPago'
                ),
                'value_options' => array(
                    '0' => 'Não',
                    '1' => 'Sim',
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
                'onclick' => '$("#vendas_form").salvar("vendas");'
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
                'onclick' => '$("#vendas_form").cancelar("vendas");'
            ),
            'options' => array(
                'label' => 'Cancelar'
            )
        ));

    }

}
