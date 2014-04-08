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

function inc_rechercher_livre_dist($titre='',$auteur='',$illustrateur='',$edition='',$prix_vente='',$isbn='') {

	$form = "<input type='hidden' name='rechercher_livre' value='oui' />\n"
	. chercher_livre_titre($titre)
	. chercher_livre_auteur($auteur)
	. chercher_livre_illustrateur($illustrateur)
	. chercher_livre_edition($edition)
	. chercher_livre_prix_vente($prix_vente)
	. chercher_livre_isbn($isbn)

	. ("<div align='right'><input class='fondo' type='submit' value='"
	. _T('bouq:bouton_rechercher')
	. "' /></div>");

	return generer_action_auteur("rechercher_livre", $id_livre, $retour, $form, " method='post' name='formulaire'");
}

function chercher_livre_titre($titre) {
	return	"\n<p>" .
		'<b>'._T('bouq:par_titre').'</b> '.
		"\n<br /><input type='text' name='titre' class='formo spip_small' value=\"" .
	  	entites_html($titre) .
		"\" size='40' " .
		  " />\n</p>";
}

function chercher_livre_auteur($auteur) {
	return	"\n<p>" .
		'<b>'._T('bouq:par_auteur').'</b> '.
		"\n<br /><input type='text' name='auteur' class='formo spip_small' value=\"" .
	  	entites_html($auteur) .
		"\" size='40' " .
		  " />\n</p>";
}

function chercher_livre_illustrateur($illustrateur) {
	return	"\n<p>" .
		'<b>'._T('bouq:par_illustrateur').'</b> '.
		"\n<br /><input type='text' name='illustrateur' class='formo spip_small' value=\"" .
	  	entites_html($illustrateur) .
		"\" size='40' " .
		  " />\n</p>";
}

function chercher_livre_edition($edition) {
	return	"\n<p>" .
		'<b>'._T('bouq:par_edition').'</b> '.
		"\n<br /><input type='text' name='edition' class='formo spip_small' value=\"" .
	  	entites_html($edition) .
		"\" size='40' " .
		  " />\n</p>";
}

function chercher_livre_prix_vente($prix_vente) {
	return	"\n<p>" .
		'<b>'._T('bouq:par_prix_vente').'</b> '.
		"\n<br /><input type='text' name='prix_vente' class='formo spip_small' value=\"" .
	  	entites_html($prix_vente) .
		"\" size='40' " .
		  " />\n</p>";
}

function chercher_livre_isbn($isbn) {
	return	"\n<p>" .
		'<b>'._T('bouq:par_isbn').'</b> '.
		"\n<br /><input type='text' name='isbn' class='formo spip_small' value=\"" .
	  	entites_html($isbn) .
		"\" size='40' " .
		  " />\n</p>";
}
?>
