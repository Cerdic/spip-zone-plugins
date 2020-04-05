<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function jeux_upgrade($nom_meta_base_version,$version_cible){
    include_spip('inc/meta');
    include_spip('base/upgrade');
    include_spip('base/create');
	include_spip('base/abstract_sql');
    include_spip('base/jeux_tables');
    $maj = array();
    $maj['create']  = array(array('creer_base'),array('maj_tables',array('spip_jeux')));
    $maj['0.11']    = array(array('jeux_upgrade_0_11'));
    $maj['0.12']    = array(array('jeux_upgrade_0_12'));
    $maj['0.13']    = array(array('jeux_upgrade_0_13'));
    $maj['0.14']    = array(array('jeux_upgrade_0_14'));
    $maj['0.15']    = array(array('jeux_upgrade_0_15'));
    $maj['0.16']    = array(array('jeux_upgrade_0_16'));
    $maj['0.17']    = array(array('jeux_upgrade_0_17'));
    $maj['0.18']    = array(array('jeux_upgrade_0_18'));
    $maj['0.19']    = array(array('jeux_upgrade_0_19'));
    $maj['0.20']    = array(array('jeux_upgrade_0_20'));
    maj_plugin($nom_meta_base_version,$version_cible,$maj);
    
}

function jeux_vider_tables($nom_meta_base_version) {
	include_spip('base/abstract_sql');
	sql_drop_table('spip_jeux');
	sql_drop_table("spip_jeux_resultats");
	effacer_meta($nom_meta_base_version);

}

function jeux_updgrade_0_11(){
	// ajout du champ 'nom' a la table spip_jeux, si pas deja existant
	$desc = sql_showtable('spip_jeux', true);
	if (!isset($desc['field']['nom'])){
		sql_alter("TABLE spip_jeux ADD `nom` text DEFAULT '' NOT NULL AFTER `date`");
		// ajout d'un nom par defaut aux jeux existants
		$res = sql_select(array('id_jeu'),array('spip_jeux'));
		$sans = _T('jeux:sans_type');
		while ($row = sql_fetch($res))
			sql_updateq('spip_jeux', array('nom'=>'$sans'), "id_jeu=".$row['id_jeu']);
	}
	// ajout du champ 'titre' a la table spip_jeux, si pas deja existant
	if (!isset($desc['field']['titre'])){
		sql_alter("TABLE spip_jeux ADD `titre` text DEFAULT '' NOT NULL AFTER `nom`");
		// ajout d'un titre par defaut aux jeux existants
		$res = sql_select(array('id_jeu'), 'spip_jeux');
		$sans = _T('jeux:sans_titre_prive');
		while ($row = sql_fetch($res))
			sql_update('spip_jeux', array('titre'=>$sans), "id_jeu=".$row['id_jeu']);
	}
}
		
function jeux_upgrade_0_12(){
	// changement de noms 'titre' => 'titre_prive' et 'nom' => 'type_jeu'
	$desc = sql_showtable('spip_jeux', true);
	if (isset($desc['field']['titre']))
		sql_alter('TABLE spip_jeux CHANGE `titre` `titre_prive` TEXT');
	if (isset($desc['field']['nom']))
		sql_alter('TABLE spip_jeux CHANGE `nom` `type_jeu` TEXT');
}
		
function jeux_upgrade_0_13(){
	// suppression de 'titre' et 'nom'
	$desc = sql_showtable('spip_jeux', true);
	if (isset($desc['field']['titre']))
		sql_alter('TABLE spip_jeux DROP `titre`');
	if (isset($desc['field']['nom']))
		sql_alter('TABLE spip_jeux DROP `nom`');
}
function jeux_upgrade_0_14(){
	// ajout de 'total'
	$desc = sql_showtable("spip_jeux_resultats", true);
	if (!isset($desc['field']['total']))
	sql_alter('TABLE spip_jeux_resultats ADD `total` int(12) NOT NULL DEFAULT 0 AFTER `resultat_long`');
}
function jeux_upgrade_0_15(){
	// ajout de 'resultat_unique'
	$desc = sql_showtable('spip_jeux', true);
	if (!isset($desc['field']['resultat_unique']))
			sql_alter("TABLE spip_jeux ADD `resultat_unique` NOT NULL DEFAULT 'non'");
}
function jeux_upgrade_0_16(){
	// fusion de 'resultat_unique' et 'enregistrer_resultat' vers 'type_resultat'
	// types d'enregistrement disponibles : 'defaut', 'aucun', 'premier', 'dernier', 'meilleur', 'tous'
	sql_alter('TABLE spip_jeux CHANGE `resultat_unique` `type_resultat` VARCHAR(10)');
	sql_updateq('spip_jeux',array('type_resultat'=>'premier'),  "`type_resultat`='oui'");
	sql_updateq('spip_jeux',array('type_resultat'=>'dernier'), "`type_resultat`='non'");
	sql_updateq('spip_jeux',array('type_resultat'=>'aucun'), "`enregistrer_resultat`='non'");
	sql_alter('TABLE spip_jeux DROP `enregistrer_resultat`');
}
function jeux_upgrade_0_17(){
	// tenir compte du bug sur les prefixes
	$desc = sql_showtable('spip_jeux', true);
	if (isset($desc['field']['titre']))
		sql_alter('TABLE spip_jeux CHANGE `titre` `titre_prive` TEXT');
	if (isset($desc['field']['nom']))
		sql_alter('TABLE spip_jeux CHANGE `nom` `type_jeu` TEXT');
	if (!isset($desc['field']['resultat_unique']))
		sql_alter("TABLE spip_jeux ADD `resultat_unique` VARCHAR(10) NOT NULL DEFAULT 'non'");
}

function jeux_upgrade_0_18(){
    // changement du champ statut pour suivre le modèle chat
    sql_alter("TABLE spip_jeux CHANGE `statut` `statut` varchar(255) DEFAULT '0' NOT NULL");
}
function jeux_upgrade_0_19(){
    // renommer contenu en texte
    sql_alter("TABLE spip_jeux CHANGE `contenu` `texte` TEXT DEFAULT '' NOT NULL");
}
function jeux_upgrade_0_20(){
    // remettre titre_prive pour les quelques versions de la 3.0 bugué
    $desc = sql_showtable('spip_jeux', true);
    if (isset($desc['field']['titre']))
	sql_alter('TABLE spip_jeux CHANGE `titre` `titre_prive` TEXT');
}
?>