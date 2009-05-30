<?php

/*
 *  Plugin Bouquinerie pour SPIP
 *  Copyright (C) 2008  Polez Kévin
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function debut_liste() {
	return "<table width='100%' cellpadding='2' cellspacing='0' border='0'>";
}

function fin_liste() {
	return "</table>";
}

function liste_ligne($ligne,$align='left',$style='true') {
	if (!$ligne) return '<tr><td style="background-color:#000; text-align:'.$align.';">'.$ligne.'</td></tr>';
	else if ($style) return '<tr class="tr_liste"><td style="text-align:'.$align.';">'.$ligne.'</td></tr>';
	return '<tr><td style="text-align:'.$align.';">'.$ligne.'</td></tr>';
}
function debut_page_bouq($titre,$nom_page) {

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page($titre, "bouquinerie", $nom_page);

	echo "<br /><br />"
		."<div style='display : block;
		 padding : 10px 10px 10px 10px;
		 margin : 0px 200px 0px 200px;
		 background-color: #fff;
  		 border: 2px solid #000;
		 float : left;
		 '>";

	echo "<div style='display: block;
		 font-size: 1.6em; 
		 font-weight: bold;
		 text-align: left;
		 padding-bottom : 5px;
		 border-bottom: 1px solid #000;'>"
		.'<table>'
		.'<tr><td><img src="'.find_in_path('images/bibliotheque_petit.png').'" /></td>'
		.'<td>&nbsp;&nbsp;'._T('bouq:bouquinerie') . ' - ' .$titre.'</td></tr>'
		.'</table></div>';
	return $nom_page;
}

function fin_page_bouq() {
	echo '</div>';
	echo '<br style="clear: both" />';
	echo fin_page();
}

function cadre_gauche_bouq($titre,$lignes) {
	if (version_compare($GLOBALS['spip_version_code'],'2.0000','>')) // SPIP 2.0
		echo debut_cadre_trait_couleur('',true,'','<span style="color:#000;">'.$titre.'</span>');
	else 
		echo debut_cadre_trait_couleur('',true,'',$titre);

	echo debut_liste();
	foreach($lignes as $ligne) {
		echo liste_ligne($ligne);
	}
	echo fin_liste();
	echo fin_cadre_trait_couleur(true);
}

function bouq_afficher_admin() {
	cadre_gauche_bouq(_T('bouq:administration'),array(
		'<a href="'.generer_url_ecrire('cfg','cfg=bouq').'">'._T('bouq:configuration').'</a>',
		'<a href="'.generer_url_ecrire('supprimer_bouquinerie').'">'._T('bouq:supprimer_bouquinerie').'</a>'
	));
}

function bouq_afficher_infos() {
	cadre_gauche_bouq(_T('bouq:infos'),array(
		'<a href="'._T('bouq:url_documentation').'">'._T('bouq:documentation'). '</a>',
		'Plugin Bouquinerie '. $GLOBALS['meta']['bouq_version'].'<br />'. _T('bouq:licence')
	));
}

function bouq_afficher_raccourcis() {

	cadre_gauche_bouq(_T('bouq:raccourcis'),array(
		'<a href="'.generer_url_ecrire("admin_bouquinerie").'">'._T('bouq:bouquinerie').'</a>'
		));
}

function debut_cadre_form_bouq() {
	echo debut_cadre_trait_couleur("",true); // SPIP 2.0
	echo debut_cadre_formulaire("",true); // SPIP 2.0
}

function fin_cadre_form_bouq() {
	echo fin_cadre_formulaire(true); // SPIP 2.0
	echo fin_cadre_trait_couleur(true); // SPIP 2.0
}

?>
