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
$obj = new carteira();
//Metodo que ativa o menu
$obj->activeMenu('cadastros','carteira');
//Incluimos o html
switch ($url[2]) {
	case 'listar':
		include'modulos/titulo/html/carteira-listar.html';
		break;

	case 'adicionar':
		if(!isset($_POST['data'])){
			include 'modulos/titulo/html/carteira.html';
	    }else{$obj->salvar();}
		break;

	case 'editar':
		if(!isset($_POST['data'])){
			include 'modulos/titulo/html/carteira.html';
	    }else{$obj->editar();}
		break;

	case 'excluir':	
		$obj->excluir($_POST['id']);
		break;

	case 'padrao':	
		$obj->padrao($_POST['id']);
		break;

	case 'cpfCnpj':
		$obj->cpfCnpj($_POST['titular']);
		break;

	case 'banco':
		include'modulos/titulo/html/carteira-banco.html';
		break;

	default:
		include 'html/404_alt.html';
		break;
}
	