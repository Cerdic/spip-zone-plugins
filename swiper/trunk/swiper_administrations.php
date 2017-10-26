<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Swiper
 *
 * @plugin     Swiper
 * @copyright  2017
 * @author     Charles Stephan
 * @licence    GNU/GPL
 * @package    SPIP\Swiper\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin Swiper.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function swiper_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	include_spip('inc/cextras');
	include_spip('base/upgrade');
	include_spip('base/swiper');

	$maj['create'] = array(array('maj_tables', array('spip_swipers')));
	cextras_api_upgrade(swiper_declarer_champs_extras(), $maj['create']);

	$c = lire_config('documents_objets');
	$d = explode(",", $c);
	if (!in_array("spip_swipers", $d))
		ecrire_meta('documents_objets', $c."spip_swipers,");

	$s = lire_config('swiper');
	if (!in_array("swiper_options", $s))
		ecrire_config('swiper/swiper_options', "{\nkeyboard : true,\npagination: {\nel: '.swiper-pagination',\ntype: 'bullets'\n},\nscrollbar: {\nel: '.swiper-scrollbar',\ndraggable: true\n},\nnavigation: {\nnextEl: '.swiper-button-next',\nprevEl: '.swiper-button-prev'\n}\n}");

	maj_plugin($nom_meta_base_version, $version_cible, $maj);

}


/**
 * Fonction de désinstallation du plugin Swiper.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function swiper_vider_tables($nom_meta_base_version) {

	include_spip('inc/cextras');
	include_spip('base/upgrade');
	include_spip('base/swiper');

	// sql_drop_table('spip_swipers');
	cextras_api_vider_tables(swiper_declarer_champs_extras(), $maj['create']);

	# Nettoyer les liens courants (le génie optimiser_base_disparus se chargera de nettoyer toutes les tables de liens)
	// sql_delete('spip_documents_liens', sql_in('objet', array('swiper')));
	// sql_delete('spip_mots_liens', sql_in('objet', array('swiper')));
	// sql_delete('spip_auteurs_liens', sql_in('objet', array('swiper')));
	# Nettoyer les versionnages et forums
	sql_delete('spip_versions', sql_in('objet', array('swiper')));
	sql_delete('spip_versions_fragments', sql_in('objet', array('swiper')));
	sql_delete('spip_forum', sql_in('objet', array('swiper')));

	$c = lire_config('documents_objets');
	$c = str_replace('spip_swipers,', '', $c);
 	ecrire_meta('documents_objets', $c);
	effacer_meta("swiper");

	effacer_meta($nom_meta_base_version);
}
