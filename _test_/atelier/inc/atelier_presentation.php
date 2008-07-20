<?php

/*
 *  Plugin Atelier pour SPIP
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

function atelier_debut_page($titre,$nom_page) {
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page($titre, "atelier", $nom_page);

	include_spip('inc/plugin');
	$info = plugin_get_infos('atelier');

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
		.'<tr><td><img src="'.find_in_path($info['icon']).'" width="50" height="50"/></td>'
		.'<td>&nbsp;&nbsp;'._T('atelier:atelier') . ' - ' .$titre.'</td></tr>'
		.'</table></div>';

	return $nom_page;
}

function atelier_fin_page() {
	echo '</div>';
	echo '<br style="clear: both" />';
	echo fin_page();
}

function atelier_debut_gauche($nom_page) {
	echo debut_gauche($nom_page,true);
}

function atelier_fin_gauche() {
	echo fin_gauche();
}

function atelier_debut_droite($nom_page) {
	echo debut_droite($nom_page,true);
}

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

function cadre_atelier($titre,$lignes) {
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

function atelier_cadre_infos() {
	include_spip('inc/plugin');
	$info = plugin_get_infos('atelier');

	cadre_atelier(_T('atelier:titre_infos'),array(
		'<a href="http://spip-contrib.net/spip.php?page=article2780">'._T('atelier:documentation'). '</a>',
		'<a href="http://doc.spip.org/">'._T('atelier:documentation_code'). '</a>',
		'Plugin Atelier '. $info['version'].'<br />'. _T('atelier:licence')
	));
}

function atelier_cadre_fichiers_temp() {

	cadre_atelier(_T('atelier:titre_fichiers_temporaires'),array(
		'<a href="'.generer_url_ecrire("fichiers_temporaires","fichier=charger_pipelines.php").'">'
				._T('atelier:charger_pipelines').'</a>',
		'<a href="'.generer_url_ecrire("fichiers_temporaires","fichier=charger_plugins_fonctions.php").'">'
			._T('atelier:charger_plugins_fonctions').'</a>',
		'<a href="'.generer_url_ecrire("fichiers_temporaires","fichier=charger_plugins_options.php").'">'
			._T('atelier:charger_plugins_options').'</a>',
		'<a href="'.generer_url_ecrire("fichiers_temporaires","fichier=meta_cache.txt").'">'
			._T('atelier:meta_cache').'</a>',
		'<a href="'.generer_url_ecrire("fichiers_temporaires","fichier=plugin_xml.cache").'">'
			._T('atelier:plugin_xml').'</a>'
	));
}

function atelier_cadre_raccourcis($autres='') {
	$liens = array(
		'<a href="'.generer_url_ecrire("atelier").'">'._T('atelier:retour_atelier').'</a>'
		);
	if (is_array($autres))
		foreach($autres as $autre) array_push(&$liens,$autre);

	cadre_atelier(_T('atelier:raccourcis'), $liens);
}
function atelier_debut_cadre_form() {
	echo debut_cadre_trait_couleur("",true); 
	echo debut_cadre_formulaire("",true);
}

function atelier_fin_cadre_form() {
	echo fin_cadre_formulaire(true);
	echo fin_cadre_trait_couleur(true);
}

?>
