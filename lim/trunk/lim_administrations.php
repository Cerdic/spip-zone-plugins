<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Lim
 *
 * @plugin     Lim
 * @copyright  2015
 * @author     Pierre Miquel
 * @licence    GNU/GPL
 * @package    SPIP\Lim\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'installation et de mise à jour du plugin Lim.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function lim_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['1.1.0'] = array(
        array('lim_creation_meta_objets', array())
    );

    $maj['1.2.0'] = array(
        array('lim_metas_order', array())
    );

    $maj['1.3.0'] = array(
        array('lim_spip33', array())
    );

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * maj v1 -> v1.1
 * si la meta lim_rubriques a été renseignée dans la v1,
 * il faut créer et renseigner la nouvelle méta 'lim_objets' en conséquence
 *
**/
function lim_creation_meta_objets() {
	include_spip('inc/config');
	
	if ($rubrique = lire_config('lim_rubriques')) {
		$valeur = '';
		foreach ($rubrique as $key => $value) {
			$valeur .= table_objet_sql($key).',';
		}
		ecrire_config('lim_objets', $valeur);
	}
}

/**
 * Maj 1.2.0 : mise en ordre de la config
 * + transformation des config à virgule en liste tableau normal.
 **/
function lim_metas_order() {
	include_spip('inc/config');

	if ($divers = lire_config('lim/divers')) {
		effacer_config('lim/divers');
		ecrire_config('lim/divers/form_auteur', $divers);
	}

	if ($portfolio = lire_config('lim/divers/form_auteur/portfolio')) {
		ecrire_config('lim/divers/portfolio', $portfolio);
		effacer_config('lim/divers/form_auteur/portfolio');
	}
	
	if ($forums_publics = lire_config('lim/forums_publics')) {
		ecrire_config('lim/divers/forums_publics', $forums_publics);
		effacer_config('lim/forums_publics');
	}

	if ($petitions = lire_config('lim/petitions')) {
		ecrire_config('lim/divers/petitions', $petitions);
		effacer_config('lim/petitions');
	}

	if ($objets_logos = lire_config('lim_logos')) {

		// On transforme en tableau liste
		$config_nouvelle = explode(',', $objets_logos);
		$config_nouvelle = array_map('trim', $config_nouvelle);
		$config_nouvelle = array_filter($config_nouvelle);

		ecrire_config('lim/logos/objets', $config_nouvelle);
		effacer_config('lim_logos');
	}

	if ($objets_rubriques = lire_config('lim_objets')) {

		// On transforme en tableau liste
		$config_nouvelle = explode(',', $objets_rubriques);
		$config_nouvelle = array_map('trim', $config_nouvelle);
		$config_nouvelle = array_filter($config_nouvelle);

		ecrire_config('lim/rubriques/objets', $config_nouvelle);
		effacer_config('lim_objets');
	}

	/* Pas possible de migrer cette méta pour cause de régression avec plugins existants */
	/* les surcharges d'autorisations "creer{objet}dans ne marcheraient plus (gloups!) */
	//
	// if ($restrictions_rubriques = lire_config('lim_rubriques')) {
	// 	ecrire_config('lim/rubriques/restrictions', $restrictions_rubriques);
	// 	effacer_config('lim_rubriques');
	// }

	if ($cadenas = lire_config('lim/objets_fige')) {
		ecrire_config('lim/rubriques/cadenas', $cadenas);
		effacer_config('lim/objets_fige');
	}
}

function lim_spip33() {
	effacer_config('lim/divers/portfolio');
	effacer_config('lim/divers/petitions');
}


/**
 * Fonction de désinstallation du plugin Lim.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function lim_vider_tables($nom_meta_base_version) {

	effacer_meta('lim');
	effacer_meta('lim_rubriques');
	effacer_meta($nom_meta_base_version);
}
