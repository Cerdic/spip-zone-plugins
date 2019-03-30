<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Import_ics
 *
 * @plugin     Import_ics
 * @copyright  2013
 * @author     Amaury
 * @licence    GNU/GPL
 * @package    SPIP\Import_ics\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip('inc/cextras');
include_spip('base/import_ics');
include_spip('action/editer_objet');
include_spip('inc/autoriser');

/**
 * Fonction d'installation et de mise à jour du plugin Import_ics.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function import_ics_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('maj_tables',array('spip_almanachs', 'spip_almanachs_liens')),
		array('maj_tables',array('spip_evenements')),
		array('sql_alter',"TABLE spip_evenements ADD uid text NOT NULL"),
		array('sql_alter',"TABLE spip_evenements ADD sequence bigint(21) DEFAULT '0' NOT NULL"),
		array('sql_alter',"TABLE spip_evenements ADD last_modified_distant text NOT NULL"),
	);

	$maj['1.0.1'] = array(
		array('maj_tables', array('spip_almanachs')),
	);

	$maj['1.0.2'] = array(
		array('maj_tables', array('spip_almanachs')),
	);
	$maj["1.0.3"] = array(
		array('import_ics_declarer_champs_extras',import_ics_declarer_champs_extras()),
	);
	$maj["1.0.4"] = array(
		array('sql_alter',"TABLE spip_evenements ADD last_modified_distant text NOT NULL"),
	);
	$maj["1.0.5"] = array(
		array('sql_alter',"TABLE spip_almanachs ADD derniere_synchro datetime NOT NULL DEFAULT '0000-00-00 00:00:00'"),
	);
	$maj["1.0.6"] = array(
		array('publier_almanachs_tous')
	);
	$maj["1.0.7"] = array(
		array('dupliquer_decalage')
	);
	$maj["1.0.8"] = array(
		array("recreer_champs_versionnage_distant"),
		array("mettre_a_jour_date_creation")
	);
	$maj["1.0.9"] = array(
		array('sql_alter','TABLE spip_almanachs DROP id_mot'),
		array('effacer_config','import_ics/mot_facultatif'),
		array('effacer_config','import_ics/groupe_mots'),
		array('effacer_config','import_ics/id_mot'),
		array('effacer_config','import_ics/id_groupe')
	);
	$maj["1.0.10"] = array(
		array('sql_alter',"TABLE spip_almanachs ADD derniere_erreur datetime NOT NULL DEFAULT '0000-00-00 00:00:00'"),
	);
	$maj["1.0.11"] = array(
		array('sql_alter',"TABLE spip_almanachs ADD dtend_inclus varchar(10) DEFAULT '0' NOT NULL"),
	);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Import_ics.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function import_ics_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_almanachs");
	sql_drop_table("spip_almanachs_liens");

	sql_alter("TABLE spip_evenements DROP COLUMN uid");
	sql_alter("TABLE spip_evenements DROP COLUMN sequence");
	sql_alter("TABLE spip_evenements DROP COLUMN last_modified_distant");
  cextras_api_vider_tables(import_ics_declarer_champs_extras());
	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('almanach')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('almanach')));
	sql_delete("spip_forum",                 sql_in("objet", array('almanach')));

	effacer_meta($nom_meta_base_version);
}

/**
* Lors du passage en 3.4.0, on duplique la colonne decalage en fonction d'heure d'été / heure d'hiver
**/
function dupliquer_decalage(){
	sql_alter("TABLE spip_almanachs CHANGE decalage decalage_ete tinyint NOT NULL DEFAULT 0");
	sql_alter("TABLE spip_almanachs ADD decalage_hiver tinyint NOT NULL DEFAULT 0 AFTER decalage_ete");
	sql_update("spip_almanachs",array('decalage_hiver'=>'decalage_ete'));
}

/**
* Lors du passage en 3.0, on publie tout les almanachs,
* pour que la rupture de compat ne soit pas trop forte
**/
function publier_almanachs_tous(){
	if ($almanachs = sql_select('id_almanach','spip_almanachs')){
		while ($res = sql_fetch($almanachs)){
			$id_almanach = $res['id_almanach'];
			autoriser_exception('instituer','almanach',$id_almanach);
			objet_instituer('almanach',$id_almanach,array("statut"=>'publie'));
			autoriser_exception('instituer','almanach',$id_almanach,false);
		}
	}
	sql_free($almanachs);
}

/**
* Lors du passage en 3.4.5, on crée les champs uid, sequence et last_modified_distant,
* qu'on avait oublié de déclarer en champs extra
**/
function recreer_champs_versionnage_distant(){
	$trouver_table = charger_fonction('trouver_table', 'base');
	$desc = $trouver_table('evenements');
	$field = $desc['field'];
	if (!isset($field['sequence'])){
		array('sql_alter',"TABLE spip_evenements ADD sequence bigint(21) DEFAULT '0' NOT NULL");
	}
	if (!isset($field['uid'])){
		array('sql_alter',"TABLE spip_evenements ADD uid text NOT NULL");
	}
	if (!isset($field['last_modified_distant'])){
		array('sql_alter',"TABLE spip_evenements ADD last_modified_distant text NOT NULL");
	}
}



/** Lors du passage en 3.4.5, mettre à jour le champs date_creation s'il est égale à 0000-00-00 00:00:00
*
**/
function mettre_a_jour_date_creation(){
	sql_update('spip_evenements', array('date_creation' => 'maj'),'date_creation="0000-00-00 00:00:00"');
}
