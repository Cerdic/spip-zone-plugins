<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *  Plugin Mots-Partout                                                    *
 *                                                                         *
 *  Copyright (c) 2006-2008                                                *
 *  Pierre ANDREWS, Yoann Nogues, Emmanuel Saint-James                     *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
 *    This program is free software; you can redistribute it and/or modify *
 *    it under the terms of the GNU General Public License as published by * 
 *    the Free Software Foundation.                                        *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_once(_DIR_PLUGIN_MOTSPARTOUT."/mots_partout_choses.php");
include_spip('base/abstract_sql');

function action_mots_partout() {
  /*
  global $hash, $id_auteur;
  if (!include_spip("inc/securiser_action"))
	include_spip("inc/actions");
  if (!verifier_action_auteur("mots_partout $nom_chose", $hash, $id_auteur)) {
	include_ecrire('inc_minipres');
	minipres(_T('info_acces_interdit'));
  }
  */   

  $choses = secureIntArray(_request('choses'));

  $limit =  addslashes(_request('limit'));
  if($limit == '') $limit = 'rien';
  $id_limit =  intval(_request('identifiant_limit'));
  if($id_limit < 1) $id_limit = 0;
  $nb_aff = intval(_request('nb_aff'));
  if($nb_aff < 1) $nb_aff = 20;
  $switch = addslashes(_request('switch'));
  if($switch == '') $switch = 'voir';
  $strict = intval(_request('strict'));

  /***********************************************************************/
  /* recuperation de la chose sur laquelle on travaille*/
  /***********************************************************************/


  $nom_chose = addslashes(_request('nom_chose'));
  if(!isset($choses_possibles[$nom_chose])) {
	list($nom_chose,) = each($choses_possibles);
	reset($choses_possibles);
  }
  $id_chose = $choses_possibles[$nom_chose]['id_chose'];
  $table_principale = $choses_possibles[$nom_chose]['table_principale'];
  $table_auth = $choses_possibles[$nom_chose]['table_auth'];
  $tables_limite = $choses_possibles[$nom_chose]['tables_limite'];

  /***********************************************************************/
  /* action */
  /***********************************************************************/
  list($mots_voir, $mots_cacher, $mots_ajouter, $mots_enlever) = splitArrayIds(_request('mots'));
  $choses = secureIntArray($choses);
  $switch = addslashes($switch);
  if($switch == '') $switch = 'voir';
  $strict = intval($strict);
  $table = 'spip_mots_'.$nom_chose;

  if(count($mots_ajouter) && count($choses)) {
	if(count($mots_ajouter)) {
	  foreach($mots_ajouter as $m) {
		$from = 'spip_mots';
		$select = 'id_groupe';
		$where = "id_mot = $m";
		$unseul = false;
		$titre_groupe = '';
		if($id_groupe = intval(sql_getfetsel($select,$from,$where))) {
		  $from = ('spip_groupes_mots');
		  $select =('unseul, titre');
		  $where = ("id_groupe = $id_groupe");
		  if($row = sql_fetsel($select,$from,$where)) {
			$unseul = ($row['unseul'] == 'oui');
			$titre_groupe = $row['titre'];
		  }
		}
		foreach($choses as $d) {
		  if($unseul) {
			$from = array($table,'spip_mots');
			$select = array("count('id_mot') as cnt");
			$where = array("spip_mots.id_groupe = $id_groupe", $table . ".id_mot = spip_mots.id_mot", $table . ".$id_chose = $d");
			$group = $table . ".$id_chose";
			if($row = sql_getfetsel($select,$from,$where,$group)) {
				$warnings[] = array(_T('motspartout:dejamotgroupe',array('groupe' => $titre_groupe, 'chose' => $d)));
				continue; 
			}
		  }
		  sql_insertq($table, array("id_mot" => $m, $id_chose => $d));
		}
	  }
	}
  }
  if ($mots_enlever AND $choses) {
	foreach($mots_enlever as $m) {
	    sql_delete($table, "id_mot=$m AND " . sql_in($id_chose, $choses));
	}
  }

  $par_choses = '';

  foreach($choses as $c) if ($c) $par_choses .= "&choses[]=$c";

  $par_mots = '';

  if(is_array(_request('mots'))) {
	foreach(_request('mots') as $id => $m) 
	  if ($m) $par_mots .= "&mots[$id]=$m";
  }

  $redirect = _request('redirect')."&nom_chose=$nom_chose&strict=$strict&switch=$switch&ajax=$ajax&warning=$warning&redirect=$redirect$par_choses$par_mots";

  redirige_par_entete($redirect);

}

?>
