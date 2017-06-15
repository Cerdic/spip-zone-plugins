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
	);

	$maj['create'] = array(
		array('maj_tables',array('spip_noisettes_pages', 'spip_noisettes')),
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
function noizetier_vider_tables($nom_meta_version_base) {
	// On efface les tables du plugin
	sql_drop_table('spip_noizetier_pages');
	sql_drop_table('spip_noisettes');

	// On efface la version enregistrée du schéma des données du plugin
	effacer_meta($nom_meta_version_base);
	// On efface la configuration du plugin
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
 * Migration du schéma 0.5 au 0.6.
 *
 * Les actions effectuées sont les suivantes:
 * - ajout de la tables `spip_noisettes_pages` pour stocker l'ensemble des pages et compositions
 * explicites et virtuelles.
 * - ajout du champ `balise` à la table `spip_noisettes` pour déterminer si le noiZetier doit inclure
 * la noisette concernée dans un div englobant.
 * - mise à jour de la taille des champs type, composition et objet dans la table `spip_noisettes`
 * - ajout d'une liste de variables de configuration initialisées
 * - transfert des compositions virtuelles de la meta `noizetier_compositions` dans la nouvelle
 * table `spip_noisettes_pages` et suppression définitive de la meta.
 *
 * @param array $config_defaut
 * 		Tableau des variables de configuration intialisées.
 *
 * @return void
 */
function maj_060($config_defaut) {

	// Ajout de la tables des pages du noizetier qui contiendra pages et compositions qu'elles soient
	// explicites ou virtuelles.
	include_spip('base/create');
	maj_tables('spip_noizetier_pages');

	// Ajout de la colonne 'balise' qui indique pour chaque noisette si le noiZetier doit l'inclure dans un div
	// englobant ou pas. Le champ prend les valeurs 'on', '' ou 'defaut' qui indique qu'il faut prendre
	// en compte la valeur configurée par défaut (configuration du noizetier).
	sql_alter("TABLE spip_noisettes ADD balise varchar(6) DEFAULT 'defaut' NOT NULL AFTER parametres");
	sql_alter("TABLE spip_noisettes ADD maj timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL");
	// Mise à jour des tailles des colonnes type, composition et objet pour cohérence
	sql_alter("TABLE spip_noisettes MODIFY type varchar(127) NOT NULL");
	sql_alter("TABLE spip_noisettes MODIFY composition varchar(127) NOT NULL");
	sql_alter("TABLE spip_noisettes MODIFY balise varchar(25) NOT NULL");

	// Mise à jour de la configuration du plugin
	include_spip('inc/config');
	$config = lire_config('noizetier', array());
	if ($config and isset($config['objets_noisettes'])) {
		$config_defaut['objets_noisettes'] = $config['objets_noisettes'];
	}
	ecrire_config('noizetier', $config_defaut);

	// Insertion de la liste des compositions virtuelles dans la table 'spip_noisettes_pages'
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
		// Insertion dans la table spip_noisettes_pages
		if ($compositions_060) {
			sql_insertq_multi('spip_noizetier_pages', $compositions_060);
		}
	}
	// On efface la meta des compositions maintenant que celles-ci sont stockées
	// dans une table dédiées aux pages du noizetier
	effacer_meta('noizetier_compositions');
}