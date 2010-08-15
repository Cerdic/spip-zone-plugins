<?php
#---------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                             #
#  File    : inc/spipbb_rubriquage                              #
#  Authors : scoty 2007                                         #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs      #
#  Contact : scoty!@!koakidi!.!com                              #
# [fr]   generer arbo rubriques comme spip                      #
# [en]                                                          #
#---------------------------------------------------------------#

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

// * [fr] Acces restreint, plugin pour SPIP * //
// * [en] Restricted access, SPIP plugin * //

if (!defined("_ECRIRE_INC_VERSION")) return;
spipbb_log('included',2,__FILE__);

// ------------------------------------------------------------------------------
// voir http://doc.spip.org/@sous_enfant_rub
// ------------------------------------------------------------------------------
function sous_enfant_rubfo($collection2){
	global $lang_dir, $spip_lang_dir, $spip_lang_left;
	if (!function_exists('debut_block_invisible')) include_spip('inc/vieilles_defs');

	$result3 = sql_select("*", "spip_rubriques", "id_parent=$collection2",'', array('0+titre,titre'));

	if (!sql_count($result3)) return '';
	$retour = debut_block_invisible("enfants$collection2")."\n<ul style='margin: 0px; padding: 0px; padding-top: 3px;'>\n";
	while($row=sql_fetch($result3)){
			$id_rubrique2=$row['id_rubrique'];
			$id_parent2=$row['id_parent'];
			$titre2=supprimer_numero($row['titre']);
			changer_typo($row['lang']);

			$retour.="<div class='arial11' " .
			  http_style_background("rubrique-12.gif", "left center no-repeat; padding: 2px; padding-$spip_lang_left: 18px; margin-$spip_lang_left: 3px")
			  			. "><a href='"
						. generer_url_ecrire("spipbb_admin","id_salon=$id_rubrique2")
						. "'><span dir='$lang_dir'>"
						. typo($titre2)."</span></a></div>\n";
	}
	$retour .= "</ul>\n\n".fin_block()."\n\n";

	return $retour;
} // sous_enfant_rubfo

// ------------------------------------------------------------------------------
// voir http://doc.spip.org/@enfant_rub
// ------------------------------------------------------------------------------
function enfant_rubfo($collection){
	global $couleur_foncee, $lang_dir;
	global $spip_display, $spip_lang_left, $spip_lang_right, $spip_lang;


	$les_enfants = "";

	$result = sql_select("id_rubrique, id_parent, titre, descriptif, lang ", "spip_rubriques", "id_parent=$collection",'', array('0+titre,titre'));

	# compter les forums
	if($nombre_forums=sql_count($result)) {
		$flag_ordonne = ($nombre_forums>1)?true:false;
	}
	else $flag_ordonne = false;


	while($row=sql_fetch($result)) {
		$id_rubrique=$row['id_rubrique'];
		$id_parent=$row['id_parent'];
		$titre=supprimer_numero($row['titre']);

		$ifond = $ifond ^ 1;
		$coul_ligne = ($ifond) ? $couleur_claire : '#ffffff';

		if($id_rubrique == $GLOBALS['spipbb']['id_secteur']) {
			$icone_secteur = _DIR_IMG_SPIPBB."spipbb-24.png";
		}
		else { $icone_secteur = "secteur-24.gif"; }

		$les_sous_enfants = sous_enfant_rubfo($id_rubrique);

		changer_typo($row['lang']);

		$descriptif=propre($row['descriptif']);

		if ($spip_display == 4) $les_enfants .= "";

		if (function_exists('bouton_block_depliable')) $bouton = bouton_block_depliable("&nbsp;",false,"enfants$id_rubrique");
		else $bouton = bouton_block_invisible("enfants$id_rubrique");

		$les_enfants .= "\n<tr class='verdana3' bgcolor='".$coul_ligne."'><td width='6%' valign='top'>"
			. http_img_pack(($id_parent ? "rubrique-24.gif" : $icone_secteur), '','')
			. "</td><td width='2%' valign='top'>"
			. (!$les_sous_enfants ? "" : $bouton)
			. "</td><td width='93%' valign='top'>"
			. (!acces_restreint_rubrique($id_rubrique) ? "" :
				http_img_pack("admin-12.gif", '', '', _T('image_administrer_rubrique')))
			. ""
			. "<span dir='$lang_dir' style='color:$couleur_foncee;'><b>"
			. "<a href='" . generer_url_ecrire("spipbb_admin","id_salon=$id_rubrique") ."'>"
			. typo($titre)
			. "</a></b></span>"
			. (!$descriptif ? '' : "<div class='verdana1'>$descriptif</div>");

		if ($spip_display != 4) $les_enfants .= $les_sous_enfants;

		$les_enfants .= "<div style='clear:both;'></div>" . "</td>";

		if($flag_ordonne AND _request('id_salon')) {
			$les_enfants .= "<td width='3%' valign='top' class='verdana2'>\n";
			$les_enfants .= bouton_ordonne_salon($id_rubrique,generer_url_ecrire("spipbb_admin","id_salon="._request('id_salon'),true));
			$les_enfants .= "</td>";
		}

		$les_enfants .= "</tr>";

		if ($spip_display == 4) $les_enfants .= "";
	}

	changer_typo($spip_lang); # remettre la typo de l'interface pour la suite
	return $les_enfants;

} // enfant_rubfo

// ------------------------------------------------------------------------------
// voir http://doc.spip.org/@afficher_enfant_rub
// ------------------------------------------------------------------------------
function afficher_enfant_rubfo($id_rubrique, $afficher_bouton_creer=false) {
	global  $spip_lang_right;

	echo "\n<table cellpadding='3' cellspacing='0' border='0' width='100%'>\n";
	echo enfant_rubfo($id_rubrique);
	echo "</table>\n";

} // afficher_enfant_rubfo

?>
