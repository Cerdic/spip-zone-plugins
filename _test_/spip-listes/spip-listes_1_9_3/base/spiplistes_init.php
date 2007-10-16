<?php 
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

function spiplistes_install ($action) {


spiplistes_log("spiplistes_install() <<", LOG_DEBUG);

	switch($action) {
		case 'test':
			// si renvoie true, c'est que la base est à jour, inutile de re-installer
			// la valise plugin "effacer tout" apparaît.
			// si renvoie false, SPIP revient avec $action = 'install' (une seule fois)
			$result = (
				isset($GLOBALS['meta']['spiplistes_version'])
				&& ($v = $GLOBALS['meta']['spiplistes_version'])
				&& ($v >= __plugin_get_real_version())
				&& spip_mysql_showtable("spip_auteurs_elargis")
				&& spip_mysql_showtable("spip_listes")
				);
			$result = true;
spiplistes_log("PLUGIN TEST: ".($result ? "true" : "false"), LOG_DEBUG);
			return($result);
			break;
		case 'install':
			if(!isset($GLOBALS['meta']['spiplistes_version'])) {
				include_spip('base/spip-listes');
				$result = spiplistes_creer_base();
			}
			else {
				include_spip('base/spiplistes_upgrade');
				$result = spiplistes_upgrade_base();
			}
			$result = (
				$result
				&& spiplistes_initialise_spip_metas_spiplistes()
				&& spiplistes_activer_inscription_visiteurs()
				);
spiplistes_log("PLUGIN INSTALL: ".($result ? "true" : "false"), LOG_DEBUG);
			if(!$result) {
				// nota: SPIP ne filtre pas le résultat. Si retour en erreur,
				// la case à cocher du plugin sera quand même cochée
				spiplistes_log("PLUGIN INSTALL: ERROR. PLEASE REINSTALL PLUGIN...", LOG_DEBUG);
			}
			return($result);
			break;
		case 'uninstall':
			// est appellé lorsque "Effacer tout" dans exec=admin_plugin
			$result = spiplistes_vider_tables();
spiplistes_log("PLUGIN UNINSTALL: ".($result ? "true" : "false"), LOG_DEBUG);
			return($result);
			break;
		default:
			break;
	}
}

function spiplistes_initialise_spip_metas_spiplistes ($reinstall = false) {

	$ecrire_metas = false;
	
	// valeurs par défaut à l'installation
	$spiplistes_spip_metas = array(
		'spiplistes_lots' => _SPIPLISTES_LOT_TAILLE
		, 'spiplistes_charset_envoi' => _SPIPLISTES_CHARSET_ENVOI
		, 'mailer_smtp' => 'non'
		, 'abonnement_config' => 'simple'
	);
	foreach($spiplistes_spip_metas as $key => $value) {
		if($reinstall || !isset($GLOBALS['meta'][$key])) {
			ecrire_meta($key, $value);
			$ecrire_metas = true;
		}
	}
	// les preferences serialisées ('spiplistes_preferences') sont installées par exec/spiplistes_config
	
	if($ecrire_metas) {
		include_spip("inc/meta");
		ecrire_metas();
	}
	return(true);
}

function spiplistes_activer_inscription_visiteurs () {
	$accepter_visiteurs = lire_meta('accepter_visiteurs');
	if($accepter_visiteurs != 'oui') {
		$accepter_visiteurs = 'oui';
		ecrire_meta("accepter_visiteurs", $accepter_visiteurs);
		ecrire_metas();
		echo _T('spiplistes:autorisation_inscription');
	}
	return(true);
}

function spiplistes_vider_tables ($nom) {
	include_spip('base/abstract_sql');
	spip_query("DROP TABLE spip_courriers");
	spip_query("DROP TABLE spip_listes");
	spip_query("DROP TABLE spip_auteurs_courriers");
	spip_query("DROP TABLE spip_auteurs_listes");
	spip_query("DROP TABLE spip_auteurs_mod_listes");
	// ne supprime pas spip_auteurs_elargis. Ca peut servir ;-?
	effacer_meta('spiplistes_version');
	effacer_meta('spiplistes_charset_envoi');
	effacer_meta('spiplistes_lots');
	effacer_meta('abonnement_config');
	effacer_meta('spiplistes_preferences');
	ecrire_metas();
	return(true);
}

?>