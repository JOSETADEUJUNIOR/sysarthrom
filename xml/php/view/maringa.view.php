    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>MARINGÁ</title>
        <meta content="authenticity_token" name="csrf-param" />
        <meta content="wSGOWG4BgXPlVrrc7S+71dMtxfj1PTdQ4FrEcx/GYOA=" name="csrf-token" />

        <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
    <script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.6.1/html5shiv.js" type="text/javascript"></script>
    <![endif]-->

    <link href="css/style.css" media="all" rel="stylesheet" />

    <!-- For third-generation iPad with high-resolution Retina display: -->
    <!-- Size should be 144 x 144 pixels -->

    <!-- For iPhone with high-resolution Retina display: -->
    <!-- Size should be 114 x 114 pixels -->

    <!-- For first- and second-generation iPad: -->
    <!-- Size should be 72 x 72 pixels -->

    <!-- For non-Retina iPhone, iPod Touch, and Android 2.1+ devices: -->
    <!-- Size should be 57 x 57 pixels -->

    <!-- For all other devices -->
    <!-- Size should be 32 x 32 pixels -->

    <script src="js/script.js"></script>
    </head>

    <body>
    <!--
    <div class="navbar navbar-default navbar-static-top">
    <div class="container">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="#">Polaris Agendamentos</a>
    <div class="navbar-collapse collapse navbar-responsive-collapse">
    <ul class="nav navbar-nav">
    <li  id = "rel1" class="lista_menu "><a class="a_menu" data-id = "1" href = "#">php/data/maringa.data</a></li>
    <li  id = "rel2" class="lista_menu "><a class="a_menu" data-id = "2" href = "#">Maringá</a></li>
    </ul>
    <ul class="nav navbar-nav navbar-right">
    <li><a href="#" onclick="launchFullscreen(document.documentElement);">Tela Cheia</a></li>
    </ul>
    </div>
    </div>
    </div>
    -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">

    <!--
    <span  class="">Abertas ()</span>&nbsp;
    <span  class="label label-warning">Liberadas ()</span>&nbsp;
    <span  class="label label-success">Enviadas ()</span>&nbsp;
    -->
    <?php
        $filename = 'Index.xml';
        if (file_exists($filename)) {
            echo "Última atualização em: " . date ("d/m/Y H:i:s.", fileatime($filename));
        }
    ?>
    <table id="tabelah" style="margin: 0 0 0 0; table-layout: fixed" class="header-fixed table table-bordered table-condensed table-hover">
        <thead id="header">
            <tr>
                <th style="padding:2px" width="10%" class="th_cabecalho"><a class="a_ordem" data-ord="1" href="#">Data</a></th>
                <th style="padding:2px" width="10%" class="th_cabecalho"><a class="a_ordem" data-ord="2" href="#">Hora</a></th>
                <th style="padding:2px" width="80%" class="th_cabecalho"><a class="a_ordem" data-ord="3" href="#">Paciente</a></th>
                <th style="padding:2px" width="42%" class="th_cabecalho"><a class="a_ordem" data-ord="4" href="#">Médico</a></th>
                <th style="padding:2px" width="32%" class="th_cabecalho"><a class="a_ordem" data-ord="5" href="#">Hospital</a></th>
                <th style="padding:2px" width="52%" class="th_cabecalho"><a class="a_ordem" data-ord="6" href="#">Cirurgia</a></th>
                <th style="padding:2px" width="37%" class="th_cabecalho"><a class="a_ordem" data-ord="7" href="#">Convênio</a></th>
                <th style="padding:2px" width="15%" class="th_cabecalho"><a class="a_ordem" data-ord="8" href="#">Instrum.</a></th>
            </tr>
        </thead>
    </table>
    <div id="div_dados" style="height: 10px" height="2000px">
        <table style="margin: 0 0 0 0; table-layout: fixed" id="tabela" class="header-fixed table table-bordered table-condensed table-hover">
            <tbody id="tbody">


            </tbody>
        </table>
    </div>

    <div id="cirurgiaDetalhe" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-body">

        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
            <button class="btn btn-primary">Save changes</button>
        </div>
    </div>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Cirurgia</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Paciente</label>
                                <input type="text" class="form-control" id="mostraPaciente" disabled>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Observa&ccedil;&otilde;es</label>
                                <textarea id="mostraObs" disabled class="form-control" rows="6"></textarea>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
    //setTimeout(function(){
    //   window.location.reload(1);
    //}, 30000);

    function mostraObs(obs) {
        $("#mostraPaciente").val($('#pas' + obs).val());
        $("#mostraObs").html($('#obs' + obs).val());
    }

    var page = 1
    var total_count = 1000
    var timeout = 10000
    var to;
    var nst = 0;
    var ord = 0;
    var rel = 1;

    function launchFullscreen(element) {
        if (element.requestFullscreen) {
            element.requestFullscreen();
        } else if (element.mozRequestFullScreen) {
            element.mozRequestFullScreen();
        } else if (element.webkitRequestFullscreen) {
            element.webkitRequestFullscreen();
        } else if (element.msRequestFullscreen) {
            element.msRequestFullscreen();
        }
    }

    function reloadItems(data, status) {
    /* ref. ordenacao
    $(".th_cabecalho").removeClass('info');
    $(".a_ordem[data-ord=" + new String(Math.abs(ord))+"]").parent().addClass('info');
    */
    $(".lista_menu").removeClass('active');
    $("#rel" + new String(rel)).addClass('active');
    $("#tbody").html(data);
    to = setTimeout(reloadPage, timeout);
    }

    function reloadPage() {
        url = 'php/data/maringa.data.php';
        clearTimeout(to);
        $.get(url, '', reloadItems, 'html');
    }

    function scrollDown() {
        $('#div_dados').animate({
            scrollTop: $("#div_dados").scrollTop() + $("#div_dados > table > tbody > tr:first").height()
        }, 500, function() {
            if ($('#div_dados').scrollTop() == nst) {
                $('#div_dados').animate({
                    scrollTop: 0
                }, 1000, function() {
                    setTimeout(scrollDown, 2000)
                });

            } else {
                setTimeout(scrollDown, 1000)
            }
            nst = $('#div_dados').scrollTop()

        });

    }
    $(function() {
        $("#tbody").on('click', 'tr', function() {

            $("#myModal").modal({
                show: true

            })
        });
        $(".a_menu").click(function() {
            rel = $(this).data("id");
            localStorage.defaultRel = rel;
            clearTimeout(to)
            to = setTimeout(reloadPage, 1);
        });
    /* ref. ordenacao
    $(".a_ordem").click(function(){
    newOrd = parseInt($(this).data("ord"));
    if (Math.abs(newOrd) == Math.abs(ord)){
    ord = -ord;
    }else{
    ord = newOrd;
    }
    localStorage.defaultOrd = ord;
    clearTimeout(to)
    to = setTimeout(reloadPage, 1);
    });
    */
    /*

    []
    []
    */
    if (typeof(Storage) !== "undefined") {
        if (localStorage.defaultRel) {
            rel = localStorage.defaultRel
        }
        if (localStorage.defaultOrd) {
            ord = localStorage.defaultOrd
        }

    }
    clearTimeout(to);
    to = setTimeout(reloadPage, 1);
    //setTimeout(scrollDown, 1000)
    $("#pagina_anterior").click(function() {
        page = page - 1;
        if (page < 1) {
            page = total_count;
        }
        clearTimeout(to);
        url = 'php/data/maringa.data.php';
        $.get(url, '', reloadItems, 'html');

    })
    $("#proxima_pagina").click(function() {
        page = page + 1;
        if (page > total_count) {
            page = 1;
        }
        clearTimeout(to);
        url = 'php/data/maringa.data.php';
        $.get(url, '', reloadItems, 'html');


    })
    });
    </script>
    </div>


    </div> <!-- /container -->

    </body>

    </html>