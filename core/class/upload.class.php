<?php
/**
 ** Data : 02/01/2016
 ** Autor:Whilton Reis
 ** Polaris Tecnologia
 **/

class Upload {
    
    private $file = array();
    public  $dir  = "", $extension, $size;
    public  $arq;

    //* Prepara os dados do $_FILES para uma variavel.
    public function  __construct( $_file ) {
        foreach( $_FILES[ $_file ] as $key => $values ) {
            $this->file[ $key ] = $values;
        }
    }
    
    //* Faz o upload do arquivo
    public function makeUpload() {

        //*  Caso a variável dir, estiver vazia, ele retorna um erro
        if($this->dir == "" ) {
            print_r("Você deve determinar um caminho para o arquivo.");
            exit;
        }
        if($this->isFile()) {
                    if($this->size($this->size )) {
                        if($this->isArray( $this->file[ "error" ])) {
                            try {
                                foreach($this->file[ "error" ] as $key => $error) {
                                    if($error == UPLOAD_ERR_OK) {

                                        //*  Inicia a cópia do arquivo
                                        move_uploaded_file( $this->file[ "tmp_name" ][ $key ], $this->dir . $this->arq );
                                        //* retorna o nome do arquivo, para ser salvo no banco
                                        //* return true
                                    }
                                }
                            } catch( Exception $ex ) {
                                echo $ex->getMessage();
                            }
                        } else {
                            try {

                                //* Inicia a cópia do arquivo
                                move_uploaded_file( $this->file[ "tmp_name" ], $this->dir . $this->arq);
                                //* retorna o nome do arquivo, para ser salvo no banco
                                //* return true
                            } catch( Exception $ex ) {
                                echo $ex->getMessage();
                            }
                    }
            } else {

                //* print_r("O arquivo é acima do tamanho pré-determinado.");
                print_r('tamanho');
                exit();
            }
        } else {
            
            //* print_r("O arquivo escolhido não é permitido.");
            print_r('extensao');
            exit();
        }
    }

    //* Verifica se o arquivo é do tamanho determinado pelo programador.
    private function size($_max_size) {
        $_max_size = $this->convertMbToBt( $_max_size );
        if($this->isFile()) {
            if($this->isArray($this->file[ "size" ])) {
                $count = count( $this->file[ "size" ] );
                $counter = 0;
                foreach($this->file[ "size" ] as $newSize) {
                        ($newSize <= $_max_size) ? $counter++ : $counter-- ;
                }
            return ( $counter == $count ) ? true : false ;
            } else {
            return ( $this->file[ "size" ] <= $_max_size ) ? true : false ;
            }
        }
    }

    //* Verifica se o arquivo enviado é de uma das extensões permitidas.
    private function isFile() {
        if($this->isArray($this->extension)) {
            $extensions = implode( "|", $this->extension );
            $_file_test = $this->isArrayEmpty( $this->file[ "name" ] );
            if($this->isArray($_file_test)) {
                $count = count( $_file_test );
                $counter = 0;
                foreach($_file_test as $values) {
                    ( preg_match( "/.+\.({$extensions})/", $values ) ) ? $counter++ : $counter-- ;
                }
                return ( $count == $counter ) ? true : false ;
            } else {
                return ( preg_match( "/.+\.({$extensions})/", $_file_test ) ) ? true : false ;
            }
        }
    }

    //* Verifica se existe algum campo vazio.
    private function isArrayEmpty($_array) {
        if(is_array($_array)) {
            $_array_search = array_search( "", $_array );
            if( is_numeric( $_array_search ) ) {
                unset( $_array[ $_array_search ] );
            }
        }
        return $_array;
    }

    //*  Verifica se é array.
    private function isArray($_array) {
        return (is_array($_array)) ? true : false ;
    }

    //* Transforma o valor em MB para Byte
    private function convertMbToBt($_size) {
        return $_size * pow( 2, 1024 );
    }

}