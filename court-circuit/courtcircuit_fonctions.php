<?php

function balise_URL_RUBRIQUE_dist($p) {
	$id_rubrique = champ_sql('id_rubrique', $p);
	$code = "courtcircuit_calculer_balise_URL_RUBRIQUE ($id_rubrique)";
	$p->code = $code;
	$p->interdire_scripts = false;
	return $p;
}

function courtcircuit_calculer_balise_URL_RUBRIQUE ($id_rubrique) {
	$url_base = generer_url_entite(intval($id_rubrique), 'rubrique', '', '', true);
	include_spip('inc/courtcircuit');
	$url_redirect = courtcircuit_url_redirection($id_rubrique);
	return ($url_redirect!='') ? $url_redirect : $url_base;
}

?>