<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;

// Ce fichier doit imperativement definir la fonction ci-dessous:

// Actuellement tous les squelettes se terminent par .html
// pour des raisons historiques, ce qui est trompeur

// http://doc.spip.org/@public_styliser_dist
function public_styliser_dist($fond, $id_rubrique, $lang='', $connect='', $ext='html', $contexte='') {
	$id_rubrique_article = $id_rubrique;
	// Trouver un squelette de base dans le chemin
	if (!$base = find_in_path("$fond.$ext")) {
		// Si pas de squelette regarder si c'est une table
		$trouver_table = charger_fonction('trouver_table', 'base');
		if (preg_match('/^table:(.*)$/', $fond, $r)
		AND $table = $trouver_table($r[1], $connect)
		AND include_spip('inc/autoriser')
		AND autoriser('webmestre')
		) {
				$fond = $r[1];
				$base = _DIR_TMP . 'table_'.$fond . ".$ext";
				if (!file_exists($base)
				OR  $GLOBALS['var_mode']) {
					$vertebrer = charger_fonction('vertebrer', 'public');
					ecrire_fichier($base, $vertebrer($table));
				}
		} else { // on est gentil, mais la ...
			include_spip('public/debug');
			erreur_squelette(_T('info_erreur_squelette2',
				array('fichier'=>"'$fond'")),
				$GLOBALS['dossier_squelettes']);
			$f = find_in_path(".$ext"); // on ne renvoie rien ici, c'est le resultat vide qui provoquere un 404 si necessaire
			return array(substr($f, 0, -strlen(".$ext")), $ext, $ext, $f);
		}
	}

	// supprimer le ".html" pour pouvoir affiner par id_rubrique ou par langue
	$squelette = substr($base, 0, - strlen(".$ext"));

	$trouve = false;

	// On selectionne, dans l'ordre :
	// fond=10
	if ($id_rubrique) {
		$f = "$squelette=$id_rubrique";
		if (@file_exists("$f.$ext")) {
			$squelette = $f;
			$trouve = true;
		}
		
		else {
			// fond-10 fond-<rubriques parentes>
			do {
				$f = "$squelette-$id_rubrique";
				if (@file_exists("$f.$ext")) {
					$squelette = $f;
					$trouve = true;
					break;
				}
			} while ($id_rubrique = quete_parent($id_rubrique));
		}
	}

	

	if(!$trouve) {
		$fonds = array();
		$fonds['article'] = array('1', 'articles', 'id_article');
		$fonds['rubrique'] = array('1', 'rubriques', 'id_rubrique');
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
			  $stop = true;
			}
		  } 
		  
		  if((!$trouve) && (!$stop) && ($n = sql_mot_squelette($id_rubrique_article,$id_groupe,'rubriques','id_rubrique',true))) {
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
	$r = sql_fetch(sql_select($select1,$from1,$where1));
	if ($r) {
	  include_spip("inc/charsets");
	   include_spip("inc/filtres");
	  return translitteration(preg_replace('/["\'.\s]/','_',extraire_multi($r['titre'])));	
	}
	if(!$recurse) return '';
	$id = quete_parent($id);
  }
  return '';
}

?>