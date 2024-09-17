<?php
/**
 ** Data : 02/01/2022
 ** Autor:Whilton Reis
 ** Polaris Tecnologia
 **/

class Configuracao extends Polar {
    /* 
     * Metodo recebe o nome da constante e o novo valor passado.
     * Lê o conteúdo do arquivo especificado e substitui a definição
     * da constante pelo novo valor usando expressões regulares 
     */
    public function editar() {   
        //* Verifica se o usuário tem permissão para editar as configurações
        if ($this->permissao($_SESSION['usuarioID'],'configuracao','edit')) {                
            //* Recebe o array dos campos enviados via POST
            $data = $_POST['data'];
            
            //* Caminho do arquivo PHP
            $arquivo = 'config.php';

            //* Carrega o conteúdo do arquivo
            $conteudo = file_get_contents($arquivo);

            //* Expressão regular para encontrar a definição da constante
            $expressao = "/define\(\s*['\"]" .  $data[0] . "['\"]\s*,\s*['\"].*?['\"]\s*\)\s*;/";

            //* Novo conteúdo para a constante
            $novaConstante = "define('" .  $data[0] . "', '" .  $data[1] . "');";

            //* Substitui o valor antigo pelo novo
            $novoConteudo = preg_replace($expressao, $novaConstante, $conteudo);

            //* Verifica se a substituição foi bem-sucedida
            if ($novoConteudo === null) {
                print_r('erro');
            } else {
                //* Salva o conteúdo modificado de volta no arquivo
                file_put_contents($arquivo, $novoConteudo);
                print_r('sucesso');
            }
        }
    }

    public function exibir() {
        //* Verifica se o usuário tem permissão para editar as configurações
        if ($this->permissao($_SESSION['usuarioID'],'configuracao','view')) {
            //* Recupera o conteúdo do arquivo com as constantes
            $arquivo = file_get_contents('config.php');

            //* Regex para encontrar definições de constantes
            $expressao = '/define\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"]([^\'"]+)[\'"]\s*\)\s*;/';

            //* Encontra todas as correspondências no conteúdo do arquivo
            preg_match_all($expressao, $arquivo, $partes, PREG_SET_ORDER);

            //* Itera sobre as correspondências e armazena o nome e o valor das constantes no array
            foreach ($partes as $parte) {
                $nome  = $parte[1];
                $valor = $parte[2];
                $constantes[$nome] = $valor;
            }

            //* Retorna o array com nome e valor das constantes
            return $constantes;
        }
    }

    public function base() {
        //* Defina um array com as configurações permitidas
        $configuracoesPermitidas = ['tituloSistema'];

        //* Recupera o conteúdo do arquivo com as constantes
        $arquivo = file_get_contents('config.php');

        //* Regex para encontrar definições de constantes
        $expressao = '/define\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"]([^\'"]+)[\'"]\s*\)\s*;/';

        //* Encontra todas as correspondências no conteúdo do arquivo
        preg_match_all($expressao, $arquivo, $partes, PREG_SET_ORDER);

        //* Itera sobre as correspondências e armazena o nome e o valor das constantes no array
        foreach ($partes as $parte) {
            //* Verifica se a constante é permitida
            if (in_array($parte[1], $configuracoesPermitidas)) {
                $nome  = $parte[1];
                $valor = $parte[2];
                $constantes[$nome] = $valor;
            }
        }

        //* Retorna o array com nome e valor das constantes
        return $constantes;
    }
}
