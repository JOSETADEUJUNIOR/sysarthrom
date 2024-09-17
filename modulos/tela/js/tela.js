/**
 ** Data : 02/01/2018
 ** Autor:Whilton Reis
 ** Polaris Tecnologia
 **/
 
//Datatable
var table = $('#datatable-tela').DataTable(
{
	responsive: true,
	//searching: false,
	//paginate: false,
	bLengthChange: false,
	ordering: false,
	"language": {
		"decimal"          : "",
		"emptyTable"       : "Sem data disponíveis na tabela",
		"info"             : "", //Exibindo _START_ a _END_ de _TOTAL_ Registros
		"infoEmpty"        : "", //Exibindo 0 a 0 de 0 Registros
		"infoFiltered"     : "(Filtrado de _MAX_ registros)",
		"infoPostFix"      : "",
		"thousands"        : ",",
		"lengthMenu"       : "Exibir _MENU_",
		"loadingRecords"   : "<img src='images/loading.gif' width='32px'>",
		"processing"       : "<img src='images/loading.gif' width='32px'>",
		"search"           : "",
		"searchPlaceholder": "Buscar",
		"zeroRecords"      : "Nenhum registro correspondente encontrado.",
		"paginate"         : {
			"first"        : "Primeiro",
			"last"         : "Último",
			"next"         : "<i class='fa fa-chevron-right' aria-hidden='true'></i>",
			"previous"     : "<i class='fa fa-chevron-left' aria-hidden='true'></i>"
		},
	}
});

//setInterval(function () {
//    $("#datatable-tela").DataTable().draw();
//}, 1000);


function mostraObs(obs) {
    $("#mostraPaciente").val($('#pas' + obs).val());
    $("#mostraObs").html($('#obs' + obs).val());
}

$(function() {
    $("#tbody").on('click', 'button', function() {
        $("#myModal").modal({
            show: true
        })
    });
});