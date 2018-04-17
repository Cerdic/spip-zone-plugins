<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin wp_import
 *
 * @plugin     wp_import
 * @copyright  2018
 * @author     Peetdu
 * @licence    GNU/GPL
 * @package    SPIP\wp_import\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'installation et de mise à jour du plugin wp_import.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function wp_import_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(array('wp_import_ajouter_menu_developpement', $GLOBALS['visiteur_session']['id_auteur'])); 

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin wp_import.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function wp_import_vider_tables($nom_meta_base_version) {

	effacer_meta($nom_meta_base_version);
}


/**
 * Ajouter le menu développment à l'auteur indiqué
 *
 * @param int $id_auteur
 * @return bool
**/
function wp_import_ajouter_menu_developpement($id_auteur) {
	if (!$id_auteur = intval($id_auteur)) {
		return false;
	}
	include_spip('action/editer_objet');
	$prefs = sql_getfetsel('prefs', 'spip_auteurs', 'id_auteur=' . $id_auteur);
	if (!$prefs or !$prefs = unserialize($prefs)) {
		return false;
	}
	$prefs['activer_menudev'] = 'oui';
	if ($err = objet_modifier('auteur', $id_auteur, array('prefs' => serialize($prefs)))) {
		spip_log('Ajout du menu developpement en erreur : ' . $err, 'fabrique');
	}
	return true;
}
