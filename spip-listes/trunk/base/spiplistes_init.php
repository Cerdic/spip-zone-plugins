<?php 
/**
 * Installation du plugin. Initialisaiton des tables et preferences generiques.
 *
 * @version Original From SPIP-Listes-V :: Id: spiplistes_init.php paladin@quesaco.org
 * @package spiplistes
 */
 // $LastChangedRevision$
 // $LastChangedBy$
 // $LastChangedDate$

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/spiplistes_api_globales');
include_spip('inc/spiplistes_api');

/**
 * Installation du plugin
 *
 * A chaque appel de exec/admin_plugin, si le plugin est active,
 * spip detecte spiplistes_install() et l'appelle 3 fois :
 * 1/ $action = 'test'
 * 2/ $action = 'install'
 * 3/ $action = 'test'
 * @global string $GLOBALS['meta']['spiplistes_version']
 * @return boolean
 */
function spiplistes_install ($action) {

	include_spip('inc/spipliste_api_globales');
	spiplistes_debug_log('spiplistes_install()');

	$maj['create'] = array(
		array('maj_tables', array('spip_courriers','spip_listes','spip_auteurs_listes','spip_auteurs_courriers','spip_auteurs_mod_listes')),
		);
	$maj[''] = array(
		array('maj_tables', array('spip_courriers','spip_listes','spip_auteurs_listes','spip_auteurs_courriers','spip_auteurs_mod_listes')),
		);







	switch($action) {
		case 'test':
			// si renvoie true, c'est que la base est a jour, inutile de re-installer
			// la valise plugin "effacer tout" apparait.
			// si renvoie false, SPIP revient avec $action = 'install' (une seule fois)
			$spiplistes_version = $GLOBALS['meta']['spiplistes_version'];
			$result = (
				$spiplistes_version
				&& ($spiplistes_version >= spiplistes_real_version_get(_SPIPLISTES_PREFIX))
				&& sql_showtable("spip_listes",true)
				);
			//spiplistes_debug_log("TEST: ".($result ? "OK" : "NO"));
			return($result);
			break;
		case 'install':
			if(!$GLOBALS['meta']['spiplistes_version']) {
				$result = spiplistes_base_creer();
				$str_log = "create";
			}
			else {
				// logiquement, ne devrait pas passer par la (upgrade assure par mes_options)
				include_spip('base/spiplistes_upgrade');
				$result = spiplistes_upgrade();
				$str_log = "upgrade";
			}
			$result = (
				$result
				&& spiplistes_initialise_spip_metas_spiplistes()
				&& spiplistes_activer_inscription_visiteurs()
				);
			$str_log = "INSTALL: $str_log " . spiplistes_str_ok_error($result);
			if(!$result) {
				// nota: SPIP ne filtre pas le resultat. Si retour en erreur,
				// la case a cocher du plugin sera quand meme cochee
				$str_log .= ": PLEASE REINSTALL PLUGIN";
			}
			else {
				echo(_T('spiplistes:_aide_install'
					, array('url_config' => generer_url_ecrire(_SPIPLISTES_EXEC_CONFIGURE))
					));
			}
			spiplistes_log($str_log);
			return($result);
			break;
		case 'uninstall':
			// est appelle lorsque "Effacer tout" dans exec=admin_plugin
			$result = spiplistes_vider_tables();
			spiplistes_log("UNINSTALL: " . spiplistes_str_ok_error($result));
			return($result);
			break;
		default:
			break;
	}
	return (FALSE);
}



/**
 * @global string $GLOBALS['meta'][_SPIPLISTES_META_PREFERENCES]
 * @return boolean
 */
function spiplistes_initialise_spip_metas_spiplistes ($reinstall = false) {

	if(!isset($GLOBALS['meta'][_SPIPLISTES_META_PREFERENCES])) {
		$GLOBALS['meta'][_SPIPLISTES_META_PREFERENCES] = "";
	}

	// par default, simuler les envois a l'installation
	spiplistes_ecrire_key_in_serialized_meta ('opt_simuler_envoi', "oui", _SPIPLISTES_META_PREFERENCES);
	
	// les autres preferences serialisees ('_SPIPLISTES_META_PREFERENCES') sont installees par exec/spiplistes_config

	// autres valeurs par defaut a l'installation
	$spiplistes_spip_metas = array(
		'spiplistes_lots' => _SPIPLISTES_LOT_TAILLE
		, 'spiplistes_charset_envoi' => _SPIPLISTES_CHARSET_ENVOI
		, 'mailer_smtp' => 'non'
		, 'abonnement_config' => 'simple'
		, 'spiplistes_version' => spiplistes_real_version_get(_SPIPLISTES_PREFIX)
	);
	foreach($spiplistes_spip_metas as $key => $value) {
		if($reinstall 
			|| !isset($GLOBALS['meta'][$key])
			|| ($GLOBALS['meta'][$key] != $value)
		) {
			ecrire_meta($key, $value);
		}
	}
	
	spiplistes_ecrire_metas();
	
	return(true);
}

/**
 * @global string $GLOBALS['meta']['accepter_visiteurs']
 * @return boolean
 */
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
} // spiplistes_activer_inscription_visiteurs ()

/**
 * @return boolean
 */
function spiplistes_vider_tables () {

	include_spip('base/abstract_sql');


	foreach(array('spip_listes'
				  , 'spip_courriers'
				  , 'spip_auteurs_courriers'
				  , 'spip_auteurs_listes'
				  , 'spip_auteurs_mod_listes'
				 ) as $table) {
		spiplistes_log('DROP TABLE '.$table);
		sql_drop_table($table, true);
	}
		
	// effacer les metas (prefs, etc.)
	$sql_spiplistes_metas = array(
		'spiplistes_version'
		, 'spiplistes_base_version'
		, 'spiplistes_charset_envoi'
		, 'spiplistes_lots'
		, 'abonnement_config'
		, _SPIPLISTES_META_PREFERENCES
		);
	spiplistes_log("DELETE meta: " . implode(", ", $sql_spiplistes_metas));
	sql_delete('spip_meta', "nom=".implode(" OR nom=", array_map("sql_quote", $sql_spiplistes_metas)));

	// recharge les metas en cache 
	spiplistes_ecrire_metas();
	
	return(true);
} // spiplistes_vider_tables ()
?>