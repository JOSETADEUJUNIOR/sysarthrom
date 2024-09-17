<?php
/**
 ** Data : 02/01/2022
 ** Autor:Whilton Reis
 ** Polaris Tecnologia
 **/

class Principal extends Polar {
   
    /* 
	 * Metodo Listar usa o framework Polar --> ServeSide
	 * Metodo que faz a ligação da datatable com o banco de dados
	 * parametros: tabela, indice, colunas, editar, excluir, visualizar
	 */
	public function listarAgenda() {   
		//* Nome da tabela
		$tabela = 'tb_agenda';

		//* Join utilizado na consulta
		$join   = false;

		//* Condição WHERE para filtrar os resultados
		if($this->permissao($_SESSION['usuarioID'],'agenda','reg')){
			//* Se o usuário tem permissão para visualizar todos os registros, a condição WHERE é omitida
		    $where = false;
		}else{
			//* Caso contrário, apenas o registro do próprio usuário é exibido
			$where = 'td_usuario ='.$_SESSION['usuarioID'];
		}

		//* Nome da coluna que serve como índice na tabela
		$indice = 'td_id';

		//* Array com as colunas a serem listadas na tabela
		$colunas= array( 
			'td_id',
			'td_data',
			'td_titulo'
		);	

		//* Monta o conjunto de botões dependendo das permissões do usuário
		if($this->permissao($_SESSION['usuarioID'],'agenda','view')) {
	        //* Botões de visualização
	        $view = array(
	            array(
	                'visualizar()',                //* Função associada ao botão de visualização
	                'btn btn-info btn-xs m-b-5',   //* Classe CSS do botão
	                '<i class="fa fa-search"></i>' //* Ícone do botão de visualização
	            )
	        );     
            
            //* Botões de edições
			if($this->permissao($_SESSION['usuarioID'],'agenda','edit')) {
				$editar = array(					
					array(
						'editar()',                       
						'btn btn-primary btn-xs m-b-5', //* Classe CSS do botão
						'<i class="fa fa-pencil"></i>'  //* Ícone do botão de visualização
					)
				);     
			} else {
				$editar = array();
			}
            
            //* Botões de exclusão
			if($this->permissao($_SESSION['usuarioID'],'agenda','delete')) {
				$excluir = array(					
					array(
						'excluir()',
						'btn btn-danger btn-xs m-b-5', //* Classe CSS do botão
						'<i class="fa fa-remove"></i>' //* Ícone do botão de visualização
					)
				);     
			} else {
				$excluir = array();
			}
            
            //* Combina todos os botões em um único array
			$botao = array_merge($view, $editar, $excluir);

			//* Retorna os resultados do método serveSide(), Framework Polar
			return $this->serveSide(
				$colunas, 
				$indice, 
				$tabela, 
				$join,
				$where,
				$botao
			);

		} else {       

			return false;
		}
	}

	public function notificacoes() {   
		//* Verifica se o usuário tem permissão para visualizar a agenda
	    if($this->permissao($_SESSION['usuarioID'],'agenda','view')) {
	        
	        //* Conexão com o banco de dados
	        $this->connect(); 

	        //* Seleciona registros da tabela 'tb_agenda' para o usuário atual ($_SESSION['usuarioID']) 
	        //* com status igual a 1 (presumivelmente, indica notificações ativas)
	        $this->select('tb_agenda', '*', '', 'td_usuario ='.$_SESSION['usuarioID'].' AND td_status = 1', 'td_id ASC');

	        //* Inicializa a variável HTML que armazenará o conteúdo das notificações
	        $html = '';

	        //* Verifica se há notificações retornadas pela consulta anterior
	        if($noti = $this->numRows()) {
	            //* Se houver notificações, constrói um HTML que inclui o número de notificações
	            $html .= " <span class='badge' id='bell-on'> ";
	            $html .= "  $noti  ";
	            $html .= " </span> ";
	        }
	        
	        //* Retorna o HTML das notificações, possivelmente vazio se não houver notificações
	        return $html;

	        //* Fecha a conexão com o banco de dados
	        $this->disconnect();
	        //* Este trecho de código nunca é alcançado, pois 'return' encerra a execução da função.
	    }
	}

	public function exibirAgenda($id) {
		//* Verifica se o usuário tem permissão para visualizar a agenda
	    if($this->permissao($_SESSION['usuarioID'],'agenda','view')) {	        

	        //* Conexão com o banco de dados
	        $this->connect(); 

	        //* Seleciona um registro específico da tabela 'tb_agenda' com base no ID fornecido
	        $this->select('tb_agenda', '*', '', 'td_id='.$id.'', 'td_id ASC');

	        //* Atualiza o registro selecionado para definir o status como 0 (presumivelmente, indica que foi exibido)
	        $this->update('tb_agenda', array('td_status' => 0), 'td_id = '.$id.'');

	        //* Exibe o resultado da consulta no formato JSON
	        echo json_encode($this->getResult());
	        //* Presumivelmente, getResult() retorna o resultado da consulta anterior.

	        //* Fecha a conexão com o banco de dados
	        $this->disconnect();
	    }
	}


	public function salvarAgenda() {   
		//* Verifica se o usuário tem permissão para editar a agenda
	    if($this->permissao($_SESSION['usuarioID'],'agenda','edit')) {

	        //* Conexão com o banco de dados
	        $this->connect(); 

	        //* Recebe o array dos campos enviados via POST
	        $data = $_POST['data'];

	        //* Monta array dos dados para inserção ou atualização na tabela 'tb_agenda'
	        $dados = array(
	            'td_titulo'    => $this->variavel($data[0], true),
	            'td_descricao' => $this->variavel($data[1], true),
	            'td_data'      => $this->formataData($this->variavel($data[2], true), 'entrada'),
	            'td_status'    => 1,
	            'td_usuario'   => $_SESSION['usuarioID']
	        );        

	        //* Verifica se há um ID enviado via POST
	        if(!empty($_POST['id'])) {
	            //* Se houver ID, atualiza o registro na tabela 'tb_agenda'
	            $sql = $this->update('tb_agenda', $dados, 'td_id = '.$_POST['id'].'');
	            
	        } elseif($this->permissao($_SESSION['usuarioID'],'agenda','adicionar')) {
	            //* Se não houver ID, insere um novo registro na tabela 'tb_agenda'
	            $sql = $this->insert('tb_agenda', $dados);
	        }

	        //* Verifica se a operação foi bem-sucedida
	        if($sql) {
	            print_r('sucesso');
	        } else {
	            print_r('erro');
	        }

	        //* Fecha a conexão com o banco de dados
	        $this->disconnect();
	    }
	}

	public function excluirAgenda($id) {
		//* Verifica se o usuário tem permissão para excluir a agenda
	    if($this->permissao($_SESSION['usuarioID'],'agenda','delete')) {

	        //* Conexão com o banco de dados
	        $this->connect();

	        //* Verifica se a requisição teve sucesso ao excluir o registro da tabela 'tb_agenda'
	        if($this->delete('tb_agenda','td_id='.$id.'')) {
	            print_r('sucesso');
	        } else {
	            print_r('erro');
	        }

	        //* Fecha a conexão com o banco de dados
	        $this->disconnect();
	    }
	}

	//* Função para gerar o gráfico A
	public function graficoA() {
		//* Verifica se o usuário tem permissão para visualizar o dashboard
	    if($this->permissao($_SESSION['usuarioID'],'dashboard','view')) {

	        //*Conecta ao banco de dados
	        $this->connect();

	        //*Seleciona os dados da tabela 'tb_grafico'
	        $this->select('tb_grafico', '*', '', '', 'td_id ASC');

	        //* Array de dados
	        $array = $this->getResult();

	        //*Cria variáveis para construir o gráfico
	        $item  = '';    
	        $count = '';
	        $color = '';    

	        //* Seleciona os dados das tabelas 'tb_usuarios' e 'tb_cirurgico'
	        $this->select('tb_usuarios');
	        $usuarios = $this->numRows();

	        $this->select('tb_cirurgico');
	        $cirurgico = $this->numRows();

	        //* Itera sobre os dados do array
	        foreach ($array as $key => $value) {   
	            if($value['td_id'] <= 2) {   
	                $donut[1]  = $usuarios;
	                $donut[2]  = $cirurgico;

	                $codigo = $value['td_id'];

	                $item  .= '"'.$value['td_item'] . '",';
	                $count .= ' '.$donut[$codigo]   . ' ,';
	                $color .= '"'.$value['td_cor']  . '",'; 
	            }
	        }

	        //*Monta o gráfico em JavaScript		
	        $html  = ' <script> ';
	        $html .= ' new Chart(document.getElementById("bar-chartA"), {type: "doughnut", ';
	        $html .= ' data: { ';
	        $html .= ' labels: ['.$item.'], ';
	        $html .= ' datasets: [{ ';
	        $html .= ' label: " Total ",';
	        $html .= ' backgroundColor: ['.$color.'], ';
	        $html .= ' data: [' . $count . ']}]}, ';
	        $html .= ' options: { ';
	        $html .= ' legend: { display: true }, ';
	        $html .= ' title:  { display: true,text: "Cadastros"}}}); ';
	        $html .= ' </script> ';
	        //*Imprime o gráfico
	        print_r($html);

	        //*Fecha a conexão com o banco de dados
	        $this->disconnect();
	    }
	}

	//* Função para gerar o gráfico B
	public function graficoB() {
		//* Verifica se o usuário tem permissão para visualizar o dashboard
	    if($this->permissao($_SESSION['usuarioID'],'dashboard','view')) {

	        //*Ano atual
	        $ano = date('Y');

	        //*Conecta ao banco de dados
	        $this->connect();

	        //*Seleciona os dados da tabela 'tb_grafico' para o ano atual
	        $this->select('tb_grafico', '*', '', 'td_ano = '.$ano.'', 'td_id ASC');

	        //* Array de dados
	        $array = $this->getResult();

	        //*Cria variáveis para construir o gráfico
	        $item  = '';    
	        $count = '';
	        $color = '';    

	        //* Itera sobre os dados do array
	        foreach ($array as $key => $value) {
	            if($value['td_id'] > 2 && $value['td_id'] <= 14) {
	                $item  .= '"'.$value['td_item']  . '",';
	                $count .= ' '.$value['td_count'] . ' ,';
	                $color .= '"'.$value['td_cor']   . '",'; 
	            }
	        }

	        //*Monta o gráfico em JavaScript
	        $html  = ' <script> ';
	        $html .= ' new Chart(document.getElementById("bar-chartB"), {type: "bar", ';
	        $html .= ' data: { ';
	        $html .= ' labels: ['.$item.'], ';
	        $html .= ' datasets: [{ ';
	        $html .= ' label: " Total ", ';
	        $html .= ' backgroundColor: ['.$color.'], ';
	        $html .= ' data: [' . $count . ']}]}, ';
	        $html .= ' options: { ';
	        $html .= ' legend: { display: false }, ';
	        $html .= ' title:  { display: true,text: "Cirurgias de '.$ano.'"}}}); ';
	        $html .= ' </script> ';

	        //*Imprime o gráfico
	        print_r($html);

	        //*Fecha a conexão com o banco de dados
	        $this->disconnect();
	    }
	}
}