/*GIF AJAX*/
function gifOpen() {
    $('body').css('overflow', 'hidden');
    $("#LoadGif").removeClass();
    $("#LoadGif").addClass('sis_display_block');
}
/*GIF AJAX*/
function gifClose() {
    $('body').css('overflow', 'auto');
    $("#LoadGif").removeClass();
    $("#LoadGif").addClass('sis_display_none');
}

function isEmpty(obj) {
    for (var prop in obj) {
        if (obj.hasOwnProperty(prop))
            return false;
    }

    return true;
}

/*formata data easy ui*/
function myformatter(date) {
    var y = date.getFullYear();
    var m = date.getMonth() + 1;
    var d = date.getDate();
    return (d < 10 ? ('0' + d) : d) + '/' + (m < 10 ? ('0' + m) : m) + '/' + y;
}
function myparser(s) {
    if (!s)
        return new Date();
    var ss = (s.split('/'));
    var y = parseInt(ss[0], 10);
    var m = parseInt(ss[1], 10);
    var d = parseInt(ss[2], 10);
    if (!isNaN(y) && !isNaN(m) && !isNaN(d)) {
        return new Date(d, m - 1, y);
    } else {
        return new Date();
    }
}

$(document).ready(function () {

    /*correção do bug modal - padding*/
    $('.modal-close').click(function () {
        setTimeout(function () {
            $('body').attr('style', 'padding-right: 0;');
        }, 800);
    });


    /*tempo de exibiçao do flash*/
    jQuery('.flashTime').delay(4500).fadeOut(4500);
    //    /*mascara money moeda Real R$*/
    $(".money-real").maskMoney({prefix: 'R$ ', allowNegative: true, thousands: '.', decimal: ',', affixesStay: false});


    //<!--*IMPUT OBRIGATORIO - CSS e JS*-->
    $("[required=required]").css("background-color", "#FAF1F1");

    $("#txtboxToFilter").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                // Allow: Ctrl+A
                        (e.keyCode == 65 && e.ctrlKey === true) ||
                        // Allow: home, end, left, right
                                (e.keyCode >= 35 && e.keyCode <= 39)) {
                    // let it happen, don't do anything
                    return;
                }
                // Ensure that it is a number and stop the keypress
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            });

    // Exibe mensagens de erro do SGP vindas do servidor, através do modal.
    if($('#server-messages').length > 0) {
        if ("" !== $('#server-messages').html().trim()) {
            // Decodifica o html vindo do flash message
            var serverMessagesEncoded = $('#server-messages').find('li').html().trim();
            var parser = new DOMParser;
            var dom = parser.parseFromString( serverMessagesEncoded,'text/html');
            var serverMessages = dom.body.textContent;

            var type = $('#type-message').val();
            var txt_header = 'Informação';
            switch (type) {
                case 'error':
                    type = 'danger';
                    txt_header = 'Erro';
                    break;
                case 'info': 
                    txt_header = 'Informação';
                    break;
                case 'warning': 
                    txt_header = 'Atenção';
                    break;
                case 'success': 
                    txt_header = 'Sucesso';
                    break;
            }
            type = 'error' === type ? 'danger' : type;
            mostrarModalMsg({
                type: type,
                txt_header: txt_header,
                txt_body: $.parseHTML((serverMessages)),
                buttons: [{
                        type: 'default',
                        text: 'Fechar',
                        attributes: {
                            'data-dismiss': 'modal',
                        }
                    }
                ]
            });
        }
    }
});



function DataFormatada(Data) {
    var data = new Date();
    var d = data.getDate();
    var m = data.getMonth() + 1;
    var y = data.getFullYear();
    dt = d + '/' + m + '/' + y;
    return dt;

}



function excluir_messagem_usuario(classe) {
    var url = $('.' + classe).attr('url');
    var msg = $('.' + classe).attr('msg');
    var titulo = $('.' + classe).attr('title');
    $('#ResMessage').html('<h5> ' + msg + ' </h5>');
    $('#dialog').dialog({
        title: '' + titulo,
        width: 400,
        height: 150,
        closed: false,
        cache: false,
        modal: true,
        buttons: [{
                text: 'Confirmar',
                handler: function () {
                    window.location = "" + url;
                }
            }, {
                text: 'Cancelar',
                handler: function () {
                    $(".panel-tool-close").click();
                }
            }],
    });
    $('#dialog').dialog('open');
}

/**
 * 
 * @param {string} cpf CPF
 * @param {boolean} exibirAlert Informa se a função deve ou não exibir mensagem de erro através da função alert() 
 * @returns {Boolean}
 */
function validarCPF(cpf, exibirAlert) {
    var mastrarMensagem = ('undefined' == typeof exibirAlert) || exibirAlert ? true : false;

    var filtro = /^\d{3}.\d{3}.\d{3}-\d{2}$/i;

    if (!filtro.test(cpf))
    {
        if(mastrarMensagem){
            window.alert("CPF inválido. Tente novamente.");
        }
        return false;
    }

    cpf = remove(cpf, ".");
    cpf = remove(cpf, "-");
    if (cpf.length != 11 || cpf == "00000000000" || cpf == "11111111111" ||
            cpf == "22222222222" || cpf == "33333333333" || cpf == "44444444444" ||
            cpf == "55555555555" || cpf == "66666666666" || cpf == "77777777777" ||
            cpf == "88888888888" || cpf == "99999999999")
    {
        if(mastrarMensagem) {
            window.alert("CPF inválido. Tente novamente.");
        }
        return false;
    }
    soma = 0;
    for (i = 0; i < 9; i++)
    {
        soma += parseInt(cpf.charAt(i)) * (10 - i);
    }
    resto = 11 - (soma % 11);
    if (resto == 10 || resto == 11)
    {
        resto = 0;
    }
    if (resto != parseInt(cpf.charAt(9))) {
        if(mastrarMensagem) {
            window.alert("CPF inválido. Tente novamente.");
        }
        return false;
    }

    soma = 0;
    for (i = 0; i < 10; i++)
    {
        soma += parseInt(cpf.charAt(i)) * (11 - i);
    }
    resto = 11 - (soma % 11);
    if (resto == 10 || resto == 11)
    {
        resto = 0;
    }

    if (resto != parseInt(cpf.charAt(10))) {
        if(mastrarMensagem) {
            window.alert("CPF inválido. Tente novamente.");
        }
        return false;
    }

    return true;
}

function validarCNPJ(cnpj) {

    cnpj = cnpj.replace(/[^\d]+/g, '');

    if (cnpj == '')
        return false;

    if (cnpj.length != 14)
        return false;

    // Elimina CNPJs invalidos conhecidos
    if (cnpj == "00000000000000" ||
            cnpj == "11111111111111" ||
            cnpj == "22222222222222" ||
            cnpj == "33333333333333" ||
            cnpj == "44444444444444" ||
            cnpj == "55555555555555" ||
            cnpj == "66666666666666" ||
            cnpj == "77777777777777" ||
            cnpj == "88888888888888" ||
            cnpj == "99999999999999")
        return false;

    // Valida DVs
    tamanho = cnpj.length - 2
    numeros = cnpj.substring(0, tamanho);
    digitos = cnpj.substring(tamanho);
    soma = 0;
    pos = tamanho - 7;
    for (i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2)
            pos = 9;
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(0))
        return false;

    tamanho = tamanho + 1;
    numeros = cnpj.substring(0, tamanho);
    soma = 0;
    pos = tamanho - 7;
    for (i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2)
            pos = 9;
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(1))
        return false;

    return true;

}

function remove(str, sub) {
    i = str.indexOf(sub);
    r = "";
    if (i == -1)
        return str;
    {
        r += str.substring(0, i) + remove(str.substring(i + sub.length), sub);
    }
    return r;
}

/**
 * MASCARA ( mascara(o,f) e execmascara() ) CRIADAS POR ELCIO LUIZ
 * elcio.com.br
 */
function mascara(o, f) {
    v_obj = o
    v_fun = f
    setTimeout("execmascara()", 1)
}

function execmascara() {
    v_obj.value = v_fun(v_obj.value);
}

function cpf_mask(v) {
    v = v.replace(/\D/g, "");                 //Remove tudo o que não é dígito
    v = v.replace(/(\d{3})(\d)/, "$1.$2");    //Coloca ponto entre o terceiro e o quarto dígitos
    v = v.replace(/(\d{3})(\d)/, "$1.$2");    //Coloca ponto entre o setimo e o oitava dígitos
    v = v.replace(/(\d{3})(\d)/, "$1-$2");   //Coloca ponto entre o decimoprimeiro e o decimosegundo dígitos
    return v;
}

/**
 * Expandir e destacar menu de acordo com a página
 */
$(document).ready(function () {
    $('a#system-logout').click(function () {
        localStorage.clear();
    });
    // Expandir usando local storage
    //if(window.localStorage){
    if (0) { // Não está funcionando adequadamente. Não funciona quando o redirecionamento é feito via programação no servidor

        // Expandir menu automaticamente
        if ((System.projeto != window.location.href) && (System.projeto_sgp != window.location.href)) {
            var pathSelectors = localStorage.getItem('pathSelectors');
            if (pathSelectors) {
                var parts = pathSelectors.split(',');
                var path = '';
                $.each(parts, function (i, p) {
                    path += p + ' > ';
                    if (0 === p.indexOf('li:nth')) {
                        $(path).click();
                    }
                });

                // Destaca o link
                var selector = localStorage.getItem('selector');
                $(selector + ' > a').css({"background": "#0A0A0A", "outline": 0});
            }
        }

        // Gerando o seletor css e guardando no local storage
        $('#main-menu li a').click(function (e) {
            //e.preventDefault();
            $('#main-menu li, #main-menu ul').removeClass('mark-path');
            var a = $(this);
            var el = a.parent();
            var sair = false;
            var counter = 0;

            // Marcando o caminho até o #main-menu
            while (!sair) {
                if (50 === counter)
                    sair = true;
                if (!el.is('#main-menu')) {
                    el.addClass('mark-path');
                    el = el.parent();
                } else {
                    sair = true;
                }
            }

            var marks = $('.mark-path');
            var selectorArray = ['#main-menu'];
            $.each(marks, function (i, m) {
                selectorArray.push($(m).prop('tagName').toLowerCase() + ':nth(' + $(m).prevAll('li').length + ')');
            });

            localStorage.setItem('pathSelectors', selectorArray);
            localStorage.setItem('selector', selectorArray.join(' > '));
        });

    } else {
        var parts = window.location.href.split('.');
        var href = parts[parts.length - 1].substr(2);

        //Se o link não segui o padrão module/controller/action, então extrai-os da url
        if (href.split('/').length > 4) {
            var href_split = href.split('/');
            href = href_split[0] + '/';
            href += href_split[1] + '/';
            href += href_split[2] + '/';
            href += href_split[3];
        }

        //Variável que mapeia os links na url que não correspodem a nenhum link do menu
        var route_map = {
            "sgc_acompanhamento_rcoTodasInstancias": "/sgc/acompanhamento/rcoInstancia",
            "sgc_acompanhamento_todasRco": "/sgc/acompanhamento/rcoInstancia",
            "sgc_acompanhamento_requisicaoLotacao": "/sgc/acompanhamento",
            "sgc_acompanhamento_comprasUnidade": "/sgc/acompanhamento",
            "sgc_acompanhamento_andamento": "/sgc/acompanhamento",
            "sgc_acompanhamento_formularioImpressao": "/sgc/acompanhamento"
        };

        //Se a rota na url não corresponder a um link no menu
        if ($('#main-menu li a[href=' + CSS.escape(href) + ']').length == 0) {
            $.each(route_map, function (i, r) {
                if (i == href.replace(new RegExp(/\//g), '_').substr(1)) {
                    href = r;
                }
            });
        }

        //Destacando opção de link corrente
        if ($('#main-menu li a[href=' + CSS.escape(href) + ']').find('img.sis_imagem').length == 0) {
            $('#main-menu li a[href=' + CSS.escape(href) + ']').css({"background": "#0A0A0A", "outline": 0});
        }

        //Função recursiva para expandir os menus a partir do de nível 1
        function expandirMenu(el) {
            if (el.closest('ul[class*=level]').length > 0) {
                expandirMenu($(el).parent().closest('ul[class*=level]'));
            }
            $(el).prev('a').click();
        }

        expandirMenu($('#main-menu li a[href=' + CSS.escape(href) + ']').parent().closest('ul[class*=level]'));
    }
});

/**
 * Função para esconder inputs de filtros do datagrid
 * 
 * @param {array} fields Nomes dos colunas que não devem conter filtros
 */
function esconderCampoFilter(fields) {
    $.each(fields, function (i, f) {
        if ($('tr[class*=datagrid-filter]').find('input[name=' + f + ']').length > 0) {
            $('tr[class*=datagrid-filter]').find('input[name=' + f + ']').parent().css({display: 'none'});
        }
    });
}

//Transforma elementos select em comboboxes
jQuery(document).ready(function ($) {
    selectCombobox('select.select-combobox');
});

/**
 * 
 * @param {string} selector
 * @returns {undefined}
 */
function selectCombobox(selector){

    var select = $(selector);
    
    // Se houver evento onchange
    if('undefined' != typeof select.attr('onchange')){
        var onchange = select.attr('onchange');
        // Se houver parênteses, remove-o para evitar erro na chamada da função através de window
        if(-1 !== onchange.indexOf('(')){
            onchange = onchange.substring(0,onchange.indexOf('('));
        }

        $(select).combobox({
            valueField: 'id',
            textField: 'text',
            onChange: function(){
                window[onchange]();
            }
        });
    } else {
        $(select).combobox({
            valueField: 'id',
            textField: 'text',
        });
        
    }

    var name = $(select).attr('textboxname');
    var required = $(select).attr('required') ? true : false;

    var bgcolor = $(select).attr('required') ? 'rgb(250, 241, 241)' : '#ffffff';
    $(select).next().attr('style', 'width:100%;border: 1px solid #cccccc;');
    $(select).next().find('span.textbox').attr('style', 'width:100%;border: 1px solid #cccccc;');
    $(select).next().find('.textbox-text').attr('style', 'height:21px;width:100%;background-color:' + bgcolor + ';color:#555;padding-left:10px;');
    $(select).next().find('.textbox-text').attr('required', required);
    $(select).next().find('.textbox-addon .combo-arrow').attr('style', 'opacity:1;cursor:default;background-color:' + bgcolor + ';background-image:url(' + System.projeto + '/../images/arrow_down_transp.png);');
    $(select).next().find('input.textbox-value').attr('name', name);
    if ($(select).attr('required')) {
        $(select).next().find('.textbox-text').prop('required', true);
    }

    $(select).removeAttr('required');

    $('input[name=' + name + ']').closest('.textbox').find('.textbox-text').click(function () {
        var itemId = $(this).next().val();

        if (!itemId) {
            $(this).val('');
        }
    });

    $('input[name=' + name + ']').closest('.textbox').find('.textbox-text').blur(function () {
        var itemId = $(this).next().val();

        if (!itemId) {
            $(this).val('');
        }
    });
    
}

//Exibe mensagens de erro
function mostrarMensagemErro(mensagem) {
    if ($('#dialog-alert-msg').length > 0) {
        $('#dialog-alert-msg').remove();
    }

    var msgContainer = '';

    msgContainer += '<div class="modal fade in alert-danger" id="dialog-alert-msg" tabindex="-1" role="dialog" aria-labelledby="myModalLabelFornecedor" aria-hidden="false">';
    msgContainer += '    <div class="modal-dialog" role="document">';
    msgContainer += '        <div class="modal-content" style="border:solid 1px #a94442">';
    msgContainer += '            <div class="modal-header" style="background-color:#f2dede !important; border-color:#a94442 !important;">';
    msgContainer += '                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>';
    msgContainer += '                <h4 class="modal-title" id="myModalLabelFornecedor">Erro!</h4>';
    msgContainer += '            </div>';
    msgContainer += '            <div class="modal-body" id="fornecedorAlertMsgBody">';
    msgContainer += '                 <p>' + mensagem + '</p>';
    msgContainer += '            </div>';
    msgContainer += '        </div>';
    msgContainer += '    </div>';
    msgContainer += '</div>';

    $('body').append(msgContainer);
    $('#dialog-alert-msg').modal();

}

/**
 * 
 * @param {type} type_alert_color
 * @param {type} txt_header
 * @param {type} txt_body
 * @param {type} txt_bt_ok
 * @param {type} txt_bt_cancel
 * @param {type} acao
 * @returns {undefined}
 */
function mostrarModalMsg(options) {
    
    var borderColor;
    var icon = '';
    if('undefined' == typeof options.title_icon){
        options.title_icon = true;
    }

    switch (options.type) {
        case 'success':
            borderColor = 'solid 1px #28a745 !important;';
            if(options.title_icon){
                icon = '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>';
            }
            break;
        case 'danger':
            borderColor = 'solid 1px #dc3545 !important;';
            if(options.title_icon){
                icon = '<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>';
            }
            break;
        case 'warning':
            borderColor = 'solid 1px #ffc107 !important;';
            if(options.title_icon){
                icon = '<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>';
            }
            break;
        case 'info':
            borderColor = 'solid 1px #17a2b8 !important;';
            if(options.title_icon){
                icon = '<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>';
            }
            break;
        case 'default':
            borderColor = 'solid 1px #ccc !important;';
    }

    // Largura da janela madal.
    styleCenterWidth = '';
    if('number' === typeof options.width){
        var width = window.innerWidth * options.width;
        var marginLeft = width / 2;
        styleCenterWidth = 'width:'+width+'px;left:50%;margin-left:-'+marginLeft+'px;';
    }

    // Altura da janela modal.
    styleCenterHeight = '';
    if('number' === typeof options.height){
        var height = window.innerHeight * options.height;
        //var marginTop = height / 2;
        var marginTop = 0;
        styleCenterHeight = 'height:'+height+'px;top:50%;margin-top:-'+marginTop+'px;';
    }
    
    // Largura do corpo da janela madal.
    var styleWidthPx = '';
    if('number' === typeof options.widthBody){
        var widthBody = window.innerWidth * options.widthBody;
        styleWidthPx = 'width:'+widthBody+'px;';
    }
    
    // Altura do corpo da janela madal.
    var styleHeightPx = '';
    if('number' === typeof options.heightBody){
        var heightBody = window.innerHeight * options.heightBody;
        styleHeightPx = 'height:'+heightBody+'px;';
    }

    // Barra de rolagem vertical no corpo
    var scrollY = '';
    if(options.scrollY){
        scrollY = 'overflow-y: scroll;';
    }
    
    var htmlModal = criarModalMensagens();
    var modal = $(htmlModal);

    //var modal = $('#modal-sistema-alerts');
    modal.find(".modal-content").attr('style', 'border: ' + borderColor+ ';'+ styleCenterWidth + styleCenterHeight);
    modal.find(".modal-header").attr('style', 'background-color:transparent !important; border-bottom: ' + borderColor);
    modal.find(".modal-header h5 strong").addClass("text-" + options.type).html(icon+' '+options.txt_header);
    modal.find("#bt-fechar").addClass("text-" + options.type);
    modal.find(".modal-body").addClass("text-" + options.type + " bg-" + options.type).attr('style', styleWidthPx + styleHeightPx + scrollY).html(options.txt_body);
    modal.find(".modal-footer").attr('style', 'border-top: ' + borderColor);
    
    // Atribui id
    if('undefined' !== typeof options.id){
        modal.attr('id',options.id);
    }
    
    // Cria os botões
    if('undefined' !== typeof options.buttons){
        modal.find(".modal-footer").html('');
        $.each(options.buttons,function(i,b){
            var buttonEl = $('<button></buttons>');
            var btnType = 'undefined' === typeof b.type ? options.type: b.type;
            var btnText = 'undefined' === typeof b.text ? 'Button': b.text;

            buttonEl.addClass('btn btn-'+btnType);
            buttonEl.html(btnText);

            // Adiciona os atributos
            if('undefined' !== typeof b.attributes){
                $.each(b.attributes,function(j,a){
                    if('class' == j){
                        buttonEl.addClass(a);
                    } else {
                        buttonEl.attr(j,a);
                    }
                });
            }

            modal.find(".modal-footer").prepend(buttonEl);
        });
    } else {
        // Se não houver butão, adiciona o butão de fechar.
        var buttonEl = $('<button></buttons>');
        buttonEl.addClass('btn btn-default');
        buttonEl.html('Fechar');
        buttonEl.attr('data-dismiss', 'modal');
        modal.find(".modal-footer").prepend(buttonEl);
    }

    // Eventos
    // O evento padrão é shown.bs.modal
    if('undefined' !== typeof options.event){
        $.each(options.event, function(i,e){
            var type = e.type ? e.type : 'shown.bs.modal';
            if('undefined' !== typeof e.handler){
                modal.one(type,e.handler);
            }            
        });
    }

    modal.modal();
    
    // Remove o padding-right que gera quando as chamadas de modais se sobrepõem
    modal.one('hidden.bs.modal',function(){
        $('body').css({'padding-right':'0px'});
        modal.remove();
    });
    $('#scren-modal, .modal').one('hidden.bs.modal',function(){
        $('body').css({'padding-right':'0px'});
    });

}

function allModalClosed() {
    var allClosed = true;
    $.each($('.modal, #scren-modal'), function (i, m) {
        if ('undefined' != typeof $(m).data('bs.modal') && ($(m).data('bs.modal').isShown)) {
            allClosed = false;
        }
    });
    return allClosed;
}

function resetModalMsg() {
    var modal = $('.modal-sgp');
    modal.find(".modal-content").removeClass().addClass("modal-content");
    modal.find(".modal-header h5 strong").removeClass().html("");
    modal.find("#bt-fechar").removeClass().addClass("close");
    modal.find(".modal-body").removeClass().addClass("modal-body").html("");
    modal.find(".modal-footer #ok").removeClass().addClass("btn btn-primary").html("Ok").attr("ng-click", "");
    modal.find(".modal-footer #cancelar").html("Cancelar");
    modal.modal('hide');
}

function closeModalMsg(id){
    if(id){
        $('#'+id).modal('hide');
    } else {
        console.error('Erro: ID não definido. Se o modal não possui id, atribua-o um.');
    }
}

function criarModalMensagens() {    

    var html = '';
    html += '<div class="modal fade modal-sgp" tabindex="-1" role="dialog">';
    html += '    <div class="modal-dialog" role="document">';
    html += '        <div class="modal-content">';
    html += '            <div class="modal-header">';
    html += '                <button type="button" class="close" id="bt-fechar" data-dismiss="modal" aria-label="Close">';
    html += '                    <span aria-hidden="true">&times;</span>';
    html += '                </button>';
    html += '                <h5 class="modal-title"><strong>Modal title</strong></h5>';
    html += '            </div>';
    html += '            <div class="modal-body">';
    html += '                <p>Modal body text goes here.</p>';
    html += '            </div>';
    html += '            <div class="modal-footer">';
    html += '            </div>';
    html += '        </div>';
    html += '    </div>';
    html += '</div>';

    return html;

}

/**
 * Função para mostrar mensagem "Carregando...".
 * @returns void
 */
function mostrarModalCarregando(){
    var styleModalContent = 'text-align:center; border:none; background:transparent; margin-top:50%; box-shadow:0px 0px 0px transparent; color:#ffffff';
    var htmlModal = criarModalMensagens();
    var modal = $(htmlModal);
    var num;
    
    if( 'undefined' === typeof $('body').data().nummodalcarregando ){
        $('body').data().nummodalcarregando = [];
        num = 0;
    } else if(0 === $('body').data().nummodalcarregando.length){
        num = 0;        
    } else {
        var num = $('body').data().nummodalcarregando[$('body').data().nummodalcarregando.length-1] + 1;
    }
    $('body').data().nummodalcarregando.push(num);
    var id = 'modal-carregando-' + num;

    modal
            .modal({
                backdrop: 'static',
                keyboard: false
            })
            .attr('id',id)
            .find('.modal-content')
            .attr('style',styleModalContent)
            .html('<h1>Carregando...</h1>');
    
    modal.one('hidden.bs.modal',function(){
        var id = 'modal-carregando-'+$('body').data().nummodalcarregando.pop();
        $('#'+id).remove();
        $('body').css({'padding-right':'0px'});
    });
}

/**
 * Função para esconder mensagem "Carregando...".
 * @returns void
 */
function esconderModalCarregando() {
    var num = $('body').data().nummodalcarregando[$('body').data().nummodalcarregando.length-1];
    var id = 'modal-carregando-'+num;

    $('#'+id).modal('hide');
}

jQuery(document).ready(function ($) {
    $('body').delegate('.numero', 'keydown', function (e) {
        if (
                $.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                (e.keyCode == 65 && e.ctrlKey === true) ||
                (e.keyCode >= 35 && e.keyCode <= 39)) {
            return;
        }

        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
});

/**
 * Returns a function, that, as long as it continues to be invoked, will not
 * be triggered. The function will be called after it stops being called for
 * N milliseconds. If `immediate` is passed, trigger the function on the
 * leading edge, instead of the trailing.
 * 
 * @param {type} func
 * @param {type} wait
 * @param {type} immediate
 * @returns {Function}
 */
function debounce(func, wait, immediate) {
    var timeout;
    return function () {
        var context = this, args = arguments;
        var later = function () {
            timeout = null;
            if (!immediate)
                func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow)
            func.apply(context, args);
    }
}

/**
 * 
 * @type Function
 */
var getAbsoluteUrl = (function () {
    var a;

    return function (url) {
        if (!a)
            a = document.createElement('a');
        a.href = url;

        return a.href;
    };
})();

/**
 * Função para limitar datas habilidatas do datebox easyui. Se dti e dtf forem null, false ou vazio, então todas as datas serão habilitadas.
 * @param {string|Date} dti Todas as datas anteriores a esta serão desabilitadas. Deve ser string e estar no formato yyyy-mm-dd ou um objeto Date.
 * @param {string|Date} dtf Todas as datas posteriores a esta serão desabilitadas. Deve ser string e estar no formato yyyy-mm-dd ou um objeto Date.
 * @param {object} idEl ID do elemento que contém o date box.
 * @returns {undefined}
 */
function limitarDatasHabilitadas(dti, dtf, idEl){
    var el = $(idEl);

    if(idEl){
        
        if (!(dti || dtf)) {
            el.datebox('calendar').calendar({
                validator: function (date) {
                    return true;
                }
            });
        } else {
        
            dti = !dti ? '' : dti;
            dtf = !dtf ? '' : dtf;

            // Se estiver no formato yyyy-mm-dd hh:ii:ss, então remove hh:ii:ss
            dti = !/^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}$/.test(dti) ? dti.split(' ')[0]: dti;
            dtf = !/^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}$/.test(dtf) ? dtf.split(' ')[0]: dtf;

            // Se estiver no formato dd/mm/yyyy hh:ii:ss, então remove hh:ii:ss
            dti = !/^\d{2}\/\d{2}\/\d{4}\s\d{2}:\d{2}:\d{2}$/.test(dti) ? dti.split(' ')[0]: dti;
            dtf = !/^\d{2}\/\d{2}\/\d{4}\s\d{2}:\d{2}:\d{2}$/.test(dtf) ? dtf.split(' ')[0]: dtf;

            // Se a(s) data(s) estiver(em) no formato BR (dd/mm/yyyy), então converte-a(s) para o formato USA (yyyy-mm-dd).
            dti = /^\d{2}\/\d{2}\/\d{4}$/.test(dti) ? formatarDataBrUs(dti) : dti;
            dtf = /^\d{2}\/\d{2}\/\d{4}$/.test(dtf) ? formatarDataBrUs(dtf) : dtf;

            el.datebox('calendar').calendar({
                validator: function(date){
                    var d1;
                    var d2;
                    if(dti && !dtf){
                        d1 = new Date(dti+' 00:00:00');
                        return d1<=date;
                    } else if(dtf && !dti){
                        d2 = new Date(dtf+' 00:00:00');
                        return date<=d2;
                    } else if(dti && dtf){
                        d1 = new Date(dti+' 00:00:00');
                        d2 = new Date(dtf+' 00:00:00');
                        return d1<=date && date<=d2;
                    }
                }
           });
        }
    }
}

/**
 * Converte uma data do formato brasileiro (dd/mm/yyyy) para o americano (yyyy-mm-dd) e vice versa
 * @param {type} dt
 * @returns {string}
 */
function formatarDataBrUs(dt){
    // Padrão do formato BR
    var dataFormatada = '';
    
    if(/^\d{2}\/\d{2}\/\d{4}$/.test(dt)){
        var dtParts = dt.split('/');
        dataFormatada = dtParts[2]+'-'+dtParts[1]+'-'+dtParts[0];
    } else if(/^\d{4}-\d{2}-\d{2}$/.test(dt)) {        
        var dtParts = dt.split('-');
        dataFormatada = dtParts[2]+'/'+dtParts[1]+'/'+dtParts[0];
    }
    return dataFormatada;
}

function getDataAtual(formatoUsa){

    var date = new Date();
    var y = date.getFullYear();
    var m = date.getMonth() + 1;
    var d = date.getDate();
    var hoje = (d<10?('0'+d):d)+'/'+(m<10?('0'+m):m)+'/'+y;
    
    if(formatoUsa){
        hoje = y+'-'+(m<10?('0'+m):m)+'-'+(d<10?('0'+d):d);
    }
    return hoje;
}

/**
 * Remove o espaço entre o easyui-datebox e o input onde é colocada a data selecionada.
 * @returns {undefined}
 */
function removerEspacoDatebox(){
    $('.combo-p').removeClass('panel');
}

function convertBrlToUs(v) {
    var vlrParts = v.split(',');
    var vlr = vlrParts[0].replace(/\./g, ',');
    return vlr + '.' + vlrParts[1];
}

function convertBrlToUs2(v) {
    var vlrParts = v.split(',');
    var vlr = vlrParts[0].replace(/\./g, '');
    return vlr + '.' + vlrParts[1];
}

/**
 * Função para tornar um campo datebox não editável via teclado.
 * @param {string} name Nome do input data
 * @returns {void}
 */
function tornarDateboxReadOnly(name){
    $('input[name='+name+']').closest('span').find('.textbox-text').prop('readonly',true);
}

/**
 * Verifica se uma data (dt1) é menor a outra (dt2). As datas devem estar no formato dd/mm/yyyy.
 * @param {string} dt1
 * @param {string} dt2
 * @returns {Boolean} Retornar true se dt1 for menor ou igual a dt2. Caso contrário, retorna false.
 */
function validarDatas(dt1, dt2){
    var ret = false;
    var p = /^\d{2}\/\d{2}\/\d{4}$/;
    if( ( 'undefined' != typeof dt1) && ('undefined' != typeof dt2) && (p.test(dt1)) && (p.test(dt2)) ){
        var partsDt1 = dt1.split('/');
        var partsDt2 = dt2.split('/');
        var data1 = partsDt1[2]+'-'+partsDt1[1]+'-'+partsDt1[0];
        var data2 = partsDt2[2]+'-'+partsDt2[1]+'-'+partsDt2[0];
        ret = data1 < data2;
    }
    return ret;    
}