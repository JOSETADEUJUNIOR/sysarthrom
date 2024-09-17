/**
 ** Data : 02/01/2018
 ** Autor:Whilton Reis
 ** Polaris Tecnologia
 **/
 
//** Inicialização da DataTable com configurações específicas
var table = $('#datatable-cirurgico').DataTable( {
	//** Ordena a primeira coluna da datagrid
	order: [[0, 'desc']],
	//** Desativa a opção de alterar o número de entradas exibidas por página
	"bLengthChange" : false,
    //** Desativa a indicação de processamento durante ações (como carregar dados)
	"processing"    : false,
    //** Ativa o modo server-side para processamento dos dados
	"serverSide"    : true,
    //** Configuração AJAX para buscar os dados da tabela
	"ajax": {
		"url":  "solicitacoes-cirurgico-listar",//** URL para buscar os dados
		"type": "POST"//** Tipo de requisição
	},
    //** Configuração para permitir reordenar as linhas da tabela
	rowReorder: {
		selector: 'td:nth-child(2)' //** Selector usado para reordenar
	},
    //** Ativa a funcionalidade de responsividade da tabela
	responsive: true,
    //** Configuração do idioma da DataTable
	"language": {
		"decimal"          : "",
		"emptyTable"       : "Sem dados disponíveis na tabela",
		"info"             : "",          //*Exibindo _START_ a _END_ de _TOTAL_ Registros
		"infoEmpty"        : "",          //*Exibindo 0 a 0 de 0 Registros
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
    //** Configuração das colunas da tabela
    "columns": [
        null, //** Configuração padrão para a coluna
        null, //** Configuração padrão para a coluna
        null, //** Configuração padrão para a coluna
        null, //** Configuração padrão para a coluna
        null, //** Configuração padrão para a coluna
        null, //** Configuração padrão para a coluna
        null, //** Configuração padrão para a coluna
        { "orderable": false } //** Configuração para desativar ordenação na última coluna
    ]
});

//** Espera que o documento esteja completamente carregado antes de executar o código jQuery
$(function() {   		    
	//** Define um manipulador de eventos para o envio do formulário com ID 'form-cirurgico'
    $("#form-cirurgico").on("submit", function(event) {   
        //** Modifica o conteúdo do botão de salvar para indicar que está salvando
        set('botao_salvar').innerHTML = '<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i> Salvando...';
        //** Impede o comportamento padrão do formulário de ser enviado
        event.preventDefault();
        //** Serializa os dados do formulário para enviá-los via AJAX
        var data = new FormData(this);
        //** Envia uma requisição AJAX para salvar os dados do usuário
        $.ajax({
            url: "solicitacoes-cirurgico-salvar",
            type: "post",
            data: data,
            contentType: false,
            processData: false,
            success: function(data) {
            	//** Verifica se a resposta da requisição é 'sucesso'
            	if(data == 'sucesso') {   
            		//** Exibe uma mensagem de sucesso após um curto período de tempo
            		setTimeout(function() {   
						//** Recarrega os dados na tabela cirurgico
			            $('#datatable-cirurgico').DataTable().ajax.reload();
			            //** Fecha o modal cirurgico
					    $('#cirurgico').modal('hide');
					}, 500); 
                    //** Requisição AJAX para enviar um email avisando sobre o agendamento
					setTimeout(function() {
				    	$.ajax({
							type: "GET",
							url: "solicitacoes-cirurgico-avisonovoagendamento"
						});
                        
                        //** Exibe a mensagem de enviando o registro enquanto a requisição AJAX envia o email
						alerta('<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i> Aguarde enviando registro...');
                        
                        //** Exibe uma mensagem de sucesso após um curto período de tempo
                        setTimeout(function() {
							sucesso('Registro salvo com sucesso.');

						}, 2500);

					}, 700);                 
            	} else {   
            		 //** Verifica o retorno das condições e exibe a mensagem na notificação
		          	if(data == 'horario') {   
	            		erro('Data e hora do agendamento muito próximo da cirurgia<br>favor ligar para 43 3327-3636.');
	            	} else if(data == 'feriado') {   
	            		erro('Não é possivel fazer agendamento em finais de semana e feriados.<br>favor ligar para 43 3327-3636.');
	            	} else if(data = 'extensao') {
		                erro('Um arquivo selecionado não é válido!');
		        	} else if(data = 'tamanho') {
		                erro('O arquivo é acima do tamanho pré-determinado de 5MB');
		        	} else {
		        		//** Se a resposta não for 'sucesso', exibe uma mensagem de erro
                        erro('Registro não pode ser salvo.');
                    }	          		
	          	}
            	//* Modifica botão
            	set('botao_salvar').innerHTML = '<i class="fa fa-save"></i> Salvar'
            }
        });	    

    });
});

//* Executa a função quando o documento estiver pronto
$(function() { 
    //* Adiciona um evento de submissão ao formulário com o ID 'form-email'
    $("#form-email").on("submit", function(event) {       	
    	//* Modifica o botão com o ID 'botao_email' para mostrar um ícone de carregamento e a mensagem "Enviando..."
        set('botao_email').innerHTML = '<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i> Enviando...'
        //* Previne o comportamento padrão do formulário (que é recarregar a página)
        event.preventDefault();
        //* Serializa os dados do formulário para serem enviados via AJAX
        var data = $(this).serialize();
        //* Envia uma requisição AJAX para a URL "solicitacoes-cirurgico-email"
        $.ajax({
            url: "solicitacoes-cirurgico-email",
            type: "post",
            data: data,            
            //* Função a ser chamada em caso de sucesso na requisição
            success: function(data) {
                //* Se o servidor retornar 'sucesso'
            	if(data == 'sucesso') {  
                    //* Exibe uma mensagem de sucesso após 500 milissegundos
            		setTimeout(function() {
						sucesso('Email enviado com sucesso.');
                        //* Restaura o botão ao seu estado original com o ícone de envelope
						set('botao_email').innerHTML = '<i class="fa fa-envelope"></i> Enviar'
						//* Recarrega a tabela com os dados mais recentes
			            $('#datatable-cirurgico').DataTable().ajax.reload();
                        //* Fecha o modal com o ID 'email'
			            $('#email').modal('hide');
					}, 500);                    
            	} 
                //* Se o servidor retornar 'horario'
                else if(data == 'horario') {   
            		erro('Horario limite para envio é 17:30<br>Favor ligar para 43 3327-3636.');
            	} 
                //* Para outros casos de erro
                else {
            		erro('Email não pode ser enviado.');
                    //* Restaura o botão ao seu estado original com o ícone de envelope
					set('botao_email').innerHTML = '<i class="fa fa-envelope"></i> Enviar'
					//* Recarrega a tabela com os dados mais recentes
		            $('#datatable-cirurgico').DataTable().ajax.reload();
                    //* Fecha o modal com o ID 'email'
		            $('#email').modal('hide')
            	}
            }
        });
    });
});

//* Função para desabilitar o campo de anexo1
function desabilitar1(valor) {  
  //* Obtém o estado de desabilitado do elemento com ID 'arquivo1'
  var status = document.getElementById('arquivo1').disabled;
  //* Se o valor for 'sim' e o campo não estiver desabilitado
  if (valor == 'sim' && !status) {    
    //* Desabilita o campo de anexo1
    document.getElementById('arquivo1').disabled = true;
  } 
  //* Caso contrário, habilita o campo de anexo1
  else {
    document.getElementById('arquivo1').disabled = false;
  }
}

//* Função para desabilitar o campo de anexo2
function desabilitar2(valor) {
  //* Obtém o estado de desabilitado do elemento com ID 'arquivo2'
  var status = document.getElementById('arquivo2').disabled;
  //* Se o valor for 'sim' e o campo não estiver desabilitado
  if (valor == 'sim' && !status) {
    //* Desabilita o campo de anexo2
    document.getElementById('arquivo2').disabled = true;
  } 
  //* Caso contrário, habilita o campo de anexo2
  else {
    document.getElementById('arquivo2').disabled = false;
  }
}

//* Função para desabilitar o campo de anexo3
function desabilitar3(valor) {
  //* Obtém o estado de desabilitado do elemento com ID 'arquivo3'
  var status = document.getElementById('arquivo3').disabled;
  //* Se o valor for 'sim' e o campo não estiver desabilitado
  if (valor == 'sim' && !status) {
    //* Desabilita o campo de anexo3
    document.getElementById('arquivo3').disabled = true;
  } 
  //* Caso contrário, habilita o campo de anexo3
  else {
    document.getElementById('arquivo3').disabled = false;
  }
}

//* Função para configurar valores e retornar um elemento pelo ID
function set(id) {   
	//* Configura o estilo do elemento do tipo file
	$(':file').filestyle({
		input : true,
		buttonName : 'btn-default',
		buttonText : 'Anexar'
	});
	//* Retorna o elemento pelo ID
	return document.getElementById(id);
}

//* Função para visualizar um formulário preenchido com dados obtidos via AJAX
function visualizar(id) {	
	//* Faz uma requisição AJAX para obter os dados do formulário com o ID específico
	$.ajax({
		type: "GET",
		dataType: 'JSON',
		url: "solicitacoes-cirurgico-exibir-" + id,
		success: function(data) {
			//* Limpa o formulário
	        limpaFormulario();
			//* Preenche o formulário com os dados obtidos
			set('convenio').value     = ucwords(data[0].td_convenio);
			set('id').value           = data[0].td_id;			
			set('hospital').value     = ucwords(data[0].td_hospital);
			set('medico').value       = ucwords(data[0].td_medico);	
			set('paciente').value     = ucwords(data[0].td_paciente);	
			set('data').value         = FormDate(data[0].td_data);	
			set('hora').value         = data[0].td_hora;	
			set('procedimento').value = ucwords(data[0].td_procedimento);	
			set('material').value     = ucwords(data[0].td_material);
			set('observacoes').value  = ucwords(data[0].td_observacao);
			set('solicitante').innerHTML = '<b>COD:</b>' + id + ' - <b>Solicitante: </b>' + ucwords(data[0].td_solicitante);
			//* Exibe anexos se estiverem disponíveis
			if(data[0].td_liberacao) {
				liberacao('uploads/' + data[0].td_liberacao, data[0].td_liberacao);
			} else {
			    set('anexo4').innerHTML  = "";
			}
			if(data[0].td_liberacao_complementar) {
				liberacao_complementar('uploads/' + data[0].td_liberacao_complementar, data[0].td_liberacao_complementar);
			} else {
			    set('anexo5').innerHTML  = "";
			}
			if(data[0].td_pedido_medico) {
				pedidoMedico('uploads/' + data[0].td_pedido_medico, data[0].td_pedido_medico);
			} else {
			    set('anexo6').innerHTML  = "";
			}
            //* Limpa os campos de anexos 1, 2 e 3
            set('anexo1').innerHTML  = "";
	        set('anexo2').innerHTML  = "";
	        set('anexo3').innerHTML  = "";
            //* Marca os campos preenchidos
			marcaCampos();
			//* Bloqueia o botão de salvar e os campos do formulário
	        $("#botao_salvar").prop('disabled', true);
	        $(".form-control").prop('disabled', true);
			//* Exibe o modal com os dados do formulário
			$('#cirurgico').modal('show');
		}
	});
}

//* Função para exibir o anexo de liberação 1
function liberacao(destino, data){
   $.ajax({
      //* Realiza uma requisição AJAX para o destino especificado
      url: destino,
      success: function(result){        
        //* Se a requisição for bem-sucedida, define o HTML do elemento 'anexo4' para exibir um link para o anexo de liberação 1
        set('anexo4').innerHTML  = '<a target="_blank" href="uploads/' + data + '"><button type="button" class="btn btn-default"><i class="fa fa-paperclip"></i></label> Liberação 1</button></a>';
      },     
      error: function(result){
        //* Se houver erro na requisição, limpa o HTML do elemento 'anexo4'
        set('anexo4').innerHTML  = "";
      }
   });
}

//* Função para exibir o anexo de liberação 2
function liberacao_complementar(destino, data){
   $.ajax({
      //* Realiza uma requisição AJAX para o destino especificado
      url: destino,
      success: function(result){        
        //* Se a requisição for bem-sucedida, define o HTML do elemento 'anexo5' para exibir um link para o anexo de liberação 2
        set('anexo5').innerHTML  = '<a target="_blank" href="uploads/' + data + '"><button type="button" class="btn btn-default"><i class="fa fa-paperclip"></i></label> Liberação 2</button></a>';
      },     
      error: function(result){
        //* Se houver erro na requisição, limpa o HTML do elemento 'anexo5'
        set('anexo5').innerHTML  = "";
      }
   });
}

//* Função para exibir o anexo de pedido médico
function pedidoMedico(destino, data){
   $.ajax({
      //* Realiza uma requisição AJAX para o destino especificado
      url: destino,
      success: function(result){
        //* Se a requisição for bem-sucedida, define o HTML do elemento 'anexo6' para exibir um link para o anexo de pedido médico
        set('anexo6').innerHTML  = '<a target="_blank" href="uploads/' + data + '"><button type="button" class="btn btn-default"><i class="fa fa-paperclip"></i></label> Pedido Médico</button></a>';
      },     
      error: function(result){
        //* Se houver erro na requisição, limpa o HTML do elemento 'anexo6'
        set('anexo6').innerHTML  = "";
      }
   });
}

//* Função para editar um formulário preenchido com dados obtidos via AJAX
function editar(id) {	
	//* Faz uma requisição AJAX para obter os dados do formulário com o ID específico
	$.ajax({
		type: "GET",
		dataType: 'JSON',
		url: "solicitacoes-cirurgico-exibir-" + id,
		success: function(data) {
			//* Limpa o formulário
	        limpaFormulario();
			//* Preenche o formulário com os dados obtidos
			set('convenio').value     = ucwords(data[0].td_convenio);
			set('id').value           = data[0].td_id;			
			set('hospital').value     = ucwords(data[0].td_hospital);
			set('medico').value       = ucwords(data[0].td_medico);	
			set('paciente').value     = ucwords(data[0].td_paciente);	
			set('data').value         = FormDate(data[0].td_data);	
			set('hora').value         = data[0].td_hora;	
			set('procedimento').value = ucwords(data[0].td_procedimento);	
			set('material').value     = ucwords(data[0].td_material);
			set('observacoes').value  = ucwords(data[0].td_observacao);
			set('solicitante').innerHTML = '<b>COD:</b>' + id + ' - <b>Solicitante: </b>' + ucwords(data[0].td_solicitante);
			//* Define os campos de anexo com os checkboxes e inputs de arquivo correspondentes
			set('anexo1').innerHTML   = "<input type='checkbox' data-toggle='toggle' onclick=desabilitar1('sim')><label>&nbsp;&nbsp;Liberação 1</label><input type='file' name='arquivo1' class='filestyle' id='arquivo1' required>";
			set('anexo2').innerHTML   = "<input type='checkbox' data-toggle='toggle' onclick=desabilitar2('sim')><label>&nbsp;&nbsp;Liberação 2</label><input type='file' name='arquivo2' class='filestyle' id='arquivo2' required>";
			set('anexo3').innerHTML   = "<input type='checkbox' data-toggle='toggle' onclick=desabilitar3('sim')><label>&nbsp;&nbsp;Pedido Médico</label><input type='file' name='arquivo3' class='filestyle' id='arquivo3' required>";
			//* Exibe os anexos, se disponíveis
			if(data[0].td_liberacao) {
				liberacao('uploads/' + data[0].td_liberacao, data[0].td_liberacao);
			}
			if(data[0].td_liberacao_complementar) {
				liberacao_complementar('uploads/' + data[0].td_liberacao_complementar, data[0].td_liberacao_complementar);
			}
			if(data[0].td_pedido_medico) {
				pedidoMedico('uploads/' + data[0].td_pedido_medico, data[0].td_pedido_medico);
			}	
            //* Marca os campos preenchidos
			marcaCampos();
			//* Desabilita os campos de anexo 1, 2 e 3
			desabilitar1('sim');
			desabilitar2('sim');
			desabilitar3('sim');
			//* Desbloqueia o botão de salvar e os campos do formulário
	        $("#botao_salvar").prop('disabled', false);
	        $(".form-control").prop('disabled', false);
			//* Exibe o modal com os dados do formulário
			$('#cirurgico').modal('show');
		}
	});
}

//* Função para excluir um registro
function excluir(id) {	
	//* Botões de opção
	var sim = '<a class="btn btn-sm btn-default yes" onclick="executaExclusao(' + id + ')">Sim</a>';
	var nao = '<a class="btn btn-sm btn-danger no">Não</a>';
	//* Mensagem de confirmação
	confirma('Tem certeza que deseja excluir o registro?<div class="clearfix"></div><br>' + sim +' '+ nao);
}

//* Função para executar a exclusão do registro
function executaExclusao(id) {   
    //* Exibe uma mensagem de alerta para indicar que a exclusão está em andamento
    alerta('<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i> Aguarde excluindo registro...'); 

    //* Realiza uma requisição AJAX para indicar que a exclusão está em andamento
    $.ajax({
        type: "GET",
        url: "solicitacoes-cirurgico-avisoexclusao-" + id,
        success: function() {
            //* Realiza a exclusão do registro via AJAX
            $.ajax({
                type: "GET",
                url: "solicitacoes-cirurgico-excluir-" + id,
                success: function(data) {
                    //* Verifica se a exclusão foi bem-sucedida
                    if(data == 'sucesso') {   
                        //* Se sim, exibe uma mensagem de sucesso
                        sucesso('Registro excluído com sucesso.');            
                    } else {   
                        //* Se não, exibe uma mensagem de erro
                        erro('Registro não pode ser excluído');
                    }   
                    //* Atualiza a tabela após a exclusão
                    $('#datatable-cirurgico').DataTable().ajax.reload();       
                }
            });
        },
        error: function() {
            //* Em caso de falha na primeira requisição, exibe uma mensagem de erro
            erro('Erro ao iniciar exclusão. Tente novamente.');
        }
    });
}


//* Função para confirmar o recebimento do agendamento
function ler(id) {   
	//* Realiza uma requisição AJAX para indicar que o recebimento está sendo processado
	$.ajax({
		type: "GET",
		url: "solicitacoes-cirurgico-recebido-" + id
	});

	//* Exibe uma mensagem de alerta para indicar que o envio da confirmação está em andamento
	alerta('<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i> Aguarde enviando confirmação...');
    
	//* Realiza o envio da confirmação via AJAX
	$.ajax({
		type: "GET",
		url: "solicitacoes-cirurgico-checkado-" + id,
		success: function(data) {
			//* Verifica se o envio da confirmação foi bem-sucedido
			if(data == 'sucesso') {   
        		//* Se sim, exibe uma mensagem de sucesso
			    sucesso('Confirmação enviada com sucesso.');                    
        	} else {   
        		//* Se não, exibe uma mensagem de erro
        		erro('Registro não pode ser editado');
        	}
			//* Atualiza a tabela após o envio da confirmação
			$('#datatable-cirurgico').DataTable().ajax.reload();
		}
	});
}

//*Função para confirmar agendamento
 function desmarcar(id) {   
     $.ajax({
		type: "GET",
		url: "solicitacoes-cirurgico-confirmado-" + id
	});

	alerta('<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i> Aguarde enviando confirmação...');
   
 	$.ajax({
 		type: "GET",
 		url: "solicitacoes-cirurgico-desmarcar-" + id,
 		success: function(data) {
 			if(data == 'sucesso') {   
         		//* Mensagem de sucesso
 			    sucesso('Registro editado com sucesso.');                    
         	} else {   
         		erro('Registro não pode ser exditado');
         	}
 			//* Atualiza a tabela
 			$('#datatable-cirurgico').DataTable().ajax.reload();
 		}
 	});
 }

//* Função para abrir o modal de agendamento cirúrgico
function cirurgico() {   
    //* Limpa o ID do formulário
	set('id').value = '';
	//* Limpa o formulário
	limpaFormulario();
    //* Desbloqueia o botão de salvar
    $("#botao_salvar").prop('disabled', false);
    //* Desbloqueia os campos do formulário
    $(".form-control").prop('disabled', false); 

    //* Configura os campos de anexo como obrigatórios e exibe os checkboxes e inputs de arquivo correspondentes
    set('anexo1').innerHTML  = "<input type='checkbox' checked data-toggle='toggle' onclick=desabilitar1('sim')><label>&nbsp;&nbsp;Liberação 1</label><input type='file' name='arquivo1' class='filestyle' id='arquivo1' required>";
    set('anexo2').innerHTML  = "<input type='checkbox' checked data-toggle='toggle' onclick=desabilitar2('sim')><label>&nbsp;&nbsp;Liberação 2</label><input type='file' name='arquivo2' class='filestyle' id='arquivo2' required>";
    set('anexo3').innerHTML  = "<input type='checkbox' checked data-toggle='toggle' onclick=desabilitar3('sim')><label>&nbsp;&nbsp;Pedido Médico</label><input type='file' name='arquivo3' class='filestyle' id='arquivo3' required>";
    set('anexo4').innerHTML  = "";
    set('anexo5').innerHTML  = "";
    set('anexo6').innerHTML  = "";
    set('solicitante').innerHTML = "Novo Agendamento";
}

//* Função para abrir o modal de envio de email
function email(id) {   
    //* Define o ID do formulário
	set('id_mail').value = id;
	//* Limpa o formulário
	limpaFormulario();
    //* Habilita os campos do formulário
    $(".form-control").prop('disabled', false);
    //* Exibe o modal de email
    $('#email').modal('show');
}

//* Função para marcar os campos preenchidos como obrigatórios ou opcionais
function marcaCampos() {
	requerido('convenio'     , 'p1'); //* Obrigatório
	requerido('hospital'     , 'p2'); //* Obrigatório
	requerido('medico'       , 'p3'); //* Obrigatório
	requerido('paciente'     , 'p4'); //* Obrigatório
	requerido('data'         , 'p5'); //* Obrigatório
	requerido('hora'         , 'p6'); //* Obrigatório
	requerido('procedimento' , 'p7'); //* Obrigatório
	requerido('material'     , 'p8'); //* Obrigatório
	opcional('observacoes'   , 'p9'); //* Opcional
	requerido('para'         , 'p10'); //* Obrigatório
	opcional('cc'            , 'p11'); //* Opcional
}


//* Função para limpar o formulário
function limpaFormulario() {
	//* Para cada formulário com o ID 'form-cirurgico' e 'form-email'
	$('#form-cirurgico, #form-email').each(function() {
		//* Marca os campos preenchidos com um pequeno atraso
		setTimeout(function() {
		    marcaCampos();
		}, 200);
		//* Limpa o formulário
		this.reset();
	});
}

//* Função para verificar se um campo é preenchido (obrigatório)
function requerido(campo, div) {
	//* Verifica se o campo foi preenchido
	if (set(campo).value.length > 0) {
		var ico = 'check'; //* Ícone de verificação se preenchido
		var cla = 'nbrigatorio'; //* Classe para estilo de CSS
	} else {
		var ico = 'asterisk'; //* Ícone de asterisco se não preenchido
		var cla = 'obrigatorio'; //* Classe para estilo de CSS
	}
	//* Define o ícone e a classe no elemento de marcação
	set(div).innerHTML = '<i class="fa fa-' + ico + ' ' + cla + '"></i>';
}

//* Função para verificar se um campo é preenchido (opcional)
function opcional(campo, div) {
	//* Verifica se o campo foi preenchido
	if (set(campo).value.length > 0) {
		set(div).innerHTML = '<i class="fa fa-check nbrigatorio"></i>'; //* Marca como preenchido
	} else {
		set(div).innerHTML = '&nbsp;&nbsp;'; //* Deixa em branco se não preenchido
	}
}

//* Função para exibir um alerta de erro
function erro(mensagem){
	if (mensagem !== undefined) {		
		$.Notification.autoHideNotify('error', 'top right', 'Erro', mensagem);
	}
}

//* Função para exibir um alerta de sucesso
function sucesso(mensagem){
	if (mensagem !== undefined) {		
		$.Notification.autoCloseNotify('success', 'top right', 'Parabéns', mensagem);
	}
}

//* Função para exibir um alerta de informação
function info(mensagem){
	if (mensagem !== undefined) {		
		$.Notification.autoHideNotify('info', 'top right', 'Informação', mensagem);
	}
}

//* Função para exibir um alerta de atenção
function alerta(mensagem){
	if (mensagem !== undefined) {		
		$.Notification.notify('warning', 'top right', 'Atenção', mensagem);
	}
}

//* Função para exibir uma mensagem de confirmação
function confirma(mensagem){
	if (mensagem !== undefined) {		
		$.Notification.notify('white', 'top right', 'Confirmação', mensagem);
	}
}

//* Configuração do datepicker para datas
$(function () {
    $('.date').datetimepicker(
    	{
    		format: 'L',
    		locale: 'pt-br'
    	});
});

//* Configuração do datepicker para horários
$(function () {
    $('.time').datetimepicker(
    	{   
    		format: 'LT',
    		locale: 'pt-br'
    	});
});

//* Função para formatar data no formato dd/mm/yyyy
function FormDate(dataInput) {
	data = new Date(dataInput);
	return data.toLocaleDateString('pt-BR', {timeZone: 'UTC'});
}

//* Função para converter a primeira letra de cada palavra em maiúscula
function ucwords(str) {
    var txt = str.toLowerCase();
    return (txt + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
        return $1.toUpperCase();
    });
}

//* Função para gerar um código baseado no timestamp atual
function gerarCodigo() {
    return new Date().getTime();
}
