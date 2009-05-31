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

// Ce fichier doit imperativement definir la fonction ci-dessous:

//----------------------------------------------------------------------------
// Actuellement tous les squelettes se terminent par .html
// pour des raisons historiques, ce qui est trompeur
//----------------------------------------------------------------------------
function public_styliser($fond, $id_rubrique, $lang='', $connect='', $ext='html') {
	$spipbb_meta = @unserialize($GLOBALS['meta']['spipbb']);

	if (!is_array($spipbb_meta) OR ($spipbb_meta['configure']!='oui')  OR empty($spipbb_meta['id_secteur'])) {
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
			spipbb_log("id_rub1:".$id_rubrique.":sq:".$fond.":meta:".$spipbb_meta['id_secteur'],3,"p_s");

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
				spipbb_log("id_fin:".$id_rubrique.":squel:".$squel.":nom:$sq.$ext",3,"p_s");
			} else spipbb_log("id_rub2:".$id_rubrique.":sq:".$fond.":meta:".$spipbb_meta['id_secteur'],3,"p_s");
		}
		else spipbb_log("id_rub3:".$id_rubrique.":sq:".$fond.":meta:".$spipbb_meta['id_secteur'].":conf:".$spipbb_meta['configure'].":sql:".$spipbb_meta['config_squelette'],3,"p_s2") ;
	}

	// traitement normal
	if (!isset($squel) OR (isset($squel) AND !$squel) )
	{
		// On selectionne, dans l'ordre :
		// fond=10
		if ($id_rubrique) {
			$f = "$squelette=$id_rubrique";
			if (@file_exists("$f.$ext"))
				$squelette = $f;
			else {
				// fond-10 fond-<rubriques parentes>
				do {
					$f = "$squelette-$id_rubrique";
					if (@file_exists("$f.$ext")) {
						$squelette = $f;
						break;
					}
				} while ($id_rubrique = quete_parent($id_rubrique));
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
	
	spipbb_log("return:$squelette, $ext, $ext, $squelette.$ext",3);
	return array($squelette, $ext, $ext, "$squelette.$ext");
} // public_styliser

?>