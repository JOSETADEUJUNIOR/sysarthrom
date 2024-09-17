<?php
/**
** Data : 08/11/2019
** Autor:Whilton Reis
** Polaris Tecnologia
**/

$string = utf8_encode(file_get_contents('../../Index.xml'));

$xmlfile  = str_replace('</ROWSET>
	<?xml version="1.0"?>
	<ROWSET>', "", $string);

$cirurgias = simplexml_load_string($xmlfile) or die("Error: Cannot create XML object");
$list      = '';
$idObs     = 1;

foreach ($cirurgias as $arthrom):

	if($arthrom->CIRURGIA){
		$dataAtual    = explode('/', $arthrom->CIRURGIA);
		$dataOriginal = $dataAtual[1].$dataAtual[0];
		$mes          = $dataAtual[1];
	}else{
		$dataOriginal = 0;
		$mes          = 0;
	}

	if(date('w', strtotime(date('Y-m-d'))) == 5){
		$dia  = date('d') + 3;
		if($dia < 10)
			$dia = '0'.$dia;
		$novaData = $mes.$dia;

	}else{
		$dia  = date('d') + 1;
		if($dia < 10)
			$dia = '0'.$dia;
		$novaData = $mes.$dia;
	}

	if($arthrom->STATUS == 'AGENDAMENTO - LONDRINA' && $arthrom->CIRURGIA) //&& $dataOriginal <= $novaData
	{   

        //Define a celula vazia se não informado
		$arthrom->INSTRUMENTADOR == 'NAO INFORMADO' ? $inst = '' : $inst = $arthrom->INSTRUMENTADOR;

		//Define a cor da celula
        if($arthrom->ENVIADO != ''){
        	$Acor = '#00FF7F';
        }else{
        	$Acor = '';
        }

        if ($arthrom->AGENDADO != '') {
        	$Bcor = '#00AAFF';
        }else{
        	$Bcor = '';
        }

        if ($arthrom->SEPARADO != '') {
        	$Ccor = '#FFD700';
        }else{
        	$Ccor = '';
        }

        //Pega a primeira linha da observação do estoque
		$resumo = explode("\n", $arthrom->OBSESTOQUE);

		$list .= '<tr style="cursor:pointer; font-weight: bold; background-color:'.$Acor.''.$Bcor.''.$Ccor.'" class="row_cirurgia" onclick="mostraObs('.$idObs.')">';
		$list .= '<td style="padding: 2px" width="10%">'.$arthrom->CIRURGIA.'</td>';
		$list .= '<td style="padding: 2px" width="10%">'.$arthrom->HORA.'</td>';
		$list .= '<td style="padding: 2px" width="80%">'.$arthrom->PACIENTE.'</td>';
		$list .= '<td style="padding: 2px" width="42%">'.$arthrom->MEDICO.'</td>';
		$list .= '<td style="padding: 2px" width="32%">'.$arthrom->HOSPITAL.'</td>';
		$list .= '<td style="padding: 2px" width="52%">'.$resumo[0].'</td>';
		$list .= '<td style="padding: 2px" width="37%">'.$arthrom->CONVENIO.'</td>';
		$list .= '<td style="padding: 2px" width="15%">'.$inst.'</td>';
		$list .= '<input type="hidden" id="obs'.$idObs.'" value="'.$arthrom->OBSESTOQUE.'">';
		$list .= '<input type="hidden" id="pas'.$idObs.'" value="'.$arthrom->PACIENTE.'">';
		$list .= '</tr>';
	}

	$idObs++;
endforeach;
echo $list;