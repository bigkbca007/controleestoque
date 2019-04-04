(function ($) {

    $('.datagrid-wrap.panel-body').css({padding: '0px'});

    var LOAD_IMG = '/img/loading6.gif';
    var LOAD_MSG = 'AGUARDE...';

    $(document).ajaxStart(function () {
        var textCarregando = '<h1><img src="' + LOAD_IMG + '" style="width:100px;" /> ' + LOAD_MSG + '</h1>';
        mostrarModalCarregando(textCarregando);
    });
    $(document).ajaxStop(function () {
        esconderModalCarregando();
    });

    // Extende o easyui para adicionar função para voltar o comobobox autocomplete para select normal
    $.extend($.fn.combo.methods, {
        removecombo: function (jq) {
            return jq.each(function () {
                var state = $.data(this, 'combo');
                state.panel.panel('destroy');
                state.combo.remove();
                $(this).removeClass('combo-f combobox-f').show();
            });
        }
    });

    $.fn.extend({
        table: null,

        /**
         * Função para salvar um item. Dever ser chamado por um form. Exemplo: $('#id-form').salvar();
         * 
         * @param string item_id Componente do id da datatable ('item_id'+'-table') e do form ('item_id'+'_form')
         * @returns {undefined}
         */
        salvar: function (item_id) {
            try {
                //Removendo mensagem (de erro, warnings, etc.) de tentativas anteriros
                var thisEl = $(this);
                var selector = $('#listagem');
                //Remove mensagens de avisos, caso haja, para avitar empilhamento de mensagens.
                $('#system_messages').remove();

                var inputs = $(this).find('input,select,textarea');
                var dt = $('#' + item_id + '-table').DataTable();
                var grid = $('#grid-estoque-' + item_id);
                var dataInputs = []; // Dados dos inputs

                // Monta objeto json com os campos do form
                $.each(inputs, function (i, input) {
                    if('undefined' !== typeof $(input).attr('name')){
                        var val = $(input).val().indexOf('\n') == -1 ? $(input).val() : JSON.stringify($(input).val()).replace(/\"/g, '');
                        dataInputs.push('"' + $(input).attr('name') + '":"' + val + '"');
                    }
                });
                //Verifica se está editando um item que não possui quantidade em estoque
                if ($('#codProduto option.temp_edit').length > 0) {
                    dataInputs.push('"editProdSemQtde":1');
                }
                
                // Informa a quantidade de produtos adicionais para filtrá-los.
                dataInputs.push('"numProdutosAdicionais":'+ ($('#vendas_form').find('div[id*=produto-]').length - 1));

                var jsonDataStr = '{' + dataInputs.join(',') + '}';
                var url = $(this).attr('action');

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: JSON.parse(jsonDataStr)
                }).done(function (resp) {
                    var r = JSON.parse(resp);
                    if (1 == r.success) {
                        $('*[id*=grid-estoque-]').datagrid('reload');
                        $('#'+item_id+'_form')[0].reset();

                        //$('#btCancelar').addClass('hide');
                        //$('#btAdicionar').html('Salvar');
                        //$.fn.atualizarCombobox();
                        $.fn.esconderFormAdd($('#' + item_id + '_form'));

                        //Se for o móduolo de vendas, então atualiza o combo de produtos
                        if ('vendas' == item_id) {
                            $.fn.atualizarCombosProdutos();
                        }
                        
                        mostrarModalMsg({
                            type: 'success',
                            txt_header: 'Sucesso',
                            txt_body: r.msg
                        });
                    } else {
                        //Faz com que a mensagem seja renderizada imediatamente antes do elemento referenciado por 'item_id'
                        mostrarModalMsg({
                            type: 'danger',
                            txt_header: 'Erro',
                            txt_body: r.msg
                        });
                        //selector = $(thisEl);
                        //$.fn.mostrarMensagem(r.msg, r.type, selector);
                    }
                });

                return false;
            } catch (e) {
                //$.fn.mostrarMensagem('Erro: exceção na função salvar() - [' + e + ']', 'danger', selector);
                mostrarModalMsg({
                    type: 'danger',
                    txt_header: 'Erro',
                    txt_body: 'Erro: exceção na função salvar() - [' + e + ']'
                });
                esconderModalCarregando();
            }
        },

        atualizarCombosProdutos(){
            $.ajax({
                url: 'vendas/produtos/getselectoptions',
                method: 'GET'
            }).done(function (resp) {
                if (resp) {
                    var comboboxes = $('select[id*=codProduto-]');
                    var produtos = JSON.parse(resp);
                    produtos.unshift({id:'',text:''});
                    $.each(comboboxes, function(i,s){
                        if('codProduto-0' != $(s).attr('id')){
                            $(s).combobox('loadData',produtos);
                        }
                    });

                    // Atualiza o combo modelo clone
                    var options = '';
                    $.each(produtos, function (i, p) {
                        options += '<option value="' + p.id + '">' + p.text + '</option>';
                    });
                    $('#codProduto-0').html(options);
                }
            });
        },
        
        /**
         * 
         * @param string msg Mensagem a a ser exibida
         * @param string tipo (success, alert, danger ou info)
         * @param string|int ID do elemente que conterá a mensagem
         * @returns {undefined}
         */
        mostrarMensagem: function (msg, type, msg_container_selector) {
            var alert_class;
            var msg_body = '';

            switch (type) {
                case 'success':
                    alert_class = 'alert-success';
                    break;
                case 'warning':
                    alert_class = 'alert-warning';
                    break;
                case 'danger':
                    alert_class = 'alert-danger';
                    break;
                case 'info':
                    alert_class = 'alert-info';
                    break;
            }
            msg_body += '<div id="system_messages" class="alert ' + alert_class + ' alert-dismissable" role="alert" style="clear:both;">';
            msg_body += '    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
//            msg_body += '    <ul class="ul-msg" style="list-style-type:none;">';
//            msg_body += '        <li>' + msg + '</li>';
            msg_body += '<strong>' + msg + '</strong>';
//            msg_body += '    </ul>';
            msg_body += '</div>';

            //Renderiza a mensagem antes do elemento selecionado
            $(msg_container_selector).before(msg_body);

            //Auto scroll para #system_message
            var altura_barra_topo = $('#barra-topo').height();
            var pos_top_sm = $('#system_messages').offset().top;
            var padding = 5;
            var offset = pos_top_sm - altura_barra_topo - padding;
            $('html, body').animate({scrollTop: offset}, 'fast');
        },

        /**
         * Função para carregar e preencher os dados do form para edição.
         * Projetada para ser chamada de dentro de uma tabela
         * OBSERVAÇÃO: Para que esta função funcione, é necessário que os nomes
         *             dos inputs nos formulários que a usam estejam iguais aos
         *             das tabelas no banco de dados.
         * 
         * @param {type} id
         * @returns {undefined}
         */
        carregarFormEditar: function (id, formId, rota) {

            try {
                var selector = $(this).closest('table');
                //Remove mensagens de avisos, caso haja, para avitar empilhamento de mensagens.
                $('#system_messages').remove();

                //Removendo option temporário para edição de produto com quantidade 0 (zero)
                if ('vendas_form' == formId) {
                    if ($('#codProduto option.temp_edit').length > 0) {
                        $('#codProduto option.temp_edit').remove();
                        $('#quantidade').prop('disabled', false);
                    }
                }

                $.ajax({
                    url: '/' + rota + 'getdataform',
                    method: 'POST',
                    format: 'json',
                    data: {
                        id: id
                    }
                }).done(function (resp) {
                    //$.unblockUI();
                    var r = JSON.parse(resp);
                    if (1 == r.success) {
                        var form = $('#' + formId);
                        var el;

                        form[0].reset();
                        $('#form-add-container').find('button.btn-danger').trigger('click');

                        //Esta variável indica se o produto a ser editar consta no combo
                        //O nome do input deve ser igual ao do compo no banco de dados
                        $.each(r.data, function (i, d) {
                            el = form.find('*[name=' + i + ']');
                            if (0 !== el.length) {
                                // Seta o campo Cliente
                                if('codCliente' === i){
                                    $('#codCliente').combobox('setValue', d); 
                                }

                                if ('select' === el.prop('tagName').toLowerCase()) {
                                    $.each(el.find('option'), function (j, option) {
                                        if ($(option).val() == d) {
                                            $(option).prop('selected', true);
                                        }
                                    });
                                } else {
                                    el.val(d);
                                }
                            }
                        });

                        // Adiciona e preenche os campos de produto
                        //$.each(r.data.produtos,function(j,p){
                        //    if(j > 0){
                        //        $('#produtos .btn.btn-success').trigger('click');
                        //    }
                        //    
                        //    // Se a quantidade do produto for 0, significa que está editando um produto que já não consta no estoque.
                        //    // Neste caso, deve-se disponibilizá-lo no combo Produto.
                        //    if(0 == p.quantidade){
                        //        var data = $('#codProduto-1').combobox('getData');
                        //        data.push({value:p.codProduto, text:p.nome});
                        //        $('#codProduto-1').combobox('loadData',data);
                        //    }
                        //    $('#codProduto-1').combobox('setValue',p.codProduto);
                        //    $('#precoPago-1').val(p.precoPago);
                        //    $('#quantidade-1').val(p.quantidade);
                        //    $('#desconto-1').val(p.desconto);
                        //    // Obs.: Insere sempre no produto 1 porque quando clica (via trigger) no botão add produto,
                        //    //       os campos vazios passam a ser o 1.
                        //});

                        ////Verificando se o produto a ser editar consta no combo de produtos
                        //if ('vendas_form' == form.attr('id')) {
                        //    var produtoNoCombo = false;
                        //    $.each($('#codProduto').find('option'), function (j, option) {
                        //        if (produtoNoCombo)
                        //            return;//continua se o intem já está no combo
                        //        if ($(option).val() == r.data.codProduto) {
                        //            produtoNoCombo = true;
                        //        }
                        //    });
                        //    //Se a o produto não constar mais no combo, deve-se buscá-lo
                        //    if (!produtoNoCombo) {
                        //        $.ajax({
                        //            url: '/vendas/produtos/getselectoption?codProduto=' + r.data.codProduto,
                        //            method: 'GET',
                        //        }).done(function (resp) {
                        //            var produto = JSON.parse(resp);
                        //            var option = '<option value="' + produto.codProduto + '" selected class="temp_edit">' + produto.nome + '</option>';
                        //            $('#codProduto').append(option);
                        //            $('#quantidade').prop('disabled', true);
                        //        });
                        //    }
                        //}

                        //Define que é edição
                        form.find('input[name=id]').val(1);

                        $('#btCancelar').removeClass('hide');
                        $('#btAdicionar').html('Alterar');

                        //$('#valor').mask("#.##0,00", {reverse: true});

                        //Rodando para o topo, onde está o formulário preenchido para edição
                        $("html, body").animate({scrollTop: 0}, "fast");

                        //Expandindo o form
                        $.fn.mostrarFormAdd();
                        
                        $('#produtos').hide();
                    } else {
                        $.fn.mostrarMensagem('Nenhum dado foi encontrado para este id.', 'info', selector);
                    }
                });
            } catch (e) {
                //$.unblockUI();
                esconderModalCarregando();
                $.fn.mostrarMensagem('Erro: exceção na função carregarFormEditar() - ' + e, 'danger', selector);
            }
        },

        cancelar: function () {
            //$.unblockUI();
            esconderModalCarregando();
            $.fn.esconderFormAdd($("form[id*=_form]"));
            return false;
        },

        // ATIVAR --------------------------------------------------------------
        modalAtivar: function (id, rota, id_target_table) {
            mostrarModalMsg({
                type: 'warning',
                txt_header: 'Atenção',
                txt_body: "Deseja ativar este item?",
                id: 'modal-ativar',
                buttons: [
                    {type: 'default', text: 'Fechar', attributes: {'data-dismiss':'modal'} },
                    {type: 'warning', text: 'Ativar', attributes: {onclick:'$.fn.ativar(' + id + ', "' + rota + '", "' + id_target_table + '")'} },
                ]
            });
        },
        ativar: function(id, rota, id_target_table){

            var modal_selector = "#modal-sistema";

            $.ajax({
                url: '/' + rota + 'ativarajax',
                method: 'post',
                dataType: 'json',
                data: {
                    id: id,
                }
            }).done(function (resp) {
                if (1 == resp.success) {
                    $("#"+id_target_table).datagrid('reload');
                    $("#modal-ativar").modal('hide');
                    mostrarModalMsg({
                        type: 'success',
                        txt_header: 'Sucesso',
                        txt_body: resp.msg
                    });
                } else {
                    mostrarModalMsg({
                        type: 'danger',
                        txt_header: 'Erro',
                        txt_body: resp.msg
                    });
                }
                $(modal_selector).modal('hide');
            });
        },
        
        //REMOVER --------------------------------------------------------------
        modalRemover: function (id, rota, id_target_table) {

            //var id_target_table = $(this).closest('table').attr('id');
            var txt_body = 'Gostaria de remover este item?';
            console.log(id_target_table);
            
            //Remove mensagens de avisos, caso haja, para avitar empilhamento de mensagens.
            $('#system_messages').remove();
            
            if('grid-estoque-vendas' == id_target_table){
                txt_body  = '';
                txt_body += '<div clas="row">';
                txt_body += '    <div clas="col-lg-12">';
                txt_body += '        <div class="form-group col-lg-12">';
                txt_body += '            <label>Gostaria de remover este item?</label>';
                txt_body += '        </div>';
                txt_body += '        <hr>';
                txt_body += '        <div class="form-group col-lg-12">';
                txt_body += '            <label>Devolver todos os produtos desta venda?</label>';
                txt_body += '            <div class="radio">';
                txt_body += '                <label>';
                txt_body += '                    <input type="radio" name="devolver" id="devolverNao" value="0" checked> Não';
                txt_body += '                </label>';
                txt_body += '            </div>';
                txt_body += '            <div class="radio">';
                txt_body += '                <label>';
                txt_body += '                    <input type="radio" name="devolver" id="devolverSim" value="1"> Sim (Todos os produtos voltam para o estoque)';
                txt_body += '                </label>';
                txt_body += '            </div>';
                txt_body += '        </div>';
                txt_body += '    </div>';
                txt_body += '</div>';
            }

            mostrarModalMsg({
                type: 'warning',
                txt_header: 'Atenção',
                txt_body: txt_body,
                id: 'modal-remover',
                buttons: [
                    {type: 'default', text: 'Fechar', attributes: {'data-dismiss':'modal'} },
                    {type: 'warning', text: 'Remover', attributes: {onclick:'$.fn.remover(' + id + ', "' + rota + '", "' + id_target_table + '")'} },
                ]
            });

//            $('#question').addClass('warning-delete-msg');
//            $('#question h1')
//                    .html('<span class="glyphicon glyphicon-exclamation-sign"></span> Gostaria de excluir este item?')
//                    .addClass('warning-delete-msg-color');
//            $('#question hr').addClass('warning-delete-msg-divider');
//            $('#sim')
//                    .attr('onclick', '$.fn.remover(' + id + ', "' + rota + '", "' + id_target_table + '")')
//                    .addClass('btn-danger');
//            $('#nao')
//                    .attr('onclick', '$.fn.cancelar()')
//                    .addClass('btn-default');
//
//            $.blockUI({message: $('#question'), css: {width: 'auto'}});
        },

        remover: function (id, rota, id_target_table) {

            var modal_selector = "#modal-sistema";

            $.ajax({
                url: '/' + rota + 'removerajax',
                method: 'post',
                data: {
                    id: id,
                    devolver: ($('input[name=devolver]').length > 0) ? $('input[name=devolver]:checked').val() : null
                }
            }).done(function (r) {
                var resp = JSON.parse(r);

                if (1 == resp.success) {
                    //if ('produtos' == item_id) {
                        $("#"+id_target_table).datagrid('reload');
                    //} else {
                    //    $('#' + id_target_table).DataTable().ajax.reload();
                    //}
                    $('#modal-remover').modal('hide');
                    mostrarModalMsg({
                        type: 'success',
                        txt_header: 'Sucesso',
                        txt_body: resp.msg
                    });
                    if(1 == $('input[name=devolver]:checked').val()){
                        $.fn.atualizarCombosProdutos();
                    }
                } else {
                    mostrarModalMsg({
                        type: 'danger',
                        txt_header: 'Erro',
                        txt_body: resp.msg
                    });
                }
                $(modal_selector).modal('hide');
            });
        },
        //REMOVER FIM-----------------------------------------------------------

        //CLONAR ---------------------------------------------------------------
        modalClonar: function (id, rota) {

            var id_target_table = $(this).closest('table').attr('id');

            //Remove mensagens de avisos, caso haja, para avitar empilhamento de mensagens.
            $('#system_messages').remove();

            $('#question').addClass('warning-delete-msg');
            $('#question h1')
                    .html('<span class="glyphicon glyphicon-exclamation-sign"></span> Gostaria de duplicar este item?')
                    .addClass('warning-delete-msg-color');
            $('#question hr').addClass('warning-delete-msg-divider');
            $('#sim')
                    .attr('onclick', '$.fn.clonar(' + id + ', "' + rota + '", "' + id_target_table + '")')
                    .addClass('btn-primary');
            $('#nao')
                    .attr('onclick', '$.fn.cancelar()')
                    .addClass('btn-default');

            //$.blockUI({message: $('#question'), css: {width: 'auto'}});
        },

        //Clona item da lista de produtos
        clonar: function (id, rota, id_target_table) {

            var modal_selector = "#modal-sistema";

            $.ajax({
                url: '/' + rota + 'clonarajax',
                method: 'post',
                data: {
                    id: id
                }
            }).done(function (r) {
                var resp = JSON.parse(r);

                if (1 == resp.success) {
                    if ('undefined' == id_target_table) {
                        $('*[id*=grid-estoque-]').datagrid('reload');
                    } else {
                        $('#' + id_target_table).DataTable().ajax.reload();
                    }
                    var selector = $('#listagem');
                    $.fn.mostrarMensagem(resp.msg, resp.type, selector);
                } else {
                    //var selector = $('#' + id_target_table);
                    var selector = $('#listagem');
                    $.fn.mostrarMensagem(resp.msg, resp.type, selector);
                }
                $(modal_selector).modal('hide');
            });
        },
        //CLONAR FIM -----------------------------------------------------------

        limparModal: function () {
            $('#modal-projeto .modal-header h4').html('');
            $('#modal-projeto .modal-body').html('');
            $('#modal-projeto #modal-button').removeAttr('onclick');
            $('#modal-projeto #modal-button').html('');
            $('#modal-projeto .modal-content').removeAttr('style');
            $('#modal-cancelar').attr('data-dismiss', 'modal');
            $('#modal-projeto #modal-button').show();
        },

        mostrarValorTotal: function (id_table, id_container, num_colum) {
            var dt = $("#" + id_table).DataTable();
            var valor_total = dt.data().ajax.json().valorTotal;

            $("#" + id_container + ' span').html("Valor Total: R$ " + $.fn.formatMoney(valor_total, 2, ',', '.'));
        },
        formatMoney: function (numero, c, d, t) {
            var n = numero,
                    c = isNaN(c = Math.abs(c)) ? 2 : c,
                    d = d == undefined ? "." : d,
                    t = t == undefined ? "," : t,
                    s = n < 0 ? "-" : "",
                    i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
                    j = (j = i.length) > 3 ? j % 3 : 0;
            return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
        },
        /**
         * Mudar o tamnaho de uma elemento.
         * Esta função redimensiona a janela modal de acordo com o tamanho atual da janela do browser.
         * 
         * @param string id_modal Seletor jQuery do elemento
         * @param float percent_largura_tela Percentual da largura da janela do browser que será aplicado ao modal. Deve ser > 0 e <= 1
         * @param float percent_altura_tela Percentual da altura da janela do browser que será aplicado ao modal. Deve ser > 0 e <= 1
         * @returns {undefined}
         */
        mudarTamanhoModal(seletor, percent_largura_tela, percent_altura_tela) {
            var style = "";
            if (percent_largura_tela > 0 && percent_largura_tela <= 1) {
                var width = $(window).width() * percent_largura_tela;
                style += "left:50%;";
                style += "width:" + width + 'px;';
                style += "margin-left:-" + width / 2 + 'px;';
            }
            if (percent_altura_tela > 0 && percent_altura_tela <= 1) {
                var height = $(window).height() * percent_altura_tela;
                style += "top:50%;";
                style += "height:" + height + 'px;';
                style += "margin-top:-" + height / 2 + 'px;';
            }
            $(seletor).attr('style', style);
        },

        atualizarCombobox: function () {

            $.ajax({
                url: '/vendas/categorias/getcombooptions'
            }).done(function (resp) {
                $('#codCategoriaPai option').remove();
                $('#codCategoriaPai').append(resp);
            });
        },

        mostrarFormAdd: function () {
            $('#system_messages').remove();
            $('#form-add-container').slideDown(500);
            $('#mostrar-form').fadeOut(500, function () {
                $('#esconder-form').fadeIn(500);
            });

        },

        esconderFormAdd: function (form) {
            $('#form-add-container').slideUp(500);
            $('#esconder-form').fadeOut(500, function () {
                $('#mostrar-form').fadeIn(500);
            });
            
            $('#produtos').show();
            
            // Remove inputs dos produtos adicionais.
            $('#form-add-container form')[0].reset();
            $('#form-add-container').find('button.btn-danger').trigger('click');

            if (form) {
                $(form).find('input[name=id]').val(0);
                $(form).find('#btCancelar').addClass('hide');
                $(form).find('#btAdicionar').html('Salvar');
                $(form)[0].reset();
            }

            //Removendo option temporário para edição de produto com quantidade 0 (zero)
            if ('vendas_form' == form.attr('id')) {
                if ($('#codProduto option.temp_edit').length > 0) {
                    $('#codProduto option.temp_edit').remove();
                    $('#quantidade').prop('disabled', false);
                }
            }
        },

        addProdutoVenda: function () {
            var clone = $('#modelo-clone-produto').clone();
            var numProdutos = $('div[id*=produto-]').length;

            // Limpando clone
            var cloneInputs = $(clone).find('input, select ,textarea');
            $.each(cloneInputs, function (i, ci) {
                if ('select' == $(ci).prop('tagName').toLowerCase()) {
                    $(ci).find('option[value=""]').prop('selected', true);
                    $(ci).combobox()
                } else {
                    $(ci).val('');
                }
                //$(ci).attr('id', $(ci).attr('id').replace(/\-[\d]+/, '-' + (numProdutos + 1)));
                //$(ci).attr('name', $(ci).attr('name').replace(/\-[\d]+/, '-' + (numProdutos + 1)));
            });

            clone.attr('id', 'produto-' + (numProdutos + 1));

            clone.removeClass('hide');

            // Anexando os novos inputs para o produto
            $('#produtos').prepend(clone);

            // Mudando os botões
            var buttons = $('#produtos').find('button');
            $.each(buttons, function (i, bt) {

                if (i == 0) {
                    $(bt)
                            .removeClass('btn-danger')
                            .addClass('btn-success')
                            .attr('onclick', '$.fn.addProdutoVenda();')
                            .find('span.glyphicon')
                            .removeClass('glyphicon-minus')
                            .addClass('glyphicon-plus');
                } else {
                    $(bt)
                            .removeClass('btn-success')
                            .addClass('btn-danger')
                            .attr('onclick', '$.fn.removerProdutoVenda(this);')
                            .find('span.glyphicon')
                            .removeClass('glyphicon-plus')
                            .addClass('glyphicon-minus');
                }
            });

            $.fn.resetIdAndLabelProduto();
        },

        removerProdutoVenda: function (button) {
            // Desabilitar botões de menos (-)?

            // Romovendo produto
            $(button).closest('div[id*=produto-]').remove();
            $.fn.resetIdAndLabelProduto();

        },

        resetIdAndLabelProduto: function () {

            var produtos = $('div[id*=produto-]');
            var numId;
            var inputsProduto;
            $.each(produtos, function (i, p) {
                numId = i + 1;

                // Mudando o id do div container do produto
                $(p).attr('id', 'produto-' + numId);

                // Mudando ids dos elementos inputs do produto
                inputsProduto = $(p).find('input, select, textarea');
                $.each(inputsProduto, function (j, ip) {
                    if('undefined' != typeof $(ip).attr('id')){
                        $(ip).attr('id', $(ip).attr('id').replace(/\-[\d]+/, '-' + numId));                        
                    }                    
                    if('undefined' != typeof $(ip).attr('name')){
                        $(ip).attr('name', $(ip).attr('name').replace(/\-[\d]+/, '-' + numId));
                    }
                    if('undefined' != typeof $(ip).attr('textboxname')){
                        $(ip).attr('textboxname', $(ip).attr('textboxname').replace(/\-[\d]+/, '-' + numId));
                    }
                    if('undefined' != typeof $(ip).attr('comboname')){
                        $(ip).attr('comboname', $(ip).attr('comboname').replace(/\-[\d]+/, '-' + numId));
                    }
                });

                // Mudando atributo for dos labels
                labelsProduto = $(p).find('label');
                $.each(labelsProduto, function (k, l) {
                    if ('undefined' != typeof $(l).attr('for')) {
                        $(l).attr('for', $(l).attr('for').replace(/\-[\d]+/, '-' + numId));
                    }
                });
            });

        },

        getDadosProdutoVenda: function (produto) {
            var id = $(produto).val();
            var idProduto = $(produto).attr('id');
            var num = idProduto.split('-')[1];

            $.ajax({
                url: 'vendas/getDadosProdutoVenda',
                method: 'POST',
                data: {
                    codProduto: id
                }
            }).done(function (resp) {
                var data = JSON.parse(resp);
                var pv = data.preco_pago - (data.preco_pago * (data.desconto / 100));
                pv = pv.toFixed(2);
                var preco_pago = pv.toString().replace('.', ',');

                $('#desconto-' + num).val(data.desconto);
                $('#precoPago-' + num).val(preco_pago);
            });
        },

        aplicarDesconto: function (desconto) {
            var idDesconto = $(desconto).attr('id');
            var num = idDesconto.split('-')[1];

            var codProduto = $('#codProduto-' + num).val();
            var desconto = $(desconto).val();
            var teclasPermitidas = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
            var teclaEhNumero = -1 !== teclasPermitidas.indexOf(parseInt(desconto));

            if (codProduto && /^\d*$/.test(desconto) && teclaEhNumero /*|| 'backspace' === k.key.toLowerCase()*/) {

                if (!desconto) {
                    desconto = 0;
                }

                $.ajax({
                    url: 'vendas/getDesconto',
                    method: 'POST',
                    data: {
                        codProduto: codProduto,
                        desconto: desconto
                    }
                }).done(function (resp) {
                    $('#precoPago-' + num).val(resp);
                });
            }
        }

    });
})(jQuery);
