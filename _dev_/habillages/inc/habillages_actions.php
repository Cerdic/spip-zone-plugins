<?php

function habillages_determine_upload()
{
	global $connect_toutes_rubriques, $connect_login, $connect_statut ;
	$dossier_upload = "../plugins/habillages/habillages-data/logos/";
	
	if (!$GLOBALS['flag_upload']) return false;
	if (!$connect_statut) {
		$var_auth = charger_fonction('auth', 'inc');
		$var_auth = $var_auth();
	}
	if ($connect_statut != '0minirezo') return false;
	return $dossier_upload . 
	  ($connect_toutes_rubriques ? '' : ($connect_login . '/'));
}

?>