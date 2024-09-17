<?php
/**
 ** Data: 02/01/2018
 ** Autor: Whilton Reis
 ** Polaris Tecnologia
 **/

abstract class Bd {

    //* Variáveis extras que são requeridas por outra função, como variável booleana
    private $con = false;      //* Verifica se a conexão está ativa
    private $mysqli = "";      //* Este será o nosso objeto mysqli
    private $result = array(); //* Qualquer resultado de uma consulta será armazenado aqui
    private $myQuery = "";     //* Usado para o processo de depuração com retorno SQL
    private $numResults = "";  //* Usado para retornar o número de linhas

    //* Função para fazer conexão com o banco de dados
    protected function connect() {
        if (!$this->con) {   //* mysql_connect () com variáveis definidas no início da classe de banco de dados
            $this->mysqli = new mysqli(db_host, db_user, db_pass, db_nome);

            /* Alterar o conjunto de caracteres para utf8 */
            if (!$this->mysqli->set_charset("utf8")) {
                printf("Erro ao carregar o conjunto de caracteres utf8: %s\n", $this->mysqli->error);
                exit();
            }

            if ($this->mysqli->connect_errno > 0) {
                array_push($this->result, $this->mysqli->connect_error);
                return false; //* Problema ao selecionar o retorno do banco de dados FALSO
            } else {
                $this->con = true;
                return true; //* A conexão foi feita retorna VERDADEIRA
            }
        } else {
            return true; //* A conexão já foi feita retorna VERDADEIRA
        }
    }

    //* Função para se desconectar do banco de dados
    protected function disconnect() {
        //* Se houver uma conexão com o banco de dados
        if ($this->con) {

            //* Encontramos uma conexão, tente fechá-la
            if ($this->mysqli->close()) {

                //* Nós fechamos com sucesso a conexão, configuramos a variável de conexão como falsa
                $this->con = false;

                //* Retornar verdadeiro tjat nós fechamos a conexão
                return true;
            } else {

                //* Não conseguimos fechar a conexão, retornar falso
                return false;
            }
        }
    }

    //* Função genérica sql
    protected function sql($sql) {
        $query = $this->mysqli->query($sql);
        $this->myQuery = $sql; //* Pass back the SQL
        if ($query) {
            //* Se a consulta retornar >= 1, atribua o número de linhas a numResults
            $this->numResults = $query->num_rows;

            //* Percorre os resultados da consulta pelo número de linhas retornadas
            for ($i = 0; $i < $this->numResults; $i++) {
                $r = $query->fetch_array();
                $key = array_keys($r);
                for ($x = 0; $x < count($key); $x++) {

                    //* Sanitiza as chaves para permitir apenas valores alfabéticos
                    if (!is_int($key[$x])) {
                        if ($query->num_rows >= 1) {
                            $this->result[$i][$key[$x]] = $r[$key[$x]];
                        } else {
                            $this->result = null;
                        }
                    }
                }
            }
            return true; //* A consulta foi bem-sucedida
        } else {
            array_push($this->result, $this->mysqli->error);
            return false; //* Nenhuma linha foi retornada
        }
    }

    //* Função para selecionar dados da tabela
    protected function select($tabela, $rows = '*', $join = null, $where = null, $order = null, $limit = null) {

        //* Cria a consulta a partir das variáveis passadas para a função
        $q = 'SELECT ' . $rows . ' FROM ' . $tabela;
        if ($join != null) {
            $q .= ' JOIN ' . $join;
        }
        if ($where != null) {
            $q .= ' WHERE ' . $where;
        }
        if ($order != null) {
            $q .= ' ORDER BY ' . $order;
        }
        if ($limit != null) {
            $q .= ' LIMIT ' . $limit;
        }
        $this->myQuery = $q; //* Passa o SQL de volta

        //* Verifica se a tabela existe
        if ($this->tabelaExiste($tabela)) {

            //* A tabela existe, execute a consulta
            $query = $this->mysqli->query($q);
            if ($query) {

                //* Se a consulta retornar >= 1, atribua o número de linhas a numResults
                $this->numResults = $query->num_rows;
                
                //* Cria o array para retorno
                $this->result = array();

                //* Percorre os resultados da consulta pelo número de linhas retornadas
                for ($i = 0; $i < $this->numResults; $i++) {
                    $r = $query->fetch_array();
                    $key = array_keys($r);
                    for ($x = 0; $x < count($key); $x++) {
                        
                        //* Sanitiza as chaves para permitir apenas valores alfabéticos
                        if (!is_int($key[$x])) {
                            if ($query->num_rows >= 1) {
                                $this->result[$i][$key[$x]] = $r[$key[$x]];
                            } else {
                                $this->result[$i][$key[$x]] = null;
                            }
                        }
                    }
                }
                return true; //* A consulta foi bem-sucedida
            } else {
                array_push($this->result, $this->mysqli->error);
                return false; //* Nenhuma linha foi retornada
            }
        } else {
            return false; //* A tabela não existe
        }
    }

    //* Função para inserir no banco de dados
    protected function insert($tabela, $params = array()) {

        //* Verifique se a tabela existe
        if ($this->tabelaExiste($tabela)) {
            $sql = 'INSERT INTO `' . $tabela . '` (`' . implode('`, `', array_keys($params)) . '`) VALUES ("' . implode('", "', $params) . '")';
            $this->myQuery = $sql; //* Passa o SQL de volta

            //* Faça a consulta para inserir no banco de dados
            if ($ins = $this->mysqli->query($sql)) {
                array_push($this->result, $this->mysqli->insert_id);
                return true; //* Os dados foram inseridos
            } else {
                array_push($this->result, $this->mysqli->error);
                return false; //* Os dados não foram inseridos
            }
        } else {
            return false; //* A tabela não existe
        }
    }

    //* Função para deletar tabela ou linha(s) do banco de dados
    protected function delete($tabela, $where = null) {

        //* Verifica se a tabela existe
        if ($this->tabelaExiste($tabela)) {

            //* A tabela existe, verifique se estamos deletando linhas ou tabela
            if ($where == null) {
                $delete = 'DROP TABLE ' . $tabela; //* Cria a consulta para excluir a tabela
            } else {
                $delete = 'DELETE FROM ' . $tabela . ' WHERE ' . $where; //* Cria a consulta para excluir linhas
            }

            //* Envie a consulta para o banco de dados
            if ($del = $this->mysqli->query($delete)) {
                array_push($this->result, $this->mysqli->affected_rows);
                $this->myQuery = $delete; //* Passa o SQL de volta
                return true; //* A consulta foi executada corretamente
            } else {
                array_push($this->result, $this->mysqli->error);
                return false; //* A consulta não foi executada corretamente
            }
        } else {
            return false; //* A tabela não existe
        }
    }

    //* Função para atualizar linha no banco de dados
    protected function update($tabela, $where, $params = array()) {

    //* Verifica se a tabela existe
    if ($this->tabelaExiste($tabela)) {

        //* Cria Array para manter todas as colunas para atualizar
        $args = array();
        foreach ($params as $field => $value) {

            //* Separa cada coluna com seu valor correspondente
            $args[] = $field . '="' . $value . '"';
        }

        //* Cria a consulta
        $sql = 'UPDATE ' . $tabela . ' SET ' . implode(',', $args) . ' WHERE ' . $where;
        
        //* Faça a consulta para o banco de dados
        $this->myQuery = $sql; //* Passa o SQL de volta
        if ($query = $this->mysqli->query($sql)) {
            array_push($this->result, $this->mysqli->affected_rows);
            return true; //* A atualização foi bem-sucedida
        } else {
            array_push($this->result, $this->mysqli->error);
            return false; //* A atualização não foi bem-sucedida
        }
    } else {
        return false; //* A tabela não existe
    }
}


    //* Função privada para verificar se a tabela existe para uso com consultas
    private function tabelaExiste($tabela) {
        $tabelasInDb = $this->mysqli->query('SHOW TABLES FROM ' . db_nome . ' LIKE "' . $tabela . '"');
        if ($tabelasInDb) {
            if ($tabelasInDb->num_rows == 1) {
                return true; //* A tabela existe
            } else {
                array_push($this->result, $tabela . " não existe neste banco de dados");
                return false; //* A tabela não existe
            }
        }
    }

    //* Função protegida para retornar os dados ao usuário
    protected function directResult() {
        $val = $this->result;
        return $val;
    }

    //* Função protegida para retornar os dados ao usuário em array
    protected function getResult() {
        $val = $this->result;
        $this->result = array();
        return $val;
    }

    //* Transmite o SQL para a depuração
    protected function getSql() {
        $val = $this->myQuery;
        $this->myQuery = array();
        return $val;
    }

    //* Passa o número de linhas de volta
    protected function numRows() {
        $val = $this->numResults;
        $this->numResults = array();
        return $val;
    }

    //* Escapa sua string
    protected function escapeString($data) {
        return $this->mysqli->real_escape_string($data);
    }
}
