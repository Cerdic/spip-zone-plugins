<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;

// Ce fichier doit imperativement definir la fonction ci-dessous:

function public_styliser($fond, $id_rubrique, $lang, $contexte) {
	
  // Actuellement tous les squelettes se terminent par .html
  // pour des raisons historiques, ce qui est trompeur
	$ext = 'html';
	// Accrocher un squelette de base dans le chemin, sinon erreur
	if (!$base = find_in_path("$fond.$ext")) {
		include_spip('public/debug');
		erreur_squelette(_T('info_erreur_squelette2',
			array('fichier'=>"'$fond'")),
			$GLOBALS['dossier_squelettes']);
		$f = find_in_path("404.$ext");
		return array(substr($f, 0, -strlen(".$ext")),
			     $ext,
			     $ext,
			     $f);
	}

	// supprimer le ".html" pour pouvoir affiner par id_rubrique ou par langue
	$squelette = substr($base, 0, - strlen(".$ext"));
	$trouve = false;
	$id_rubrique = intval($id_rubrique);
	$id_rub_init = $id_rubrique;

	// On selectionne, dans l'ordre :
	// fond=10
	$f = "$fond=$id_rubrique";
	if (($id_rubrique > 0) AND ($squel=find_in_path("$f.$ext"))){
		$squelette = substr($squel, 0, - strlen(".$ext"));
		$trouve = true;
	}	
	else {
		// fond-10 fond-<rubriques parentes>
		while ($id_rubrique > 0) {
			$f = "$fond-$id_rubrique";
			if ($squel=find_in_path("$f.$ext")) {
				$squelette = substr($squel, 0, - strlen(".$ext"));
				$trouve = true;
				break;
			}
			else
				$id_rubrique = sql_parent($id_rubrique);
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
		lang_select($lang);
		$f = "$squelette.".$GLOBALS['spip_lang'];
		lang_dselect();
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
	$r = spip_abstract_fetch(spip_abstract_select($select1,$from1,$where1));
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
