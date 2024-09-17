<?php
$filename = 'Index.xml';
if (file_exists($filename)) {
    echo "Última atualização em: " . date ("d/m/Y H:i:s.", fileatime($filename));
}
?>
<hr>
<a href='londrina'><h1>Londrina</h1></a><br>
<a href='maringa'><h1>Maringa</h1></a><br>
<a href='oeste'><h1>Cascavel</h1></a>