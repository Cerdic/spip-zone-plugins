<?php 

// base/spiplistes_init.php

// Original From SPIP-Listes-V :: Id: spiplistes_init.php paladin@quesaco.org

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

///////////////////////////////////////
// A chaque appel de exec/admin_plugin, si le plugin est activé, 
// spip détecte spiplistes_install() et l'appelle 3 fois :
// 1/ $action = 'test'
// 2/ $action = 'install'
// 3/ $action = 'test'
// 

include_spip('inc/spiplistes_api_globales');
include_spip('inc/spiplistes_api');

function spiplistes_install ($action) {

//spiplistes_log("spiplistes_install() <<", _SPIPLISTES_LOG_DEBUG);

	switch($action) {
		case 'test':
			// si renvoie true, c'est que la base est à jour, inutile de re-installer
			// la valise plugin "effacer tout" apparaît.
			// si renvoie false, SPIP revient avec $action = 'install' (une seule fois)
			$spiplistes_version = $GLOBALS['meta']['spiplistes_version'];
			$result = (
				$spiplistes_version
				&& ($spiplistes_version >= __plugin_real_version_get(_SPIPLISTES_PREFIX))
				&& spip_mysql_showtable("spip_auteurs_elargis")
				&& spip_mysql_showtable("spip_listes")
				);
			if(
				!spiplistes_spip_est_inferieur_193() 
				&& (_request('action') == 'desinstaller_plugin')
			) {
				// dans action/desinstaller_plugin.php
				// pour réellement désinstaller le plugin dans les metas et cache
				// SPIP 192 attend false, 193 true
				$result = !$result;
			}
			spiplistes_log("TEST: ".($result ? "TRUE" : "FALSE"), _SPIPLISTES_LOG_DEBUG);
			return($result);
			break;
		case 'install':
			if(!$GLOBALS['meta']['spiplistes_version']) {
				$result = spiplistes_base_creer();
			}
			else {
				// logiquement, ne devrait pas passer par là (upgrade assuré par mes_options)
				include_spip('base/spiplistes_upgrade');
				//$result = spiplistes_upgrade_base();
			}
			$result = (
				$result
				&& spiplistes_initialise_spip_metas_spiplistes()
				&& spiplistes_activer_inscription_visiteurs()
				);
			if(!$result) {
				// nota: SPIP ne filtre pas le résultat. Si retour en erreur,
				// la case à cocher du plugin sera quand même cochée
				spiplistes_log("spiplistes INSTALL: ERROR. PLEASE REINSTALL PLUGIN...");
			}
			spiplistes_log("spiplistes INSTALL: ".($result ? "OK" : "NO"));
			return($result);
			break;
		case 'uninstall':
			// est appellé lorsque "Effacer tout" dans exec=admin_plugin
			$result = spiplistes_vider_tables();
			spiplistes_log("UNINSTALL: ".($result ? "TRUE" : "FALSE"));
			return($result);
			break;
		default:
			break;
	}
}


function spiplistes_base_creer () {

//spiplistes_log("spiplistes_base_creer() <<", _SPIPLISTES_LOG_DEBUG);

	// demande à SPIP de créer les tables (base/create.php)
	include_spip('base/create');
	include_spip('base/abstract_sql');
	include_spip('base/db_mysql');
	include_spip('base/spiplistes_tables');
	creer_base();
	spiplistes_log("INSTALL: database creation");

	ecrire_meta('spiplistes_version', __plugin_real_version_get(_SPIPLISTES_PREFIX));

	$spiplistes_base_version = __plugin_real_version_base_get(_SPIPLISTES_PREFIX);
	ecrire_meta('spiplistes_base_version', $spiplistes_base_version);
	spiplistes_ecrire_metas();
	
	$spiplistes_base_version = $GLOBALS['meta']['spiplistes_base_version'];

	return($spiplistes_base_version);
}


function spiplistes_initialise_spip_metas_spiplistes ($reinstall = false) {

	$spiplistes_current_version =  __plugin_current_version_get(_SPIPLISTES_PREFIX);
	$spiplistes_real_version = __plugin_real_version_get(_SPIPLISTES_PREFIX);
	$opt_simuler_envoi = spiplistes_pref_lire('opt_simuler_envoi');
	if($spiplistes_current_version || !$opt_simuler_envoi) {
	// si mise à jour ou première install, passe en simulation d'envoi
		$opt_simuler_envoi = 
			($spiplistes_current_version < $spiplistes_real_version)
				// mise à jour de spiplistes ?
			? "oui"
				// reprend pref
			: "non"
			;
	}
	else {
		$opt_simuler_envoi = "oui";
	}
	if(!isset($GLOBALS['meta'][_SPIPLISTES_META_PREFERENCES])) {
		$GLOBALS['meta'][_SPIPLISTES_META_PREFERENCES] = "";
	}
	//spiplistes_log("### _SPIPLISTES_META_PREFERENCES : $opt_simuler_envoi");
	__plugin_ecrire_key_in_serialized_meta ('opt_simuler_envoi', $opt_simuler_envoi, _SPIPLISTES_META_PREFERENCES);
	// les autres preferences serialisées ('_SPIPLISTES_META_PREFERENCES') sont installées par exec/spiplistes_config

	// autres valeurs par défaut à l'installation
	$spiplistes_spip_metas = array(
		'spiplistes_lots' => _SPIPLISTES_LOT_TAILLE
		, 'spiplistes_charset_envoi' => _SPIPLISTES_CHARSET_ENVOI
		, 'mailer_smtp' => 'non'
		, 'abonnement_config' => 'simple'
	);
	foreach($spiplistes_spip_metas as $key => $value) {
		if($reinstall || !isset($GLOBALS['meta'][$key])) {
			ecrire_meta($key, $value);
		}
	}
	
	spiplistes_ecrire_metas();
	return(true);
}

function spiplistes_activer_inscription_visiteurs () {
	$accepter_visiteurs = $GLOBALS['meta']['accepter_visiteurs'];
	if($accepter_visiteurs != 'oui') {
		$accepter_visiteurs = 'oui';
		ecrire_meta("accepter_visiteurs", $accepter_visiteurs);
		spiplistes_ecrire_metas();
		echo "<br />"._T('spiplistes:autorisation_inscription');
		spiplistes_log("ACTIVER accepter visiteur");
	}
	return(true);
}

function spiplistes_vider_tables () {

spiplistes_log("spiplistes_vider_tables() <<", _SPIPLISTES_LOG_DEBUG);

	include_spip('base/abstract_sql');
	
	sql_drop_table(
		"spip_listes, spip_courriers, spip_auteurs_courriers, spip_auteurs_listes, spip_auteurs_mod_listes"
		, true);
	
	// ne supprime pas spip_auteurs_elargis. Ca peut servir ;-?
	effacer_meta('spiplistes_version');
	effacer_meta('spiplistes_base_version');
	effacer_meta('spiplistes_charset_envoi');
	effacer_meta('spiplistes_lots');
	effacer_meta('abonnement_config');
	effacer_meta(_SPIPLISTES_META_PREFERENCES);
	spiplistes_ecrire_metas();
	
	return(true);
}

?>