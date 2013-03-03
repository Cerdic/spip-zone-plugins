<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Installation du schéma de données propre au plugin en tenant compte des évolutions
 *
 * @param $nom_meta_base_version
 * @param $version_cible
 */
function boussole_upgrade($nom_meta_base_version, $version_cible){

	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_boussoles', 'spip_boussoles_extras'))
	);

	// On ajoute la table des extras et on supprime toutes les boussoles
	// Seule la boussole SPIP sera réinstallée par défaut.
	// Pour les autres il faudra de toute façon adapter la boussole avant de les réinstaller
	$maj['0.2'] = array(
		array('maj_tables', array('spip_boussoles_extras')),
		array('nettoyer_donnees_boussole')
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);

	// On ajoute la boussole SPIP par defaut.
	if (!isset($GLOBALS['meta']['boussole_infos_spip'])) {
		include_spip('inc/deboussoler');
		list($ok, $message) = boussole_ajouter('spip', 'spip');
		if (!$ok)
			spip_log("Administrations - Erreur lors de l'ajout de la boussole spip : " . $message, 'boussole' . _LOG_ERREUR);
		else
			spip_log("Administrations - Ajout de la boussole spip ok", 'boussole' . _LOG_INFO);
	}

	spip_log('Installation des tables du plugin','boussole' . _LOG_INFO);

}

/**
 * Suppression de l'ensemble du schéma de données propre au plugin
 *
 * @param $nom_meta_base_version
 */
function boussole_vider_tables($nom_meta_base_version) {
	// On nettoie les metas de mises a jour des boussoles
	nettoyer_donnees_boussole();

	// on efface ensuite la table et la meta habituelle designant la version du plugin
	sql_drop_table("spip_boussoles");
	sql_drop_table("spip_boussoles_extras");
	effacer_meta($nom_meta_base_version);

	spip_log('Désinstallation des tables du plugin','boussole' . _LOG_INFO);
}

/**
 * Suppression de l'ensemble des données des tables et metas propres au plugin boussole
 *
 */
function nettoyer_donnees_boussole() {
	$alias = array();

	$akas_boussole = sql_allfetsel('aka_boussole', 'spip_boussoles', array(), 'aka_boussole');
	if ($akas_boussole) {
		foreach (array_map('reset', $akas_boussole) as $_aka_boussole) {
			$alias[] = 'boussole_infos_' . $_aka_boussole;
		}
		sql_delete('spip_meta', sql_in('nom', $alias));
	}

	sql_delete('spip_boussoles');
}

?>
