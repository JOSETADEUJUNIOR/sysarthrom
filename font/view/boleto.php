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
$obj = new boleto();
//Metodo que ativa o menu
$obj->activeMenu('cadastros','boleto_c');
//Incluimos o html
switch ($url[2]) {
	case 'listar':
		include'modulos/titulo/html/boleto-listar.html';
		break;

	case 'adicionar':
		if(!isset($_POST['data'])){
		    include'modulos/titulo/html/boleto.html';
	    }else{$obj->salvar();}
		break;

	case 'editar':
		if(!isset($_POST['data'])){
		    include'modulos/titulo/html/boleto.html';
	    }else{$obj->editar();}
		break;

	case 'excluir':	
		$obj->excluir($_POST['id']);
		break;

	case 'visualizar':	    	    
	    $obj = new titulo();
		$obj->gerarTitulo('avulso', $url[3]);
		break;

	case 'visualizarcarne':
	    $impressao = 'carne';
		include'lib/mpdf60/index.php';
		break;

	default:
		include 'html/404_alt.html';
		break;
}
	