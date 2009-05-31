<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
 *                                                                         *
 * Plugin profil_etendu
 * Gestion des droits par rubrique et interfaces publiques
 *
 * Auteurs :
 * Stephane LAURENT
 * 2007 - Distribue sous licence GNU/GPL
\***************************************************************************/
	
$GLOBALS['profil_etendu_base_version'] = 1.0;

function profil_etendu_upgrade(){
	$version_base = $GLOBALS['profil_etendu_base_version'];
	$current_version = 0.0;
	if (   (isset($GLOBALS['meta']['profil_etendu_base_version']) )
			&& (($current_version = $GLOBALS['meta']['profil_etendu_base_version'])==$version_base))
		return;

	if ($current_version==0.0){
		if (is_array($GLOBALS['champs_etendus'])){
			$profils=array_keys($GLOBALS['champs_etendus']);
			foreach($profils as $profil)
				creer_table_profil($profil);
		}
		$current_version=1.0;
		ecrire_meta('profil_etendu_base_version',$current_version=$version_base);
		spip_log("Plugin atelier version : ".$current_version);
	}

	ecrire_metas();
}

function profil_etendu_vider_tables() {
	if (is_array($GLOBALS['champs_etendus'])){
		$profils=array_keys($GLOBALS['champs_etendus']);
		foreach($profils as $profil)
			spip_query("DROP TABLE `spip_".$profil."`");
	}
	effacer_meta('profil_etendu_base_version');
	ecrire_metas();
}

function profil_etendu_install($action){
	global $profil_etendu_base_version;
	switch ($action){
		case 'test':
			return (isset($GLOBALS['meta']['profil_etendu_base_version']) AND ($GLOBALS['meta']['profil_etendu_base_version']>=$profil_etendu_base_version));
			break;
		case 'install':
			profil_etendu_upgrade();
			break;
		case 'uninstall':
			profil_etendu_vider_tables();
			break;
	}
}	
function creer_table_profil($type_profil){
	$q="CREATE TABLE spip_".$type_profil." (";
	$champs=etendu_champs($type_profil);
	foreach (array_keys($champs) as $champ){
		$q.="`".$champ."` ";
		if ((($champs[$champ]=="radio")||($champs[$champ]=="radio_form")||($champs[$champ]=="select"))&&(is_array($GLOBALS['enum_conf'][$champ])))
			$q.="ENUM('".join(array_keys($GLOBALS['enum_conf'][$champ]),"','")."'),";
		elseif ($champs[$champ]=="bloc")
			$q.="TEXT,";
		elseif ($champs[$champ]=="checkbox")
			$q.="ENUM('oui','non') NOT NULL default 'non',";
		else $q.="varchar(255) default NULL,";	
	}
	$q.="`id_auteur` int(11) NOT NULL default '0',maj DATETIME,PRIMARY KEY  (`id_auteur`))";
//	spip_log("installation formulaire etendu:".$type_profil);
	$result=spip_query($q);
}
?>