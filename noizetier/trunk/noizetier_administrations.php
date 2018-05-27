<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Installation du schéma de données propre au plugin et gestion des migrations suivant
 * les évolutions du schéma.
 *
 * Le schéma comprend des tables et des variables de configuration propres au plugin.
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
function noizetier_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	// Configurations par défaut
	$config_060 = array(
		'objets_noisettes' => array(),
		'balise_noisette' => 'on',
		'ajax_noisette' => 'on',
		'profondeur_max' => 1,
	);

	$maj['create'] = array(
		array('maj_tables',array('spip_noizetier_pages', 'spip_types_noisettes', 'spip_noisettes')),
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
function noizetier_vider_tables($nom_meta_base_version) {

	// On efface les tables du plugin
	sql_drop_table('spip_noizetier_pages');
	sql_drop_table('spip_types_noisettes');
	sql_drop_table('spip_noisettes');

	// On efface la version enregistrée du schéma des données du plugin
	effacer_meta($nom_meta_base_version);
	// On efface la configuration du plugin
	effacer_meta('noizetier');
}

/**
 * Migration du schéma 0.5 au 0.6.
 *
 * Les actions effectuées sont les suivantes:
 * - ajout de la tables `spip_noisettes_pages` pour stocker l'ensemble des pages et compositions
 *   explicites et virtuelles.
 * - ajout du champ `balise` à la table `spip_noisettes` pour déterminer si le noiZetier doit inclure
 *   la noisette concernée dans un div englobant et ajout du champ plugin pour étendre le stockage au-delà
 *   du noiZetier.
 * - mise à jour de la taille des champs type, composition et objet dans la table `spip_noisettes`
 * - ajout d'une liste de variables de configuration initialisées
 * - transfert des compositions virtuelles de la meta `noizetier_compositions` dans la nouvelle
 *   table `spip_noizetier_pages` et suppression définitive de la meta.
 *
 * @param array $config_defaut
 * 		Tableau des variables de configuration intialisées.
 *
 * @return void
 */
function maj_060($config_defaut) {

	// Ajout de la tables des pages du noizetier qui contiendra pages et compositions qu'elles soient
	// explicites ou virtuelles et de la tables des types de noisette.
	include_spip('base/create');
	maj_tables(array('spip_noizetier_pages', 'spip_types_noisettes'));

	// Modification de la table spip_noisettes
	// -- Ajout de la colonne 'balise' qui indique pour chaque noisette si le noiZetier doit l'inclure dans un div
	//    englobant ou pas. Le champ prend les valeurs 'on', '' ou 'defaut' qui indique qu'il faut prendre
	//    en compte la valeur configurée par défaut (configuration du noizetier).
	// -- Ajout de la colonne 'plugin' qui vaut 'noizetier' pour ce plugin.
	// -- Ajout de la colonne 'id_conteneur'.
	// -- Ajout de la colonne 'est_conteneur' toujours à la valeur 'non' car il n'existe pas de noisette de ce type
	//    dans les versions précédentes du plugin.
	sql_alter("TABLE spip_noisettes ADD plugin varchar(30) DEFAULT '' NOT NULL AFTER id_noisette");
	sql_alter("TABLE spip_noisettes ADD id_conteneur varchar(255) DEFAULT '' NOT NULL AFTER plugin");
	sql_alter("TABLE spip_noisettes ADD est_conteneur varchar(3) DEFAULT 'non' NOT NULL AFTER type_noisette");
	sql_alter("TABLE spip_noisettes ADD balise varchar(6) DEFAULT 'defaut' NOT NULL AFTER parametres");
	// -- Changement du nom du champ 'rang' en 'rang_noisette'
	sql_alter("TABLE spip_noisettes CHANGE rang rang_noisette smallint DEFAULT 1 NOT NULL");
	// -- Changement du nom du champ 'noisette' en 'type_noisette' et de sa taille
	sql_alter("TABLE spip_noisettes CHANGE noisette type_noisette varchar(255) DEFAULT '' NOT NULL");
	// -- Suppression des index pour des colonnes dont on va modifier la taille ou le type
	sql_alter("TABLE spip_noisettes DROP INDEX type");
	sql_alter("TABLE spip_noisettes DROP INDEX composition");
	sql_alter("TABLE spip_noisettes DROP INDEX bloc");
	sql_alter("TABLE spip_noisettes DROP INDEX noisette");
	// Mise à jour des tailles des colonnes type, composition et objet pour cohérence
	sql_alter("TABLE spip_noisettes MODIFY bloc varchar(255) DEFAULT '' NOT NULL");
	sql_alter("TABLE spip_noisettes MODIFY type varchar(127) NOT NULL");
	sql_alter("TABLE spip_noisettes MODIFY composition varchar(127) NOT NULL");
	sql_alter("TABLE spip_noisettes MODIFY objet varchar(25) NOT NULL");
	// -- Création des index détruits précédemment et des nouveaux index plugin et conteneur
	sql_alter("TABLE spip_noisettes ADD INDEX type (type)");
	sql_alter("TABLE spip_noisettes ADD INDEX composition (composition)");
	sql_alter("TABLE spip_noisettes ADD INDEX bloc (bloc)");
	sql_alter("TABLE spip_noisettes ADD INDEX type_noisette (type_noisette)");
	sql_alter("TABLE spip_noisettes ADD INDEX plugin (plugin)");
	sql_alter("TABLE spip_noisettes ADD INDEX id_conteneur (id_conteneur)");
	sql_alter("TABLE spip_noisettes ADD INDEX rang_noisette (rang_noisette)");
	// -- Remplissage de la nouvelle colonne plugin avec la valeur 'noizetier'
	//    et de la colonne id_conteneur à partir des autres colonnes existantes.
	$select = array('id_noisette', 'type', 'composition', 'objet', 'id_objet', 'bloc');
	$from = 'spip_noisettes';
	$noisettes = sql_allfetsel($select, $from);
	if ($noisettes) {
		include_spip('ncore/noizetier');
		foreach ($noisettes as $_cle => $_noisette) {
			// C'est le plugin noizetier
			$noisettes[$_cle]['plugin'] = 'noizetier';
			// On calcule le conteneur au format tableau et on appelle la fonction de service de construction
			// de l'id du conteneur
			$conteneur = array();
			if (!empty($_noisette['objet']) and !empty($_noisette['id_objet']) and intval($_noisette['id_objet'])) {
				$conteneur['objet'] = $_noisette['objet'];
				$conteneur['id_objet'] = $_noisette['id_objet'];
				$conteneur['squelette'] = $_noisette['bloc'];
			}
			else {
				$page = $_noisette['type'] . ($_noisette['composition'] ? "-{$_noisette['composition']}" : '');
				$conteneur['squelette'] = "{$_noisette['bloc']}/${page}";
			}
			$noisettes[$_cle]['id_conteneur'] = noizetier_conteneur_identifier('noizetier', $conteneur);
		}
		sql_replace_multi($from, $noisettes);
	}

	// Mise à jour de la configuration du plugin
	include_spip('inc/config');
	$config = lire_config('noizetier', array());
	if ($config and isset($config['objets_noisettes'])) {
		$config_defaut['objets_noisettes'] = $config['objets_noisettes'];
	}
	ecrire_config('noizetier', $config_defaut);

	// Suppression des caches devenus inutiles
	include_spip('inc/flock');
	supprimer_fichier(_DIR_CACHE . 'noisettes_descriptions.php');
	supprimer_fichier(_DIR_CACHE . 'noisettes_ajax.php');
	supprimer_fichier(_DIR_CACHE . 'noisettes_contextes.php');
	supprimer_fichier(_DIR_CACHE . 'noisettes_inclusions.php');

	// Déplacement de la liste des compositions virtuelles dans la table 'spip_noisettes_pages'
	$compositions = lire_config('noizetier_compositions', array());
	if ($compositions) {
		$compositions_060 = array();
		foreach ($compositions as $_type => $_compositions) {
			foreach ($_compositions as $_composition => $_description) {
				// Type et composition (ne sont jamais vides)
				$_description['type'] = $_type;
				$_description['composition'] = $_composition;
				// Construction de l'identifiant de la page
				$_description['page'] = "${_type}-${_composition}";
				// Nom par défaut si non précisé (identifiant de la page)
				if (empty($_description['nom'])) {
					$_description['nom'] = $_description['page'];
				}
				// Icone par défaut si non précisé
				if (empty($_description['icon'])) {
					$_description['icon'] = 'composition-24.png';
				}
				// Traitement des necessite pour identifier l'activité de la page
				$_description['est_active'] = 'oui';
				if (!empty($_description['necessite'])) {
					foreach ($_description['necessite'] as $_plugin_necessite) {
						if (!defined('_DIR_PLUGIN_' . strtoupper($_plugin_necessite))) {
							$_description['est_active'] = 'non';
							break;
						}
					}
				}
				// Blocs, necessite et branche: des tableaux à sérialiser
				$_description['blocs_exclus'] = isset($_description['blocs_exclus'])
					? serialize($_description['blocs_exclus'])
					: serialize(array());
				$_description['necessite'] = isset($_description['necessite'])
					? serialize($_description['necessite'])
					: serialize(array());
				$_description['branche'] = isset($_description['branche'])
					? serialize($_description['branche'])
					: serialize(array());
				// Indicateur de type d'objet
				include_spip('base/objets');
				$tables_objets = array_keys(lister_tables_objets_sql());
				$_description['est_page_objet'] = in_array(table_objet_sql($_type), $tables_objets) ? 'oui' : 'non';
				// Indicateur de composition virtuelle
				$description['est_virtuelle'] = 'oui';
				$compositions_060[] = $_description;
			}
		}
		if ($compositions_060) {
			if (sql_preferer_transaction()) {
				sql_demarrer_transaction();
			}
			sql_insertq_multi('spip_noizetier_pages', $compositions_060);
			if (sql_preferer_transaction()) {
				sql_terminer_transaction();
			}
		}
	}
	// On efface la meta des compositions maintenant que celles-ci sont stockées
	// dans une table dédiées aux pages du noizetier
	effacer_meta('noizetier_compositions');
}
