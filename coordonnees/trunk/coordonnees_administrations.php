<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Coordonnees
 *
 * @plugin     Coordonnees
 * @copyright  2013
 * @author     Marcimat / Ateliers CYM
 * @licence    GNU/GPL
 * @package    SPIP\Coordonnees\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin Coordonnees
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function coordonnees_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	$maj['create'] = array(
		array('maj_tables', array('spip_adresses')),
		array('maj_tables', array('spip_adresses_liens')),
		array('maj_tables', array('spip_numeros')),
		array('maj_tables', array('spip_numeros_liens')),
		array('maj_tables', array('spip_emails')),
		array('maj_tables', array('spip_emails_liens')),
		array('ecrire_meta', 'coordonnees', serialize(array('objets'=>array('spip_auteurs'))))
	);
	$maj['1.1'] = array(
		array('sql_update', array(array("voie" => "CONCAT(numero, ' ', voie)"),array("numero IS NOT NULL", "numero <> ''"))),
		array('sql_alter', "TABLE spip_adresses DROP COLUMN numero")
	);

	$maj['1.2'] = array(
		array('sql_alter', 'TABLE spip_adresses CHANGE type_adresse titre varchar(255) not null default ""'),
		array('sql_alter', 'TABLE spip_numeros CHANGE type_numero titre varchar(255) not null default ""'),
		array('sql_alter', 'TABLE spip_emails CHANGE type_email titre varchar(255) not null default ""')
	);

	// On passe les pays en code ISO, beaucoup plus génériques que les ids SQL
	$maj['1.3'] = array(
		array('sql_alter', 'TABLE spip_adresses ADD pays_code varchar(2) not null default ""'),
		array('coordonnees_upgrade_1_3'),
		array('sql_alter', 'TABLE spip_adresses DROP pays'),
		array('sql_alter', 'TABLE spip_adresses CHANGE pays_code pays varchar(2) not null default ""'),
	);

	// On avait supprime les types, mais ils reviennent en force mais dans les LIENS
	$maj['1.4'] = array(
			array('sql_alter', 'TABLE spip_adresses_liens ADD type varchar(25) not null default ""'),
			array('sql_alter', 'TABLE spip_adresses_liens DROP PRIMARY KEY'),
			array('sql_alter', 'TABLE spip_adresses_liens ADD PRIMARY KEY (id_adresse, id_objet, objet, type)'),
			array('sql_alter', 'TABLE spip_numeros_liens ADD type varchar(25) not null default ""'),
			array('sql_alter', 'TABLE spip_numeros_liens DROP PRIMARY KEY'),
			array('sql_alter', 'TABLE spip_numeros_liens ADD PRIMARY KEY (id_numero, id_objet, objet, type)'),
			array('sql_alter', 'TABLE spip_emails_liens ADD type varchar(25) not null default ""'),
			array('sql_alter', 'TABLE spip_emails_liens DROP PRIMARY KEY'),
			array('sql_alter', 'TABLE spip_emails_liens ADD PRIMARY KEY (id_email, id_objet, objet, type)'),

			);
	// mettre les auteurs par defaut comme objet «coordonnable»
	$maj['1.5'] = array(
		array('ecrire_meta','coordonnees', serialize(array('objets'=>array('auteur')))),
	);

	// ajout du champs region a la table adresses
	$maj['1.6'] = array(
		array('maj_tables', array('spip_adresses')),
	);

	// migration de certaines valeurs pour pouvoir faire fonctionner les selecteurs pendant l'edition
	//!\ comme on n'est pas certain de tous les migrer il y a donc rupture de compatibilite ? :-S
	$maj['1.7'] = array(
		array('sql_updateq', "spip_adresses_liens", array('type'=>'work'), "LOWER(type) LIKE 'pro%'"),
		array('sql_updateq', "spip_numeros_liens", array('type'=>'work'), "LOWER(type) LIKE 'pro%'"),
		array('sql_updateq', "spip_adresses_liens", array('type'=>'home'), "LOWER(type) LIKE 'perso%'"),
		array('sql_updateq', "spip_adresses_liens", array('type'=>'home'), "LOWER(type) LIKE 'dom%'"),
		array('sql_updateq', "spip_numeros_liens", array('type'=>'home'), "LOWER(type) LIKE 'perso%'"),
		array('sql_updateq', "spip_numeros_liens", array('type'=>'cell'), "LOWER(type) LIKE 'cel%'"),
		array('sql_updateq', "spip_numeros_liens", array('type'=>'cell'), "LOWER(type) LIKE 'mob%'"),
	);

	// Definition des tables principales par declarer_tables_objets_sql au lieu de declarer_tables_principales
	$maj['1.8'] = array(
		array('maj_tables', array('spip_adresses', 'spip_adresses_liens', 'spip_numeros', 'spip_numeros_liens', 'spip_emails', 'spip_emails_liens')),
	);
	// Oublié le champ 'type' dans les tables de liens
	$maj['1.8.1'] = array(
		array('maj_tables', array('spip_adresses_liens', 'spip_numeros_liens', 'spip_emails_liens')),
	);

	// Metas : conversion des objets «coordonnables» : on utilise les noms des tables (auteur -> spip_auteurs)
	$maj['1.8.2']= array(
		array('coordonnees_upgrade_1_8_2'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Coordonnees
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function coordonnees_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_adresses");
	sql_drop_table("spip_adresses_liens");
	sql_drop_table("spip_numeros");
	sql_drop_table("spip_numeros_liens");
	sql_drop_table("spip_emails");
	sql_drop_table("spip_emails_liens");

	effacer_meta('coordonnees');
	effacer_meta($nom_meta_base_version);
}


/**
 * Fonction mise a jour du plugin Coordonnees vers 1.3
 * @return void
**/
function coordonnees_upgrade_1_3() {
	// On parcourt les adresses pour remplir le code du pays
	$adresses = sql_allfetsel('id_adresse,pays', 'spip_adresses');
	if ($adresses and is_array($adresses)){
		foreach ($adresses as $adresse){
			$ok &= sql_update(
				'spip_adresses',
				array('pays_code' => '(SELECT code FROM spip_pays WHERE id_pays='.intval($adresse['pays']).')'),
				'id_adresse='.intval($adresse['id_adresse'])
			);
		}
	}
}


/**
 * Fonction mise a jour du plugin Coordonnees vers 1.8.2
 * Metas : conversion des objets «coordonnables» : on utilise les noms des tables (auteur -> spip_auteurs)
 * @return void
**/
function coordonnees_upgrade_1_8_2() {
	include_spip('inc/config');
	if ( $objets = lire_config('coordonnees/objets', null, true) AND is_array($objets) AND count($objets) > 0 ) {
		foreach ($objets as $objet) $objets_sql[] = table_objet_sql($objet);
		effacer_config('coordonnees/objets');
		ecrire_config('coordonnees/objets', $objets_sql);
	}
}

?>
