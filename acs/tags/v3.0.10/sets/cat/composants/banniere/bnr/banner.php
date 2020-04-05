<?php  // Doit renvoyer UNIQUEMENT une image

$url = $GLOBALS['ACS_CHEMIN'].'/banniere/';
// la variable globale est rendue disponible en appellant ce fichier par un wrap

require_once('banner_lib.php');

$bnrList = getBanners($url, $_GET['exclut']);

$bnr = $bnrList[rand(0, count($bnrList) - 1)];
if ($bnr) {
  header ("Location: ".$url."$bnr");
}
else {
  header ("Location: ".$url.$_GET['exclut']);
}
die;
?>