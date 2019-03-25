<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


// Installation et mise à jour
function petitesannonces_upgrade($nom_meta_version_base, $version_cible) {
	$maj = array();
	$maj['0.1.0'] = array(
		array('ecrire_config','notifications', array(
			'diffusion_nouveaute_partielle' => 'non',
			'prevenir_auteurs_articles' => 'on',
			'prevenir_auteurs_articles_refus' => 'on',
			'pas_prevenir_publieur' => 'on',
			'prevenir_admins_restreints' => 'on',
			'prevenir_auteurs' => 'on',
			'thread_forum' => 'on',
			'forum_article' => 'on',
			'forums_admins_restreints' => 'on',
			'messagerie' => 'on'
		))
	);
	// Maj du plugin.
	include_spip('base/upgrade');
	maj_plugin($nom_meta_version_base, $version_cible, $maj);
}

// Désinstallation
// function petitesannonces_vider_tables($nom_meta_version_base) {
// 	effacer_meta('petitesannonces');
// 	effacer_meta($nom_meta_version_base);
// }
