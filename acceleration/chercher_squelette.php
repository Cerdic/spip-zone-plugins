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
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


function chercher_squelette($fond, $id_rubrique, $lang) {
  global $contexte;
  static $sqlparent = array();
  $ext = $GLOBALS['extension_squelette'];

  // Accrocher un squelette de base dans le chemin
  if (!$base = find_in_path("$fond.$ext")) {
	// erreur webmaster : $fond ne correspond a rien
	erreur_squelette(_T('info_erreur_squelette2',
						array('fichier'=>$fond)),
					 $dossier_squelettes);
	return '';
  }
  
  // supprimer le ".html" pour pouvoir affiner par id_rubrique ou par langue
  $squelette = substr($base, 0, - strlen(".$ext"));
  $trouve = false;
  $id_rub_init = $id_rubrique;

  // On selectionne, dans l'ordre :
  // fond=10
  $f = "$fond=$id_rubrique";
  if (($id_rubrique > 0) AND (isset($GLOBALS['declare-fonds']["$f.$ext"]))){
		$squel = $GLOBALS['declare-fonds']["$f.$ext"];
		if ($squel===NULL)
			$squel=find_in_path("$f.$ext");
		if ($squel){
			$squelette = substr($squel, 0, - strlen(".$ext"));
			$trouve = true;
		}
  } else {
	// fond-10 fond-<rubriques parentes>
		while ($id_rubrique > 0) {
		  $f = "$fond-$id_rubrique";
		  if (isset($GLOBALS['declare-fonds']["$f.$ext"])){
				$squel = $GLOBALS['declare-fonds']["$f.$ext"];
				if ($squel===NULL)
					$squel=find_in_path("$f.$ext");
			  if ($squel) {
					$squelette = substr($squel, 0, - strlen(".$ext"));
					$trouve = true;
					break;
				}
		  }
		  else {
		  	if (!isset($sqlparent[$id_rubrique]))
		  		$sqlparent[$id_rubrique]=sql_parent($id_rubrique);
				$id_rubrique = $sqlparent[$id_rubrique];
		  }
		}
  }

  // Affiner par lang
  if ($lang) {
	lang_select($lang);
	$f = "$squelette.$lang";
	if (@file_exists("$f.$ext"))
	  $squelette = $f;
  }
  
  return $squelette;
}

?>
