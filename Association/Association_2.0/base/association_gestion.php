<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007
	* Bernard Blazin & François de Montlivault
	* http://www.plugandspip.com 
	* Version pour SPIP 2: Emmanuel Saint-James
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
	
if (!defined("_ECRIRE_INC_VERSION")) return;

// A chaque modif de la base SQL ou ses conventions (raccourcis etc)
// le fichier plugin.xml doit indiquer le numero de depot qui l'implemente sur
// http://zone.spip.org/trac/spip-zone/timeline

function association_version_base()
{
	static $version = 0;
	if (!$version) {
		$f = _DIR_PLUGINS . 'Association_2.0/plugin.xml';
		if (!lire_fichier($f, &$r))
			spip_log("fichier $f illisble");
		elseif (preg_match("@<version>(\d+)</version>@", $r, $r))
			$version = $r[1];
	}
	return $version;
}

// MAJ des tables de la base SQL
// Retourne 0 si ok, le dernier numero de MAJ ok sinon

function association_upgrade(){			

	$courante = association_version_base();

	if (!isset($GLOBALS['meta']['asso_base_version']))
		return association_maj_0($courante, 'asso_base_version');
	else {
	// compatibilite avec les numeros de version non entiers
		$installee = (($GLOBALS['meta']['asso_base_version'] > 1) ?
			$GLOBALS['meta']['asso_base_version'] :
			($GLOBALS['meta']['asso_base_version'] * 100));
		
		$GLOBALS['association_maj_erreur'] = 0;
		if ($courante > $installee) {
			include_spip('base/association');
			include_spip('base/upgrade');
			maj_while($installee, association_version_base(), $GLOBALS['association_maj'], 'asso_base_version');
			// signaler que les dernieres MAJ sont a refaire
			if ($GLOBALS['association_maj_erreur']) ecrire_meta('asso_base_version', $GLOBALS['association_maj_erreur']-1);
		}
		return $GLOBALS['association_maj_erreur'];
	}
}

function association_maj_0($version, $meta){
	global $association_tables_principales;
	include_spip('base/association');
	include_spip('base/abstract_sql');
	foreach($association_tables_principales as $table => $desc)
		sql_create($table, $desc['field'], $desc['key'], true, false);
	ecrire_meta($meta, $version);
	return 0; // Reussite (supposee !)
}
			

$GLOBALS['association_maj'][21] = array(array('sql_alter',"TABLE spip_asso_adherents ADD publication text NOT NULL AFTER secteur"));

$GLOBALS['association_maj'][30] = array(
	array('sql_drop_table', "spip_asso_bienfaiteurs"),
	array('sql_drop_table', "spip_asso_financiers")
					);

$GLOBALS['association_maj'][40] = array(
	array('sql_alter',"TABLE `spip_asso_comptes` ADD `valide` TEXT NOT NULL AFTER `id_journal` "));
		
$GLOBALS['association_maj'][50] = array(
	array('sql_alter',"TABLE spip_asso_activites ADD membres TEXT NOT NULL AFTER accompagne, ADD non_membres TEXT NOT NULL AFTER membres "));
		
$GLOBALS['association_maj'][60] = array(array('sql_drop_table', "spip_asso_profil"));
		
$GLOBALS['association_maj'][61] = array(
	array('spip_query',"RENAME TABLE spip_asso_banques TO spip_asso_plan"),
	array('sql_drop_table',"spip_asso_livres")
					);
$GLOBALS['association_maj'][62] = array(array('sql_alter',"TABLE spip_asso_plan ADD actif TEXT NOT NULL AFTER commentaires"));

$GLOBALS['association_maj'][63] = array(array('sql_alter',"TABLE spip_asso_ventes ADD id_acheteur BIGINT(20) NOT NULL AFTER acheteur"));
		
function association_maj_64(){

	if (_ASSOCIATION_AUTEURS_ELARGIS == 'spip_auteurs_elargis') {
		sql_alter("TABLE spip_auteurs_elargis ADD validite date NOT NULL default '0000-00-00'");
		sql_alter("TABLE spip_auteurs_elargis ADD montant float NOT NULL default '0'");
		sql_alter("TABLE spip_auteurs_elargis ADD date date NOT NULL default '0000-00-00' ");
	} else {
		if (_ASSOCIATION_INSCRIPTION2) {
			if (!$GLOBALS['association_maj_erreur']) $GLOBALS['association_maj_erreur'] = 64;
			return;
		}
		// Simulation provisoire
		@sql_alter("TABLE spip_asso_adherents ADD commentaire text NOT NULL default ''");
		@sql_alter("TABLE spip_asso_adherents ADD statut_interne text NOT NULL default '' ");
		@sql_alter("TABLE spip_asso_adherents CHANGE COLUMN nom nom_famille text DEFAULT '' NOT NULL");
	}
}

$GLOBALS['association_maj'][64] = array(array('association_maj_64'));

function association_effacer_tables(){
	include_spip('base/abstract_sql');
	include_spip('base/association');
	foreach ($GLOBALS['association_tables_principales'] as $k=>$v) {
		spip_log("table $k detruite");
		sql_drop_table($k);
	}
	effacer_meta('asso_base_version');
	effacer_meta('association');
	spip_log("plugin assoc desinstallee");
}
	
function association_install($action){
	$version_base = association_version_base();
	switch ($action){
		case 'test':
			return (isset($GLOBALS['meta']['asso_base_version']) 
				AND ($GLOBALS['meta']['asso_base_version']>=$version_base));
			break;
		case 'install':
			if (association_upgrade()) {
				echo debut_cadre_enfonce('',true);
				echo _L('Installer les plugins cfg et Inscription2 avant d\'installer ce plugin!!!'); 
				echo fin_cadre_enfonce(true);
			}
			break;
	case 'uninstall':
			association_effacer_tables();
			break;
	}
}
?>
