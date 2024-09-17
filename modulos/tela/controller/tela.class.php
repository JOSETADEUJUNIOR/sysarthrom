<?php
/**
 ** Data : 02/01/2022
 ** Autor:Whilton Reis
 ** Polaris Tecnologia
 **/
class Tela extends Polar 
{

    /* 
	 * Metodo Listar usa o framework Polar --> ServeSide
	 * Metodo que faz a ligação da datatable com o banco de dados
	 * parametros: tabela, indice, colunas, editar, excluir, visualizar
	 * Documentação do framwork disponivel na Polaris Tecnoloiga
	 */
	public function listar($cidade)
	{   

        if($this->permissao($_SESSION['usuarioID'],'telas','view'))
        {   

            // Busca o arquivo XML gerado pelo sistema ODIN
            if($cidade == 'londrina') {
                $string = utf8_encode(file_get_contents('xml/Index.xml'));
            } elseif($cidade == 'maringa') {
                $string = utf8_encode(file_get_contents('xml/Index.xml'));
            } elseif($cidade == 'cascavel') {
                $string = utf8_encode(file_get_contents('xml/xml_oeste/Index.xml'));
            }           
    		

            // Retira o cabeçalho do XML
            $xmlfile  = str_replace('</ROWSET>
            	<?xml version="1.0"?>
            	<ROWSET>', "", $string
            );

            // Abre o arquivo XML
            $cirurgias = simplexml_load_string($xmlfile) or die("Error: Cannot create XML object");
            $list      = '';
            $idObs     = 1;

            // Verifica qual cidade foi passada no parametro
            if($cidade == 'londrina') {
                $cidade = 'AGENDAMENTO - LONDRINA';
            } elseif($cidade == 'maringa') {
                $cidade = 'AGENDAMENTO - MARINGA';
            } elseif($cidade == 'cascavel') {
                $cidade = 'AGENDAMENTO - OESTE';
            }

            // Lista todas as cirurgias do XML
            foreach ($cirurgias as $arthrom):

                // Pega data da CIRURGIA
            	if($arthrom->CIRURGIA){
            		$dataAtual    = explode('/', $arthrom->CIRURGIA);
            		$dataOriginal = $dataAtual[1].$dataAtual[0];
            		$mes          = $dataAtual[1];
            	}else{
            		$dataOriginal = 0;
            		$mes          = 0;
            	}

                // Verifica se hoje é sexta-feira (5 é o código do dia da semana para sexta-feira)
                if(date('w', strtotime(date('Y-m-d'))) == 5) {
                    // Se for sexta-feira, adiciona 3 dias à data atual
                    $dia  = date('d') + 3;

                    // Se o dia for menor que 10, adiciona um zero à esquerda para manter o formato de dois dígitos do dia
                    if($dia < 10)
                        $dia = '0'.$dia;

                    // Concatena o dia com o mês para criar uma nova data
                    $novaData = $mes.$dia;

                } else {
                    // Se não for sexta-feira, adiciona 1 dia à data atual
                    $dia  = date('d') + 1;

                    // Se o dia for menor que 10, adiciona um zero à esquerda para manter o formato de dois dígitos do dia
                    if($dia < 10)
                        $dia = '0'.$dia;

                    // Concatena o dia com o mês para criar uma nova data
                    $novaData = $mes.$dia;
                }

                // Verifica se o XML é da cidade escolhida
            	if($arthrom->STATUS == $cidade && $arthrom->CIRURGIA) //&& $dataOriginal <= $novaData
            	{   
                    //Define a celula vazia se não informado
                	$arthrom->INSTRUMENTADOR == 'NAO INFORMADO' ? $inst = 'NAO INFORMADO' : $inst = $arthrom->INSTRUMENTADOR;
            
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
            		$resumo = explode("\n", $this->limitaTexto($arthrom->OBSESTOQUE));
            
            		$list .= '<tr style="background-color:'.$Acor.''.$Bcor.''.$Ccor.'" role="row" class="odd">';
            		$list .= '<td>'.ucwords(strtolower($this->limitaTexto($arthrom->CIRURGIA))).'</td>';
            		$list .= '<td>'.ucwords(strtolower($arthrom->HORA)).'</td>';
            		$list .= '<td>'.ucwords(strtolower($this->limitaTexto($arthrom->PACIENTE))).'</td>';
            		$list .= '<td>'.ucwords(strtolower($this->limitaTexto($arthrom->MEDICO))).'</td>';
            		$list .= '<td>'.ucwords(strtolower($this->limitaTexto($arthrom->HOSPITAL))).'</td>';
            		$list .= '<td>'.ucwords(strtolower($resumo[0])).'</td>';
            		$list .= '<td>'.ucwords(strtolower($this->limitaTexto($arthrom->CONVENIO))).'</td>';
            		$list .= '<td>'.ucwords(strtolower($inst)).'</td>';
            		$list .= '<input type="hidden" id="obs'.$idObs.'" value="'.$arthrom->OBSESTOQUE.'">';
            		$list .= '<input type="hidden" id="pas'.$idObs.'" value="'.$arthrom->PACIENTE.'">';
            		$list .= '<td><button onclick="mostraObs('.$idObs.')" class="btn btn-default btn-xs m-b-5">  <i class="fa fa-search"></i> </button></td>';
            		$list .= '</tr>';
            }
            
            $idObs++;
            endforeach;
            return $list;
        }
	}
    
	 //Função para limitar o texto em 30 caracteres
	public function limitaTexto($texto)
	{
	  return mb_strimwidth($this->formataTexto($texto), 0, 24, "...");   
	}
}