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

include_spip('inc/actions');

function inc_editer_livre_dist($new, $id_livre=0, $row=array()) {

	$form = "<input type='hidden' name='editer_livre' value='oui' />\n"
	. editer_livre_titre($row['titre'])
	. editer_livre_auteur($row['auteur'])
	. editer_livre_illustrateur($row['illustrateur'])
	. editer_livre_edition($row['edition'])
	. editer_livre_prix_vente($row['prix_vente'])
	. editer_livre_isbn($row['isbn'])
	. editer_livre_catalogue($row['id_catalogue'])
	. editer_livre_statut($row['statut'])
	. editer_livre_etat_livre($row['etat_livre'])
	. editer_livre_etat_jaquette($row['etat_jaquette'])
	. editer_livre_format($row['format'])
	. editer_livre_reliure($row['reliure'])
	. editer_livre_type_livre($row['type_livre'])
	. editer_livre_lieu_edition($row['lieu_edition'])
	. editer_livre_annee_edition($row['annee_edition'])
	. editer_livre_num_edition($row['num_edition'])
	. editer_livre_inscription($row['inscription'])
	. editer_livre_remarque($row['remarque'])
	. editer_livre_commentaire($row['commentaire'])
	. editer_livre_prix_achat($row['prix_achat'])
	. editer_livre_lieu($row['lieu'])
	. editer_livre_num_facture($row['num_facture'])

	. ("<div align='right'><input class='fondo' type='submit' value='"
	. _T('bouton_enregistrer')
	. "' /></div>");

	return generer_action_auteur("editer_livre", $id_livre, "", $form, " method='post' name='formulaire'");
}


function editer_livre_titre($titre) {
	return	"\n<p>" .
		_T('texte_titre_obligatoire') .
		"\n<br /><input type='text' name='titre' style='font-weight: bold; ' class='formo spip_small' value=\"" .
	  	entites_html($titre) .
		"\" size='40' " .
		  " />\n</p>";
}

function editer_livre_auteur($auteur) {
	return	"\n<p>" .
		_T('bouq:texte_auteur') .
		"\n<br /><input type='text' name='auteur' class='formo spip_small' value=\"" .
	  	entites_html($auteur) .
		"\" size='40' " .
		  " />\n</p>";
}

function editer_livre_illustrateur($illustrateur) {
	return	"\n<p>" .
		_T('bouq:texte_illustrateur') .
		"\n<br /><input type='text' name='illustrateur' class='formo spip_small' value=\"" .
	  	entites_html($illustrateur) .
		"\" size='40' " .
		  " />\n</p>";
}

function editer_livre_edition($edition) {
	return	"\n<p>" .
		_T('bouq:texte_edition') .
		"\n<br /><input type='text' name='edition' class='formo spip_small' value=\"" .
	  	entites_html($edition) .
		"\" size='40' " .
		  " />\n</p>";
}

function editer_livre_prix_vente($prix_vente) {
	return	"\n<p>" .
		_T('bouq:texte_prix_vente') .
		"\n<br /><input type='text' name='prix_vente' class='formo spip_small' value=\"" .
	  	entites_html($prix_vente) .
		"\" size='40' " .
		  " />\n</p>";
}

function editer_livre_isbn($isbn) {
	return	"\n<p>" .
		_T('bouq:texte_isbn') .
		"\n<br /><input type='text' name='isbn' class='formo spip_small' value=\"" .
	  	entites_html($isbn) .
		"\" size='40' " .
		  " />\n</p>";
}

function editer_livre_catalogue($id_catalogue) {

	$opt = '<select name="id_catalogue" value="'.$id_catalogue.'">';
	$query = sql_select("titre, id_catalogue", "spip_catalogues");
	while ($row = sql_fetch($query)) {
		if ($id_catalogue == $row['id_catalogue'])
			$opt .= '<option selected value="'.$row['id_catalogue'].'">'.$row['titre'].'</option>';
		else
			$opt .= '<option value="'.$row['id_catalogue'].'">'.$row['titre'].'</option>';
	}
	$opt .= '</select>';


	$msg = _T('bouq:titre_cadre_interieur_catalogue');
	$logo = "racine-site-24.gif";
	return debut_cadre_couleur($logo, true, "", $msg) . $opt . fin_cadre_couleur(true);
}

function editer_livre_statut($statut) {

	$opt = 	'<select name="statut" value="a_vendre">' .
		'<option value="a_vendre">'._T('bouq:statut_a_vendre').'</option>' .
		'<option value="vendu">'._T('bouq:statut_vendu').'</option>' .
		'<option value="reserve">'._T('bouq:statut_reserve').'</option>' .
		'</select>';

	$msg = _T('bouq:titre_cadre_interieur_statut');
	$logo = "racine-site-24.gif";
	return debut_cadre_couleur($logo, true, "", $msg) . $opt .fin_cadre_couleur(true);
}

function editer_livre_etat_livre($etat_livre) {

	return ("\n<p>" . _T('bouq:texte_etat_livre') .
		"<br />\n" . 
		"<textarea name='etat_livre' class='forml' rows='2' cols='40'>" .
		entites_html($etat_livre) .
		"</textarea></p>");
}

function editer_livre_etat_jaquette($etat_jaquette) {
	return ("\n<p>" . _T('bouq:texte_etat_jaquette') .
		"<br />\n" . 
		"<textarea name='etat_jaquette' class='forml' rows='2' cols='40'>" .
		entites_html($etat_jaquette) .
		"</textarea></p>");
}

function editer_livre_format($format) {
	return	"\n<p>" .
		_T('bouq:texte_format') .
		"\n<br /><input type='text' name='format' class='formo spip_small' value=\"" .
	  	entites_html($format) .
		"\" size='40' " .
		  " />\n</p>";
}

function editer_livre_reliure($reliure) {
	return	"\n<p>" .
		_T('bouq:texte_reliure') .
		"\n<br /><input type='text' name='reliure' class='formo spip_small' value=\"" .
	  	entites_html($reliure) .
		"\" size='40' " .
		  " />\n</p>";
}

function editer_livre_type_livre($type_livre) {
	return	"\n<p>" .
		_T('bouq:texte_type_livre') .
		"\n<br /><input type='text' name='type_livre' class='formo spip_small' value=\"" .
	  	entites_html($type_livre) .
		"\" size='40' " .
		  " />\n</p>";
}




function editer_livre_lieu_edition($lieu_edition) {
	return	"\n<p>" .
		_T('bouq:texte_lieu_edition') .
		"\n<br /><input type='text' name='lieu_edition' class='formo spip_small' value=\"" .
	  	entites_html($lieu_edition) .
		"\" size='40' " .
		  " />\n</p>";
}

function editer_livre_annee_edition($annee_edition) {
	return	"\n<p>" .
		_T('bouq:texte_annee_edition') .
		"\n<br /><input type='text' name='annee_edition' class='formo spip_small' value=\"" .
	  	entites_html($annee_edition) .
		"\" size='40' " .
		  " />\n</p>";
}

function editer_livre_num_edition($num_edition) {
	return	"\n<p>" .
		_T('bouq:texte_num_edition') .
		"\n<br /><input type='text' name='num_edition' class='formo spip_small' value=\"" .
	  	entites_html($num_edition) .
		"\" size='40' " .
		  " />\n</p>";
}

function editer_livre_remarque($etat_remarque) {
	return ("\n<p>" . _T('bouq:texte_remarque') .
		"<br />\n" . 
		"<textarea name='remarque' class='forml' rows='2' cols='40'>" .
		entites_html($remarque) .
		"</textarea></p>");
}

function editer_livre_inscription($inscription) {
	return ("\n<p>" . _T('bouq:texte_inscription') .
		"<br />\n" . 
		"<textarea name='inscription' class='forml' rows='2' cols='40'>" .
		entites_html($inscription) .
		"</textarea></p>");
}

function editer_livre_commentaire($commentaire) {
	return ("\n<p>" . _T('bouq:texte_commentaire') .
		"<br />\n" . 
		"<textarea name='etat_commentaire' class='forml' rows='2' cols='40'>" .
		entites_html($etat_commentaire) .
		"</textarea></p>");
}

function editer_livre_prix_achat($prix_achat) {
	return	"\n<p>" .
		_T('bouq:texte_prix_achat') .
		"\n<br /><input type='text' name='prix_achat' class='formo spip_small' value=\"" .
	  	entites_html($prix_achat) .
		"\" size='40' " .
		  " />\n</p>";
}

function editer_livre_lieu($lieu) {
	return	"\n<p>" .
		_T('bouq:texte_lieu') .
		"\n<br /><input type='text' name='lieu' class='formo spip_small' value=\"" .
	  	entites_html($lieu) .
		"\" size='40' " .
		  " />\n</p>";
}
function editer_livre_num_facture($num_facture) {
	return	"\n<p>" .
		_T('bouq:texte_num_facture') .
		"\n<br /><input type='text' name='num_facture' class='formo spip_small' value=\"" .
	  	entites_html($num_facture) .
		"\" size='40' " .
		  " />\n</p>";
}
?>
