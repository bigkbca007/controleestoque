//Arquivo default para executar ações comuns a diferentes partes do sistema

//Inicialização de date picker
jQuery(document).ready(function ($) {

    //Para que um compo possua a funcionalidade do plugin datetimepicker basta
    //adicionar a classe .campo-datetimepicker
    var months = [
        'Janeiro',
        'Fevereiro',
        'Março',
        'Abril',
        'Maio',
        'Junho',
        'Julho',
        'Agosto',
        'Setembro',
        'Outubro',
        'Novembro',
        'Dezembro'
    ];
    var dayOfWeek = [
        'Domingo',
        'Segunda',
        'Terça',
        'Quarto',
        'Quinta',
        'Sexta',
        'Sábado'
    ];
    $('.campo-data-hora').datetimepicker({
        months: months,
        dayOfWeek: dayOfWeek
    });
    $('.campo-data').datetimepicker({
        months: months,
        dayOfWeek: dayOfWeek,
        timepicker: false,
        format: 'd/m/Y'
    });
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
    $(select).next().find('.textbox-text').attr('style', 'height:34px;width:100%;background-color:' + bgcolor + ';color:#555;padding-left:10px;');
    $(select).next().find('.textbox-text').attr('required', required);
    $(select).next().find('.textbox-addon .combo-arrow').attr('style', 'height:34px;opacity:1;cursor:default;background-color:' + bgcolor + ';background-image:url("/img/input_arrow_down.png");');
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
 * @param {string} text
 * @returns void
 */
function mostrarModalCarregando(text){
    var styleModalContent = 'text-align:center; border:none; background:transparent; margin-top:50%; box-shadow:0px 0px 0px transparent; color:#ffffff';
    var htmlModal = criarModalMensagens();
    var modal = $(htmlModal);
    var num;
    
    if('undefined' == typeof text){
        text = '<h1>Carregando...</h1>';
    }
    
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
            .html(text);
    
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