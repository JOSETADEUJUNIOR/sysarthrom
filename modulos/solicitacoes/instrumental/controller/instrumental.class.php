<?php
/**
 ** Data : 02/01/2022
 ** Autor:Whilton Reis
 ** Polaris Tecnologia
 **/

class Instrumental extends Polar {
	/* 
	 * Metodo Listar usa o framework Polar --> ServeSide
	 * Metodo que faz a ligação da datatable com o banco de dados
	 * parametros: tabela, indice, colunas, editar, excluir, visualizar
	 * Documentação do framwork disponivel na Polaris Tecnoloiga
	 */
	public function listar() {   
		// Tabela
		$tabela  = 'tb_instrumental';
		// Join
		$join    = false;
		// Where
		if($this->permissao($_SESSION['usuarioID'],'instrumental','reg')) {
		    $where = false;
		} else {
			$where = 'td_usuario ='.$_SESSION['usuarioID'];
		}
		// Coluna Indice
		$indice  = 'td_id';
		// Array com as colunas a serem listadas
		$colunas = array( 
			'td_id',
			'td_data_envio', 
			'td_data_cirurgia', 
			'td_cidade',
			'td_material',
			'td_transportadora',
			'td_status'
		);		

		// Monta o conjunto de botões dependendo das permissões do usuário
		if($this->permissao($_SESSION['usuarioID'],'instrumental','view')) {   
            // Botões de status
			if($this->permissao($_SESSION['usuarioID'],'instrumental','status')) {
				$status = array(	
					array(
						'status()',
						'btn btn-default btn-xs m-b-5',
						'<i class="fa fa-ellipsis-h" title="Status"></i>'
					)					
				);     
			} else {
				$status = array();
			}

            // Botões de visualização
			$view = array(
				array(
					'visualizar()',                // Função
					'btn btn-info btn-xs m-b-5',   // Classe
					'<i class="fa fa-search"></i>' // Nome
				)
			);     
            
            // Botões de edição
			if($this->permissao($_SESSION['usuarioID'],'instrumental','edit')) {
				$editar = array(	
					array(
						'editar()',                       
						'btn btn-primary btn-xs m-b-5',
						'<i class="fa fa-pencil"></i>'
					)					
				);     
			} else {
				$editar = array();
			}
            
            // Botões de exclusão
			if($this->permissao($_SESSION['usuarioID'],'instrumental','delete')) {
				$excluir = array(					
					array(
						'excluir()',
						'btn btn-danger btn-xs m-b-5',
						'<i class="fa fa-remove"></i>'
					)
				);     
			} else {
				$excluir = array();
			}
            
            // Combina todos os botões em um único array
			$botao = array_merge($status, $view, $editar, $excluir);

			// Retorna os resultados do método serveSide(), Framework Polar
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

	// Método para exibir detalhes de um instrumental
	public function exibir($id)	{   
		if($this->permissao($_SESSION['usuarioID'],'instrumental','view')) {

			// Conexão com o banco
			$this->connect(); 
			$this->select('tb_instrumental', '*', '', 'td_id='.$id.'', 'td_id ASC');
	        echo json_encode($this->getResult());

	        // Fecha a conexão
			$this->disconnect();
		}
	}

	// Método para salvar informações de um instrumental
	public function salvar() {   
		if($this->permissao($_SESSION['usuarioID'],'instrumental','edit')) {

		    // Conexão com o banco
			$this->connect(); 

	        // Recebe o array dos campos
	        $data = $_POST['data'];
	        
	        // Monta array dos dados
			$dados = array(
			    'td_solicitante'      => $this->variavel($_SESSION['usuarioNome'], true),
				'td_data_envio'       => $this->variavel($this->formataData($data[0], 'entrada'), true),
				'td_data_cirurgia'    => $this->variavel($this->formataData($data[1], 'entrada'), true),
				'td_cidade'           => $this->variavel($data[2], true), //True = Obrigatório		    
	            'td_transportadora'   => $this->variavel($data[3], true),
				'td_material'         => $this->variavel($data[4], true),
				'td_observacao'       => $this->variavel($data[5], false),
				'td_status'           => $this->variavel('Aguardando...', true),
				'td_leitura'          => $this->variavel('0', true),
				'td_usuario'          => $this->variavel($_SESSION['usuarioID'], true)
			);

            // Verifica a entrada ID
    		if(!empty($_POST['id'])) {   
    			$sql  = $this->update('tb_instrumental',$dados,'td_id = '.$_POST['id'].'');

    		} elseif($this->permissao($_SESSION['usuarioID'],'instrumental','adicionar')) {
        	    $sql = $this->insert('tb_instrumental',$dados);
    		}

    		// Verifica se requisição teve sucesso.
			if($sql) {   
	            print_r('sucesso');	            
			} else {
	            print_r('erro');	            
			}

			// Fecha a conexão
			$this->disconnect();
		}
	}

	// Método para atualizar o status de um instrumental
	public function atualizar()	{   
        if($this->permissao($_SESSION['usuarioID'],'instrumental','edit')) {

		    // Conexão com o banco
			$this->connect(); 

	        // Recebe o array dos campos
	        $data = $_POST['data'];
	        
	        // Monta array dos dados
			$dados = array(
				'td_status'        => $this->variavel($data[0], true),
				'td_justificativa' => $this->variavel($data[1], false)
			);
			  
            // Verifica a entrada ID
    		if(!empty($_POST['id_'])) { 
        	    $sql  = $this->update('tb_instrumental',$dados,'td_id = '.$_POST['id_'].'');
    		}

    		// Verifica se requisição teve sucesso.
			if($sql) {   
	            print_r('sucesso');	            
			} else {
	            print_r('erro');	            
			}	

			// Fecha a conexão
			$this->disconnect();
		}
	}

	// Método para excluir um instrumental
	public function excluir($id) {   
		if($this->permissao($_SESSION['usuarioID'],'instrumental','delete')) {
			$this->connect();

			$this->select('tb_instrumental', '*', '', 'td_id='.$id.'', 'td_id ASC');
	        $arq = $this->getResult();       

			// Verifica se requisição teve sucesso.
			if($this->delete('tb_instrumental','td_id='.$id.'')) {   
	            print_r('sucesso');
			} else {
	            print_r('erro');
			}

			// Fecha a conexão
			$this->disconnect();	
		}	
	}

	// Método para enviar aviso de novo agendamento
	public function avisoNovoAgendamento() {
		$this->email($this->ultimoID());
	}

	// Método para retornar o último ID de instrumental inserido
	public function ultimoID() {

		// Conexão com o banco
		$this->connect(); 
		$this->sql('SELECT td_id FROM tb_instrumental ORDER By td_id DESC LIMIT 1');
		$id = $this->getResult();
		if(!empty($_POST['id'])) {
            return $_POST['id'];
		} else {
			return $id[0]['td_id'];
		}
		
        // Fecha a conexão
		$this->disconnect();
	}

	// Método para enviar e-mail com informações sobre instrumental
	public function email($id) {   
		// Verifica se o usuário tem permissão para enviar e-mails relacionados a instrumental
		if($this->permissao($_SESSION['usuarioID'],'instrumental','email')) {

			// Estabelece conexão com o banco de dados
	        $this->connect(); 

	        // Seleciona os dados do instrumental
			$this->select('tb_instrumental', '*', '', 'td_id='.$id.'', 'td_id ASC');
			$value = $this->getResult();
            
            // Monta a mensagem do e-mail
			$msg = '<h3>Cópia de Solicitação.</h3><hr>';
            
            // Monta os anexos da mensagem
	        $link = '';

	        //* Carrega os arquivos padrão para pagina
		    $template = array(
		    	//* Monta o email para envio do aviso
		    	'logo'          => 'https://londrina.arthrom.com/images/logo.png',
	        	'titulo'        => $msg,			
				'descricao'     => 'Este é um email gerado automaticamente. Por favor, não responda a este email, pois ele não é monitorado.',
				'url'           => 'https://londrina.arthrom.com',
				'codigo'        => $id,
				'data_env'      => $this->formataData($value[0]['td_data_envio'], 'saida'),
				'data_cir'      => $this->formataData($value[0]['td_data_cirurgia'], 'saida'),
				'cidade'        => $value[0]['td_cidade'],
				'material'      => $value[0]['td_material'],
				'transportadora'=> $value[0]['td_transportadora'],
				'observacoes'   => $value[0]['td_observacao'],
				'status'        => $value[0]['td_status'],
				'justificativa' => $value[0]['td_justificativa'],
				'tituloAnexo'   => 'Anexos:',
				'links'         => $link,
				'font'         => 'https://londrina.arthrom.com/font/fonts/',
				'urlContato'    => 'https://arthrom.com/contato'
			);
            
            //* Renderiza a view
	        $mensagem = $this->view('templates/email/instrumental.html', $template);
            
            // Cria uma nova instância do PHPMailer
			$eviaEmail   = new Email();
			$toAddresses = [emailDestino3,emailDestino4];
			$subject     = tituloEmail2;
			$body        = $mensagem;
			$altBody     = $mensagem;

			$eviaEmail->sendEmail($toAddresses, $subject, $body, $altBody);
		}
	}
}

