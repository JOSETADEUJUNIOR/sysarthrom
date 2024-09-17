<?php
/**
 ** Data : 02/01/2022
 ** Autor:Whilton Reis
 ** Polaris Tecnologia
 **/

class Usuario extends Polar {
	/* 
	 * Metodo Listar usa o framework Polar --> ServeSide
	 * Metodo que faz a ligação da datatable com o banco de dados
	 * parametros: tabela, indice, colunas, editar, excluir, visualizar
	 */
	public function listar() {   
	    //* Nome da tabela
	    $tabela = 'tb_usuarios';

	    //* Join utilizado na consulta
	    $join   = 'tb_nivel ON td_nivel = td_id_nivel';

	    //* Condição WHERE para filtrar os resultados
	    if($this->permissao($_SESSION['usuarioID'],'usuarios','reg')){
	        //* Se o usuário tem permissão para visualizar todos os registros, a condição WHERE é omitida
	        $where = false;
	    } else {
	        //* Caso contrário, apenas o registro do próprio usuário é exibido
	        $where = 'td_id =' . $_SESSION['usuarioID'];
	    }

	    //* Nome da coluna que serve como índice na tabela
	    $indice = 'td_id';

	    //* Array com as colunas a serem listadas na tabela
	    $colunas = array( 
	        'td_id',
	        'td_nome', 
	        'td_email', 
	        'td_nome_nivel',
	        'td_data'            
	    );          

	    //* Monta o conjunto de botões dependendo das permissões do usuário
	    if($this->permissao($_SESSION['usuarioID'],'usuarios','view')) {
	        //* Botões de visualização
	        $view = array(
	            array(
	                'visualizar()',                //* Função associada ao botão de visualização
	                'btn btn-info btn-xs m-b-5',   //* Classe CSS do botão
	                '<i class="fa fa-search"></i>' //* Ícone do botão de visualização
	            )
	        );     
	            
	        //* Botões de edição
	        if($this->permissao($_SESSION['usuarioID'],'usuarios','edit')) {
	            $editar = array(                    
	                array(
	                    'editar()',                       
	                    'btn btn-primary btn-xs m-b-5', //* Classe CSS do botão de edição
	                    '<i class="fa fa-pencil"></i>'  //* Ícone do botão de edição
	                )
	            );     
	        } else {
	            $editar = array();
	        }
	            
	        //* Botões de exclusão
	        if($this->permissao($_SESSION['usuarioID'],'usuarios','delete')) {
	            $excluir = array(                    
	                array(
	                    'excluir()',
	                    'btn btn-danger btn-xs m-b-5', //* Classe CSS do botão de exclusão
	                    '<i class="fa fa-remove"></i>' //* Ícone do botão de exclusão
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

	public function nivel($id)	{
		//* Verifica se o usuário tem permissão para editar usuários
	    if ($this->permissao($_SESSION['usuarioID'],'usuarios','nivel')) {
		    //* Estabelece a conexão com o banco de dados
		    $this->connect();

		    if($id) {
		        //* Se um ID foi fornecido, seleciona o registro correspondente na tabela 'tb_nivel'
		        $this->select('tb_nivel', '*', '', '', 'td_id_nivel='.$id.'');
		    } else {
		        //* Caso contrário, seleciona todos os registros na tabela 'tb_nivel'
		        $this->select('tb_nivel', '*', '', '', 'td_id_nivel ASC');
		    }

		    //* Retorna os resultados da consulta como JSON
		    echo json_encode($this->getResult());

		    //* Fecha a conexão com o banco de dados
		    $this->disconnect();
		} else {
			//* Estabelece a conexão com o banco de dados
		    $this->connect();

		    if($id) {
		        //* Se um ID foi fornecido, seleciona o registro correspondente na tabela 'tb_nivel'
		        $this->select('tb_nivel', '*', '', 'td_id_nivel='.$id.'', 'td_id_nivel ASC');
		    } else {
		        //* Caso contrário, seleciona todos os registros na tabela 'tb_nivel'
		        $this->select('tb_nivel', '*', '', '', 'td_id_nivel ASC');
		    }

		    //* Retorna os resultados da consulta como JSON
		    echo json_encode($this->getResult());

		    //* Fecha a conexão com o banco de dados
		    $this->disconnect();
		}
	}

	public function exibir($id)	{   
	    //* Verifica se o usuário tem permissão para visualizar usuários
	    if($this->permissao($_SESSION['usuarioID'],'usuarios','view')) {

	        //* Estabelece a conexão com o banco de dados
	        $this->connect();

	        //* Seleciona o registro correspondente ao ID fornecido na tabela 'tb_usuarios'
	        $this->select('tb_usuarios', '*', '', 'td_id='.$id.'', 'td_id ASC');

	        //* Retorna os resultados da consulta como JSON
	        echo json_encode($this->getResult());

	        //* Fecha a conexão com o banco de dados
	        $this->disconnect();
	    }
	}

	public function salvar() {   
	    //* Verifica se o usuário tem permissão para editar usuários
	    if ($this->permissao($_SESSION['usuarioID'],'usuarios','edit')) {

	        //* Estabelece a conexão com o banco de dados
	        $this->connect();        

	        //* Recebe os dados do formulário via POST
	        $data = $_POST['data'];

	        //* Monta um array com os dados do usuário
	        if($data[3]) {
	            //* Se a senha foi fornecida, inclui a senha no array de dados
	            $dados = array(
	                'td_nome'  => ucwords(strtolower($this->variavel($data[0], true))),
	                'td_email' => strtolower($this->variavel($data[1], true)),
	                'td_login' => $this->variavel($data[2], true),
	                'td_senha' => $this->variavel(sha1($data[3]), true),
	                'td_nivel' => $this->variavel($data[4], true)
	            );
	        } else  {
	            //* Se a senha não foi fornecida, cria o array de dados sem a senha
	            $dados = array(
	                'td_nome'  => ucwords(strtolower($this->variavel($data[0], true))),
	                'td_email' => strtolower($this->variavel($data[1], true)),
	                'td_login' => $this->variavel($data[2], true),
	                'td_nivel' => $this->variavel($data[4], true)
	            );
	        }
	        
	        //* Verifica se um ID foi fornecido no formulário
	        if(!empty($_POST['id'])) {
	            //* Se um ID foi fornecido, atualiza o registro na tabela 'tb_usuarios'
	            $sql = $this->update('tb_usuarios',$dados,'td_id = '.$_POST['id'].'');
	            
	        } elseif($this->permissao($_SESSION['usuarioID'],'usuarios','adicionar')) {
	            //* Se nenhum ID foi fornecido, insere um novo registro na tabela 'tb_usuarios'
	            $sql = $this->insert('tb_usuarios',$dados);
	        }

	        //* Verifica se a operação foi bem-sucedida e imprime 'sucesso' ou 'erro' conforme necessário
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
	    //* Verifica se o usuário tem permissão para excluir usuários
	    if ($this->permissao($_SESSION['usuarioID'],'usuarios','delete')) {
	        //* Estabelece a conexão com o banco de dados
	        $this->connect();

	        //* Verifica se a exclusão do registro foi bem-sucedida e imprime 'sucesso' ou 'erro' conforme necessário
	        if($this->delete('tb_usuarios','td_id='.$id.'')) {
	            print_r('sucesso');
	        } else {
	            print_r('erro');
	        }

	        //* Fecha a conexão com o banco de dados
	        $this->disconnect();
	    }
	}
}
	