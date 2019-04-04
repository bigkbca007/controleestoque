
jQuery(document).ready(function ($) {

    //Intanciação do produtos DataTable 
    var table = $('#fornecedores-table').DataTable({
        ajax: {
            url: 'fornecedores/getdata',
            type: 'POST'
        },
        bLengthChange: true,
        lengthMenu: [[50, 10, 25, 100, -1], [50, 10, 25, 100, "Todos"]],
        pageLengh: 50,
        bInfo: false,
        processing: true,
        serverSide: true,
        responsive: true,
        paging: true,
        searching: false,
        columns: [
            {data: "numLinha", orderable: false, searchable: false, width: '2%'},
            {data: "fornecedor_nome"},
            {data: "fornecedor_email"},
            {data: "fornecedor_telefone"},
            {data: "fornecedor_cnpj"},
            {data: "acao", orderable: false, searchable: false, width: '7%'}
        ],
        order: [[1, "asc"]],
        language: {
            processing: "Carregando...",
            search: "Busca:",
            lengthMenu: "Listar _MENU_ registros",
            info: "Listando de _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "Não foram encontrados registros.",
            infoFiltered: "(filtro de _MAX_ registros no total)",
            infoPostFix: "",
            loadingRecords: "Processando a lista...",
            zeroRecords: "Não foram encontrados registros.",
            emptyTable: "Não foram encontrados registros.",
            paginate: {
                first: "Inicio",
                previous: "Anterior",
                next: "Pr&oacute;xima",
                last: "Fim"
            },
            aria: {
                sortAscending: ": Ordenação Ascendente",
                sortDescending: ": Ordenação Descendente"
            }
        }
    });

    //Escondendo o campo de busca
    //$('#busca').closest('ul').hide();

    //Aplicando máscaras
    $('#telefone').mask("(99) 9999-9999");
    $('#cnpj').mask("99.999.999\/9999-99");
    $('#cep').mask("99999-999");

    //Buscando cidades
    $('#estado').change(function () {
        $('#cidade').html('<option value="">Selecione</option>');
        if('' == $(this).val()){
            $('#cidade').prop('disabled',true);
            return;
        }

        $.ajax({
            url: 'fornecedores/getcidades',
            method: 'POST',
            data: {
                sigla: $(this).val()
            }
        }).done(function (resp) {
            var cidades = JSON.parse(resp);
            if('' != cidades){
                $.each(cidades,function(i,c){
                    $('#cidade').append('<option value="'+i+'">'+c+'</option>');
                });
                $('#cidade').prop('disabled',false);
            }
        });
    });

});
