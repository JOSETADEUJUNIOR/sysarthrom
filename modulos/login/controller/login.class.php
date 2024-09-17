<?php
/**
 * Data : 02/01/2022
 * Autor: Whilton Reis
 * Polaris Tecnologia
 **/

class Login extends Bd {
    
    //* Construtor da classe
    public function __construct() {
        //* Verifica se a sessão já foi iniciada
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    //* Função que valida as credenciais do usuário
    public function validaUsuario($usuario, $senha) {
        //* Abre conexão com o banco de dados
        $this->connect();

        //* Escapa as aspas nas credenciais do usuário para evitar injeção de SQL
        $nusuario = addslashes($usuario);
        $nsenha   = addslashes($senha);

        //* Monta uma consulta SQL para procurar um usuário com o nome de usuário e senha fornecidos
        $this->select(
            'tb_usuarios',
            '*',
            '',
            'td_login = "'.$nusuario.'" AND td_senha = "'.sha1($nsenha).'"',
            'td_id ASC',
            '1'
        );

        //* Obtém o resultado da consulta
        $res = $this->getResult();

        //* Verifica se foi encontrado um usuário com as credenciais fornecidas
        if (empty($res)) {
            //* Nenhum usuário encontrado, retorna falso
            return false;
        } else {
            //* Define valores de usuário e senha passados no form
        	$_SESSION['usuarioLogin'] = $usuario;
            $_SESSION['usuarioSenha'] = $senha;

            //* Define valores na sessão com os dados do usuário
            $_SESSION['usuarioID']    = $res[0]['td_id'];
            $_SESSION['usuarioNome']  = $res[0]['td_nome'];
            $_SESSION['usuarioEmail'] = $res[0]['td_email'];

            //* Retorna verdadeiro
            return true;
        }

        //* Fecha a conexão com o banco de dados
        $this->disconnect();
    }

    //* Função que protege uma página verificando se o usuário está logado
    public function protegePagina() {
        if (!isset($_SESSION['usuarioID']) || !isset($_SESSION['usuarioNome'])) {
            //* Não há usuário logado, redireciona para a página de login
            $this->expulsaVisitante('logon');
        } else {
            //* Há usuário logado, verifica se as credenciais ainda são válidas
            if (!$this->validaUsuario($_SESSION['usuarioLogin'], $_SESSION['usuarioSenha'])) {
                //* As credenciais não correspondem, redireciona para a página de login com erro
                $this->expulsaVisitante('logon-erro');
            }
        }
    }

    //* Função para expulsar um visitante e redirecionar para a página de login
    public function expulsaVisitante($url) {
        //* Remove as variáveis da sessão
        unset(
            $_SESSION['usuarioID'], 
            $_SESSION['usuarioNome'], 
            $_SESSION['usuarioLogin'],
            $_SESSION['usuarioSenha'],
            $_SESSION['usuarioEmail']
        );

        //* Redireciona para a página de login
        header("Location: ".$url);
    }

    //* Função para efetuar logout do usuário
    public function logout($tipo = null) {
        //* Remove as variáveis da sessão
        unset(
            $_SESSION['usuarioID'], 
            $_SESSION['usuarioNome'], 
            $_SESSION['usuarioLogin'], 
            $_SESSION['usuarioSenha']
        );

        //* Verifica se o logout está sendo feito via AJAX
        if ($tipo == 'ajax') {
            //* Se for AJAX, imprime 'erro'
            print_r('erro');
        } else {
            //* Caso contrário, redireciona para a página de login
            header("Location: logon");
        }
    }

    //* Função para exibir alertas ao usuário
    public function alerta($erro) {
        //* Gera um alerta com base no tipo de erro fornecido
        switch ($erro) {
            case 'erro':
                $alerta = '<div class="alert alert-danger">';
                $alerta .= '<strong>Ops!</strong> Login ou senha incorreto!';
                $alerta .= '</div>';
                break;
            case 'erroemail':
                $alerta = '<div class="alert alert-danger">';
                $alerta .= '<strong>Ops!</strong> Email incorreto!';
                $alerta .= '</div>';
                break;
            case 'emailsucesso':
                $alerta = '<div class="alert alert-success">';
                $alerta .= '<strong>Parabéns!</strong> Sua nova senha foi enviada!';
                $alerta .= '</div>';
                break;
            default:
                $alerta = '<div class="alert alert-info" style="text-align:center">';
                $alerta .= '<strong>ATENÇÃO!</strong> <p style="text-align:center">
                          Utilizamos cookies essenciais e tecnologias semelhantes de acordo com a nossa 
                          <a target="_blank" href="'.termo.'">
                          <strong>Política de Privacidade</strong></a> 
                          e, ao continuar navegando, você concorda com estas condições!</p>';
                $alerta .= '</div>';
                break;
        }

        //* Retorna o alerta gerado
        return $alerta;
    }

    //* Função para redefinir a senha do usuário e enviar por e-mail
    public function senha() {
        //* Conecta ao banco de dados
        $this->connect();

        //* Seleciona os dados do usuário com base no endereço de e-mail fornecido
        $this->select('tb_usuarios', '*', '', 'td_email="'.$_POST['email'].'"', 'td_id ASC');

        //* Obtém os dados do usuário
        $usuario = $this->getResult();

        //* Verifica se o e-mail fornecido corresponde a um usuário no banco de dados
        if (!empty($usuario)) {
            //* Gera uma nova senha aleatória
            $novaSenha = substr(md5(uniqid(rand(), true)), 0, 8);

            //* Atualiza a senha do usuário no banco de dados
            $this->update('tb_usuarios', array('td_senha' => sha1($novaSenha)), 'td_email="'.$_POST['email'].'"');

            //* Carrega os arquivos padrão para pagina
            $template = array(
                //* Monta o email para envio do aviso
                'logo'         => 'https://londrina.arthrom.com/images/logo.png',
                'titulo'       => 'Recuperação de Senha',         
                'descricao'    => 'Este é um email gerado automaticamente. Por favor, não responda a este email, pois ele não é monitorado.',
                'url'          => 'https://londrina.arthrom.com',
                'codigo'       => rand(),
                'data'         => date('d/m/Y'),
                'hora'         => date('H:i:s'),
                'senha'        => $novaSenha,
                'font'         => 'https://londrina.arthrom.com/font/fonts/',
                'urlContato'   => 'https://arthrom.com/contato'
            );

            //* Instância um objeto Polar
            $view = new Polar();
            
            //* Renderiza a view
            $mensagem = $view->view('templates/email/senha.html', $template);

            // Cria uma nova instância do PHPMailer
            $eviaEmail   = new Email();
            $toAddresses = [$_POST['email']];
            $subject     = 'Recuperação de Senha';
            $body        = $mensagem;
            $altBody     = $mensagem;
            
            //* Envia o e-mail com a nova senha para o endereço de e-mail do usuário
            $eviaEmail->sendEmail($toAddresses, $subject, $body, $altBody);

            //* Redireciona para uma página indicando que o e-mail com a nova senha foi enviado com sucesso
            header('Location: logon-emailsucesso');
        } else {
            //* Se o e-mail não corresponder a nenhum usuário, redireciona para uma página de erro
            header('Location: logon-erroemail');
        }

        //* Fecha a conexão com o banco de dados
        $this->disconnect();
    }

}
