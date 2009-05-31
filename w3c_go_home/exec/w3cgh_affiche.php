<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_w3cgh_affiche_dist()
{
	$nom = _request('nom');
	$url = urldecode(_request('url'));
	$voir = generer_url_ecrire('w3cgh_voir',"nom=$nom&url=$url");
	
	debut_page(_T("w3cgh:titre_page"), "w3c", "w3c");
	echo "<iframe src='$voir' style='width:100%;height:600px;'></iframe>";
	echo fin_page();
}

?>