jQuery(document).ready(function ($) {
            mostrarModalMsg({
                type:'info',
                txt_header: 'PAREI AQUI',
                txt_body: 'TESTAR O E DEVOLVER REMOVER VENDA'
            });

    $(".dataTables_filter input").remove();

    //Aplicando máscaras
    $('.dinheiro').mask("#.##0,00", {reverse: true});
    $('input[id*=quantidade-], .numero-6').mask("#####9");
    $('input[id*=desconto-], .numero-2').mask("#9");

    // Subgrid
    var dg = $('#grid-estoque-vendas').datagrid({
        view: detailview,
        detailFormatter: function (index, row) {
            return '<div style="padding:2px"><table id="ddv-' + index + '" width="100%"></table></div>';
        },
        onExpandRow: function (index, row) {
            $('#ddv-' + index).datagrid({
                "title": "<p style='font-size:12px;color:#659ffb;'>Produtos Comprados</p>",
                "url": "/vendas/getprodutoscliente?id=" + row.Venda_codVenda+"&linha="+index,
                "method": "GET",
                nowrap: false,
                fitColumns:1,
                "columns": [[
                        {"field": "Produto_codProduto", "hidden": true},
                        {"field": "Produto_nome", "title": "Produto", "width": "19%"},
                        {"field": "Produto_precoPago", "title": "Preço", "width": "8%"},
                        {"field": "Produto_descricao", "title": "Descrição", "width": "33%"},
                        {"field": "VendaHasProduto_quantidade", "title": "Quantidade", "width": "8%"},
                        {"field": "Categoria_nome", "title": "Categoria", "width": "17%"},
                        {"field": "Fornecedor_nome", "title": "Fornecedor", "width": "8%"},
                        {"field": "acao", "width": "7%"}
                    ]],
                onResize: function () {
                    dg.datagrid('fixDetailRowHeight', index);
                },
                onLoadSuccess: function () {
                    setTimeout(function () {
                        dg.datagrid('fixDetailRowHeight', index);
                    }, 0);
                }
            });
        },
    });

});

/**
 * Abre modal com formulário para editar produto.
 * @param {int} codVenda
 * @param {int} codPoduto
 * @param {int} linha
 * @returns {void}
 */

function modalDevolverProduto(codVenda, codProduto, linha){

    var comboProdutos = $('#modelo-clone-produto').find('#codProduto-0').clone();
    comboProdutos.attr('name','codProduto').attr('id','codProduto');
    var form = '';
    form += '<div clas="row">';
    form += '    <div clas="col-lg-12">';
    form += '        <form id="edit_produto_form">';
    form += '            <div class="form-group col-lg-12">';
    form += '                <label>Devolver o produto?</label>';
    form += '                <div class="radio">';
    form += '                    <label>';
    form += '                        <input type="radio" name="devolver" id="devolverNao" value="0" checked> Não';
    form += '                    </label>';
    form += '                </div>';
    form += '                <div class="radio">';
    form += '                    <label>';
    form += '                        <input type="radio" name="devolver" id="devolverSim" value="1"> Sim';
    form += '                    </label>';
    form += '                </div>';
    form += '            </div>';
    //form += '            <div class="form-group col-lg-9 hide compo-justificativa">';
    //form += '                <label for="justificativa">Justificativa</label>';
    //form += '                <textarea rows="4" id="justificativa" class="form-control" style="resize:none;" disabled></textarea>';
    //form += '            </div>';
    form += '            <div class="form-group col-lg-3 hide compo-quantidade">';
    form += '                <strong id="qtde-produtos-da-venda" class="text-primary"></strong><br>';
    form += '                <strong id="qtde-produtos-disponiveis" style="color:red;"></strong><br>';
    form += '                <label for="quantidadeRemover">Quantidade</label>';
    form += '                <input type="number" id="quantidadeRemover" class="form-control" placeholder="Quantidade a Devolver" disabled>';
    form += '            </div>';
    form += '        </form>';
    form += '        <br style="clear: both;">';
    form += '    </div>';
    form += '</div>';
    
    mostrarModalMsg({
        type: 'warning',
        txt_header: 'Devolver Produto',
        txt_body: form,
        id: 'modal-devolver-produto',
        buttons:[
            {type: 'default', text: 'Fechar', attributes:{'data-dismiss': 'modal'}},
            {type: 'warning', text: 'Devolver', attributes:{onclick: 'devolverProduto('+codVenda+', '+codProduto+', '+linha+')', class:'hide', id: 'bt-devolver'}},
        ],
        //width: .7,
        event: [{
                type: 'shown.bs.modal',
                handler: function(){
                    // Busca quantidade do produto disponível no banco.
                    $.ajax({
                        url: getAbsoluteUrl('/vendas/getNumProdutosDisponiveis'),
                        method: 'POST',
                        dataType: 'json',
                        data:{
                            codVenda:codVenda,
                            codProduto:codProduto
                        }
                    }).done(function(resp){
                        if(1 == resp.success){
                            if(resp.quantidadeProdutos < 2){
                                $('#qtde-produtos-disponiveis').html(resp.quantidadeProdutos+' unidade deste produto disponível no estoque');
                            } else {
                                $('#qtde-produtos-disponiveis').html(resp.quantidadeProdutos+' unidades deste produto disponíveis no estoque');
                            }
                            if(resp.quantidadeProdutosVenda < 2){
                                $('#qtde-produtos-da-venda').html(resp.quantidadeProdutosVenda+' unidade deste produto nesta venda');
                            } else {
                                $('#qtde-produtos-da-venda').html(resp.quantidadeProdutosVenda+' unidades deste produto nesta venda');
                            }
                        }
                    });

                    $('input[name=devolver]').change(function(){
                        if(1 == $(this).val()){
                            //$('.compo-justificativa').removeClass('hide');
                            //$('#justificativa').attr('disabled', false);
                            $('.compo-quantidade').removeClass('hide');
                            $('#quantidadeRemover').attr('disabled', false);
                            $('#bt-devolver').removeClass('hide');
                        } else {                            
                            //$('.compo-justificativa').addClass('hide');
                            //$('#justificativa').attr('disabled', true);
                            $('.compo-quantidade').addClass('hide');
                            $('#quantidadeRemover').attr('disabled', true);
                            $('#bt-devolver').addClass('hide');
                        }
                    });
                }
        }]
    });
}

function devolverProduto(codVenda, codProduto, linha){
    // Validação
    if(1 == $('input[name=devolver]:checked').val()){
        var msg = '';
        if(0 == $('input[name=devolver]:checked').val()){            
            msg = "Selecione uma opção em <strong>Devolver o produto?</strong>.";
        }/* else if('' == $('#justificativa').val().trim()){
            msg = "Preecha o campo <strong>Justificativa</strong>.";
        }*/ else if('' == $('#quantidadeRemover').val()){
            msg = "Preecha o campo <strong>Quantidade</strong>.";
        } else if($('#quantidadeRemover').val() < 1){
            msg = "A <strong>Quantidade</strong> deve ser maior do que 0.";
        }
        
        if('' != msg){
            mostrarModalMsg({
                type: 'danger',
                txt_header: 'Erro',
                txt_body: msg
            });
            return;
        }
    }
    
    $.ajax({
        url:getAbsoluteUrl('/vendas/devolverProduto'),
        method: 'POST',
        dataType: 'json',
        data: {
            codVenda:codVenda,
            codProduto:codProduto,
            quantidadeRemover:$('#quantidadeRemover').val(),
            //justificativa:$('#justificativa').val().trim(),
        }
    }).done(function(resp){
        if(resp.success){
            mostrarModalMsg({
                type:'success',
                txt_header: 'Produto devolvido',
                txt_body: 'Produto devolvido com sucesso.'
            });
            try{
                if(resp.removeuVenda){
                    $('#grid-estoque-vendas').datagrid('reload');
                } else {                    
                    $('#ddv-'+linha).datagrid('reload');
                }
                
            } catch(e){
                mostrarModalMsg({type:'danger',txt_header: 'Erro', txt_body:e});
            }
            $('#modal-devolver-produto').modal('hide');
            
            $.fn.atualizarCombosProdutos();
        } else {
            var message = 'Ocorreu um erro ao tentar devolver o produto.';
            if(resp.msg){
                message = resp.msg;
            }
            mostrarModalMsg({
                type:'danger',
                txt_header: 'Erro',
                txt_body: message
            });
        }
    });
}

/**
 * 
 * @param {int} tipoListagem 1 = listar ativos. 2 = listar inativos. 3 = listar todos.
 * @returns {void}
 */
function mudarListagem(tipoListagem){
    var url = $('#grid-estoque-vendas').datagrid('options').url.split('?')[0] + '?tipoListagem=' + tipoListagem;
    $('#grid-estoque-vendas').datagrid('reload',url);
    
    $("#listar-ativos, #listar-inativos, #listar-todos").removeClass('ativo');
    switch(tipoListagem){
        case 0: $("#listar-inativos").addClass('ativo'); break;
        case 1: $("#listar-ativos").addClass('ativo'); break;
        case 2: $("#listar-todos").addClass('ativo'); break;
    }
}