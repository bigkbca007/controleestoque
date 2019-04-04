
jQuery(document).ready(function ($) {
/*
    //Intanciação do produtos DataTable 
    var table = $('#produtos-table').DataTable({
        ajax: {
            url: 'produtos/getdata',
            type: 'POST'
        },
        bLengthChange: true,
        lengthMenu: [[50, 10, 25, 100, -1], [50, 10, 25, 100, "Todos"]],
        bInfo: false,
        processing: true,
        serverSide: true,
        responsive: true,
        paging: true,
        searching: true,
        columns: [
            {data: "produto_codProduto", orderable: false, searchable: false, width: '2%'},
            {data: "produto_nome"},
            {data: "categoria_nome"},
            {data: "fornecedor_nome"},
            {data: "produto_precoVenda"},
            //{data: "produto_precoFabrica"},
            //{data: "produto_dtFabricacao"},
            {data: "produto_dtValidade"},
            //{data: "produto_desconto"},
            {data: "produto_quantidade"},
            {data: "acao", orderable: false, searchable: false, width: '10%'}
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

    // #myInput is a <input type="text"> element
    $('#busca').on('keyup', function () {
        table.search(this.value).draw();
    });
*/
    $('.campo-preco').mask("#.##0,00", {reverse: true});
    $('.campo-data').mask("99/99/9999");   
    $('#numDiasAlertarVencimento').mask('####9');
    $('#quantidade').mask("#####9");
});
