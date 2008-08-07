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
	echo $commencer_page($titre, "atelier", $nom_page,'',false);

	include_spip('inc/plugin');
	$info = plugin_get_infos('atelier');

	echo "<br /><br />"
		."<div style='display : block;
		 width : 90%;
		 min-height : 700px;
		 padding : 10px 10px 10px 10px;
		 margin : 0px 0px 0px 10px;
		 background-color: #fff;
  		 border: 2px solid #000;
		 float : left;
		 '>";
	echo '<span style="float:right;"><a href="'.generer_url_ecrire("").'">Interface SPIP</a></span>';

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

function atelier_debut_gauche() {
	echo '<br /><div style="width: 300px;position:absolute;">';
	//echo debut_gauche($nom_page,true);
}

function atelier_debut_droite() {
	echo '<div style="margin:0px 0px 0px 310px;text-align:left;">';
//	echo debut_droite($nom_page,true);
}

function atelier_fin_gauche() {
	echo '</div>';
	//echo fin_gauche();
}

function atelier_fin_droite() {
	echo '</div>';
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


function debut_cadre_depliable($titre,$gros_titre,$id){
	$jquery = 'jQuery(this).depliant("#'.$id.'");';

	echo "<div class='cadre cadre-liste' style='margin-top: 20px;'>"
		."<div style='position: absolute; top: -16px; left: 10px;z-index:1;'><img src='../prive/images/tache-24.gif'
alt=''  /></div>"
		."<div id='explorateur' class='titrem deplie' onmouseover='".$jquery."'><a class='titremancre'></a>$gros_titre</div>"
		.'<div><div id="'.$id.'" class="bloc_depliable blocdeplie">'
		.'<img src="../prive/images/searching.gif" alt="*" style="visibility: hidden; float: right" id="img_t_8bda" />'
		.'<div style=";" class="arial1 tranches" id="a1">'.$titre.'</div>';
}

function fin_cadre_depliable() {
	echo "<div class='nettoyeur'></div></div><div class='nettoyeur'></div></div></div>";
}

function atelier_cadre_depliable($titre, $gros_titre,$lignes,$id) {

	echo debut_cadre_depliable($titre,$gros_titre,$id);

	echo debut_liste();
	foreach($lignes as $ligne) {
		echo liste_ligne($ligne);
	}
	echo fin_liste();

	echo fin_cadre_depliable();
}
function atelier_cadre_couleur($titre, $lignes) {
	echo debut_cadre_trait_couleur('',true);
	echo '<div style="text-align:center;font-weight:bold;">'.$titre.'</div><br /><br />';
	foreach ($lignes as $key => $ligne) {
		if (is_array($ligne)) {
			if (count($ligne) > 0) {
				echo '<table cellpadding="2" cellspacing="0" style="border:1px solid #000;width:100%;">
					<caption style="font-weight:bold;">'.$key.'</caption>';
				foreach ($ligne as $l) {
					echo '<tr class="tr_liste"><td>'.$l.'</td></tr>';
				}
				echo '</table><br /><br />';
			}
		}
		else echo $ligne . '<br />';
	}

	echo fin_cadre_trait_couleur(true);

}
function cadre_atelier($titre,$lignes) {
	if (version_compare($GLOBALS['spip_version_code'],'2.0000','>')) // SPIP 2.0
		echo debut_cadre_trait_couleur('',true,'',$titre);
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
		'<a href="http://www.spip-contrib.net/Plugin-Atelier">'._T('atelier:documentation'). '</a>',
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

function atelier_colorier(&$texte) {
	include_spip('inc/filtres');
	foreach($texte as $num => $ligne) {

		$ligne = entites_html($ligne);
		$ligne = preg_replace('#function#','<span style="color:#a52829;">function</span>',$ligne);
		$ligne = preg_replace('#echo#','<span style="color:#a52829;">echo</span>',$ligne);
		$ligne = preg_replace('#if#','<span style="color:#a52829;">if</span>',$ligne);
		$ligne = preg_replace('#return#','<span style="color:#a52829;">return</span>',$ligne);
		$ligne = preg_replace('#\$(.[^/ /|,]+?)([/ /|,])#','<span style="color:#008a8c;">$${1}${2}</span>',$ligne); // variables
		$ligne = preg_replace('#//(.*)#','<span style="color:#1014ff;">//${1}</span>',$ligne); // comentaire d'une ligne
		$ligne = preg_replace('#/\*#','<font color="#1014ff">/*',$ligne); // commentaire lignes multiples
		$ligne = preg_replace('#\*/#','*/</font>',$ligne);

		// chaine de caractères : ff00ff
		// commande a52829
		// commentaire : 1014ff
		// variables : 008a8c

		$ligne = preg_replace('#'.chr(9).'#','<span style="color:#fff;">------</span>',$ligne);
		$texte[$num] = '<span style="display:block;width : 800px; overflow-y:visible;background:#fff;"><b style="color:#000;">'.$num.' </b>'. $ligne .'</span>';

	}
}
function atelier_debut_textarea($texte=array()) {

	atelier_colorier(&$texte);
	$t ='';
	foreach ($texte as$ligne) $t .= $ligne;
	return '<div style="display: block;curser:text;border : 1px solid #000; width: 480px; height: 600px; overflow: auto; text-align:left;">'
		.$t;
}

function atelier_fin_textarea() {
	return '</div>';
}
?>
