/**
 ** Data : 02/01/2018
 ** Autor:Whilton Reis
 ** Polaris Tecnologia
 **/
 
//* Inicialização da DataTable com configurações específicas
var table = $('#datatable-agenda').DataTable({
    //* Desativa a opção de alterar o número de entradas exibidas por página
    "bLengthChange": false,
    //* Desativa a indicação de processamento durante ações (como carregar dados)
    "processing": false,
    //* Ativa o modo server-side para processamento dos dados
    "serverSide": true,
    //* Configuração AJAX para buscar os dados da tabela
    "ajax": {
        "url":  "principal-agenda-listar", //* URL para buscar os dados
        "type": "POST" //* Tipo de requisição
    },
    //* Configuração para permitir reordenar as linhas da tabela
    rowReorder: {
        selector: 'td:nth-child(2)' //* Selector usado para reordenar
    },
    //* Ativa a funcionalidade de responsividade da tabela
    responsive: true,
    //* Configuração do idioma da DataTable
    "language": {
        "decimal": "",
        "emptyTable": "Sem dados disponíveis na tabela",
        "info": "", //*Exibindo _START_ a _END_ de _TOTAL_ Registros
        "infoEmpty": "", //*Exibindo 0 a 0 de 0 Registros
        "infoFiltered": "(Filtrado de _MAX_ registros)",
        "infoPostFix": "",
        "thousands": ",",
        "lengthMenu": "Exibir _MENU_",
        "loadingRecords": "<img src='images/loading.gif' width='32px'>",
        "processing": "<img src='images/loading.gif' width='32px'>",
        "search": "",
        "searchPlaceholder": "Buscar",
        "zeroRecords": "Nenhum registro correspondente encontrado.",
        "paginate": {
            "first": "Primeiro",
            "last": "Último",
            "next": "<i class='fa fa-chevron-right' aria-hidden='true'></i>",
            "previous": "<i class='fa fa-chevron-left' aria-hidden='true'></i>"
        },
    },
    //* Configuração das colunas da tabela
    "columns": [
        null, //* Configuração padrão para a coluna
		{ 
			className: "data" 
		},
		null,
        { "orderable": false } //* Configuração para desativar ordenação na última coluna
    ]
});
 
//Limita a descrição em 100 Caracteres
set('descricao').oninput=function(e){
    set('caracteresRestantes').innerHTML=(100-this.value.length);
}

//* Espera que o documento esteja completamente carregado antes de executar o código jQuery
$(function() 
{   //* Define um manipulador de eventos para o envio do formulário com ID 'form-agenda'
    $("#form-agenda").on("submit", function(event) {
    	//* Impede o comportamento padrão do formulário de ser enviado
        event.preventDefault();
        //* Serializa os dados do formulário para enviá-los via AJAX
        var data = $(this).serialize();
        //* Envia uma requisição AJAX para salvar os dados do usuário
        $.ajax({
            url: "principal-agenda-salvar",
            type: "post",
            data: data,
            success: function(data) {
            	//* Verifica se a resposta da requisição é 'sucesso'
            	if(data == 'sucesso') {   
            		//* Modifica o conteúdo do botão de salvar para indicar que está salvando
            		set('botao_salvar').innerHTML = '<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i> Salvando...'
            		//* Exibe uma mensagem de sucesso após um curto período de tempo
            		setTimeout(function() {
						sucesso('Registro salvo com sucesso.');
						//* Faz update na tabela marcando o agendamento como ativo"
		                $("#notificacoes").load("principal-agenda-notificacao");
						//* Restaura o conteúdo original do botão de salvar
						set('botao_salvar').innerHTML = '<i class="fa fa-save"></i> Salvar'
						// Atualiza a datagrid na tela
			            $('#datatable-agenda').DataTable().ajax.reload();
			            //* Fecha o modal de agenda
			            $('#agenda').modal('hide');
					}, 500);                    
            	} else {   
            		//* Se a resposta não for 'sucesso', exibe uma mensagem de erro
            		erro('Registro não pode ser salvo.');
            	}
            }
        });
    });
});

//* Função para obter um elemento HTML pelo seu ID
function set(id) {
    return document.getElementById(id);
}

//* Função para visualizar detalhes do usuário
function visualizar(id) {	
	//* Realiza uma requisição AJAX para obter os dados do usuário com o ID fornecido
	$.ajax({
		type: "GET",
		dataType: 'JSON',
		url: "principal-agenda-exibir-" + id,
		success: function(data) {
			//* Limpa o formulário antes de preenchê-lo com os novos dados
	        limpaFormulario();
			//* Preenche os campos do formulário com os dados recebidos
			set('id').value        = data[0].td_id;			
			set('titulo').value    = data[0].td_titulo;
			set('descricao').value = data[0].td_descricao;	
			//* Formata data padrão para PT_BR
			var str  = data[0].td_data.split('-');
	        var data = str[2] + "/" + str[1] + "/" + str[0];
	        //* Imprime data		
			set('data').value  = data;
			//* Bloqueia o botão de salvar
		    $("#botao_salvar").prop('disabled', true);
		    //* Bloqueia os campos do formulário para edição
		    $(".form-control").prop('disabled', true);
		    //* Faz update na tabela marcando o agendamento como ativo"
		    $("#notificacoes").load("principal-agenda-notificacao");
            //* Marca os campos como preenchidos
			marcaCampos();
			//* Fecha o modal de agenda
			$('#agenda').modal('show');
		}
	});
}

//*Função editar formulário
function editar(id) {	
	//* Realiza uma requisição AJAX para obter os dados do usuário com o ID fornecido
	$.ajax({
		type: "GET",
		dataType: 'JSON',
		url: "principal-agenda-exibir-" + id,
		success: function(data) {
			//*Limpa o formulario
	        limpaFormulario();
			//*Retorna os data para o formulário
			set('id').value        = data[0].td_id;			
			set('titulo').value    = data[0].td_titulo;
			set('descricao').value = data[0].td_descricao;	
			// Formata data para o padrão PT_BR
			var str  = data[0].td_data.split('-');
	        var data = str[2] + "/" + str[1] + "/" + str[0];
	        // Imprime data		
			set('data').value  = data;
			//*Desbloqueia botão
		    $("#botao_salvar").prop('disabled', false);
		    //*Desbloqueia Campos
		    $(".form-control").prop('disabled', false);
            //* Marca os campos como preenchidos
			marcaCampos();
			//* Fecha o modal de agenda
			$('#agenda').modal('show');
		}
	});
}

//* Função para executar a exclusão do registro do usuário
function executaExclusao(id) {
    //* Envia uma requisição AJAX para excluir o registro do usuário com o ID fornecido
    $.ajax({
        type: "GET",
        url: "principal-agenda-excluir-" + id, //* URL para excluir o registro do usuário
        success: function(data) {
            //* Verifica se a exclusão foi bem-sucedida com base na resposta da requisição
            if(data == 'sucesso') {
                //* Exibe uma mensagem de sucesso caso a exclusão tenha sido realizada com sucesso
                sucesso('Registro excluído com sucesso.');
            } else {
                //* Exibe uma mensagem de erro caso a exclusão não tenha sido realizada com sucesso
                erro('Registro não pode ser excluído');
            }
            //* Atualiza a tabela de usuários após a exclusão
            $('#datatable-agenda').DataTable().ajax.reload();
        }
    });
}

//* Função para iniciar o processo de exclusão do registro do usuário
function excluir(id) {
    //* Botões de opção para confirmação
    var sim = '<a class="btn btn-sm btn-default yes" onclick="executaExclusao(' + id + ')">Sim</a>';
    var nao = '<a class="btn btn-sm btn-danger no">Não</a>';
    //* Exibe uma mensagem de confirmação para o usuário
    confirma('Tem certeza que deseja excluir o registro?<div class="clearfix"></div><br>' + sim +' '+ nao);
}

//* Função para configurar o modal da agenda
function agenda() {
	//* Limpa o campo de ID do usuário
	set('id').value = '';
	//* Habilita o botão de salvar
    $("#botao_salvar").prop('disabled', false);
    //* Habilita os campos do formulário
    $(".form-control").prop('disabled', false);	
	 //* Limpa o formulário da agenda
	limpaFormulario();
}

//* Função para marcar os campos obrigatórios e opcionais no formulário
function marcaCampos() {
    //* Define os campos como obrigatórios
	requerido('titulo'  , 'p1');  //* Obrigatório
	requerido('descricao', 'p2'); //* Obrigatório
	requerido('data'    , 'p3');  //* Obrigatório
}


//* Função para limpar o formulário do usuário
function limpaFormulario() {
    $('#form-agenda').each(function() {
        //* Marca os campos preenchidos novamente após um pequeno atraso
        setTimeout(function() {
            marcaCampos();
        }, 200);
        //* Limpa o formulário
        this.reset();
    });
}

//* Função para verificar se um campo foi preenchido e atualizar o ícone correspondente
function requerido(campo, div) {
    if (set(campo).value.length > 0) {
        var ico = 'check'; //* Ícone de verificação para indicar que o campo foi preenchido
        var cla = 'nbrigatorio'; //* Classe para estilização do ícone
    } else {
        var ico = 'asterisk'; //* Ícone de asterisco para indicar que o campo é obrigatório
        var cla = 'obrigatorio'; //* Classe para estilização do ícone
    }
    //* Define o ícone a ser exibido na div fornecida
    set(div).innerHTML = '<i class="fa fa-' + ico + ' ' + cla + '"></i>';
}

//* Função para verificar se um campo foi preenchido opcionalmente e atualizar o ícone correspondente
function opcional(campo, div) {
    if (set(campo).value.length > 0) {
        //* Define um ícone de verificação para indicar que o campo foi preenchido
        set(div).innerHTML = '<i class="fa fa-check nbrigatorio"></i>';
    } else {
        //* Define um espaço vazio para a div se o campo estiver vazio
        set(div).innerHTML = '&nbsp;&nbsp;';
    }
}

//* Função para exibir uma mensagem de erro
function erro(mensagem){
    if (mensagem !== undefined) {
        //* Exibe uma notificação de erro no canto superior direito
        $.Notification.autoHideNotify('error', 'top right', 'Erro', mensagem);
    }
}

//* Função para exibir uma mensagem de sucesso
function sucesso(mensagem){
    if (mensagem !== undefined) {
        //* Exibe uma notificação de sucesso no canto superior direito
        $.Notification.autoHideNotify('success', 'top right', 'Parabéns', mensagem);
    }
}

//* Função para exibir uma mensagem de confirmação
function confirma(mensagem){
    if (mensagem !== undefined) {
        //* Exibe uma notificação de confirmação no canto superior direito
        $.Notification.notify('white', 'top right', 'Atenção',mensagem);
    }
}

//* Função para formatar uma data no formato pt-BR
function FormDate(dataInput){
    data = new Date(dataInput);
    return data.toLocaleDateString('pt-BR', {timeZone: 'UTC'});
}

//Datepicker
$(function () {
    $('.date').datetimepicker(
    	{
    		format: 'L',
    		locale: 'pt-br'
    	});
});