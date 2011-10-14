<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_URL_RUBRIQUE_dist($p) {
	$id_rubrique = interprete_argument_balise(1,$p);
	if (!$id_rubrique) $id_rubrique = champ_sql('id_rubrique', $p);
	$code = "courtcircuit_calculer_balise_URL_RUBRIQUE ($id_rubrique)";
	$p->code = $code;
	$p->interdire_scripts = false;
	return $p;
}

function courtcircuit_calculer_balise_URL_RUBRIQUE ($id_rubrique) {
	$url_base = generer_url_entite(intval($id_rubrique), 'rubrique', '', '', true);
	include_spip('inc/courtcircuit');
	if (isset($GLOBALS['meta']['courtcircuit']))
		$config = unserialize($GLOBALS['meta']['courtcircuit']);
	else $config = array();
	if (isset($config['liens_rubriques']) && $config['liens_rubriques']=='oui')
		$url_redirect = courtcircuit_url_redirection($id_rubrique);
	else
		$url_redirect = '';
	return ($url_redirect!='') ? $url_redirect : $url_base;
}

?>