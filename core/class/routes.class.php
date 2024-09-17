<?php
/**
 ** Data : 02/01/2018
 ** Autor:Whilton Reis
 ** Polaris Tecnologia
 **/

class Routes extends Polar {
	
	public function __construct() {
		//* Instância um objeto login
		$login = new Login();

        //* Instância um objeto menu
		$menu = new Menu();

		//* Instância um objeto configuracao
		$configuracao = new Configuracao();

		//* Chama o metodo de url amigavel 
		$url = $this->url();

		//* Carrega os arquivos padrão para pagina
	    $paginaPadrao = array(
	    	//* Monta o menu conforme permissão do usuário
	    	//* O Parametro url[0] pega a primeira parte da url e compara com o menu selecionado marcando como "active"
	    	'menu'   => $menu->montaMenu(isset($url[0])?$url[0]:''),
        	'topo'   => $this->view( 'includes/topo.php'),			
			'rodape' => $this->view( 'includes/rodape.php'),
			'update' => '?'.date('his'), // Força o css e javascript a atualizar
		);

		//* Une o array paginaPadrao com o array de configurações
	    $paginaBase = array_merge($paginaPadrao, $configuracao->base());

		//* Seleciona a view, o nome da view é o mesmo que a primeira parte da url
		switch (isset($url[0])?$url[0]:'logon') {
			case 'logon':
			    if(!isset($_POST['usuario']) && !isset($_POST['senha'])) {		

				    //* Renderiza a view, manda todos os dados do array paginaBase para a função view.
	                echo $this->view( 
	                	'modulos/login/view/login.html'
	                	, array('alerta' => $login->alerta(isset($url[1])?$url[1]:''))
	                );	
                } else {

                	//* Verifica se um formulário foi enviado
					if ($_SERVER['REQUEST_METHOD'] == 'POST') {

						//* Salva duas variáveis com o que foi digitado no formulário
						//* Detalhe: faz uma verificação com isset() pra saber se o campo foi preenchido
						$usuario = (isset($_POST['usuario'])) ? $_POST['usuario'] : '';
						$senha   = (isset($_POST['senha']))   ? $_POST['senha']   : '';
						
						//* Utiliza uma função criada no controller "modulos/login/controller/" pra validar os dados digitados
						//* O Post é feito pelo arquivo post.ajax.js em "modulos/login/js" acionada pelo botão "botao_entrar" do form de login
						if ($login->validaUsuario($usuario, $senha) == true) {	//* O usuário e a senha digitados foram validados, manda pra página interna
							print_r('sucesso');
						} else {	//* O usuário e/ou a senha são inválidos, manda de volta pro form de login
							$login->logout('ajax');
						}
					}
                }	
				break;

			case 'principal':
			    //* Função que protege uma página
			    $login->protegePagina();	

			    //* Instância um objeto principal
		        $principal = new Principal();

		        //* Se não for passado nenhum renderiza a view
		        switch (isset($url[1])?$url[1]:'') {
		        	case 'agenda':

		        	//* Verifica a permissão do usuário
			        if(!isset($url[2]) && $principal->permissao($_SESSION['usuarioID'],'agenda','view')) {

			        	//* Verifica se o usuário tem permissão para adicionar
			        	if($principal->permissao($_SESSION['usuarioID'],'agenda','adicionar')) {

	                        //* Retira a tag <!-- --> para o array
							$tag = array(
								//* Monta o o botão sem cometário
								'tag_a' => '',
								'tag_b' => ''
							);

	                    } else {
	                    	//* Carrega o html do botão para o array
							$tag = array(
								//* Monta o o botão comentado, ele não será exibido na página
								'tag_a' => '<!--',
								'tag_b' => '-->'
							);
	                    }
                        
                        //* Une o array paginaBase com o array personalizado 
	                    $paginaPersonalizada = array_merge($paginaBase, $tag);

		                //* Renderiza a view
			        	echo $principal->view('modulos/principal/view/agenda.html', $paginaPersonalizada);
			        } else {
				        switch (isset($url[2])?$url[2]:'') {
			        		case 'listar':
			        			$principal->listarAgenda();
			        			break;
			        		
			        		case 'salvar':
			        			$principal->salvarAgenda();
			        			break;

			        		case 'exibir':
			        			$principal->exibirAgenda($url[3]);
			        			break;
			                
			                case 'excluir':
			        			$principal->ExcluirAgenda($url[3]);
			        			break;
			                
			                case 'notificacao':
			        			$principal->notificacoes();
			        			break;

			        		default:
			        			echo $this->view('modulos/erro/erro404.html', $paginaBase);
			        			break;
			        	}
			        }           	        		
		        	break;
		        	
		        	default:
			        	if($principal->permissao($_SESSION['usuarioID'],'dashboard','view')) {

			        		//* Renderiza a view
			        	    echo $principal->view('modulos/principal/view/dashboard.html', $paginaBase);

						    //* Função pra imprimir grafico .
						    //* A Função grafico é ligada a tabela "tb_grafico" la contem 2 registros fixos, "Usuários" e "Agendamentos"
						    //* Os Outros registros são gravados por ano, sempre limpando o mes do ano anterior e contando os registros do ano atual.
						    $principal->graficoA();
						    $principal->graficoB();
						} else {

							//* Renderiza a view
			        	    echo $principal->view('modulos/principal/view/principal.html', $paginaBase);
						}
		        		break;
		        }        
				break;
            
            case 'cadastros':

            //* Função que protege uma página
		    $login->protegePagina();

            switch (isset($url[1])?$url[1]:'') {
				case 'usuario':		

				    //* Instância um objeto usuario	        	    
			        $usuario = new Usuario();

			        //* Se não for passado nenhum parametro ex. "salvar" ele renderiza a view com a listagem dos registros
			        if(!isset($url[2]) && $usuario->permissao($_SESSION['usuarioID'],'usuarios','view')) {
                       
                        //* Verifica se o usuário tem permissão para adicionar
			        	if($usuario->permissao($_SESSION['usuarioID'],'usuarios','adicionar')) {

	                        //* Retira a tag <!-- --> para o array
							$tag = array(
								//* Monta o o botão sem cometário
								'tag_a' => '',
								'tag_b' => ''
							);

	                    } else {
	                    	//* Carrega o html do botão para o array
							$tag = array(
								//* Monta o o botão comentado, ele não será exibido na página
								'tag_a' => '<!--',
								'tag_b' => '-->'
							);
	                    }
                        
                        //* Une o array paginaBase com o array personalizado 
	                    $paginaPersonalizada = array_merge($paginaBase, $tag);

			        	//* Renderiza a view
				        echo $usuario->view('modulos/cadastros/usuario/view/usuario.html', $paginaPersonalizada);
			        } else {	    
			        //* 
					    switch (isset($url[2])?$url[2]:'') {
					    	case 'listar':
					    		$usuario->listar();
					    		break;

					    	case 'salvar':
					    		$usuario->salvar();
					    		break;

					    	case 'exibir':
					    		$usuario->exibir($url[3]);
					    		break;

				            case 'excluir':
					    		$usuario->excluir($url[3]);
					    		break;

					    	case 'nivel':
					    		$usuario->nivel(isset($url[3])?$url[3]:'');
					    		break;

					    	default:
					    		echo $this->view('modulos/erro/erro404.html', $paginaBase);
					    		break;
					    }
					}
					break;

				default:
        			echo $this->view('modulos/erro/erro404.html', $paginaBase);
        			break;
			}
			break;
			
		    
		    case 'tela':
		        //* Função que protege uma página
			    $login->protegePagina();	    
                
                //* Instância um objeo Tela
		        $tela = new Tela();

		        //* Cria o array com os dados XML do sistema ODIN Londrina
	    	    $londrina = array('londrina' => $tela->listar('londrina'));

	    	    //* Cria o array com os dados XML do sistema ODIN Maringa
	    	    $maringa = array('maringa' => $tela->listar('maringa'));

	    	    //* Cria o array com os dados XML do sistema ODIN Cascavel
	    	    $cascavel = array('cascavel' => $tela->listar('cascavel'));

                //* Une os arrays
	    	    $paginaBase = array_merge($paginaBase, $londrina, $maringa, $cascavel);

		        if($tela->permissao($_SESSION['usuarioID'],'telas','view')) {
				    switch (isset($url[1])?$url[1]:'') {
				    	case 'londrina':
				    	    if($tela->permissao($_SESSION['usuarioID'],'londrina','view')) {
						    	//* Renderiza a view
					        	echo $tela->view('modulos/tela/view/telaLondrina.html', $paginaBase);
				            }
				    		break;

				    	case 'maringa':
					    	if($tela->permissao($_SESSION['usuarioID'],'maringa','view')) {
						    	//* Renderiza a view
					        	echo $tela->view('modulos/tela/view/telaMaringa.html', $paginaBase);
					        }
				    		break;

				    	case 'cascavel':
					    	if($tela->permissao($_SESSION['usuarioID'],'cascavel','view')) {
						    	//* Renderiza a view
					        	echo $tela->view('modulos/tela/view/telaCascavel.html', $paginaBase);
					        }

				    		break;	
				    	default:
				    		echo $this->view('modulos/erro/erro404.html', $paginaBase);
				    		break;
				    }
				} else {
					echo $this->view('modulos/erro/erro404.html', $paginaBase);
				}
				break;

		    case 'solicitacoes':
                //* Função que protege uma página
				$login->protegePagina();

				switch (isset($url[1])?$url[1]:'') {
				    case 'cirurgico':			        	    
				        //* 
				        $cirurgico = new Cirurgico();
				        //* 
				        if($cirurgico->permissao($_SESSION['usuarioID'],'cirurgico','view')) {
					        if(!isset($url[2])){

						    //* Verifica se o usuário tem permissão para adicionar
				        	if($cirurgico->permissao($_SESSION['usuarioID'],'cirurgico','adicionar')) {

		                        //* Retira a tag <!-- --> para o array
								$tag = array(
									//* Monta o o botão sem cometário
									'tag_a' => '',
									'tag_b' => ''
								);

		                    } else {
		                    	//* Carrega o html do botão para o array
								$tag = array(
									//* Monta o o botão comentado, ele não será exibido na página
									'tag_a' => '<!--',
									'tag_b' => '-->'
								);
		                    }
	                        
	                        //* Une o array paginaBase com o array personalizado 
		                    $paginaPersonalizada = array_merge($paginaBase, $tag);

							    //* Renderiza a view
						        echo $cirurgico->view('modulos/solicitacoes/cirurgico/view/cirurgico.html', $paginaPersonalizada);
					        } else {
						        //* 
							    switch (isset($url[2])?$url[2]:'') {
							    	case 'listar':
							    		$cirurgico->listar();
							    		break;

							    	case 'salvar':
							    		$cirurgico->salvar();
							    		break;

							    	case 'exibir':
							    		$cirurgico->exibir($url[3]);
							    		break;

						            case 'excluir':
							    		$cirurgico->excluir($url[3]);
							    		break;
					                
					                case 'uploadliberacao':
							    		$cirurgico->uploadliberacao();
							    		break;
					                
					                case 'uploadpedidomedico':
							    		$cirurgico->uploadPedidoMedico();
							    		break;
					                
					                case 'avisoexclusao':
							    		$cirurgico->avisoExclusao($url[3]);
							    		break;

							    	case 'recebido':
							    		$cirurgico->avisoLeitura($url[3],'Recebido');
							    		break;
					                
					                case 'confirmado':
							    		$cirurgico->avisoLeitura($url[3],'Confirmado');
							    		break;
							    	
							    	case 'alterado':
							    		$cirurgico->avisoLeitura($url[3],'Alterado');
							    		break;
							    		
					                case 'avisonovoagendamento':
							    		$cirurgico->avisoNovoAgendamento();
							    		break;		    		

							    	case 'checkado':
							    		$cirurgico->leitura($url[3]);
							    		break;
					                
					                case 'desmarcar':
							    		$cirurgico->desmarcar($url[3]);
							    		break;
					                
					                case 'email':
							    		$cirurgico->email();
							    		break;

							    	case 'permissao':
							    		$cirurgico->permissao('1','dashboard');
							    		break;

							    	default:
							    		echo $this->view('modulos/erro/erro404.html', $paginaBase);
							    		break;
							    }
							}
							break;
						} else {
							echo $this->view('modulos/erro/erro404.html', $paginaBase);
					}
					default:
			    		echo $this->view('modulos/erro/erro404.html', $paginaBase);
			    		break;

				    case 'instrumental':  
				        //* 
				        $instrumental = new Instrumental();
				        //* 
				        if($instrumental->permissao($_SESSION['usuarioID'],'instrumental','view')) {
					        if(!isset($url[2])){

					        	//* Verifica se o usuário tem permissão para adicionar
					        	if($instrumental->permissao($_SESSION['usuarioID'],'instrumental','adicionar')) {

			                        //* Retira a tag <!-- --> para o array
									$tag = array(
										//* Monta o o botão sem cometário
										'tag_a' => '',
										'tag_b' => ''
									);

			                    } else {
			                    	//* Carrega o html do botão para o array
									$tag = array(
										//* Monta o o botão comentado, ele não será exibido na página
										'tag_a' => '<!--',
										'tag_b' => '-->'
									);
			                    }
		                        
		                        //* Une o array paginaBase com o array personalizado 
			                    $paginaPersonalizada = array_merge($paginaBase, $tag);

							    //* Renderiza a view
						        echo $instrumental->view('modulos/solicitacoes/instrumental/view/instrumental.html', $paginaPersonalizada);
					        } else {
						        //* 
							    switch (isset($url[2])?$url[2]:'') {
							    	case 'listar':
							    		$instrumental->listar();
							    		break;

							    	case 'salvar':
							    		$instrumental->salvar();
							    		break;
					                
					                case 'atualizar':
							    		$instrumental->atualizar();
							    		break;

							    	case 'exibir':
							    		$instrumental->exibir($url[3]);
							    		break;

						            case 'excluir':
							    		$instrumental->excluir($url[3]);
							    		break;

							    	case 'avisonovoagendamento':
							    		$instrumental->avisoNovoAgendamento();
							    		break;
					                
							    	default:
							    		echo $this->view('modulos/erro/erro404.html', $paginaBase);
							    		break;
							    }
							}
							break;
						} else {
							echo $this->view('modulos/erro/erro404.html', $paginaBase);
					}
				}
                break;

            case 'parametrizacoes':
                //* Função que protege uma página
				$login->protegePagina();

				switch (isset($url[1])?$url[1]:'') {
				    case 'permissao':			        	    
				        //* 
				        $permissao = new Permissao();
				        //* 
				        if($permissao->permissao($_SESSION['usuarioID'],'permissao','view')) {
					        if(!isset($url[2])){

					        	//* Verifica se o usuário tem permissão para adicionar
					        	if($permissao->permissao($_SESSION['usuarioID'],'permissao','adicionar')) {

			                        //* Retira a tag <!-- --> para o array
									$tag = array(
										//* Monta o o botão sem cometário
										'tag_a' => '',
										'tag_b' => ''
									);

			                    } else {
			                    	//* Carrega o html do botão para o array
									$tag = array(
										//* Monta o o botão comentado, ele não será exibido na página
										'tag_a' => '<!--',
										'tag_b' => '-->'
									);
			                    }
		                        
		                        //* Une o array paginaBase com o array personalizado 
			                    $paginaPersonalizada = array_merge($paginaBase, $tag);

							    //* Renderiza a view
						        echo $permissao->view('modulos/parametrizacoes/permissao/view/permissao.html', $paginaPersonalizada);
					        } else {
						        //* 
							    switch (isset($url[2])?$url[2]:'') {
							    	case 'listar':
							    		$permissao->listar();
							    		break;

							    	case 'salvar':
							    		$permissao->salvar();
							    		break;

							    	case 'exibir':
							    		$permissao->exibir($url[3]);
							    		break;

						            case 'excluir':
							    		$permissao->excluir($url[3]);
							    		break;
							    		
							    	default:
							    		echo $this->view('modulos/erro/erro404.html', $paginaBase);
							    		break;
							    }
							}
							break;
						} else {
							echo $this->view('modulos/erro/erro404.html', $paginaBase);
					}
					default:
			    		echo $this->view('modulos/erro/erro404.html', $paginaBase);
			    		break;
				}
                break;
            
            case 'configuracao':
                 //* Função que protege uma página
				$login->protegePagina();
		        //*
				if($configuracao->permissao($_SESSION['usuarioID'],'configuracao','view')) {	
			        if(!isset($url[1])){
			            //* Une o array paginaBase com o array personalizado 
	                    $paginaPersonalizada = array_merge($paginaBase, $configuracao->exibir());		
					    //* Renderiza a view
				        echo $this->view('modulos/configuracao/view/configuracao.html', $paginaPersonalizada);			    
				    } else {
                        //* 
					     switch (isset($url[1])?$url[1]:'') {
					    	case 'editar':
					    		$configuracao->editar();
					    		break;

					    	case 'exibir':
					    		$configuracao->exibir();
					    		break;

					    	default:
					    		echo $this->view('modulos/erro/erro404.html', $paginaBase);
					    		break;
					    }
				    }
				} else {
					echo $this->view('modulos/erro/erro404.html', $paginaBase);
				}      	
            	break;
				case 'incidente':
					//* Função que protege uma página
				   $login->protegePagina();
				   //*
				   if($configuracao->permissao($_SESSION['usuarioID'],'configuracao','view')) {	
					   if(!isset($url[1])){
						   //* Une o array paginaBase com o array personalizado 
						   $paginaPersonalizada = array_merge($paginaBase, $configuracao->exibir());		
						   //* Renderiza a view
						   echo $this->view('modulos/incidente/view/incidente.html', $paginaPersonalizada);			    
					   } else {
						   //* 
							switch (isset($url[1])?$url[1]:'') {
							   case 'editar':
								   $configuracao->editar();
								   break;
   
							   case 'exibir':
								   $configuracao->exibir();
								   break;
   
							   default:
								   echo $this->view('modulos/erro/erro404.html', $paginaBase);
								   break;
						   }
					   }
				   } else {
					   echo $this->view('modulos/erro/erro404.html', $paginaBase);
				   }      	
				   break;

			case 'logout':
			    $login->logout();
			    break;    

		    case 'senha':
		        //* Array com a data de hoje
		        $data = array('data' => date('d/m/Y'));
		        //* Une os arrays
		        $paginaBase = array_merge($paginaBase, $data);
			    //* Renderiza a view
		        echo $this->view('modulos/login/view/senha.html', $paginaBase);
			    break;

		    case 'recuperar':
			    $login->senha();
			    break;

			case 'debug':
				# code...
				break;

			default:
				echo $this->view('modulos/erro/erro404.html', $paginaBase);
				break;
		}
	}
}