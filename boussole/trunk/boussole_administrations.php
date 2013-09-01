<?php
/**
 * Ce fichier contient les fonctions de création, de mise à jour et de suppression
 * du schéma de données propres au plugin (tables et configuration).
 *
 * @package SPIP\BOUSSOLE\Schema\Installation
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Installation du schéma de données propre au plugin et gestion des migrations suivant
 * les évolutions du schéma.
 *
 * Le schéma comprend des tables et des variables de configuration.
 *
 * @api
 * @see boussole_declarer_tables_principales()
 * @see boussole_declarer_tables_interfaces()
 *
 * @param string $nom_meta_base_version
 * 		Nom de la meta dans laquelle sera rangée la version du schéma
 * @param string $version_cible
 * 		Version du schéma de données en fin d'upgrade
 *
 * @return void
 */
function boussole_upgrade($nom_meta_base_version, $version_cible){

	$maj = array();

	// Configuration par défaut à la première activation du plugin
	$defaut_config_03 = array(
		'client' => array('serveurs_disponibles' =>
							array('spip' => array('url' => 'http://boussole.spip.net'))),
		'serveur' => array('boussoles_disponibles' => array())
	);
	$defaut_config_04 = array(
		'serveur' => array('actif' => '', 'nom' => '')
	);
	$maj['create'] = array(
		array('maj_tables', array('spip_boussoles', 'spip_boussoles_extras')),
		array('ecrire_config', 'boussole', array_merge($defaut_config_03, $defaut_config_04))
	);

	// On ajoute la table des extras et on supprime toutes les boussoles
	// Seule la boussole SPIP sera réinstallée par défaut.
	// Pour les autres il faudra de toute façon adapter la boussole avant de les réinstaller
	$maj['0.2'] = array(
		array('maj02')
	);

	// A partir de ce schéma, le plugin migre ses globales en configuration
	$maj['0.3'] = array(
		array('maj03', $defaut_config_03)
	);

	// A partir de ce schéma, le plugin migre la constante _BOUSSOLE_ALIAS_SERVEUR en configuration
	$maj['0.4'] = array(
		array('maj04', $defaut_config_04)
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);

	// Quelque que soit l'action en cours, on ajoute ou on met à jour systématiquement la boussole SPIP.
	include_spip('inc/client');
	list($ok, $message) = boussole_ajouter('spip', 'spip');
	if (!$ok)
		spip_log("Administrations - Erreur lors de l'ajout de la boussole spip : " . $message, 'boussole' . _LOG_ERREUR);
	else
		spip_log("Administrations - Ajout de la boussole spip ok", 'boussole' . _LOG_INFO);

	spip_log('Installation/mise à jour des tables du plugin','boussole' . _LOG_INFO);
}


/**
 * Suppression de l'ensemble du schéma de données propre au plugin, c'est-à-dire
 * les tables et les variables de configuration.
 *
 * @api
 *
 * @param string $nom_meta_base_version
 * 		Nom de la meta dans laquelle sera rangée la version du schéma
 *
 * @return void
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
 * Migration du schéma 0.1 au 0.2.
 *
 * Ajout de la table `spip_boussoles_extras` et suppression des boussoles autres que
 * la boussole 'spip' car il n'est pas possible de les mettre à jour,
 * leur serveur n'étant pas connu.
 *
 * @return void
 */
function maj02() {

	// Ajout de la table
	maj_tables(array('spip_boussoles_extras'));

	// Suppression des boussoles non "spip"
	include_spip('inc/client');
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
 * Migration du schéma 0.2 au 0.3.
 *
 * Les globales `$serveur_boussoles_disponibles` et `$client_serveurs_disponibles` sont
 * transférées dans des variables de configuration
 *
 * @param array $defaut_config
 * 		Configuration par défaut supplémentaire ajoutée pour ce schéma. Si le site a
 * 		déjà personnalisé les globales la configuration par défaut sera écrasée par
 * 		celle des globales migrées.
 *
 * @return void
 */
function maj03($defaut_config) {

	// On initialise la configuration ajoutée avec celle par défaut
	$config = $defaut_config;

	// Migration des éventuels serveurs configurés autres que "spip"
	if (isset($GLOBALS['client_serveurs_disponibles'])) {
		// On boucle sur tous les serveurs configurés
		foreach($GLOBALS['client_serveurs_disponibles'] as $_serveur => $_infos) {
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

	// Mise à jour de la configuration migrée. Il n'y a pas de configuration existante.
	include_spip('inc/config');
	ecrire_config('boussole', $config);

	spip_log('Maj 0.3 des données du plugin : ' . serialize(lire_config('boussole')),'boussole' . _LOG_INFO);
}


/**
 * Migration du schéma 0.3 au 0.4.
 *
 * La constante `_BOUSSOLE_ALIAS_SERVEUR` est transformée en deux variables de configuration,
 * l'une pour l'activité de la fonction serveur et l'autre pour le nom du serveur.
 *
 * @param array $defaut_config
 * 		Configuration par défaut supplémentaire ajoutée pour ce schéma. Si le site est
 * 		déjà un serveur, la configuration par défaut sera écrasée par celle de la constante migrée.
 *
 * @return void
 */
function maj04($defaut_config) {

	// Initialisation de la configuration migrée avec la configuration existante.
	include_spip('inc/config');
	$config = lire_config('boussole');

	// Migration de l'éventuel serveur installé sur le site
	// -- On met à jour l'activité et le nom du serveur
	$config['serveur']['actif'] = defined('_BOUSSOLE_ALIAS_SERVEUR') ? 'on' : $defaut_config['serveur']['actif'];
	$config['serveur']['nom'] = defined('_BOUSSOLE_ALIAS_SERVEUR') ? _BOUSSOLE_ALIAS_SERVEUR : $defaut_config['serveur']['nom'];

	// Mise à jour en BDD de la confguration migrée
	ecrire_config('boussole', $config);

	spip_log('Maj 0.4 des données du plugin : ' . serialize(lire_config('boussole')),'boussole' . _LOG_INFO);
}

?>
