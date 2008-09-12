<?php
#-----------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                               #
#  File    : public/styliser_192                                  #
#  Authors : Chryjs, 2007 et als                                  #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs        #
#  Contact : chryjs!@!free!.!fr                                   #
#  Gestion des squelettes specifiques                             #
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
if (!defined("_INC_SPIPBB_COMMON")) include_spip('inc/spipbb_common');
spipbb_log("included",3,__FILE__);

// Ce fichier doit imperativement definir la fonction ci-dessous:

function public_styliser($fond, $id_rubrique, $lang) {
	$spipbb_meta = @unserialize($GLOBALS['meta']['spipbb']);

	if (!is_array($spipbb_meta) OR ($spipbb_meta['configure']!='oui') or empty($spipbb_meta['id_secteur']) ) {
		include_once(_DIR_RESTREINT.'public/styliser.php');
		return public_styliser_dist($fond, $id_rubrique, $lang);
	}

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

	// traitement spipbb : on recherche un squelette defini
	unset($squel);
	$spipbb_meta = @unserialize($GLOBALS['meta']['spipbb']);
	$id_rubrique = intval($id_rubrique);

	if ( ($spipbb_meta['configure']=='oui') and ($spipbb_meta['config_squelette']== 'oui') ) {

		if ( is_array($spipbb_meta)
			 AND ($fond=="article" OR $fond=="rubrique")
			 AND $id_rubrique>0 )
		{
	//		echo "debug spipbb:".$id_rubrique.":sq:".$fond.":meta:".$spipbb_meta['id_secteur']."<br>\n"  ;

			$id_rub=$id_rubrique;

			while ($id_rub > 0 AND $id_rub!=intval($spipbb_meta['id_secteur'])) {
				$id_rub = sql_parent($id_rub);
			}
			if ( $id_rub==intval($spipbb_meta['id_secteur']) ) {
				switch ($fond) {
				case "article" : $sq=$spipbb_meta['squelette_filforum']; break;
				case "rubrique" : $sq=$spipbb_meta['squelette_groupeforum']; break;
				}
				if ( $squel=find_in_path("$sq.$ext") ) $squelette = substr($squel, 0, - strlen(".$ext"));
			}
		}
	//	else echo "debug spipbb:".$id_rubrique.":sq:".$fond.":meta:".$spipbb_meta['id_secteur'] ."<br>\n" ;
	}

	// traitement normal
	if (!isset($squel) OR (isset($squel) AND !$squel) )
	{
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
} // public_styliser
?>
