<?php
/**
 ** Data : 08/11/2019
 ** Autor:Whilton Reis
 ** Polaris Tecnologia
 **/

switch ($_GET['cidade']) {
	case 'londrina':
		include 'php/view/londrina.view.php';
		break;
	
	case 'maringa':
		include 'php/view/maringa.view.php';
		break;

	case 'oeste':
		include 'php/view/oeste.view.php';
		break;

	default:
		# code...
		break;
}