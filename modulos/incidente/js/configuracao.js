/**
 ** Data : 02/01/2018
 ** Autor:Whilton Reis
 ** Polaris Tecnologia
 **/
 
 //* Inicialização da DataTable com configurações específicas
var table = $('#datatable-configuracao').DataTable({
    //* Desativa a opção de alterar o número de entradas exibidas por página
    "bLengthChange": false,
    //* Desativa a indicação de processamento durante ações (como carregar dados)
    "processing": false,
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
        null, //* Configuração padrão para a coluna
        null, //* Configuração padrão para a coluna
        { "orderable": false } //* Configuração para desativar ordenação na última coluna
    ]
});

//* Espera que o documento esteja completamente carregado antes de executar o código jQuery
$(function() 
{   //* Define um manipulador de eventos para o envio do formulário com ID 'form-agenda'
    $("#form-configuracao").on("submit", function(event) {
    	//* Impede o comportamento padrão do formulário de ser enviado
        event.preventDefault();
        //* Serializa os dados do formulário para enviá-los via AJAX
        var data = $(this).serialize();
        //* Envia uma requisição AJAX para salvar os dados do usuário
        $.ajax({
            url: "configuracao-editar",
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
                        //* Restaura o conteúdo original do botão de salvar
                        set('botao_salvar').innerHTML = '<i class="fa fa-save"></i> Salvar'
                        //* Atualiza o conteúdo do valor
                        set($('#constante').val()+'Value').innerHTML = '<b>' + $('#valor').val() + '</b>';
			            //* Fecha o modal de agenda
			            $('#configuracao').modal('hide');
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

// Função para preencher um campo com um valor específico
function editar(constante, valor) {
    //* Limpa o formulário
    limpaFormulario();
    // Seleciona o campo pelo ID
     set('constante').value = constante;
     set('valor').value     = valor;
     set('t1').innerHTML    = 'Constante:<b> ' + constante + '</b>';
}

//* Função para marcar os campos obrigatórios e opcionais no formulário
function marcaCampos() {
    //* Define os campos como obrigatórios
    requerido('valor'  , 'p1');  //* Obrigatório
}

//* Função para limpar o formulário do usuário
function limpaFormulario() {
    $('#form-configuracao').each(function() {
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