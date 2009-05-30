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

function inc_editer_projet_dist($new,$id_projet=0,$row=array()) {

	$form = "<input type='hidden' name='editer_projet' value='oui' />\n"
	. editer_projet_titre($row['titre'])
	. editer_projet_descriptif($row['descriptif'])
	. editer_projet_auteur($row['id_projet'])
	. editer_projet_type($row['type'])
	. editer_projet_prefixe($row['prefixe']);
	if ($new == "oui")
		$form .= editer_projet_arbo();

	$form .= ("<div align='right'><input class='fondo' type='submit' value='"
	. _T('bouton_enregistrer')
	. "' /></div>");

	return generer_action_auteur("editer_projet", $id_projet, '', $form, " method='post' name='formulaire'");
}


function editer_projet_arbo(){
	return ' <input type="checkbox" name="arbo" value="oui" /> '._T('atelier:creer_arbo');
}

function editer_projet_titre($titre) {

	return	"\n<p>" .
		_T('texte_titre_obligatoire') .
		"\n<br /><input type='text' name='titre' class='formo spip_small' value=\"" .
	  	entites_html($titre) .
		"\" size='40' " .
		  " />\n</p>";
}

function editer_projet_descriptif($descriptif) {
	return ("\n<p>" . _T('atelier:texte_descriptif') .
		"<br />\n" . 
		"<textarea name='descriptif' class='forml' rows='10' cols='40'>" .
		entites_html($descriptif) .
		"</textarea></p>");
}

function editer_projet_auteur($id_projet) {
	$q = sql_select('id_auteur, nom','spip_auteurs');
	while ($r = sql_fetch($q)) {
		$a = sql_fetsel('id_auteur','spip_auteurs_projets','id_projet='.$id_projet.' AND id_auteur='.$r["id_auteur"]);
		if ($a['id_auteur']) $opt .= '<input type="checkbox" checked="yes" name="auteur_'.$r['id_auteur'].'" value="yes">'.$r['nom'].'</input>';
		else $opt .= '<input type="checkbox" name="auteur_'.$r['id_auteur'].'" value="yes">'.$r['nom'].'</input>';
	}

	$msg = _T('atelier:titre_projet_choix_auteur');
	$logo = "racine-site-24.gif";

	return debut_cadre_couleur($logo, true, "", $msg) . $opt . fin_cadre_couleur(true);

}

function editer_projet_type($type) {
	$opt = '<select name="type" value="'.$type.'">';
	$opt .= '<option selected value="plugin">Plugin</option>';
	$opt .= '<option value="squelette">Squelette</option>';
	$opt .= '</select>';

	$msg = _T('atelier:titre_edit_choix_type');
	$logo = "racine-site-24.gif";

	return debut_cadre_couleur($logo, true, "", $msg) . $opt . fin_cadre_couleur(true);
}

function editer_projet_prefixe($prefixe) {
	return	"\n<p>" .
		_T('atelier:texte_prefixe_obligatoire') .
		"\n<br /><input type='text' name='prefixe' class='formo spip_small' value=\"" .
	  	entites_html($prefixe) .
		"\" size='40' " .
		  " />\n</p>";
}

?>
