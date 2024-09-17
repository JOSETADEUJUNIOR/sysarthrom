<?php
/**
 ** Data : 02/01/2022
 ** Autor:Whilton Reis
 ** Polaris Tecnologia
 **/

class Cirurgico extends Polar {
	/* 
	 * Metodo Listar usa o framework Polar --> ServeSide
	 * Metodo que faz a ligação da datatable com o banco de dados
	 * parametros: tabela, indice, colunas, editar, excluir, visualizar
	 * Documentação do framwork disponivel na Polaris Tecnoloiga
	 */
	public function listar() {
	    // Tabela
	    $tabela  = 'tb_cirurgico';

	    // Join
	    $join    = false;

	    // Where
	    // Verifica se o usuário tem permissão para visualizar todos os registros ou somente os seus próprios
	    if($this->permissao($_SESSION['usuarioID'],'cirurgico','reg')){
	        $where = false;
	    } else {
	        $where = 'td_usuario ='.$_SESSION['usuarioID'];
	    }

	    // Coluna Índice
	    $indice  = 'td_id';

	    // Array com as colunas a serem listadas na tabela
	    $colunas = array( 
	        'td_id',
	        'td_hospital', 
	        'td_medico', 
	        'td_paciente',
	        'td_procedimento',
	        'td_data',
	        'td_status'
	    );                 

	    // Monta o conjunto de botões dependendo das permissões do usuário
	    if($this->permissao($_SESSION['usuarioID'],'cirurgico','view')) {
	        // Botões de status
	        if($this->permissao($_SESSION['usuarioID'],'cirurgico','status')) {
	            $status = array(    
	                array(
	                    'ler()',                       // Função
	                    'btn btn-default btn-xs m-b-5',// Classe
	                    '<i class="fa fa-check"></i>'  // Nome
	                )               
	            );     
	        } else {
	            $status = array();
	        }

	        // Botões de envio de email
	        if($this->permissao($_SESSION['usuarioID'],'cirurgico','email')) {
	            $email = array( 
	                array(
	                    'email()',                       
	                    'btn btn-warning btn-xs m-b-5',
	                    '<i class="fa fa-envelope" title="Enviar"></i>'
	                )                   
	            );     
	        } else {
	            $email = array();
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
	        if($this->permissao($_SESSION['usuarioID'],'cirurgico','edit')) {
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
	        if($this->permissao($_SESSION['usuarioID'],'cirurgico','delete')) {
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
	        $botoes = array_merge($status, $email, $view, $editar, $excluir);

	        // Retorna os resultados do método serveSide(), Framework Polar
	        return $this->serveSide(
	            $colunas, 
	            $indice, 
	            $tabela, 
	            $join,
	            $where,
	            $botoes
	        );

	    } else {       
	        return false;
	    }
	}


	public function exibir($id) {   
	    // Verifica se o usuário possui permissão para visualizar agendamentos
		if($this->permissao($_SESSION['usuarioID'],'cirurgico','view')) {

	        // Conexão com o banco
			$this->connect(); 

	        // Seleciona os dados da tabela 'tb_cirurgico' onde o 'td_id' é igual ao $id fornecido
			$this->select('tb_cirurgico', '*', '', 'td_id='.$id.'', 'td_id ASC');
			
	        // Retorna os resultados como um JSON
	        echo json_encode($this->getResult());
	        
	        // Fecha a conexão com o banco de dados
			$this->disconnect();
		}
	}


	public function salvar() {   
	    // Verifica se o usuário tem permissão para editar agendamentos
	    if($this->permissao($_SESSION['usuarioID'],'cirurgico','edit')) {
	        // Conexão com o banco
	        $this->connect(); 
	        // Recebe o array dos campos do formulário POST
	        $data = $_POST['data'];
	        
	        // Monta array dos dados para inserção ou atualização
	        $array1 = array(
	            'td_solicitante'            => $this->variavel($_SESSION['usuarioNome'], true),
	            'td_data'                   => $this->variavel($this->formataData($data[0], 'entrada'), true),
	            'td_hora'                   => $this->variavel($data[1], true),
	            'td_convenio'               => $this->variavel($data[2], true),
	            'td_hospital'               => $this->variavel($data[3], true),
	            'td_medico'                 => $this->variavel($data[4], true),
	            'td_paciente'               => $this->variavel($data[5], true),            
	            'td_procedimento'           => $this->variavel($data[6], true),
	            'td_material'               => $this->variavel($data[7], true),
	            'td_observacao'             => $this->variavel($data[8], false),
	            'td_status'                 => $this->variavel('Aguardando...', true),
	            'td_leitura'                => $this->variavel('0', true),
	            'td_usuario'                => $this->variavel($_SESSION['usuarioID'], true),
	        );
	        
	        // Inicializa arrays para anexos
	        $array2 = array();
	        $array3 = array();
	        $array4 = array();
	        
	        // Verifica se existem arquivos anexados e os prepara para inserção no banco
	        if(!empty($_FILES['arquivo1']['name'])) {
	            $array2 = array(
	                'td_liberacao' => $this->variavel($this->uploadAnexo(date('ymdhis'), $_FILES['arquivo1'], 'arquivo1', 'liberacao1'), false),
	            );
	        }

	        if(!empty($_FILES['arquivo2']['name'])) {
	            $array3 = array(
	                'td_liberacao_complementar' => $this->variavel($this->uploadAnexo(date('ymdhis'), $_FILES['arquivo2'], 'arquivo2', 'liberacao2'), false),
	            );
	        }

	        if(!empty($_FILES['arquivo3']['name'])) {
	            $array4 = array(
	                'td_pedido_medico' => $this->variavel($this->uploadAnexo(date('ymdhis'), $_FILES['arquivo3'], 'arquivo3', 'pedido_medico'), false),
	            );
	        }
	        
	        // Concatena os arrays de dados
	        $dados = $array1 + $array2 + $array3 + $array4;
	        
	        // Transforma data em mês // 2 é a diferença entre o numero do Mês e o ID da tabela
	        $mesID = date( 'm', strtotime( $this->formataData($data[0], 'entrada') ) )+2;
	        
	        // Seleciona os dados da tabela
	        $this->select('tb_grafico', '*', '', 'td_id='.$mesID.'', 'td_id ASC');
	        
	        // Array de dados
	        $array = $this->getResult();
	        
	        // Ano atual
	        $ano = date('Y');
	        
	        // Cria o array que incrementa o contador e altera o ano
	        $cont = array('td_count' => $array[0]['td_count']+1,'td_ano' => $ano );
	        
	        // Atualiza tabela com dados incrementados
	        $inc = $this->update('tb_grafico',$cont,'td_id = '.$mesID.'');
	        
	        // Verifica se a atualização foi bem-sucedida
	        if($inc) {   
			    // Extrai a data e a hora formatadas do array $data
			    $horaData = $this->formataData($data[0], 'entrada').' '.$data[1];
			    
			    // Obtém a hora atual e a hora definida em formato de timestamp
			    $horaAtual    = strtotime(date('Y-m-d H:i'));
			    $horaDefinida = strtotime($horaData);

			    // Calcula o tempo restante em segundos até a hora definida
			    $tempo = str_replace('-','',$horaDefinida - $horaAtual);
			    
			    // Verifica se o tempo restante é maior ou igual ao tempo definido nas configurações
			    // e se a hora definida é maior que a hora atual
			    if($tempo >= $this->tempoParaSegundos(antecedenciaCirurgia) && $horaDefinida > $horaAtual) {   
			        if(empty($this->dias())) {
			            // Verifica se o ID foi enviado via POST
			            if(!empty($_POST['id'])) {   
			                // Atualiza o registro na tabela 'tb_cirurgico' se o ID estiver presente
			                $sql  = $this->update('tb_cirurgico',$dados,'td_id = '.$_POST['id'].'');

			            } elseif($this->permissao($_SESSION['usuarioID'],'cirurgico','adicionar')) {
			                // Insere um novo registro na tabela 'tb_cirurgico' se o ID não estiver presente
			                $sql = $this->insert('tb_cirurgico',$dados);
			            }
			        } else {
			            // Se houver feriado, exibe 'feriado' e encerra o script
			            print_r('feriado');
			            exit();
			        }
			    } else {
			        // Se não atender às condições de tempo, exibe 'horario' e encerra o script
			        print_r('horario');
			        exit();
			    }
			} else {
			    // Se $inc for falso, exibe 'erro'
			    print_r('erro');
			}

	        // Verifica se a operação foi bem-sucedida
	        if($sql) {   
	            print_r('sucesso');
	        } else {
	            print_r('erro');
	        }

	        // Fecha a conexão
	        $this->disconnect();
	    }
	}

	// Método para excluir um agendamento com base no ID fornecido
	public function excluir($id) {
	    // Verifica se o usuário tem permissão para excluir agendamentos
	    if($this->permissao($_SESSION['usuarioID'],'cirurgico','delete'))	{

	        // Estabelece conexão com o banco de dados
	        $this->connect();

	        // Seleciona os dados do agendamento que será excluído
	        $this->select('tb_cirurgico', '*', '', 'td_id='.$id.'', 'td_id ASC');
	        $arq = $this->getResult();       

	        // Verifica se a requisição de exclusão teve sucesso
	        if($this->delete('tb_cirurgico','td_id='.$id.'')) {   
	            // Remove os arquivos relacionados ao agendamento, se existirem
	            if($arq[0]['td_liberacao']) {
	                //unlink('uploads/'.$arq[0]['td_liberacao']);
	            }

	            if ($arq[0]['td_pedido_medico']) {
	                //unlink('uploads/'.$arq[0]['td_pedido_medico']);
	            } 
	            print_r('sucesso');
	        } else {
	            print_r('erro');
	        }

	        // Fecha a conexão
	        $this->disconnect();	
	    }	
	}

	// Método para enviar um aviso de novo agendamento criado ou alterado
	public function avisoNovoAgendamento() {
	    // Envia e-mails de aviso para destinatários específicos
	    $this->email(
	        $this->ultimoID(), 
	        emailDestino1, 
	        '<h3>Um agendamento foi criado/alterado!</h3><hr>'
	    );

	    $this->email(
	        $this->ultimoID(), 
	        emailDestino2, 
	        '<h3>Um agendamento foi criado/alterado!</h3><hr>'
	    );
	}

	// Método para enviar um aviso de exclusão de agendamento
	public function avisoExclusao($id) {
	    // Envia e-mails de aviso para destinatários específicos
	    $this->email(
	        $id, 
	        emailDestino1, 
	        '<h3>Agendamento excluído!</h3><hr>'
	    );

	    $this->email(
	        $id, 
	        emailDestino2, 
	        '<h3>Agendamento excluído!</h3><hr>'
	    );
	}

	// Método para enviar um aviso de leitura de agendamento
	public function avisoLeitura($id, $msg) {   
	    // Estabelece conexão com o banco de dados
	    $this->connect();

	    // Obtém informações do remetente do agendamento
	    $this->sql("SELECT * FROM tb_cirurgico a INNER JOIN tb_usuarios b ON a.td_usuario=b.td_id WHERE a.td_id=".$id."");
	    $remetente = $this->getResult();

	    // Envia e-mail de aviso para o remetente do agendamento
	    $this->email(
	        $id, 
	        $remetente[0]['td_email'], 
	        '<h3>Agendamento Nº:'.$id.' foi '.$msg.'</h3><hr>'
	    );

	    // Fecha a conexão
	    $this->disconnect();
	}

	// Método para marcar um agendamento como lido
	public function leitura($id) {
	    // Verifica se o usuário tem permissão para marcar agendamentos como lidos
	    if($this->permissao($_SESSION['usuarioID'],'cirurgico','status'))	{

	        // Estabelece conexão com o banco de dados
	        $this->connect();

	        // Dados a serem atualizados para marcar o agendamento como lido
	        $dados = array(
	            'td_leitura' => $this->variavel('1', true),
	            'td_status'  => $this->variavel('Recebido', true),
	        );

	        // Verifica se a requisição teve sucesso
	        if($this->update('tb_cirurgico',$dados,'td_id = '.$id.'')) {
	            print_r('sucesso');            
	        } else {
	            print_r('erro');
	        }

	        // Fecha a conexão
	        $this->disconnect();
	    }
	}

	// Método para desmarcar um agendamento
	public function desmarcar($id) {
	    // Verifica se o usuário tem permissão para desmarcar agendamentos
	    if($this->permissao($_SESSION['usuarioID'],'cirurgico','status')) {

	        // Estabelece conexão com o banco de dados
	        $this->connect();

	        // Dados a serem atualizados para desmarcar o agendamento
	        $dados = array(
	            'td_leitura' => $this->variavel('3', true),
	            'td_status'  => $this->variavel('Confirmado', true),
	        );

	        // Verifica se a requisição teve sucesso
	        if($this->update('tb_cirurgico',$dados,'td_id = '.$id.'')) {
	            print_r('sucesso');
	        } else {
	            print_r('erro');
	        }

	        // Fecha a conexão
	        $this->disconnect();
	    }
	}

	// Método para fazer upload de anexos
	public function uploadAnexo($cod, $file, $input, $name) {   
	    // Verifica se o usuário tem permissão para editar agendamentos e fazer upload de anexos
	    if($this->permissao($_SESSION['usuarioID'],'cirurgico','edit')) {
	        if (!empty($file['name'])) {
	            require_once 'core/class/upload.class.php';

	            // Instancia a classe de upload
	            $up = new upload($input);

	            // Define o diretório de upload
	            $up->dir = "uploads/";

	            // Obtém a extensão do arquivo
	            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

	            // Define o nome do arquivo
	            $up->arq = $name.'_'.$cod.'.'.$ext;

	            // Define as extensões permitidas
	            $up->extension = array('pdf','jpg','jpeg','png','gif');

	            // Define o tamanho máximo do arquivo em MB
	            $up->size = 5;

	            // Realiza o upload do arquivo
	            $up->makeUpload();

	            // Retorna o nome do arquivo
	            return $up->arq;
	        } else {
	            print_r('vazio');
	        }
	    }
	}

	// Método para obter o último ID de agendamento
	public function ultimoID() {
	    // Estabelece conexão com o banco de dados
	    $this->connect(); 
	    // Obtém o último ID de agendamento da tabela
	    $this->sql('SELECT td_id FROM tb_cirurgico ORDER By td_id DESC LIMIT 1');
	    $id = $this->getResult();
	    if(!empty($_POST['id'])) {
	        return $_POST['id'];
	    } else {
	        return $id[0]['td_id'];
	    }
	    // Fecha a conexão
	    $this->disconnect();
	}

	// Método para verificar se o dia atual é feriado
	public function dias() {
	    // Obtém o ano atual
	    $ano = date('Y');
	    // Obtém os feriados do ano através de uma API
	    $api = file_get_contents('https://brasilapi.com.br/api/feriados/v1/'.$ano);
	    // Decodifica o JSON retornado pela API
	    $obj = json_decode($api);      
	    // Data de hoje
	    $hoje = date('Y-m-d');        
	    // Verifica se hoje é feriado
	    foreach ($obj as $key => $value) {
	        if (strtotime($value->date) == strtotime($hoje) OR date('w') == 0 OR date('w') == 6) {
	            return true;
	        }
	    }
	}

	// Função verifica se agendamento foi checado
    public function checkado($id) {

		//*  Conexão com o banco
		$this->connect(); 
		$this->sql('SELECT * FROM tb_cirurgico WHERE td_id='.$id);
		$id = $this->getResult();
		return $id[0]['td_leitura'];

        //*  Fecha a conexão
		$this->disconnect();
	}

	// Método para enviar e-mails
	public function email($id = null, $mail = null, $msg = null) {
	    // Verifica se o usuário tem permissão para enviar e-mails relacionados a agendamentos
	    if($this->permissao($_SESSION['usuarioID'],'cirurgico','email')) {
	        // Estabelece conexão com o banco de dados
	        $this->connect(); 
	        
	        // Verifica se foi passado um e-mail
	        if($mail == null) {
	            $email = $_POST['email'];
	        } else {
	            $email[] = $mail; 
	        }
	        
	        // Verifica se foi passado um ID
	        if($id == null) {
	            $id_mail= $_POST['id_mail'];
	        } else {
	            $id_mail = $id;
	        }
	        
	        // Seleciona os dados do agendamento
	        $this->select('tb_cirurgico', '*', '', 'td_id='.$id_mail.'', 'td_id ASC');
	        $value = $this->getResult();
	        
	        // Monta os anexos da mensagem
	        $link = '';

	        if($value[0]['td_liberacao']) {
	            $link.= '<a href="'.urlUpload.$value[0]['td_liberacao'].'">LIBERAÇÃO 1</a><br>';
	        }

	        if($value[0]['td_liberacao_complementar']) {
	            $link.= '<a href="'.urlUpload.$value[0]['td_liberacao_complementar'].'">LIBERAÇÃO 2</a><br>';
	        }

	        if($value[0]['td_pedido_medico']) {
	            $link.= '<a href="'.urlUpload.$value[0]['td_pedido_medico'].'">PEDIDO MÉDICO</a>';
	        }

	        //* Carrega os arquivos padrão para pagina
		    $template = array(
		    	//* Monta o email para envio do aviso
		    	'logo'         => 'https://londrina.arthrom.com/images/logo.png',
	        	'titulo'       => $msg,			
				'descricao'    => 'Este é um email gerado automaticamente. Por favor, não responda a este email, pois ele não é monitorado.',
				'url'          => 'https://londrina.arthrom.com',
				'codigo'       => $id_mail,
				'data'         => $this->formataData($value[0]['td_data'], 'saida'),
				'hora'         => $value[0]['td_hora'],
				'convenio'     => $value[0]['td_convenio'],
				'hospital'     => $value[0]['td_hospital'],
				'medico'       => $value[0]['td_medico'],
				'paciente'     => $value[0]['td_paciente'],
				'procedimento' => $value[0]['td_procedimento'],
				'material'     => $value[0]['td_material'],
				'observacoes'  => $value[0]['td_observacao'],
				'tituloAnexo'  => 'Anexos:',
				'links'        => $link,
				'urlContato'   => 'https://arthrom.com/contato'
			);
            
            //* Renderiza a view
	        $mensagem = $this->view('templates/email/cirurgia.html', $template);

			// Cria uma nova instância do PHPMailer
			$eviaEmail   = new Email();
			if(!$email[1]) {
				$toAddresses = [$email[0]];
			} else {
				$toAddresses = [$email[0],$email[1]];
			}
			$subject     = tituloEmail1;
			$body        = $mensagem;
			$altBody     = $mensagem;

			$eviaEmail->sendEmail($toAddresses, $subject, $body, $altBody);

	        // Fecha a conexão
	        $this->disconnect();
	    }
	}
}