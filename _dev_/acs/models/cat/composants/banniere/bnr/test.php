<?php
require_once('banner_lib.php');

$url = _DIR_RACINE.'IMG/_acs/cat/img/banniere/';
$bnrList = getBanners($url, $_GET['exclut']);
print_r($bnrList);
echo rand(0, count($bnrList) - 1), '/'.count($bnrList);
?>
