<?php

/**
* @ Polaris Tecnologia
* @ Autor: Whilton Reis
* @ Data : 20/11/2016
*/

//Instânciamos a classe
$obj = new titulo();
//Incluimos a view
switch ($url[1]) {
	case 'pdf':
	    //Verificamos se o boleto é referente ao mês
	    if($url[4] == date(mY))
		$obj->gerarTitulo('avulso', $url[3]);
		//Marcamos o titulo como entregue  
        $obj->tituloEntregue($url[3], 'pdf');  
		break;

	case 'img':
	    //Marcamos o titulo como entregue
        $obj->tituloEntregue($url[2], 'img'); 
               
	default:
		include'html/404.html';
		break;
}
