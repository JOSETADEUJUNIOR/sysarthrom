<?php
/**
 ** Data : 02/01/2022
 ** Autor:Whilton Reis
 ** Polaris Tecnologia
 **/

class Permissao extends Polar { 
    /* 
	 * Metodo Listar usa o framework Polar --> ServeSide
	 * Metodo que faz a ligação da datatable com o banco de dados
	 * parametros: tabela, indice, colunas, editar, excluir, visualizar
	 */
	public function listar() {   
		//* Nome da tabela
		$tabela = 'tb_nivel';

		//* Join utilizado na consulta
		$join   = false;

		//* Condição WHERE para filtrar os resultados
		$where = false;

		//* Nome da coluna que serve como índice na tabela
		$indice = 'td_id_nivel';

		//* Array com as colunas a serem listadas na tabela
		$colunas= array( 
			'td_id_nivel',
			'td_nome_nivel',
			'td_data_criacao',
			'td_ativo'			
		);	

		// Monta o conjunto de botões dependendo das permissões do usuário
		if($this->permissao($_SESSION['usuarioID'],'permissao','view')) {  
		     //* Botões de visualização
	        $view = array(
	            array(
	                'visualizar()',                //* Função associada ao botão de visualização
	                'btn btn-info btn-xs m-b-5',   //* Classe CSS do botão
	                '<i class="fa fa-search"></i>' //* Ícone do botão de visualização
	            )
	        ); 

            // Botões de edições
			if($this->permissao($_SESSION['usuarioID'],'permissao','edit')) {
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
            
            // Botões de exclusão
			if($this->permissao($_SESSION['usuarioID'],'permissao','delete')) {
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
            
            // Combina todos os botões em um único array
			$botao = array_merge($view, $editar, $excluir);

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

	public function salvar() {   
	    if($this->permissao($_SESSION['usuarioID'],'permissao','edit')) {
	        //* Estabelece a conexão com o banco de dados
	        $this->connect(); 

	        // Recebe o array dos campos
	        $data = $_POST['data'];

	        // Monta array dos dados
	        $dados = array(
	            'td_nome_nivel' => $this->variavel($data[0], true),
	            'td_ativo'      => 'Ativo'
	        );     

	        if(!empty($_POST['id'])) {
	            //* Se um ID foi fornecido, seleciona o registro correspondente na tabela 'tb_nivel'
	            $sql = $this->update('tb_nivel',$dados,'td_id_nivel = '.$_POST['id'].'');

	            // Obtém o ID do nível existente
	            $nivel_id = $_POST['id']; 

	        } elseif($this->permissao($_SESSION['usuarioID'],'permissao','adicionar')) {
	            //* Caso contrário, seleciona todos os registros na tabela 'tb_nivel'
	            $sql = $this->insert('tb_nivel',$dados);

	            // Obtém o ID do novo nível inserido
	            $nivel_id = $this->directResult()[0];
	        }

	        // Array para armazenar as permissões
			$paginas = array(
			    array(
			        'td_id_pagina'    => 1, 
			        'td_adicionar'    => isset($_POST['adicionar_dashboard']),
			        'td_view'         => isset($_POST['visualizar_dashboard']), 
			        'td_edit'         => isset($_POST['editar_dashboard']), 
			        'td_delete'       => isset($_POST['excluir_dashboard']), 
			        'td_registros'    => false, 
			        'td_status'       => false, 
			        'td_email'        => false,
			        'td_nivel'        => false
			    ),

			    (isset($_POST['visualizar_dashboard']) || isset($_POST['visualizar_agenda'])) ? 
			        array(
			            'td_id_pagina'    => 2,
			            'td_adicionar'    => 1,
			            'td_view'         => 1,
			            'td_edit'         => 1,
			            'td_delete'       => 1,
			            'td_registros'    => 1,
			            'td_status'       => 1,
			            'td_email'        => 1,
			            'td_nivel'        => false
			        ) :  array(
			            'td_id_pagina'    => 2,
			            'td_adicionar'    => 0,
			            'td_view'         => 0,
			            'td_edit'         => 0,
			            'td_delete'       => 0,
			            'td_registros'    => 0,
			            'td_status'       => 0,
			            'td_email'        => 0,
			            'td_nivel'        => false
			        )
			    ,

			    array(
			        'td_id_pagina'    => 3,
			        'td_adicionar'    => isset($_POST['adicionar_cirurgico']),
			        'td_view'         => isset($_POST['visualizar_cirurgico']),
			        'td_edit'         => isset($_POST['editar_cirurgico']),
			        'td_delete'       => isset($_POST['excluir_cirurgico']),
			        'td_registros'    => isset($_POST['registros_cirurgico']),
			        'td_status'       => isset($_POST['status_cirurgico']),
			        'td_email'        => isset($_POST['email_cirurgico']),
			        'td_nivel'        => false
			    ),

			    (isset($_POST['visualizar_maringa']) || isset($_POST['visualizar_londrina']) || isset($_POST['visualizar_cascavel'])) ? 
			        array(
			            'td_id_pagina'    => 4,
			            'td_adicionar'    => 1,
			            'td_view'         => 1,
			            'td_edit'         => 1,
			            'td_delete'       => 1,
			            'td_registros'    => 1,
			            'td_status'       => 1,
			            'td_email'        => 1,
			            'td_nivel'        => false
			        ) : array(
			            'td_id_pagina'    => 4,
			            'td_adicionar'    => 0,
			            'td_view'         => 0,
			            'td_edit'         => 0,
			            'td_delete'       => 0,
			            'td_registros'    => 0,
			            'td_status'       => 0,
			            'td_email'        => 0,
			            'td_nivel'        => false
			        )
			    ,

			    (isset($_POST['visualizar_cirurgico']) || isset($_POST['visualizar_instrumental'])) ? 
			        array(
			            'td_id_pagina'    => 5,
			            'td_adicionar'    => 1,
			            'td_view'         => 1,
			            'td_edit'         => 1,
			            'td_delete'       => 1,
			            'td_registros'    => 1,
			            'td_status'       => 1,
			            'td_email'        => 1,
			            'td_nivel'        => false
			        ) : array(
			            'td_id_pagina'    => 5,
			            'td_adicionar'    => 0,
			            'td_view'         => 0,
			            'td_edit'         => 0,
			            'td_delete'       => 0,
			            'td_registros'    => 0,
			            'td_status'       => 0,
			            'td_email'        => 0,
			            'td_nivel'        => false
			        )
			    ,

			    array(
			        'td_id_pagina'    => 6,
			        'td_adicionar'    => isset($_POST['adicionar_usuarios']),
			        'td_view'         => isset($_POST['visualizar_usuarios']),
			        'td_edit'         => isset($_POST['editar_usuarios']),
			        'td_delete'       => isset($_POST['excluir_usuarios']),
			        'td_registros'    => isset($_POST['registros_usuarios']),
			        'td_status'       => false,
			        'td_email'        => false,
			        'td_nivel'        => isset($_POST['nivel_usuarios']),
			    ),

			    array(
			        'td_id_pagina'    => 7,
			        'td_adicionar'    => isset($_POST['adicionar_agenda']),
			        'td_view'         => isset($_POST['visualizar_agenda']),
			        'td_edit'         => isset($_POST['editar_agenda']),
			        'td_delete'       => isset($_POST['excluir_agenda']),
			        'td_registros'    => isset($_POST['registros_agenda']),
			        'td_status'       => false,
			        'td_email'        => false,
			        'td_nivel'        => false
			    ),

			    array(
			        'td_id_pagina'    => 8,
			        'td_adicionar'    => isset($_POST['adicionar_instrumental']),
			        'td_view'         => isset($_POST['visualizar_instrumental']),
			        'td_edit'         => isset($_POST['editar_instrumental']),
			        'td_delete'       => isset($_POST['excluir_instrumental']),
			        'td_registros'    => isset($_POST['registros_instrumental']),
			        'td_status'       => isset($_POST['status_instrumental']),
			        'td_email'        => isset($_POST['email_instrumental']),
			        'td_nivel'        => false
			    ),

			    (isset($_POST['visualizar_usuarios']) ? 
			        array(
			            'td_id_pagina'    => 9,
			            'td_adicionar'    => 1,
			            'td_view'         => 1,
			            'td_edit'         => 1,
			            'td_delete'       => 1,
			            'td_registros'    => 1,
			            'td_status'       => 1,
			            'td_email'        => 1,
			            'td_nivel'        => false
			        ) :  array(
			            'td_id_pagina'    => 9,
			            'td_adicionar'    => 0,
			            'td_view'         => 0,
			            'td_edit'         => 0,
			            'td_delete'       => 0,
			            'td_registros'    => 0,
			            'td_status'       => 0,
			            'td_email'        => 0,
			            'td_nivel'        => false
			        )
			    ),

			    array(
			        'td_id_pagina'    => 10,
			        'td_adicionar'    => false,
			        'td_view'         => isset($_POST['visualizar_maringa']),
			        'td_edit'         => false,
			        'td_delete'       => false,
			        'td_registros'    => false,
			        'td_status'       => false,
			        'td_email'        => false,
			        'td_nivel'        => false
			    ),

			    array(
			        'td_id_pagina'    => 11,
			        'td_adicionar'    => false,
			        'td_view'         => isset($_POST['visualizar_londrina']),
			        'td_edit'         => false,
			        'td_delete'       => false,
			        'td_registros'    => false,
			        'td_status'       => false,
			        'td_email'        => false,
			        'td_nivel'        => false
			    ),

			    (isset($_POST['visualizar_permissao']) ? 
			        array(
			            'td_id_pagina'    => 12,
			            'td_adicionar'    => 1,
			            'td_view'         => 1,
			            'td_edit'         => 1,
			            'td_delete'       => 1,
			            'td_registros'    => 1,
			            'td_status'       => 1,
			            'td_email'        => 1,
			            'td_nivel'        => false
			        ) :  array(
			            'td_id_pagina'    => 12,
			            'td_adicionar'    => 0,
			            'td_view'         => 0,
			            'td_edit'         => 0,
			            'td_delete'       => 0,
			            'td_registros'    => 0,
			            'td_status'       => 0,
			            'td_email'        => 0,
			            'td_nivel'        => false
			        )
			    ),

			    array(
			        'td_id_pagina'    => 13,
			        'td_adicionar'    => isset($_POST['adicionar_permissao']),
			        'td_view'         => isset($_POST['visualizar_permissao']),
			        'td_edit'         => isset($_POST['editar_permissao']),
			        'td_delete'       => isset($_POST['excluir_permissao']),
			        'td_registros'    => false,
			        'td_status'       => false,
			        'td_email'        => false,
			        'td_nivel'        => false
			    ),

			    array(
			        'td_id_pagina'    => 14,
			        'td_adicionar'    => false,
			        'td_view'         => isset($_POST['visualizar_cascavel']),
			        'td_edit'         => false,
			        'td_delete'       => false,
			        'td_registros'    => false,
			        'td_status'       => false,
			        'td_email'        => false,
			        'td_nivel'        => false
			    ),

			    array(
			        'td_id_pagina'    => 15,
			        'td_adicionar'    => false,
			        'td_view'         => isset($_POST['visualizar_configuracao']),
			        'td_edit'         => isset($_POST['editar_configuracao']),
			        'td_delete'       => false,
			        'td_registros'    => false,
			        'td_status'       => false,
			        'td_email'        => false,
			        'td_nivel'        => false
			    )
			);


	        // Limpa todas as permissões existentes para o nível atual
	        $this->delete('tb_permissao', 'td_nivel_id=' . $nivel_id);

	        // Insere as permissões para o nível atual
	        for ($i = 0; $i < count($paginas); $i++) {
			    $pagina = $paginas[$i];
			    $sql = $this->insert('tb_permissao', array(
			        'td_pagina_id'  => $pagina['td_id_pagina'],
			        'td_nivel_id'   => $nivel_id,
			        'td_adicionar'  => $pagina['td_adicionar'],
			        'td_view'       => $pagina['td_view'],
			        'td_edit'       => $pagina['td_edit'],
			        'td_delete'     => $pagina['td_delete'],
			        'td_registros'  => $pagina['td_registros'],
			        'td_status'     => $pagina['td_status'],
			        'td_email'      => $pagina['td_email'],
			        'td_nivel'      => $pagina['td_nivel'],
			    ));
			}

	        // Verifica se a requisição teve sucesso.
	        if($sql) {
	            print_r('sucesso');
	        } else {
	            print_r('erro');
	        }

	        //* Fecha a conexão com o banco de dados
	        $this->disconnect();
	    }
	}


	public function excluir($id) {
		//* Verifica se o usuário tem permissão para excluir níveis
		if($this->permissao($_SESSION['usuarioID'],'permissao','delete')) {
			//* Estabelece a conexão com o banco de dados
			$this->connect();

			//* Verifica se a exclusão do registro foi bem-sucedida
			if($this->delete('tb_permissao','td_nivel_id='.$id.'')) {
				//* Exclui o nivel após excluir as permissões
				$this->delete('tb_nivel','td_id_nivel='.$id.'');
	            print_r('sucesso');
			} else {
	            print_r('erro');
			}

			//* Fecha a conexão com o banco de dados
			$this->disconnect();
		}
	}

	public function exibir($id) {
		//* Verifica se o usuário tem permissão para exibir páginas
		if ($this->permissao($_SESSION['usuarioID'], 'permissao', 'view')) {
		    
		    //* Estabelece a conexão com o banco de dados
		    $this->connect();

		    //* Constrói a consulta SQL
		    $sql = $this->sql("
		    	    SELECT * 
		            FROM tb_nivel a
		            JOIN tb_permissao b ON a.td_id_nivel = b.td_nivel_id
		            JOIN tb_paginas c ON c.td_id_pagina = b.td_pagina_id
		            WHERE b.td_nivel_id = " . $id
		    );

		    //* Executa a consulta SQL usando a função sql da classe Bd
		    if ($sql) {
		        //* Retorna os resultados da consulta como JSON
		        echo json_encode($this->getResult());
		    } else {
		        //* Se a consulta falhar, retorna uma mensagem de erro
		        echo json_encode(array("error" => "Erro ao executar consulta SQL"));
		    }

		    //* Fecha a conexão com o banco de dados
		    $this->disconnect();
		}
	}

}
	