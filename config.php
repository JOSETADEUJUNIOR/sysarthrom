<?php

/**
* @ Polaris Tecnologia
* @ Autor: Whilton Reis
* @ Data : 20/11/2016
*/

date_default_timezone_set('America/Sao_Paulo');

//constante de configuração do banco de dados
/* define('db_host'  , 'localhost');
define('db_nome'  , 'londrina_portal');
define('db_user'  , 'londrina_user');
define('db_pass'  , 'F4$WX[~CjT[='); */

define('db_host'  , 'localhost');
define('db_nome'  , 'portal_londrina');
define('db_user'  , 'root');
define('db_pass'  , null);

//constante titulo do sistema
define('tituloSistema', 'Arthrom');

//constante de email origem de envio
define('emailOrigem', 'noreply1@arthrom.com');

//constante url do termo de aceite
define('termo', 'https://londrina.arthrom.com/pdf/termo.pdf');

//constantes servidor de email
define('emailHost',    'smtpt.f1.ultramail.com.br');
define('emailUsuario', 'noreply1=arthrom.com');
define('emailSenha',   'Cadeadofixo9@');

//constantes de configuração para agendamento de cirugia
define('urlUpload'     , 'https://londrina.arthrom.com/uploads/');
define('emailDestino1' , 'gerencia.arthrom@gmail.com');
define('emailDestino2' , 'agendamento.arthrom@gmail.com');
define('tituloEmail1'  , 'Agendamento Cirurgico');

//constantes de configuração para agendamento de instrumental
define('emailDestino3' , 'estoque@arthrom.com');
define('emailDestino4', 'estoque1@arthrom.com');
define('tituloEmail2'  , 'Agendamento Instrumental');

//* constante que define o tempo de antecedencia para agendar cirurgia
define('antecedenciaCirurgia', '18:00:00');