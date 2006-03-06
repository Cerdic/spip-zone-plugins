<?php

//	  action/mots_partout_cherche.php
//    Fichier créé pour SPIP avec un bout de code emprunté à celui ci.
//    Distribué sans garantie sous licence GPL./
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

define('_DIR_PLUGIN_MOTS_PARTOUT',(_DIR_PLUGINS . 'mots_partout'));

function mots_partout_cherche() {
  global $choses, $mots;
  global $ajax;

    /***********************************************************************/
  /* récuperation de la chose sur laquelle on travaille*/
  /***********************************************************************/

  $nom_chose = addslashes($_POST['nom_chose']);
  if(!isset($choses_possibles[$nom_chose])) {
	list($nom_chose,) = each($choses_possibles);
	reset($choses_possibles);
  }
  $id_chose = $choses_possibles[$nom_chose]['id_chose'];
  $table_principale = $choses_possibles[$nom_chose]['table_principale'];
  $table_auth = $choses_possibles[$nom_chose]['table_auth'];
  $tables_limite = $choses_possibles[$nom_chose]['tables_limite'];

  
  list($mots_voir, $mots_cacher, $mots_ajouter, $mots_enlever) = splitArrayIds($_REQUEST['mots']);
  $choses = secureIntArray($_REQUEST['choses']);

  $limit =  addslashes($_POST['limit']);
  if($limit == '') $limit = 'rien';
  $id_limit =  intval($_POST['identifiant_limit']);
  if($id_limit < 1) $id_limit = 0;
  $nb_aff = intval($_POST['nb_aff']);
  if($nb_aff < 1) $nb_aff = 20;
  $switch = addslashes($_POST['switch']);
  if($switch == '') $switch = 'voir';
  $strict = intval($_POST['strict']);
  

  /**********************************************************************/
  /* recherche des choses.*/
  /***********************************************************************/

  if(count($choses) == 0) {
	$select = array();
	$select[] = "DISTINCT main.$id_chose";
	
	$from = array();
	$where = array();
	$group = '';
	$order = array();
	
	if(isset($limit) && $limit != 'rien') {
	  $table_lim = $tables_limite[$limit]['table'];
	  $nom_id_lim = $tables_limite[$limit]['nom_id'];
	  
	  $from[0] = "$table_lim as main";
	  $where[0] = "main.$nom_id_lim IN ($id_limit)"; 
	  if(count($mots_voir) > 0) {
		$from[1] = "spip_mots_$nom_chose as table_temp";
		$where[1] = "table_temp.$id_chose = main.$id_chose";
		$where[] = "table_temp.id_mot IN (".calcul_in($mots_voir).')';
		if($strict) {
		  $select[] = 'count(id_mot) as tot';
		  $group = "main.$id_chose";
		  $order = array('tot DESC');
		}
	  }
	  if(count($mots_cacher) > 0) {
		$from[1] = "spip_mots_$nom_chose as table_temp";
		$where[1] = "table_temp.$id_chose = main.$id_chose";
		$where[] = "table_temp.id_mot not IN (".calcul_in($mots_cacher).')';
		if($strict) {
		  $select[] = 'count(id_mot) as tot';
		  $group = "main.$id_chose";
		  $order = array('tot DESC');
		}
	  }	
	} else if((count($mots_voir) > 0)||(count($mots_cacher) > 0)){
	  if(count($mots_voir) > 0) {
		$from[0] = "spip_mots_$nom_chose as main";
		$where[] = "main.id_mot IN (".calcul_in($mots_voir).')';
		if($strict) {
		  $select[] = 'count(id_mot) as tot';
		  $group = "main.$id_chose";
		  $order = array('tot DESC');
		}
	  }
	  if(count($mots_cacher) > 0) {
		$from[0] = "spip_mots_$nom_chose as main";
		$where[] = "main.id_mot not IN (".calcul_in($mots_cacher).')';
		if($strict) {
		  $select[] = 'count(id_mot) as tot';
		  $group = "main.$id_chose";
		  $order = array('tot DESC');
		}
	  }
	} else {
	  $from[] = "$table_principale as main"; 
	}

	$res=spip_abstract_select($select,$from,$where,$group,$order);
	
	$choses = array();
	$avec_sans = (count($mots_cacher) > 0);
	if($avec_sans) $in_sans = calcul_in($mots_cacher);
	while ($row = spip_abstract_fetch($res)) {
	  if(!isset($table_auth) ||
		 (isset($table_auth) &&
		  (verifier_admin() ||
		   verifier_auteur($table_auth,$id_chose,$row[$id_chose])
		   )
		  )
		 ) {
		if($avec_sans) {
		  $test = spip_abstract_select(array($id_chose),array("spip_mots_$nom_chose"),array("id_mot IN ($in_sans)","$id_chose = ".$row[$id_chose]));
		  if(spip_abstract_count($test) > 0) {
			continue;
		  }
		  spip_abstract_free($test);
		}
		if(count($mots_voir) > 0 && $strict) {
		  if($row['tot'] >= count($mots_voir)) {
			$choses[] = $row[$id_chose];
		  } else {
			break;
		  }
		} else {
		  $choses[] = $row[$id_chose];
		}
	  }
	}
	spip_abstract_free($res);
  }

  if(count($choses) > 0) {
	$query = "SELECT spip_mots_$nom_chose.id_mot FROM spip_mots_$nom_chose WHERE spip_mots_$nom_chose.$id_chose".((count($choses))?(' IN('.calcul_in($choses).')'):'');

	afficher_tranches_requete($query, 3,'debut',false,$nb_aff);
	
	$res = spip_query($query);
	
	while ($row = spip_fetch_array($res)) {
	  $show_mots[] = $row['id_mot'];
	}
	spip_free_result($res);
  } 
  
  if(!$ajax) 	redirige_par_entete(urldecode($redirect));

}

?>
