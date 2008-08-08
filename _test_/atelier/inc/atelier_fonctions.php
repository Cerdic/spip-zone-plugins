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

function atelier_recuperer_nom_auteur($id_auteur) {
	$r = sql_fetsel('nom','spip_auteurs',"id_auteur=$id_auteur");
	return $r['nom'];
}

function atelier_recuperer_versions($id_projet) {
	$r = sql_fetsel('versions','spip_projets',"id_projet=$id_projet");
	if ($r['versions'] != '') $versions = explode ('/',$r['versions']);
	return $versions;
}

function atelier_recuperer_taches($id_projet, $version) {
	$taches = array();
	$q = sql_select('id_tache, titre, etat',
			'spip_taches',
			'id_projet='.$id_projet.' AND version="'.$version.'"',
			'',
			'etat');

	while ($r = sql_fetch($q))
		$taches[] = '<a href="'.generer_url_ecrire('taches','id_tache='.$r['id_tache']).'">'.$r['titre'].'</a></td><td style="text-align:right;">'.$r['etat'];
	return $taches;
}

function atelier_recuperer_auteurs_projets($id_projet) {
	$auteurs = '';
	$q = sql_select('id_auteur','spip_auteurs_projets',"id_projet=$id_projet");
	while ($r = sql_fetch($q)) {
		$a = sql_fetsel('nom','spip_auteurs','id_auteur='.$r['id_auteur']);
		$auteurs .= $a['nom'] .' ';
	}
	return $auteurs;
}

function atelier_recuperer_taches_ouvertes($id_projet, $version) {
	$taches = array();
	$q = sql_select('id_tache, titre, etat',
			'spip_taches',
			'id_projet='.$id_projet.' AND version="'.$version.'" AND etat="ouverte"',
			'',
			'etat');

	while ($r = sql_fetch($q))
		$taches[] = '<a href="'.generer_url_ecrire('taches','id_tache='.$r['id_tache']).'">'.$r['titre'].'</a>';
	return $taches;
}

function atelier_recuperer_taches_fermees($id_projet, $version) {
	$taches = array();
	$q = sql_select('id_tache, titre, etat',
			'spip_taches',
			'id_projet='.$id_projet.' AND version="'.$version.'" AND etat="fermee"',
			'',
			'etat');

	while ($r = sql_fetch($q))
		$taches[] = '<a href="'.generer_url_ecrire('taches','id_tache='.$r['id_tache']).'">'.$r['titre'].'</a>';
	return $taches;
}

// modifie la config du plugin spixplorer pour le projet en cours
function atelier_init_spx($prefixe) {
	lire_fichier(_DIR_PLUGINS.'spixplorer/config/spx_conf.php',&$contenu);
	$contenu = preg_replace('#(\$GLOBALS\[\'spx\'\]\["home_dir"\]) \= "(.*)"#','${1} = "plugins/'.$prefixe.'"',$contenu);
	ecrire_fichier(_DIR_PLUGINS.'spixplorer/config/spx_conf.php',$contenu);
}

// prepare un texte pour inclusion dans fichier php
function text_to_php($value) {
	$value = preg_replace("#'#","\'",$value);
	$value = preg_replace("#é#","&eacute;",$value);
	$value = preg_replace("#è#","&egrave;",$value);
	$value = preg_replace("#à#","&agrave;",$value);
	$value = preg_replace("#ê#","&ecirc;",$value);
	$value = preg_replace("#â#","&acirc;",$value);
	$value = preg_replace("#î#","&icirc;",$value);
	$value = preg_replace("#ï#","&iuml;",$value);
	$value = preg_replace("#œ#","&oelig;",$value);
	$value = preg_replace("#ù#","&ugrave;",$value);
	$value = preg_replace("#û#","&ucirc;",$value);
	$value = preg_replace("#ç#","&ccedil;",$value);
	$value = preg_replace("#É#","&Eacute;",$value);
	$value = preg_replace("#È#","&Egrave;",$value);
	$value = preg_replace("#À#","&Agrave;",$value);
	$value = preg_replace("#Ê#","&Ecirc;",$value);
	$value = preg_replace("#Â#","&Acirc;",$value);
	$value = preg_replace("#Î#","&Icirc;",$value);
	$value = preg_replace("#Ï#","&Iuml;",$value);
	$value = preg_replace("#Œ#","&OElig;",$value);
	$value = preg_replace("#Ù#","&Ugrave;",$value);
	$value = preg_replace("#Û#","&Ucirc;",$value);
	$value = preg_replace("#Ç#","&Ccedil;",$value);
	return $value;
}

// prepare un texte pour inclusion dans plugin.xml
function text_to_plugin($value) {

	$value = preg_replace("#è#","&#232;",$value);
	$value = preg_replace("#é#","&#233;",$value);
	$value = preg_replace("#ê#","&#234;",$value);

	$value = preg_replace("#à#","&#224;",$value);
	$value = preg_replace("#á#","&#225;",$value);
	$value = preg_replace("#â#","&#226;",$value);

	$value = preg_replace("#î#","&#238;",$value);
	$value = preg_replace("#ï#","&#239;",$value);

	$value = preg_replace("#ù#","&#249;",$value);
	$value = preg_replace("#û#","&#251;",$value);
	$value = preg_replace("#ç#","&#231;",$value);

	$value = preg_replace("#Œ#","&#156;",$value);
	$value = preg_replace("#œ#","&#156;",$value);

	$value = preg_replace("#À#","&#192;",$value);
	$value = preg_replace("#Á#","&#193;",$value);
	$value = preg_replace("#Â#","&#194;;",$value);

	$value = preg_replace("#È#","&#200;",$value);
	$value = preg_replace("#É#","&#201;",$value);
	$value = preg_replace("#Ê#","&#202;",$value);

	$value = preg_replace("#Î#","&#206;",$value);
	$value = preg_replace("#Ï#","&#207;",$value);

	$value = preg_replace("#Ù#","&#217;",$value);
	$value = preg_replace("#Û#","&#219;",$value);
	$value = preg_replace("#Ü#","&#220;",$value);

	$value = preg_replace("#Ç#","&#199;",$value);
	$value = preg_replace("#€#","&#128;",$value);
	$value = preg_replace("#©#","&#169;",$value);

	return $value;
}
?>
