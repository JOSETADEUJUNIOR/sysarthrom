<?php
/**
 ** Data : 02/01/2018
 ** Autor:Whilton Reis
 ** Polaris Tecnologia
 **/

class Polar extends Bd {

	//* Função para tratamento de url
	//* A Url é passada por partes, sendo elas:
	//* 1º Módulo
	//* 2º Pagina
	//* 3º Ação
	//* 4º ID
	//* Ex: cadastro-usuario-editar-34
	public function url() {   
	    //* Verifica se foi passado uma url, se não etorna para o logon
		return (isset($_GET['url'])) ? explode('-', $_GET['url']) : '';
	}

	//* Função para debug
	public function debug()	{   //* Debuga o ultimo erro no PHP
        print_r(error_get_last());
	}

	//* Função para renderização do html
	//* Recebe os paramentros view e array
	//* View: Arquivos padrão HTML fixos que contem chamadas entre chaves Ex: {{ Titulo }}
	//* Array: Dados passados pelas chaves Ex: array('Titulo' => 'Pagina de Cadastro');
	public function view( $view = null, $array = null ) { 

        //* verificando se o arquivo existe
		if ( is_file( $view ) )

			//* retornando conteúdo do arquivo
			$view = file_get_contents( $view );   

        //* verifica se existe um array
        if( isset( $array ) )

			//* recebemos um array com as tags
			foreach ( $array as $a => $b )
				$view = str_replace( '{{ '.$a.' }}', $b, $view );    

        //*  retorno o html com conteúdo final
		return $view;
	}

    //* Função para verificar vetor
    //* Verifica se um vetor ou array foi passado vazio
	public function vetor($data, $vazio = true) {

		//* Cria um array dos dados
		$data = array();
		foreach ($data as $value) {

			//* Retira espaçõs do vetor
			$verifica = str_replace(' ', '', $value);

			//* Verifica se o valor é nulo ou vazio
			if (is_null($verifica) || empty($verifica) && $verifica != '0' && $vazio != false) {
				print_r('Verifique campos vazios.');
				exit;
			} else {
				$data[] = htmlspecialchars($value, ENT_QUOTES);
			}
		}
		return $data;
	}

	//* Função para verificar variavel
	//* Mesma função que a vetor
	public function variavel($data, $vazio = true) {

		//* Retira espaçõs da variavel
		$verifica = str_replace(' ', '', $data);

		//* Verifica se o valor é nulo ou vazio
		if (is_null($verifica) || empty($verifica) && $verifica != '0' && $vazio != false) {
			print_r('Verifique campos vazios.');
			exit;
		} else {
			return htmlspecialchars($data, ENT_QUOTES);
		}
	}

    //* Função para formatar data
    //* Recebe data tipo 0000-00-00 e devolve 00/00/000 ( saida )
    //* Recebe data tipo 00/00/000 e devolve 0000-00-00 ( entrada )
	public function formataData($data, $tipo) { 		
		if($tipo == 'entrada') {   
			$var  = explode('/', $data);
			$data = $var[2].'-'.$var[1].'-'.$var[0]; 
		} elseif($tipo == 'saida') {
			$var  = explode('-', $data);
			$data = $var[2].'/'.$var[1].'/'.$var[0];  
		}    
		return $data;    
	}

    //* Função para verificar se string é uma data
	public function campoData($string) {         
        $string = str_replace('/', '-', $string);     
        $stamp  = strtotime($string);
        //* 
        if (is_numeric($stamp) && !strpos($string, ':') && strpos($string, '-')) {              
            $mes = date( 'm', $stamp ); 
            $dia = date( 'd', $stamp ); 
            $ano = date( 'Y', $stamp ); 
            return $dia.'/'.$mes.'/'.$ano;                 
        }  
        return $this->limitaTexto($string); 
    }

    //* Função para limitar o texto em 30 caracteres
	public function limitaTexto($texto)	{
	  return mb_strimwidth($this->formataTexto($this->tirarAcentos($texto)), 0, 30, "...");   
	}

	//* Função para tirar acentos
	public function tirarAcentos($string) {
         return preg_replace(
         	array(
         		"/(á|à|ã|â|ä)/",
         		"/(Á|À|Ã|Â|Ä)/",
         		"/(é|è|ê|ë)/",
         		"/(É|È|Ê|Ë)/",
         		"/(í|ì|î|ï)/",
         		"/(Í|Ì|Î|Ï)/",
         		"/(ó|ò|õ|ô|ö)/",
         		"/(Ó|Ò|Õ|Ô|Ö)/",
         		"/(ú|ù|û|ü)/",
         		"/(Ú|Ù|Û|Ü)/",
         		"/(ñ)/",
         		"/(Ñ)/"
         	),
         	explode(" ","a A e E i I o O u U n N"), 
         	$string
         );
	}

	 //* Função para colocar texto com primeira sentença maiuscula
	public function formataTexto($texto) { 
	  $minuscula = strtolower($texto);
	  return ucwords($minuscula);   
	}

    //* Função para verificar se o usuário tem permissão para acessar uma página
	public function permissao($id, $pagina, $acao) {

		//* Conecta ao banco de dados
	    $this->connect();

	    //*  Cria um select para retornar as permissões
		$this->select(
			'tb_usuarios',
			'*',
			'tb_nivel ON tb_usuarios.td_nivel = tb_nivel.td_id_nivel
			 JOIN tb_permissao ON tb_nivel.td_id_nivel = tb_permissao.td_nivel_id
			 JOIN tb_paginas ON tb_permissao.td_pagina_id = tb_paginas.td_id_pagina',
			'td_id = "'.$id.'" AND td_nome_pagina = "'.$pagina.'"',
			'td_id ASC','1'
		);
        
	    if ($res = $this->getResult()) {	            
	        if ($acao === 'adicionar' && $res[0]['td_adicionar']) {  //*  Permissão de adicionar concedida
	            return true; 
	        } elseif ($acao === 'view' && $res[0]['td_view']) {     //*  Permissão de visualização concedida
	        	return true;	        
	        } elseif ($acao === 'edit' && $res[0]['td_edit']) {     //*  Permissão de edição concedida
	            return true;
	        } elseif ($acao === 'delete' && $res[0]['td_delete']) { //*  Permissão de exclusão concedida
	            return true;
	        } elseif ($acao === 'reg' && $res[0]['td_registros']) { //*  Permissão de listar todos os registros
	            return true;
	        } elseif ($acao === 'status' && $res[0]['td_status']) { //*  Permissão pra mudar status
	            return true;
	        } elseif ($acao === 'email' && $res[0]['td_email']) {   //*  Permissão pra enviar email
	            return true;
	        } elseif ($acao === 'nivel' && $res[0]['td_nivel']) {   //*  Permissão pra alterar nivel
	            return true;
	        }
	    }        
	    
        //*  O usuário não tem permissão para a ação solicitada
	    return false; 
	}
    
    //* Função para converter horas em segundos ex: 18:00:00 p/ 64800
	public function tempoParaSegundos($tempo) {
	    list($horas, $minutos, $segundos) = explode(':', $tempo);
	    return $horas * 3600 + $minutos * 60 + $segundos;
	}

    //* Função serveside para datatables  
	public function serveSide($colunas, $indice, $tabela, $join, $where, $botao) {   //* Conecta ao banco de dados
		$this->connect();
		/**
		 * Codificação
		 */
		mb_internal_encoding('UTF-8'); 
		/**
		 * Paginação
		 */
		$limite = "";
		if ( isset( $_POST['start'] ) && $_POST['length'] != '-1' ) {
		    $limite = intval( $_POST['start'] ).", ".intval( $_POST['length'] );
		}	
		/**
		 * Orderção
		 */
		$regraOrdenacao = array();
		if ( isset( $_POST['order'][0]['column'] ) ) {
		    for ( $i=0 ; $i<count($_POST['order']) ; $i++ ) {
		        if ( $_POST['columns'][$i]['orderable'] == 'true' ) {
		            $regraOrdenacao[] = "`".$colunas[ intval( $_POST['order'][$i]['column'] ) ]."` ".($_POST['order'][$i]['dir']==='asc' ? 'asc' : 'desc');
		        }
		    }
		}
		//* 
		if (!empty($regraOrdenacao)) {
		    $order = implode(", ", $regraOrdenacao);
		} else {
		    $order = "";
		}
		/**
		* Filtragem
		*/		
        $contarColuna = count($colunas);
		 
		if ( isset($_POST['search']['value']) && $_POST['search']['value'] != "" ) {
		    $regraDeFiltro = array();

		    for ( $i=0 ; $i<$contarColuna ; $i++ ) {
		        if ( $_POST['columns'][$i]['searchable'] == true) {
		            $regraDeFiltro[] = "`".$colunas[$i]."` LIKE '%".$_POST['search']['value']."%'";
		        }
		    }
		    if (!empty($regraDeFiltro)) {
		        $regraDeFiltro = array('('.implode(" OR ", $regraDeFiltro).')');
		    }
		}
        //*  Filtro individual de colunas
		for ( $i=0 ; $i<$contarColuna ; $i++ ) {
		    if ( isset($_POST['columns'][$i]['searchable']) && $_POST['columns'][$i]['searchable'] == true && $_POST['columns'][$i]['search']['value'] != '' ) {
		        $regraDeFiltro[] = "`".$colunas[$i]."` LIKE '%".$_POST['search']['value']."%'";
		    }
		}
		 
		if (!empty($regraDeFiltro)) {
		    if($where) {
		    	$where = $where.' AND '.implode(" AND ", $regraDeFiltro);
		    } else {
		    	$where = implode(" AND ", $regraDeFiltro);
		    }		    
		} else {
		    $where;
		}	  
		/**
		* Consultas SQL
		* Obter dados para exibir
		*/
		$queryColunas = array();
		foreach ($colunas as $col) {
		    if ($col != ' ') {
		        $queryColunas[] = $col;
		    }
		}
		 
		$query  = $this->select($tabela, 'SQL_CALC_FOUND_ROWS '.implode(", ", $queryColunas), $join, $where, $order, $limite);
		$result = $this->getResult();
		  
		//*  O comprimento do conjunto de dados após a filtragem
		$query  = $this->select($tabela, 'SQL_CALC_FOUND_ROWS '.implode(", ", $queryColunas), $join, $where);
		$filtro = $this->numRows();		 
		
        //*  Comprimento total do conjunto de dados
		$query  = $this->select($tabela, 'SQL_CALC_FOUND_ROWS '.implode(", ", $queryColunas), $join);
		$total  = $this->numRows();  
		/**
		 * Saida
		 */
		$saida = array(
		    "draw"                 => intval(isset($_POST['draw'])?$_POST['draw']:''),
		    "iTotalRecords"        => $total,
		    "iTotalDisplayRecords" => $filtro,
		    "aaData"               => array(),
		);
		  
		foreach($result as $aRow){
		    $row = array();
		    for ( $i=0 ; $i<$contarColuna ; $i++ ) {
		        $row[] = $this->campoData($aRow[ $colunas[$i] ]);
		    }
            

            //* Parte adicionada para mudança de status do botão de checagem dos agendamentos
            //* Interage com a função "checkado" no controller cirurgico
		    $btn = ''; 
            
            if ($tabela == 'tb_cirurgico') {
            	foreach ($botao as $key => $value) {
	            	$checkado = $this->checkado($aRow[ $indice ]);
	            	if($checkado == 0) {
		            	$fun = explode('()', $value[0]);
		            	$btn.= " <button onclick='".$fun[0]."(".$aRow[ $indice ].")' class='".$value[1]."' > ";
		            	$btn.= " ".$value[2]." </button> ";
		            } elseif($checkado == 3 && $value[0] == 'ler()') {
		                $fun = explode('()', $value[0]);
		            	$btn.= " <button onclick='' class='btn btn-success btn-xs m-b-5' > ";
		            	$btn.= " ".$value[2]." </button> ";
		            } else {   
		            	if($value[0] == 'ler()') {   
		            		$fun = explode('()', $value[0]);
		            	    $btn.= " <button onclick='desmarcar(".$aRow[ $indice ].")' class='btn btn-warning btn-xs m-b-5' > ";
		            	    $btn.= " ".$value[2]." </button> ";
		            	} else {
	                        $fun = explode('()', $value[0]);
		            	    $btn.= " <button onclick='".$fun[0]."(".$aRow[ $indice ].")' class='".$value[1]."' > ";
		            	    $btn.= " ".$value[2]." </button> ";
		            	}
		            }
	            };
            } else {   
            	foreach ($botao as $key => $value) {
	            	$fun = explode('()', $value[0]);
		            $btn.= " <button onclick='".$fun[0]."(".$aRow[ $indice ].")' class='".$value[1]."' > ";
		            $btn.= " ".$value[2]." </button> ";
		        }
            }
            //* Fim
                    
            if(!empty($botao)){
            	$row[] = $btn;
            }
            
		    $saida['aaData'][] = $row;
		}

		echo json_encode( $saida );
		//*  Conexão com o banco
		$this->connect(); 
	}

}
