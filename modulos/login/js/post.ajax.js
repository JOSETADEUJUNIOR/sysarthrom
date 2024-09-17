/**
 ** Data : 02/01/2018
 ** Autor:Whilton Reis
 ** Polaris Tecnologia
 **/
 
//* Post
$(function() {
    //* Quando o documento estiver pronto, define o comportamento do formulário de login
    $("#logon").on("submit", function(event) {
        
        //* Muda o conteúdo do botão de login para um spinner
        document.getElementById('botao_entrar').innerHTML = '<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i>';
        
        //* Impede o comportamento padrão do formulário de login
        event.preventDefault();
        
        //* Serializa os dados do formulário
        var dados = $(this).serialize();
        
        //* Envia uma requisição AJAX para o servidor
        $.ajax({
            url: "logon",        //* URL para enviar a requisição
            type: "post",        //* Tipo de requisição (POST)
            data: dados,         //* Dados a serem enviados (dados do formulário serializados)
            success: function(data) {   //* Função a ser executada em caso de sucesso
                if (data == 'sucesso') {
                    //* Se a resposta for 'sucesso', redireciona para a página principal
                    location = 'principal';
                } else {
                    //* Se a resposta não for 'sucesso', redireciona para uma página de erro de validação de login
                    location = 'logon-erro-validacao';
                    
                    //* Restaura o conteúdo original do botão de login
                    document.getElementById('botao_entrar').innerHTML = 'Entrar';
                }
            }
        });
    });
});
