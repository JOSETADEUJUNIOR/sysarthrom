<?php

/**
* @ Polaris Tecnologia
* @ Autor: Whilton Reis
* @ Data : 20/11/2016
*/

spl_autoload_register(function ($class_name) {
    // Diretórios bases onde estão localizados os arquivos das classes
    $base_directories = [
        'core/class/',
        'modulos/cadastros/usuario/controller/',
        'modulos/configuracao/controller/',
        'modulos/debug/controller/',
        'modulos/erro/controller/',
        'modulos/login/controller/',
        'modulos/parametrizacoes/permissao/controller/',
        'modulos/principal/controller/',
        'modulos/solicitacoes/cirurgico/controller/',
        'modulos/solicitacoes/instrumental/controller/',
        'modulos/tela/controller/'
    ];

    // Substitua barras invertidas por barras normais no nome da classe
    $class_name = str_replace('\\', '/', $class_name);

    // Percorra os diretórios bases
    foreach ($base_directories as $directory) {
        // Caminho completo do arquivo da classe
        $file_path = strtolower($directory . $class_name . '.class.php');

        // Verifica se o arquivo da classe existe e o inclui
        if (file_exists($file_path)) {
            include $file_path;
            return;
        }
    }

    // Caso a classe não seja encontrada, você pode lidar com isso de acordo com suas necessidades
    die('A classe ' . $class_name . ' não pôde ser encontrada.');
});




