<?php

// Prepare les messages d'aide de la page de configuration du plugin

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation'); // pour compat cfg 1.0.1

// Noter les erreurs pour les afficher dans le panneau de config
// BUG: la modif de config se faisant apres le passage dans inc/autoriser,
// si de nouvelles erreurs apparaissent suite a une modif elles ne seront
// affichees qu'au hit suivant
include_spip('inc/autoriser');
global $autorite_erreurs;
if (!isset($autorite_erreurs)) {
	if (isset($GLOBALS['meta']['autorite_erreurs'])) {
		include_spip('inc/meta');
		effacer_meta('autorite_erreurs');
		ecrire_metas();
		spip_log('Autorite : OK');
	}
}
else if (serialize($autorite_erreurs) != $GLOBALS['meta']['autorite_erreurs']) {
	include_spip('inc/meta');
	ecrire_meta('autorite_erreurs', serialize($autorite_erreurs));
	ecrire_metas();
	spip_log('Erreur autorite : '.join(', ', $autorite_erreurs));
}

// Qui sont les webmestres ?
// pour le squelette cfg_autorite
function liste_webmestres($void)
{
	$webmestres = array();
	include_spip('inc/texte');
	$s = spip_query("SELECT * FROM spip_auteurs WHERE id_auteur IN (". join (',', array_filter(explode(':', _ID_WEBMESTRES), is_numeric)).")");
	while ($qui = spip_fetch_array($s)) {
		if (autoriser('webmestre','','',$qui))
			$webmestres[$qui['id_auteur']] = typo($qui['nom']);
	}
	return  join(', ', $webmestres);
}

?>
