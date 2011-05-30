<?php
/*
 * Plugin En Travaux
 * (c) 2006-2009 Arnaud Ventre, Cedric Morin
 * Distribue sous licence GPL
 *
 */


/**
 * Installation/maj base
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function entravaux_upgrade($nom_meta_base_version,$version_cible){

	$maj = array();

	include_spip('inc/autoriser');
	// seul un webmestre peut activer les travaux sur le site
	// si c'est un autre admin qui active le plugin, il ne fait rien en base
	if (autoriser('travaux')) {
		$maj['create'] = array(
			array('ecrire_meta','entravaux_id_auteur',$GLOBALS['visiteur_session']['id_auteur']),
		);
		include_spip('base/upgrade');
		maj_plugin($nom_meta_base_version, $version_cible, $maj);
	}
	else
		// sans mise a jour de $nom_meta_base_version ce qui fera une erreur dans le panneau plugin
		effacer_meta('entravaux_id_auteur');
}

/**
 * Installation/maj base
 *
 * @param string $nom_meta_base_version
 */
function entravaux_vider_tables($nom_meta_base_version) {

	effacer_meta("entravaux_id_auteur");
	effacer_meta("entravaux_message");
	effacer_meta($nom_meta_base_version);
}



?>