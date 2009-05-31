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

function public_styliser($fond, $id_rubrique, $lang) {
	
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
	
	// Traitement pour afficher page accueil de spip-Genea
	global $table_prefix;
	unset($squel);
	$id_rubrique=intval($id_rubrique);
	if (($id_rubrique>0) AND ($fond=="rubrique")) {
		$query = "SELECT id_genea FROM " . $table_prefix . "_genea WHERE id_rubrique=$id_rubrique";
		$result = spip_query($query);
		if ($row=spip_fetch_array($result)) $id_genea = intval($row['id_genea']);
		if ($id_genea>0) {
			if ( $squel=find_in_path("genea_accueil.$ext") ) $squelette = substr($squel, 0, - strlen(".$ext"));
		}
	}	

	// Traitement normal
	if (!$squel) {
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
					$id_rubrique = sql_parent($id_rubrique);
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
?>
