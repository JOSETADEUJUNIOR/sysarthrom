<?php

/**
* @ Polaris Tecnologia
* @ Autor: Whilton Reis
* @ Data : 20/11/2016
*/

//Instânciamos a classe Login
$obj = new login();
//Metodo que verifica a sessao
$obj->verificaSessao();
//Instânciamos a classe
$obj = new arquivos();
//Incluimos o html
switch ($url[2]) {
	case 'remessa':
		//Metodo que ativa o menu
	    $obj->activeMenu('arquivos','remessa');
		include'modulos/titulo/html/arquivo-remessa.html';
		break;

	case 'retorno':
		//Metodo que ativa o menu
	    $obj->activeMenu('arquivos','retorno');
	    include'modulos/titulo/html/arquivo-retorno.html';	    
		break;

	default:
		include 'html/404_alt.html';
		break;
}
	