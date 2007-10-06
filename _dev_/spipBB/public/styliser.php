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

global $spip_version_code;
if (version_compare(substr($spip_version_code,0,5),'1.925','<')){
	include_spip('public/styliser192');; // SPIP 1.9.2
} else { // SPIP 1.9.3

// Ce fichier doit imperativement definir la fonction ci-dessous:

// Actuellement tous les squelettes se terminent par .html
// pour des raisons historiques, ce qui est trompeur

// http://doc.spip.org/@public_styliser_dist
function public_styliser_dist($fond, $id_rubrique, $lang, $ext='html') {
	
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

	// traitement spipbb : on recherche un squelette defini
	unset($sqel);
	$spipbb_meta = @unserialize($GLOBALS['meta']['spipbb']);
	$id_rubrique = intval($id_rubrique);

	if ( is_array($spipbb_meta)
	  AND ($fond=="article" OR $fond=="rubrique")
	  AND $id_rubrique>0 ) {
		spip_log("debug spipbb:".$id_rubrique.":sq:".$fond.":meta:".$spipbb_meta['spipbb_id_rubrique'], 'spipbb');

		if (empty($spipbb_meta['spipbb_squelette_filforum']) OR empty($spipbb_meta['spipbb_squelette_groupeforum']) ) spipbb_init_metas($id_rubrique);
		$id_rub = $id_rubrique;

		while ($id_rub > 0 AND $id_rub!=intval($spipbb_meta['spipbb_id_rubrique'])) {
			$id_rub = quete_parent($id_rub);
		}
		if ( $id_rub==intval($spipbb_meta['spipbb_id_rubrique']) ) {
			switch ($fond) {
			case "article" : $sq=$spipbb_meta['spipbb_squelette_filforum']; break;
			case "rubrique" : $sq=$spipbb_meta['spipbb_squelette_groupeforum']; break;
			}
			if ( $squel=find_in_path("$sq.$ext") ) $squelette = substr($squel, 0, - strlen(".$ext"));
		}
	}
	else spip_log("debug spipbb:".$id_rubrique.":sq:".$fond.":meta:".$spipbb_meta['spipbb_id_rubrique'], 'spipbb') ;

	// traitement normal
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
				} else
					$id_rubrique = quete_parent($id_rubrique);
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

} // fin de la condition de version de SPIP

?>
