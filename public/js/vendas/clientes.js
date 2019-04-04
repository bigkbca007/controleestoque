
jQuery(document).ready(function ($) {

    //Intanciação do produtos DataTable 
    var table = $('#clientes-table').DataTable({
        ajax: {
            url: 'clientes/getdata',
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
        searching: true,
        columns: [
            {data: "numLinha", orderable: false, searchable: false, width: '2%'},
            {data: "cliente_nome"},
            {data: "cliente_email"},
            {data: "cliente_telefone"},
//            {data: "cliente_cpf"},
//            {data: "cliente_rg"},
            {data: "cliente_sexo"},
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

    $('#busca').on('keyup', function () {
        table.search(this.value).draw();
    });

    //Aplicando máscaras
    
    $('#telefone').mask("(99) 99999-9999");
    $('#cpf').mask("999.999.999-99");
    $('#rg').mask("99999999 99");
});
