/**
 ** Data : 02/09/2022
 ** Autor:Whilton Reis
 ** Polaris Tecnologia
 **/
 
//Datatable
var table = $('#datatable-instrumental').DataTable( {
	//"pagingType": "full_numbers", //full_numbers //full //numbers // simple // first_last_numbers
	//"lengthMenu": [10, 25, 50, 75, 100],
	order: [[0, 'desc']],
	"bLengthChange" : false,
	"processing"    : false,
	"serverSide"    : true,
	"ajax": {
		"url":  "solicitacoes-instrumental-listar",
		"type": "POST"
	},
	"createdRow" : function ( row, data_use, dataIndex ) {
        $(row).attr('id', dataIndex);
    },
    'columnDefs': [{
      'targets': "_all",
      'createdCell': function(td, cellData, rowData, row, col) {
        $(td).attr('id', 'cell-' + cellData);
      }
    }],
	rowReorder: {
		selector: 'td:nth-child(2)'
	},
	responsive: true,
	"language": {
		"decimal"          : "",
		"emptyTable"       : "Sem dados disponíveis na tabela",
		"info"             : "",          //Exibindo _START_ a _END_ de _TOTAL_ Registros
		"infoEmpty"        : "",          //Exibindo 0 a 0 de 0 Registros
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
	},
	"columns": [
		null,
		null,
		null,
		null,
		null,
		null,
		null, {
			"orderable": false
		},
	]
});

// Post
$(function() {   		    
    $("#form-instrumental").on("submit", function(event) {   
        // Modifica botão
        set('botao_salvar').innerHTML = '<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i> Salvando...';

        event.preventDefault();
        var data = new FormData(this);
        $.ajax({
            url: "solicitacoes-instrumental-salvar",
            type: "post",
            data: data,
            contentType: false,
            processData: false,
            success: function(data) 
            {
            	if(data == 'sucesso') { 
					setTimeout(function() {   
						// Atualiza a tabela
				        $('#datatable-instrumental').DataTable().ajax.reload();
						$('#instrumental').modal('hide');
					}, 500);

					setTimeout(function() {
				    	$.ajax({
							type: "GET",
							url: "solicitacoes-instrumental-avisonovoagendamento"
						});

						alerta('<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i> Aguarde enviando registro...');
	                    
	                    setTimeout(function() {   
							sucesso('Registro salvo com sucesso.');
							// Modifica botão
            	            set('botao_salvar').innerHTML = '<i class="fa fa-save"></i> Salvar'

						}, 2500);

					}, 700);                 
            	} else {   
            		erro('Registro não pode ser salvo.');
            		// Modifica botão
            	    set('botao_salvar').innerHTML = '<i class="fa fa-save"></i> Salvar'
            	}            	
            }
        });	    

    });
});

// Post
$(function() {   		    
    $("#form-status").on("submit", function(event) {   
        // Modifica botão
        set('botao_salvar_').innerHTML = '<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i> Salvando...';

        event.preventDefault();
        var data = new FormData(this);
        $.ajax({
            url: "solicitacoes-instrumental-atualizar",
            type: "post",
            data: data,
            contentType: false,
            processData: false,
            success: function(data) 
            {
            	if(data == 'sucesso') { 
					setTimeout(function() {   
						// Atualiza a tabela
				        $('#datatable-instrumental').DataTable().ajax.reload();
						$('#instrumental_status').modal('hide');
					}, 500);

					setTimeout(function() {
				    	$.ajax({
							type: "GET",
							url: "solicitacoes-instrumental-avisonovoagendamento"
						});

						alerta('<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i> Aguarde enviando registro...');
	                    
	                    setTimeout(function() {   
							sucesso('Registro salvo com sucesso.');
							// Modifica botão
            	            set('botao_salvar_').innerHTML = '<i class="fa fa-save"></i> Salvar'

						}, 2500);

					}, 700);                 
            	} else {   
            		erro('Registro não pode ser salvo.');
            		// Modifica botão
            	    set('botao_salvar_').innerHTML = '<i class="fa fa-save"></i> Salvar'
            	}            	
            }
        });	    

    });
});

function statusObrigatorio() {   
	$(document).ready(function() {	
		var select = document.getElementById('status');
		var option  = select.children[select.selectedIndex].value;
		if(option == 'Cancelado') {
            $("#justificativa").prop('required', true);
		} else { 
            $("#justificativa").prop('required', false);
		}
	});
}

//Função para setar valores
function set(id) {   
	return document.getElementById(id);
}

//Função visualizar formulário
function visualizar(id) {	
	$.ajax({
		type: "GET",
		dataType: 'JSON',
		url: "solicitacoes-instrumental-exibir-" + id,
		success: function(data) {
			//Limpa o formulario
	        limpaFormulario();
			//Retorna os data para o formulário			
			set('id').value                = data[0].td_id;	
			set('data_envio').value     = FormDate(data[0].td_data_envio);	
			set('data_cirurgia').value  = FormDate(data[0].td_data_cirurgia);	
			set('cidade').value         = ucwords(data[0].td_cidade);
			set('material').value       = ucwords(data[0].td_material);
			set('transportadora').value = ucwords(data[0].td_transportadora);
			set('observacoes').value    = ucwords(data[0].td_observacao);
            set('solicitante').innerHTML = '<b>COD:</b>' + id + ' - <b>Solicitante: </b>' + ucwords(data[0].td_solicitante);
            //Marca os campos preenchidos
			marcaCampos();
			//Bloqueia botão
	        $("#botao_salvar").prop('disabled', true);
	        //Bloqueia Campos
	        $(".form-control").prop('disabled', true);
			//console.log(data[0].td_nome);
			$('#instrumental').modal('show');
		}
	});
}

//Função visualizar formulário
function editar(id) {	
	$.ajax({
		type: "GET",
		dataType: 'JSON',
		url: "solicitacoes-instrumental-exibir-" + id,
		success: function(data) {
			//Limpa o formulario
	        limpaFormulario();
			//Retorna os data para o formulário			
			set('id').value             = data[0].td_id;	
			set('data_envio').value     = FormDate(data[0].td_data_envio);	
			set('data_cirurgia').value  = FormDate(data[0].td_data_cirurgia);	
			set('cidade').value         = ucwords(data[0].td_cidade);
			set('material').value       = ucwords(data[0].td_material);
			set('transportadora').value = ucwords(data[0].td_transportadora);
			set('observacoes').value    = ucwords(data[0].td_observacao);
            set('solicitante').innerHTML = '<b>COD:</b>' + id + ' - <b>Solicitante: </b>' + ucwords(data[0].td_solicitante);
            //Marca os campos preenchidos
			marcaCampos();
			//Bloqueia botão
	        $("#botao_salvar").prop('disabled', false);
	        //Bloqueia Campos
	        $(".form-control").prop('disabled', false);
			//console.log(data[0].td_nome);
			$('#instrumental').modal('show');
		}
	});
}

//Função visualizar formulário
function status(id) {	
	$.ajax({
		type: "GET",
		dataType: 'JSON',
		url: "solicitacoes-instrumental-exibir-" + id,
		success: function(data) {
			//Limpa o formulario
	        limpaFormulario();
			//Retorna os data para o formulário			
			set('id_').value            = data[0].td_id;	
			set('status').value         = ucwords(data[0].td_status);	
			set('justificativa').value  = ucwords(data[0].td_justificativa);
            //Marca os campos preenchidos
			marcaCampos();
			//Bloqueia botão
	        $("#botao_salvar").prop('disabled', false);
	        //Bloqueia Campos
	        $(".form-control").prop('disabled', false);
			//console.log(data[0].td_nome);
			$('#instrumental_status').modal('show');
		}
	});
}

//Função executar exclusão do registro
function executaExclusao(id){  

    alerta('<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i> Aguarde excluindo registro...'); 
    
    setTimeout(function() {
	    $.ajax({
			type: "GET",
			url: "solicitacoes-instrumental-excluir-" + id,
			success: function(data) {
				if(data == 'sucesso') {   
	        		// Atualiza a tabela
				    $('#datatable-instrumental').DataTable().ajax.reload();
	        		// Mensagem de sucesso
				    sucesso('Registro excluido com sucesso.');                    
	        	} else 	{   
	        		erro('Registro não pode ser excluido');
	        	}				
			}
		});
	}, 700);	
}

//Função excluir registro
function excluir(id) {	
	// Botões de opção
	var sim = '<a class="btn btn-sm btn-default yes" onclick="executaExclusao(' + id + ')">Sim</a>';
	var nao = '<a class="btn btn-sm btn-danger no">Não</a>';
	// Mensagem de confirmação
	confirma('Tem certeza que deseja excluir o registro?<div class="clearfix"></div><br>' + sim +' '+ nao);
}

//Funções modal 
function instrumental() {   //Limpa o ID do formulario
	set('id').value = '';
	//Limpa o formulario
	limpaFormulario();
	//Bloqueia Campos
    $(".form-control").prop('disabled', false);
    //Bloqueia botão
	$("#botao_salvar").prop('disabled', false);
    set('solicitante').innerHTML = "Novo Agendamento";
}

//Funções marca campos preenchidos
function marcaCampos() {
	requerido('data_envio'     , 'p1'); // Obrigatório
	requerido('data_cirurgia'  , 'p2'); // Obrigatório
	requerido('cidade'         , 'p3'); // Obrigatório
	requerido('transportadora' , 'p4'); // Obrigatório
	requerido('material'       , 'p5'); // Obrigatório
	opcional('observacoes'     , 'p6');
    requerido('status'         , 'p7'); // Obrigatório
	opcional('justificativa'   , 'p8');

}

//Funções limpa o formulario
function limpaFormulario() {
	$('#form-instrumental').each(function() 
	{
		//Marca os campos preenchidos
		setTimeout(function() 
		{
		    marcaCampos();
		}, 200);
		//Limpa FORM
		this.reset();
	});
}

//Função que verifica se campo foi preenchido
function requerido(campo, div) {
	if (set(campo).value.length > 0) 
	{
		var ico = 'check';
		var cla = 'nbrigatorio';
	} 
	else 
	{
		var ico = 'asterisk';
		var cla = 'obrigatorio';
	}
	set(div).innerHTML = '<i class="fa fa-' + ico + ' ' + cla + '"></i>';
}

//Função que verifica se campo foi preenchido
function opcional(campo, div) {
	if (set(campo).value.length > 0) {
		set(div).innerHTML = '<i class="fa fa-check nbrigatorio"></i>';
	} else {
		set(div).innerHTML = '&nbsp;&nbsp;';
	}
}


//Função de alertas
function erro(mensagem) {
	if (mensagem !== undefined) {		
		$.Notification.autoHideNotify('error', 'top right', 'Erro', mensagem);
	}
}

//Função de alertas
function sucesso(mensagem) {
	if (mensagem !== undefined) {		
		$.Notification.autoCloseNotify('success', 'top right', 'Parabéns', mensagem);
	}
}

function info(mensagem) {
	if (mensagem !== undefined) {		
		$.Notification.autoHideNotify('info', 'top right', 'Parabéns', mensagem);
	}
}

function alerta(mensagem) {
	if (mensagem !== undefined) {		
		$.Notification.notify('warning', 'top right', 'Atenção', mensagem);
	}
}

//Função de alertas
function confirma(mensagem) {
	if (mensagem !== undefined)	{		
		$.Notification.notify('white', 'top right', 'Atenção',mensagem);
	}
}

//Datepicker
$(function () {
    $('.date').datetimepicker(
    	{
    		format: 'L',
    		locale: 'pt-br'
    	});
});

$(function () {
    $('.time').datetimepicker(
    	{   
    		format: 'LT',
    		locale: 'pt-br'
    	});
});

//Formata Data pt_br
function FormDate(dataInput) {
	data = new Date(dataInput);
	return data.toLocaleDateString('pt-BR', {timeZone: 'UTC'});
}

function ucwords(str) {
    var txt = str.toLowerCase();
    return (txt + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
        return $1.toUpperCase();
    });
}

function gerarCodigo() {
    return new Date().getTime();
}


