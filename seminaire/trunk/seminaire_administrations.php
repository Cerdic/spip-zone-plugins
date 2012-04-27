<?php
/**
 * Plugin Séminaire LATP
 * (c) 2012 Amaury Adon
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

	include_spip('inc/cextras');
	include_spip('base/seminaire');


/**
 * Fonction d'installation du plugin et de mise à jour.
**/
function seminaire_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	cextras_api_upgrade(seminaire_declarer_champs_extras(), $maj['create']);
	/**activer les mots clés et leur configuration avancée s'ils ne le sont pas déjà**/
	 if ($GLOBALS['meta']['articles_mots']!=oui){
	 	ecrire_meta("articles_mots", "oui");
	 	ecrire_meta("config_precise_groupes", "oui");
	 	ecrire_meta("documents_objets", "spip_evenements");	
	 	}
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
**/
function seminaire_vider_tables($nom_meta_base_version) {


	effacer_meta($nom_meta_base_version);
}

?>