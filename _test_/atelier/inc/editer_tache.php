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

function inc_editer_tache_dist($new,$id_tache=0,$row=array(),$id_projet=0) {

	if (($new="oui") &&($id_projet != 0))
		$row['id_projet'] = $id_projet;

	$form = "<input type='hidden' name='editer_tache' value='oui' />\n"
	. editer_tache_titre($row['titre'])
	. editer_tache_descriptif($row['descriptif'])
	. editer_tache_projet($row['id_projet'])
	. editer_tache_etat($row['etat'])
	. editer_tache_urgence($row['urgence'])
	. editer_tache_auteur($row['id_auteur'])
	. editer_tache_version($row['version'],$row['id_projet'])

	. ("<div align='right'><input class='fondo' type='submit' value='"
	. _T('bouton_enregistrer')
	. "' /></div>");

	return generer_action_auteur("editer_tache", $id_tache, '', $form, " method='post' name='formulaire'");
}

function editer_tache_titre($titre) {

	return	"\n<p>" .
		_T('texte_titre_obligatoire') .
		"\n<br /><input type='text' name='titre' class='formo spip_small' value=\"" .
	  	entites_html($titre) .
		"\" size='40' " .
		  " />\n</p>";
}

function editer_tache_descriptif($descriptif) {
	return ("\n<p>" . _T('atelier:texte_descriptif') .
		"<br />\n" . 
		"<textarea name='descriptif' class='forml' rows='2' cols='40'>" .
		entites_html($descriptif) .
		"</textarea></p>");
}


function editer_tache_projet($id_projet) {
	$opt = '<select name="id_projet" value="'.$id_projet.'">';
	$query = sql_select("titre, id_projet", "spip_projets");
	while ($row = sql_fetch($query)) {
		if ($id_projet == $row['id_projet'])
			$opt .= '<option selected value="'.$row['id_projet'].'">'.$row['titre'].'</option>';
		else
			$opt .= '<option value="'.$row['id_projet'].'">'.$row['titre'].'</option>';
	}
	$opt .= '</select>';


	$msg = _T('atelier:titre_edit_choix_projet');
	$logo = "racine-site-24.gif";

	return debut_cadre_couleur($logo, true, "", $msg) . $opt . fin_cadre_couleur(true);
}

function editer_tache_etat($etat) {
	$opt = '<select name="etat" value="'.$etat.'">"';

	if ($etat == 'fermee') {
		$opt .= '<option value="ouverte">Ouverte</option>';
		$opt .= '<option selected value="fermee">Fermée</option>';
	}
	else {
		$opt .= '<option selected value="ouverte">Ouverte</option>';
		$opt .= '<option value="fermee">Fermée</option>';
	}


	$opt .= '</select>';

	$msg = _T('atelier:titre_edit_choix_etat');
	$logo = "racine-site-24.gif";
	return debut_cadre_couleur($logo, true, "", $msg) . $opt . fin_cadre_couleur(true);
}

function editer_tache_urgence($urgence) {
	$opt = '<select name="urgence" value="'.$urgence.'">"';
	if ($urgence) $selected = 'selected value="'.$urgence .'"';

	if ($urgence == 'tres_forte') $opt .= '<option '.$selected.'>Très forte</option>';
	else $opt .= '<option value="tres_forte">Très forte</option>';

	if ($urgence == 'forte') $opt .= '<option '.$selected.'>Forte</option>';
	else $opt .= '<option value="forte">Forte</option>';

	if ($urgence == 'moyenne') $opt .= '<option '.$selected.'>Moyenne</option>';
	else $opt .= '<option value="moyenne">Moyenne</option>';

	if ($urgence == 'faible') $opt .= '<option '.$selected.'>Faible</option>';
	else $opt .= '<option value="faible">Faible</option>';

	if ($urgence == 'tres_faible') $opt .= '<option '.$selected.'>Très faible</option>';
	else $opt .= '<option value="tres_faible">Très faible</option>';

	$opt .= '</select>';

	$msg = _T('atelier:titre_edit_choix_urgence');
	$logo = "racine-site-24.gif";
	return debut_cadre_couleur($logo, true, "", $msg) . $opt . fin_cadre_couleur(true);
}
function editer_tache_auteur($id_auteur) {
	$res = '<select name="id_auteur" value="'.$id_auteur.'">"';
	$q = sql_select('id_auteur, nom','spip_auteurs');
	while ($r = sql_fetch($q))
		$res .= '<option value="'.$r['id_auteur'].'">'.$r['nom'].'</option>';
	$res .= '</select>';

	$msg = _T('atelier:titre_edit_choix_auteur');
	$logo = "racine-site-24.gif";
	return debut_cadre_couleur($logo, true, "", $msg) . $res . fin_cadre_couleur(true);
}
function editer_tache_version($version,$id_projet) {
	$r = sql_fetsel('versions','spip_projets',"id_projet=$id_projet");
	$versions = explode('/',$r['versions']);

	$res = '<select name="version" value="'.$version.'">"';
	foreach($versions as $v)
		if ($v == $version) $res .= '<option selected value="'.$v.'">'.$v.'</option>';
		else $res .= '<option value="'.$v.'">'.$v.'</option>';
	$res .= '</select>';

	$msg = _T('atelier:titre_edit_choix_version');
	$logo = "racine-site-24.gif";
	return debut_cadre_couleur($logo, true, "", $msg) . $res . fin_cadre_couleur(true);
}
?>
