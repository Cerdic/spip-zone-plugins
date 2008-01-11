<?php
#-----------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                               #
#  File    : public/styliser - Gestion des squelettes specifiques #
#  Authors : Chryjs, 2007 et als                                  #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs        #
#  Contact : chryjs!@!free!.!fr                                   #
#-----------------------------------------------------------------#

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
if (!defined("_INC_SPIPBB_COMMON")) include_spip('inc/spipbb_common');
spipbb_log("included",3,__FILE__);

global $spip_version_code;
if (version_compare(substr($spip_version_code,0,6),_SPIPBB_REV_STYLISER,'<')){
	include_spip('public/styliser192'); // SPIP 1.9.2
} else { // SPIP 1.9.3

// Ce fichier doit imperativement definir la fonction ci-dessous:

//----------------------------------------------------------------------------
// Actuellement tous les squelettes se terminent par .html
// pour des raisons historiques, ce qui est trompeur
//----------------------------------------------------------------------------
function public_styliser($fond, $id_rubrique, $lang='', $connect='', $ext='html') {
	$spipbb_meta = @unserialize($GLOBALS['meta']['spipbb']);

	if (!is_array($spipbb_meta) OR ($spipbb_meta['configure']!='oui')) {
		include_once(_DIR_RESTREINT.'public/styliser.php');
		return public_styliser_dist($fond, $id_rubrique, $lang, $connect, $ext);
	}

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

	// traitement spipbb : on recherche un squelette defini
	unset($squel);
	$id_rubrique = intval($id_rubrique);

	if ( ($spipbb_meta['configure']=='oui') and ($spipbb_meta['config_squelette']== 'oui') ) {
		if ( is_array($spipbb_meta)
		  AND ($fond=="article" OR $fond=="rubrique")
		  AND $id_rubrique>0 ) {
			spipbb_log("id_rub:".$id_rubrique.":sq:".$fond.":meta:".$spipbb_meta['id_secteur'],3,"p_s");

			if (empty($spipbb_meta['squelette_filforum']) OR empty($spipbb_meta['squelette_groupeforum']) ) spipbb_init_metas($id_rubrique);
			$id_rub = $id_rubrique;

			while ($id_rub > 0 AND $id_rub!=intval($spipbb_meta['id_secteur'])) {
				$id_rub = quete_parent($id_rub);
			}
			if ( $id_rub==intval($spipbb_meta['id_secteur']) ) {
				switch ($fond) {
				case "article" : $sq=$spipbb_meta['squelette_filforum']; break;
				case "rubrique" : $sq=$spipbb_meta['squelette_groupeforum']; break;
				}
				$squel=find_in_path("$sq.$ext");
				if ( $squel ) $squelette = substr($squel, 0, - strlen(".$ext"));
			}
		}
		else spipbb_log("id_rub:".$id_rubrique.":sq:".$fond.":meta:".$spipbb_meta['id_secteur'],3,"p_s2") ;
	}

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
				}
				else
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
} // public_styliser

} // fin de la condition de version de SPIP

?>
