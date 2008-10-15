<?php

//	  action/mots_partout.php
//    Fichier pour SPIP avec un bout de code issu de celui ci.
//    Distribue sans garantie sous licence GPL./
//    Copyright (C) 2006  Pierre ANDREWS
//
//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

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

  if(count($mots_ajouter) && count($choses)) {
	if(count($mots_ajouter)) {
	  foreach($mots_ajouter as $m) {
		$from = array('spip_mots');
		$select = array('id_groupe');
		$where = array("id_mot = $m");
		$res = sql_select($select,$from,$where);
		$unseul = false;
		$id_groupe = 0;
		$titre_groupe = '';
		if($row = sql_fetch($res)) {
		  sql_free($res);
		  $from = array('spip_groupes_mots');
		  $select = array('unseul','titre');
		  $id_groupe = $row['id_groupe'];
		  $where = array("id_groupe = $id_groupe");
		  $res = sql_select($select,$from,$where);
		  if($row = sql_fetch($res)) {
			$unseul = ($row['unseul'] == 'oui');
			$titre_groupe = $row['titre'];
		  }
		}
		sql_free($res);
		foreach($choses as $d) {
		  if($unseul) {
			$from = array("spip_mots_$nom_chose",'spip_mots');
			$select = array("count('id_mot') as cnt");
			$where = array("spip_mots.id_groupe = $id_groupe","spip_mots_$nom_chose.id_mot = spip_mots.id_mot","spip_mots_$nom_chose.$id_chose = $d");
			$group = "spip_mots_$nom_chose.$id_chose";
			$res = sql_select($select,$from,$where,$group);
			if($row = sql_fetch($res)) {	
			  if($row['cnt'] > 0) {
				$warnings[] = array(_T('motspartout:dejamotgroupe',array('groupe' => $titre_groupe, 'chose' => $d)));
				continue; 
			  }
			}
			sql_free($res);
		  }
		  sql_insert("spip_mots_$nom_chose","(id_mot,$id_chose)","($m,$d)");
		}
	  }
	}
  }
  if (count($mots_enlever) && count($choses)) {
	$table_pref = 'spip';
	if ($GLOBALS['table_prefix']) $table_pref = $GLOBALS['table_prefix'];
	foreach($mots_enlever as $m) {
	  foreach($choses as $d) {
		spip_query('DELETE FROM '.$table_pref.'_mots_'.$nom_chose." WHERE id_mot=$m AND $id_chose=$d");
	  }
	}
  }

  $par_choses = '';

  if(count($choses)) {
	foreach($choses as $c) 
	  if ($c) $par_choses .= "&choses[]=$c";
  }

  $par_mots = '';

  if(count(_request('mots'))) {
	foreach(_request('mots') as $id => $m) 
	  if ($m) $par_mots .= "&mots[$id]=$m";
  }

  $redirect = _request('redirect')."&nom_chose=$nom_chose&strict=$strict&switch=$switch&ajax=$ajax&warning=$warning&redirect=$redirect$par_choses$par_mots";

  redirige_par_entete($redirect);

}

?>
