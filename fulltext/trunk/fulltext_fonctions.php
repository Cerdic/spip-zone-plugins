<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_AIDE_RECHERCHE($p) {
	if (!function_exists('recuperer_fond'))
		include_spip('public/assembler');
	//    $arg = interprete_argument_balise(1, $p);
	$mess_aide = str_replace("'", "\'", recuperer_fond('aide_recherche', array('lang' => $GLOBALS['spip_lang'])));
	$p -> code = "'$mess_aide'";
	$p -> statut = 'html';
	return $p;
}

function lien_objet_ptg($id, $type, $longueur = 80, $connect = NULL) {
	include_spip('inc/liens');
	$titre = traiter_raccourci_titre($id, $type, $connect);
	$titre = typo($titre['titre']);
	if (!strlen($titre))
		$titre = _T('info_sans_titre');
	$url = generer_url_entite($id, $type);
	return "<a href='$url' class='$type'>" . couper($titre, $longueur) . "</a>";
}
?>