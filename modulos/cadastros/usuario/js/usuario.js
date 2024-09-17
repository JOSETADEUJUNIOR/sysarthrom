/**
 ** Data : 02/01/2018
 ** Autor:Whilton Reis
 ** Polaris Tecnologia
 **/
 
//* Inicialização da DataTable com configurações específicas
var table = $('#datatable-usuarios').DataTable({
    //* Desativa a opção de alterar o número de entradas exibidas por página
    "bLengthChange": false,
    //* Desativa a indicação de processamento durante ações (como carregar dados)
    "processing": false,
    //* Ativa o modo server-side para processamento dos dados
    "serverSide": true,
    //* Configuração AJAX para buscar os dados da tabela
    "ajax": {
        "url":  "cadastros-usuario-listar", //* URL para buscar os dados
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
        null, //* Configuração padrão para a coluna
        null, //* Configuração padrão para a coluna
        null, //* Configuração padrão para a coluna
        null, //* Configuração padrão para a coluna
        { "orderable": false } //* Configuração para desativar ordenação na última coluna
    ]
});


//* Espera que o documento esteja completamente carregado antes de executar o código jQuery
$(function() {
    //* Define um manipulador de eventos para o envio do formulário com ID 'form-usuario'
    $("#form-usuario").on("submit", function(event) {
        //* Impede o comportamento padrão do formulário de ser enviado
        event.preventDefault();
        //* Serializa os dados do formulário para enviá-los via AJAX
        var data = $(this).serialize();
        //* Envia uma requisição AJAX para salvar os dados do usuário
        $.ajax({
            url: "cadastros-usuario-salvar",
            type: "post",
            data: data,
            success: function(data) {
                //* Verifica se a resposta da requisição é 'sucesso'
                if (data == 'sucesso') {
                    //* Modifica o conteúdo do botão de salvar para indicar que está salvando
                    set('botao_salvar').innerHTML = '<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i> Salvando...';
                    //* Exibe uma mensagem de sucesso após um curto período de tempo
                    setTimeout(function() {
                        sucesso('Registro salvo com sucesso.');
                        //* Restaura o conteúdo original do botão de salvar
                        set('botao_salvar').innerHTML = '<i class="fa fa-save"></i> Salvar';
                        //* Recarrega os dados na tabela de usuários
                        $('#datatable-usuarios').DataTable().ajax.reload();
                        //* Fecha o modal de usuário
                        $('#usuario').modal('hide');
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
        url: "cadastros-usuario-exibir-" + id,
        success: function(data) {
            //* Limpa o formulário antes de preenchê-lo com os novos dados
            limpaFormulario();
            //* Preenche os campos do formulário com os dados recebidos
            set('id').value    = data[0].td_id;
            set('nome').value  = data[0].td_nome;
            set('email').value = data[0].td_email;
            set('login').value = data[0].td_login;
            //* Chama a função nivel() para carregar os níveis de usuário
            nivel(data[0].td_nivel);
            //* Marca os campos como preenchidos
            marcaCampos();
            //* Bloqueia o botão de salvar
            $("#botao_salvar").prop('disabled', true);
            //* Bloqueia os campos do formulário para edição
            $(".form-control").prop('disabled', true);
            //* Abre o modal para exibir os detalhes do usuário
            $('#usuario').modal('show');
        }
    });
}

//*Função editar formulário
function editar(id) {   
    //* Realiza uma requisição AJAX para obter os dados do usuário com o ID fornecido
    $.ajax({
        type: "GET",
        dataType: 'JSON',
        url: "cadastros-usuario-exibir-" + id,
        success: function(data) {
            //*Limpa o formulario
            limpaFormulario();
            //*Retorna os data para o formulário
            set('id').value    = data[0].td_id;         
            set('nome').value  = data[0].td_nome;
            set('email').value = data[0].td_email;          
            set('login').value = data[0].td_login;
            //* Chama a função nivel() para carregar os níveis de usuário
            nivel(data[0].td_nivel);
            //*Marca os campos preenchidos
            marcaCampos();
            //*Desbloqueia botão
            $("#botao_salvar").prop('disabled', false);
            //*Desbloqueia Campos
            $(".form-control").prop('disabled', false);
            //*Campo senha requerido
            $("#senha").prop('required', false);
            //*Campo senha informação
            set('senhaHelp').innerHTML = 'Para não editar a senha, mantenha o campo vazio.';
            //*Abre a modal com os dados para edição
            $('#usuario').modal('show');
        }
    });
}

//* Função para carregar os níveis de usuário
function nivel(id) {
    //* Verifica se o ID foi fornecido
    if(id) {
        //* Requisição AJAX para obter os níveis de usuário com base no ID fornecido
        $.ajax({
            type: "GET",
            dataType: 'JSON',
            url: "cadastros-usuario-nivel-" + id,
            success: function(data) {
                //* Cria uma lista de opções de seleção com os dados recebidos
                var select = '<option value="">.: Selecione :.</option>';
                for (i = 0; i < data.length; i = i + 1) {
                    select += '<option selected value="' + data[i].td_id_nivel + '">' + data[i].td_nome_nivel + '</option>';
                }

                //* Define as opções criadas no elemento HTML com ID 'nivel'
                set('nivel').innerHTML = select;
            }
        });
    } else {
        //* Se nenhum ID for fornecido, busca todos os níveis de usuário
        $.ajax({
            type: "GET",
            dataType: 'JSON',
            url: "cadastros-usuario-nivel",
            success: function(data) {
                //* Cria uma lista de opções de seleção com os dados recebidos
                var select = '<option value="">.: Selecione :.</option>';
                for (i = 0; i < data.length; i = i + 1) {
                    select += '<option value="' + data[i].td_id_nivel + '">' + data[i].td_nome_nivel + '</option>';
                }

                //* Define as opções criadas no elemento HTML com ID 'nivel'
                set('nivel').innerHTML = select;
            }
        });
    }
}

//* Função para executar a exclusão do registro do usuário
function executaExclusao(id) {
    //* Envia uma requisição AJAX para excluir o registro do usuário com o ID fornecido
    $.ajax({
        type: "GET",
        url: "cadastros-usuario-excluir-" + id, //* URL para excluir o registro do usuário
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
            $('#datatable-usuarios').DataTable().ajax.reload();
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

//* Função para configurar o modal do usuário
function usuario() {
    //* Limpa o campo de ID do usuário
    set('id').value = '';   
    //* Remove a informação sobre a senha do modal
    set('senhaHelp').innerHTML = '';
    //* Limpa o formulário do usuário
    limpaFormulario();
    //* Carrega os níveis de usuário
    nivel();
    //* Define o campo de senha como obrigatório
    $("#senha").prop('required', true);
    //* Habilita o botão de salvar
    $("#botao_salvar").prop('disabled', false);
    //* Habilita os campos do formulário
    $(".form-control").prop('disabled', false);
    //* Define um atraso antes de verificar se a senha é obrigatória
    setTimeout(function() {
        requerido('senha', 'p4'); //* Define a senha como obrigatória
    }, 200);
}

//* Função para marcar os campos obrigatórios e opcionais no formulário
function marcaCampos() {
    //* Define os campos de nome, email, login e nível como obrigatórios
    requerido('nome' , 'p1'); //* Obrigatório
    requerido('email', 'p2'); //* Obrigatório
    requerido('login', 'p3'); //* Obrigatório
    requerido('nivel', 'p5'); //* Obrigatório
    //* Define o campo de senha como opcional
    opcional('senha' , 'p4'); //* Opcional
}

//* Função para limpar o formulário do usuário
function limpaFormulario() {
    $('#form-usuario').each(function() {
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
