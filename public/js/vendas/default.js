//Arquivo default para executar ações comuns a diferentes partes do sistema

//Inicialização de date picker
jQuery(document).ready(function($){
   
    //Para que um compo possua a funcionalidade do plugin datetimepicker basta
    //adicionar a classe .campo-datetimepicker
    $('.campo-data-hora').datetimepicker({
        i18n:{
            de: {
                months: [
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
                ],
                daysOfWeek: [
                    'Domingo',
                    'Segunda',
                    'Terça',
                    'Quarto',
                    'Quinta',
                    'Sexta',
                    'Sábado'
                ]
            }
        }
    });
});