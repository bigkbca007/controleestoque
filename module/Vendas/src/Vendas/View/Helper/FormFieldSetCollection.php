<?php

namespace Album\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormRow;

/**
 * Classe para renderização de formulários que permite o agrupamento de elementos.
 * @author Ederson Silva
 */
class FormFieldSetCollection extends FormRow {

    /**
     * Renderiza os elementos de um formulário, agrupando-os ou não, segundo determinados valores de atributos na definição do elemento na classe que define o formulário (Ex. SGC\Form\RequisicaoForm):
     * - "fieldsetid": Identificador do grupo. Todos os elemento que possuirem o mesmo fieldseid serão renderizados no mesmo painel (<div class="panel">).
     *                 O valor de fieldsetid será usado como título do painel. Se este atributo for string vazio, então o elemento ficará em um
     * - "fieldset_attributes": Atributos de elemento html que será aplicado ao div onde será renderizado o elemento (<div>).
     *   - "with_class": Classe bootstrap que determinará a largura do elemento. Exemplo: col-sm-6, col-md-3, etc. Se não for determinado, então usa-se o valor do parâmetro $fields_with_class.
     *   - "custom_style": Atributo html style que será aplicado ao div;
     * 
     * @param ElementInterface $form Objeto qeu herda de Zend\Form\Form contendo os elementos
     * @param type $labelPosition Where will be label rendered?
     * @param type $renderErrors Are the errors are rendered by this helper?
     * @param type $partial
     * @param type $fields_with_class Largura padrão para cada elemento. Na classe que define o formulário pode-se determinar uma largura para o elemento através do camp "widh_class" do atributo "fieldset_attributes".
     * @return string
     */
    public function __invoke(ElementInterface $form = null, $labelPosition = 'prepend', $renderErrors = 1, $partial = null, $fields_with_class = 'col-sm-6') {
        $this->labelPosition = $labelPosition;
        $this->renderErrors = $renderErrors;
        $this->partial = $partial;

        $elements = array();
        foreach ($form->getElements() as $el) {
            $fieldsetid = $el->getAttribute('fieldsetid');
            if (is_null($fieldsetid)) {
                $elements[] = $el;
            } else {
                $elements[$fieldsetid][] = $el;
            }
        }

        unset($el);

        //Montando o html
        $markup = '';
        $style = '';
        foreach ($elements as $key => $el) {
            if (is_a($el, 'Zend\Form\Element')) {
                $classes = $el->getAttribute('class');
                //Faz com que os botões seja renderizados lado a lado
                if (is_a($el, 'Zend\Form\Element\Button')) {
                    $markup .= $this->render($el);
                } else {
                    $markup .= '<div class="form-group">';
                    $markup .= '    <div class="' . $fields_with_class . '">';
                    $markup .= $this->render($el);
                    $markup .= '    </div>';
                    $markup .= '</div>';
                }
            } else {
                //array de elementos
                $markup .= '<div class="panel panel-default">';
                $markup .= $key?'    <div class="panel-heading">' . $key . '</div>':'';
                $markup .= '    <div class="panel-body">';
                $markup .= '        <div class="form-group">';
                foreach ($el as $e) {

                    $fieldset_attributes = $e->getAttribute('fieldset_attributes');
                    $with_class = false;
                    if (isset($fieldset_attributes['with_class'])) {
                        $with_class = $fieldset_attributes['with_class'];
                    }
                    $custom_style = false;
                    if (isset($fieldset_attributes['custom_style'])) {
                        $custom_style = $fieldset_attributes['custom_style'];
                    }
                    $markup .= '            <div class="' . ($with_class ? $with_class : $fields_with_class) . '" style="' . ($custom_style ? $custom_style : $style) . '">';
                    $markup .= $this->render($e);
                    $markup .= '            </div>';
                }

                $markup .= '        </div>';
                $markup .= '    </div>';
                $markup .= '</div>';
            }
        }

        return $markup;
    }

}

/*
<div class="form-group">
    <div class="col-sm-4">
        <label for="codProduto">Produto*</label>
        <select name="codProduto" id="codProduto" class="form-control" required="required"><option value="">Selecione</option>
            <option value="16">0016 - Aflorá (1 em estoque)</option>
            <option value="18">0018 - Care (1 em estoque)</option>
            <option value="17">0017 - Foot Works (1 em estoque)</option>
        </select>
    </div>
    <div class="col-sm-4">
        <label for="precoPago">Preço do Produto*</label>                <div class="input-group">
            <div class="input-group-addon">R$</div>
            <input type="text" name="precoPago" id="precoPago" class="form-control" required="required" placeholder="Preço do Produto*" value="">
        </div>
    </div>
    <div class="col-sm-4">
        <label for="quantidade">Quantidade*</label><input type="text" name="quantidade" id="quantidade" class="form-control" placeholder="Quantidade" value="">
    </div>
</div>
 */