<?php
/**
 * Plugin LinkCheck
 * (c) 2013 Benjamin Grapeloux, Guillaume Wauquier
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'installation du plugin et de mise à jour.
 * Vous pouvez :
 * - créer la structure SQL,
 * - insérer du pre-contenu,
 * - installer des valeurs de configuration,
 * - mettre à jour la structure SQL
**/
function linkcheck_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_linkchecks', 'spip_linkchecks_liens')),
		array('ecrire_config', 'linkcheck_dernier_id_objet', 0),
		array('ecrire_config', 'linkcheck_dernier_id_lien', 0),
		array('ecrire_config', 'linkcheck_dernier_objet', 0),
		array('ecrire_config', 'linkcheck_etat_parcours', false),
		array('ecrire_config', 'linkcheck/notifier_courriel', 'on'),
		array('ecrire_config', 'linkcheck/afficher_alerte', 'on')
	);

	/**
	 * Ajout du champ redirection sur spip_linkchecks
	 */
	$maj['1.0.1'] = array(
		array('maj_tables', array('spip_linkchecks', 'spip_linkchecks_liens'))
	);

	/**
	 * Ajout du champ publie sur spip_linkchecks_liens
	 */
	$maj['1.4.0'] = array(
		array('maj_tables', array('spip_linkchecks', 'spip_linkchecks_liens'))
	);

	/**
	 * Ajout de l'autoincrement sur la table spip_linkckecks si manquant
	 * Relancer la première récupération de liens
	 */
	$maj['1.4.4'] = array(
		array('linkcheck_maj_autoinc'),
		array('linkcheck_init')
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


function linkcheck_maj_autoinc() {
	sql_alter('TABLE spip_linkchecks MODIFY COLUMN id_linkcheck bigint(21) AUTO_INCREMENT');
}

function linkcheck_init() {
	$reinit = charger_fonction('linkcheck_reinit', 'action');
	$reinit();
}

/**
 * Fonction de désinstallation du plugin.
 * Vous devez :
 * - nettoyer toutes les données ajoutées par le plugin et son utilisation
 * - supprimer les tables et les champs créés par le plugin.
**/
function linkcheck_vider_tables($nom_meta_base_version) {
	sql_drop_table('spip_linkchecks');
	sql_drop_table('spip_linkchecks_liens');

	effacer_meta($nom_meta_base_version);
	effacer_meta('linkcheck_dernier_id_objet');
	effacer_meta('linkcheck_dernier_id_lien');
	effacer_meta('linkcheck_dernier_objet');
	effacer_meta('linkcheck_etat_parcours');
	effacer_meta('linkcheck/notifier_courriel');
	effacer_meta('linkcheck/afficher_alerte');
}
