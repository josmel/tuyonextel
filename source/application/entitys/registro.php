<?php
require_once "../../../../var/call.php";
$pooNa=new Navegacion;
$pais="pe";
$operadora="mo";

$url="index.php";
$fecha=date("Y-m-d H:i:s");
$string=$pooNa->regVisit($pais,$operadora,$url,$fecha);
echo $string;
?>