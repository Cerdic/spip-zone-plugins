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

// Avertissements selon version code
function autorite_erreurs_version($separateur = '</li><li>')
{
	$autorite_erreurs_version = array();
	if ($GLOBALS['spip_version_code'] < '1.9251') {
		$autorite_erreurs_version[] = _L('auteur modere forum');
		$autorite_erreurs_version[] = _L('auteur modere petition');
		$autorite_erreurs_version[] = _L('auteur modifie email');
		$autorite_erreurs_version[] = _L('redacteur voit stats');
		$autorite_erreurs_version[] = _L('redacteur modifie mots');
	}
	if ($GLOBALS['spip_version_code'] < '1.9252') {
		// autoriser(configurer)
		$autorite_erreurs_version[] = _L('configurer');
		// autoriser(sauvegarder)
		$autorite_erreurs_version[] = _L('faire des sauvegardes');
		// autoriser(detruire)
		$autorite_erreurs_version[] = _L('effacer la base');
	}
	if ($GLOBALS['spip_version_code'] < '1.9253') {
		$autorite_erreurs_version[] = _L('interdire la creation de rubriques');
	}
	return join($separateur ? $separateur : '</li><li>', $autorite_erreurs_version);
}
?>
