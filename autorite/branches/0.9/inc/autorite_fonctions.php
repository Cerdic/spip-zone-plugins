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
	include_spip('inc/plugin');

	// Version SPIP < 2.1 ou alors >= 2.1 mais utilisant toujours le define pour etablir la liste
	if (!function_exists('spip_version_compare') OR 
	spip_version_compare($GLOBALS['spip_version_branche'],"2.1.0-rc","<") OR
	defined('_ID_WEBMESTRES')) {
		$s = spip_query("SELECT * FROM spip_auteurs WHERE id_auteur IN (". join (',', array_filter(explode(':', _ID_WEBMESTRES), is_numeric)).")");
	}
	// Version SPIP >= 2.1 et utilisation du flag webmestre en base de donnees
	else {
		$s = spip_query("SELECT * FROM spip_auteurs WHERE webmestre='oui'");
	}

	while ($qui = sql_fetch($s)) {
		if (autoriser('webmestre','','',$qui))
			$webmestres[$qui['id_auteur']] = typo($qui['nom']);
	}
	return  join(', ', $webmestres);
}

// Avertissements selon version code
function autorite_erreurs_version($void)
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
		// autoriser(detruire)
		$autorite_erreurs_version[] = _L('effacer la base');
	}
	if ($GLOBALS['spip_version_code'] < '1.9253') {
		$autorite_erreurs_version[] = _L('interdire la creation de rubriques');
	}
	if ($GLOBALS['spip_version_code'] < '1.9254') {
		// autoriser(sauvegarder)
		$autorite_erreurs_version[] = _L('faire des sauvegardes');
	}
	if ($GLOBALS['spip_version_code'] < '1.9258') {
		// define(_STATUT_AUTEUR_CREATION)
		$autorite_erreurs_version[] = _L('associer des rubriques aux auteurs');
		$autorite_erreurs_version[] = _L('ignorer la notion d\'administrateur restreint');
	}
	return join('</li><li>', $autorite_erreurs_version);
}
?>
