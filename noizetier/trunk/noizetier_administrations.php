<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/meta');

// Installation et mise à jour
function noizetier_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	// Configurations par défaut
	$config_060 = array(
		'objets_noisettes' => array(),
		'balise_noisette' => 'on',
		'ajax_noisette' => 'on',
	);

	$maj['create'] = array(
		array('maj_tables',array('spip_noisettes')),
		array('ecrire_config', 'noizetier', $config_060),
	);

	$maj['0.2.0'] = array(
		array('maj_tables',array('spip_noisettes')),
	);

	$maj['0.3.0'] = array(
		array('sql_alter','TABLE spip_noisettes DROP COLUMN contexte'),
	);

	$maj['0.4.0'] = array(
		array('maj_tables',array('spip_noisettes')),
	);
	
	$maj['0.5.0'] = array(
		array('maj_tables',array('spip_noisettes')),
		array('sql_alter', 'TABLE spip_noisettes ADD INDEX (type(255))'),
		array('sql_alter', 'TABLE spip_noisettes ADD INDEX (composition(255))'),
		array('sql_alter', 'TABLE spip_noisettes ADD INDEX (bloc(255))'),
		array('sql_alter', 'TABLE spip_noisettes ADD INDEX (noisette(255))'),
		array('sql_alter', 'TABLE spip_noisettes ADD INDEX (objet)'),
		array('sql_alter', 'TABLE spip_noisettes ADD INDEX (id_objet)'),
	);

	$maj['0.6.0'] = array(
		array('maj_060', $config_060),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

// Désinstallation
function noizetier_vider_tables($nom_meta_version_base) {
	// On efface les tables du plugin
	sql_drop_table('spip_noisettes');

	// On efface la version enregistrée
	effacer_meta($nom_meta_version_base);
	// On efface les compositions enregistrées
	effacer_meta('noizetier_compositions');
	// On efface la configuration
	effacer_meta('noizetier');

	// Effacer les fichiers du cache créés par le noizetier
	include_spip('inc/flock');
	include_spip('noizetier_fonctions');
	supprimer_fichier(_CACHE_AJAX_NOISETTES);
	supprimer_fichier(_CACHE_CONTEXTE_NOISETTES);
	supprimer_fichier(_CACHE_INCLUSIONS_NOISETTES);
	supprimer_fichier(_CACHE_DESCRIPTIONS_NOISETTES);
}

/**
 * Transformer le tableau des compositions virtuelles stocké en meta et ajouter les
 * valeurs par défaut des paramètres de configuration.
 * Jusqu'au schéma 0.5.0 le tableau était de la forme [$type][$composition].
 * A partir du schéma 0.6.0 le tableau prend la forme [$type-$composition].
 *
 */
function maj_060($config_defaut) {

	// Ajout de la colonne div qui indique pour chaque noisette si le noiZetier doit l'inclure dans un div
	// englobant ou pas. Le champ prend les valeurs 'on', '' ou 'defaut' qui indique qu'il faut prendre
	// en compte la valeur configurée par défaut pour toutes les noisettes.
	sql_alter("TABLE spip_noisettes ADD balise varchar(6) DEFAULT 'defaut' NOT NULL AFTER parametres");

	// Mise à jour de la configuration du plugin
	include_spip('inc/config');
	$config = lire_config('noizetier', array());
	if ($config and isset($config['objets_noisettes'])) {
		$config_defaut['objets_noisettes'] = $config['objets_noisettes'];
	}
	ecrire_config('noizetier', $config_defaut);

	// Mise à jour de la liste des compositions virtuelles
	$compositions = lire_config('noizetier_compositions', array());
	if ($compositions) {
		// On transforme le tableau de [type][composition] en [type-composition]
		$compositions_060 = array();
		foreach ($compositions as $_type => $_compositions) {
			foreach ($_compositions as $_composition => $_description) {
				$compositions_060["${_type}-${_composition}"] = $_description;
			}
		}
		ecrire_config('noizetier_compositions', $compositions_060);
	}
}