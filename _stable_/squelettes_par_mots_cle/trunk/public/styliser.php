<?php

//    Fichier créé pour SPIP avec un bout de code emprunté à celui ci.
//    Distribué sans garantie sous licence GPL.
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
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 

if (!defined("_ECRIRE_INC_VERSION")) return;

// Ce fichier doit imperativement definir la fonction ci-dessous:

// http://doc.spip.org/@public_styliser_dist
function public_styliser_dist($fond, $id_rubrique, $lang) {
	
  // Actuellement tous les squelettes se terminent par .html
  // pour des raisons historiques, ce qui est trompeur
	$ext = 'html';
	// Accrocher un squelette de base dans le chemin, sinon erreur
	if (!$base = find_in_path("$fond.$ext")) {
		include_spip('public/debug');
		erreur_squelette(_T('info_erreur_squelette2',
			array('fichier'=>"'$fond'")),
			$GLOBALS['dossier_squelettes']);
		$f = find_in_path(".$ext"); // on ne renvoie rien ici, c'est le resultat vide qui provoquere un 404 si necessaire
		return array(substr($f, 0, -strlen(".$ext")),
			     $ext,
			     $ext,
			     $f);
	}

	// supprimer le ".html" pour pouvoir affiner par id_rubrique ou par langue
	$squelette = substr($base, 0, - strlen(".$ext"));

	// On selectionne, dans l'ordre :
	// fond=10
	$f = "$fond=$id_rubrique";
	if (($id_rubrique > 0) AND ($squel=find_in_path("$f.$ext")))
		$squelette = substr($squel, 0, - strlen(".$ext"));
	else {
		// fond-10 fond-<rubriques parentes>
		while ($id_rubrique > 0) {
			$f = "$fond-$id_rubrique";
			if ($squel=find_in_path("$f.$ext")) {
				$squelette = substr($squel, 0, - strlen(".$ext"));
				break;
			}
			else
				$id_rubrique = quete_parent($id_rubrique);
		}
	}
    
    if(!$trouve) {
		$fonds = unserialize($GLOBALS['meta']['SquelettesMots:fond_pour_groupe']);
		if (is_array($fonds) && (list($id_groupe,$table,$id_table) = $fonds[$fond])) {
		  $trouve = false;
		  $stop = false;
		  if (($id = intval($contexte[$id_table])) && ($n = sql_mot_squelette($id,$id_groupe,$table,$id_table))) {
			if ($squel = find_in_path("$fond=$n.$ext")) {
			  $squelette = substr($squel, 0, - strlen(".$ext"));
			  $trouve = true;
			  $stop = true;
			}
			else if ($squel = find_in_path("$fond-$n.$ext")) {
			  $squelette = substr($squel, 0, - strlen(".$ext"));
			  $trouve = true;
			}
		  } 
		  if((!$trouve) && (!$stop) && ($n = sql_mot_squelette($id_rub_init,$id_groupe,'rubriques','id_rubrique',true))) {	
				if ($squel = find_in_path("$fond-$n.$ext")) {
				  $squelette = substr($squel, 0, - strlen(".$ext"));
				}
		  }
		}
  }

	// Affiner par lang
	if ($lang) {
		$l = lang_select($lang);
		$f = "$squelette.".$GLOBALS['spip_lang'];
		if ($l) lang_select();
		if (@file_exists("$f.$ext"))
			$squelette = $f;
	}

	return array($squelette, $ext, $ext, "$squelette.$ext");
}

function sql_mot_squelette($id,$id_groupe,$table,$id_table,$recurse=false) {
  $select1 = array('titre');
  $from1 = array('spip_mots AS mots',
				 "spip_mots_$table AS lien");
  while($id > 0) {
	$where1 = array("$id_table=$id",
					'mots.id_mot=lien.id_mot',
					"id_groupe=$id_groupe");
	$r = sql_fetch(spip_abstract_select($select1,$from1,$where1));
	if ($r) {
	  include_spip("inc/charsets");
	   include_spip("inc/filtres");
	  return translitteration(preg_replace('/["\'.\s]/','_',extraire_multi($r['titre'])));	
	}
	if(!$recurse) return '';
	$id = sql_parent($id);
  }
  return '';
}
?>
