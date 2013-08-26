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

	// Configuration par défaut à la première activation du plugin
	$defaut_config = array(
		'client' => array('serveurs_disponibles' =>
							array('spip' => array('url' => 'http://boussole.spip.net'))),
		'serveur' => array('boussoles_disponibles' => array())
	);
	$maj['create'] = array(
		array('maj_tables', array('spip_boussoles', 'spip_boussoles_extras')),
		array('ecrire_config', 'boussole', $defaut_config)
	);

	// On ajoute la table des extras et on supprime toutes les boussoles
	// Seule la boussole SPIP sera réinstallée par défaut.
	// Pour les autres il faudra de toute façon adapter la boussole avant de les réinstaller
	$maj['0.2'] = array(
		array('maj_tables', array('spip_boussoles_extras')),
		array('maj02')
	);

	// A partir de ce schéma, le plugin migre ses globales en configuration
	$maj['0.3'] = array(
		array('maj03', $defaut_config)
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);

	// Quelque que soit l'action en cours, on ajoute ou on met à jour systématiquement la boussole SPIP.
	include_spip('inc/deboussoler');
	list($ok, $message) = boussole_ajouter('spip', 'spip');
	if (!$ok)
		spip_log("Administrations - Erreur lors de l'ajout de la boussole spip : " . $message, 'boussole' . _LOG_ERREUR);
	else
		spip_log("Administrations - Ajout de la boussole spip ok", 'boussole' . _LOG_INFO);

	spip_log('Installation/mise à jour des tables du plugin','boussole' . _LOG_INFO);
}


/**
 * Suppression de l'ensemble du schéma de données propre au plugin
 *
 * @param $nom_meta_base_version
 */
function boussole_vider_tables($nom_meta_base_version) {
	// On nettoie les metas de mises a jour des boussoles
	$meta = array();
	$akas_boussole = sql_allfetsel('aka_boussole', 'spip_boussoles', array(), 'aka_boussole');
	if ($akas_boussole) {
		foreach (array_map('reset', $akas_boussole) as $_aka_boussole) {
			$meta[] = 'boussole_infos_' . $_aka_boussole;
		}
		if ($meta)
			sql_delete('spip_meta', sql_in('nom', $meta));
	}

	// on efface ensuite la table et la meta habituelle designant la version du plugin
	sql_drop_table("spip_boussoles");
	sql_drop_table("spip_boussoles_extras");

	// on efface la meta de configuration du plugin
	effacer_meta('boussole');

	// on efface la meta du schéma du plugin
	effacer_meta($nom_meta_base_version);

	spip_log('Désinstallation des données du plugin','boussole' . _LOG_INFO);
}


/**
 * Suppression des boussoles autres que la boussole spip car on ne peut pas les mettre à jour,
 * leur serveur n'étant pas connu
 *
 */
function maj02() {
	include_spip('inc/deboussoler');

	$akas_boussole = sql_allfetsel('aka_boussole', 'spip_boussoles', array(), 'aka_boussole');
	if ($akas_boussole) {
		foreach (array_map('reset', $akas_boussole) as $_aka_boussole) {
			if ($_aka_boussole != 'spip')
				supprimer_boussole($_aka_boussole);
		}
	}
	spip_log('Maj 0.2 des données du plugin','boussole' . _LOG_INFO);
}


/**
 * Suppression des boussoles autres que la boussole spip car on ne peut pas les mettre à jour,
 * leur serveur n'étant pas connu
 *
 */
function maj03($defaut_config) {

	// On initialise la configuration du plugin avec celle par défaut
	$config = $defaut_config;

	// Migration des éventuels serveurs configurés autres que "spip"
	if (isset($GLOBALS['client_serveurs_disponibles'])) {
		// On boucle sur tous les serveurs configurés
		foreach($GLOBALS['client_serveurs_disponibles'] as $_serveur => $_infos) {
			$casier = array_shift(explode('_', $config));
			if ($_serveur != 'spip') {
				if (isset($_infos['api'])) {
					$config['client']['serveurs_disponibles'][$_serveur]['url'] = str_replace('/spip.php?action=[action][arguments]', '', $_infos['api']);
				}
				else if (isset($_infos['url'])) {
					$config['client']['serveurs_disponibles'][$_serveur] = $_infos;
				}
			}
		}
		// Suppression de la globale devenue inutile
		unset($GLOBALS['client_serveurs_disponibles']);
	}

	// Migration des éventuelles boussoles manuelles hébergés par le serveur
	if (isset($GLOBALS['serveur_boussoles_disponibles'])) {
		// On boucle sur tous les serveurs configurés
		foreach($GLOBALS['serveur_boussoles_disponibles'] as $_boussole => $_infos) {
			if ($_infos['prefixe'] == '') {
				$config['serveur']['boussoles_disponibles'][$_boussole] = $_infos;
			}
		}
		// Suppression de la globale devenue inutile
		unset($GLOBALS['serveur_boussoles_disponibles']);
	}

	// Mise à jour de la configuration migrée
	include_spip('inc/config');
	ecrire_config('boussole', $config);

	spip_log('Maj 0.3 des données du plugin','boussole' . _LOG_INFO);
}

?>
