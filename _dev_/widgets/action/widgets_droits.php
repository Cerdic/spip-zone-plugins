<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


//
// Quand widgets.js nous appelle avec la liste des liens qu'il a vu,
// on lui repond en lui donnant la liste des liens qu'il doit activer
// (car ce sont les liens autorises).
//
function action_widgets_droits_dist() {
	$rep = array();

	foreach(explode('&', _request('vus')) as $class) {
		if (preg_match(
		',(article)-(titre|surtitre|soustitre|descriptif|chapo|texte|ps)-(\d+),',
		$class, $regs)) {
			if (autoriser_modifs('article', $regs[3])) {
				$rep[] = $regs[0];
			}
		}
	}

	echo join("|",array_unique($rep));
}

// fonction d'API manquante a SPIP...
function autoriser_modifs($quoi = 'article', $id = 0) {
	if ($quoi != 'article') {
		echo "pas implemente";
		return false;
	}

	global $connect_id_auteur, $connect_statut;
	$connect_id_auteur = intval($GLOBALS['auteur_session']['id_auteur']);
	$connect_statut = $GLOBALS['auteur_session']['statut'];
	include_spip('inc/auth');
	auth_rubrique(); # definit $connect_toutes_rubriques (argh)
	return acces_article($id);
}

?>