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

function inc_atelier_edit_fichier_dist($a=array()) {
	if ($a['mode'] == "absolu") lire_fichier($a['fichier'],&$contenu);
	else lire_fichier(_DIR_PLUGINS.$a['prefixe'].'/'.$a['fichier'],&$contenu);


	$texte = file(_DIR_PLUGINS.$a['prefixe'].'/'.$a['fichier']);
	include_spip('inc/atelier_presentation');

	$form = "<input type='hidden' name='atelier_edit_fichier' value='oui' />\n"
	. '<input type="hidden" name="fichier" value="'.$a['fichier'].'" />'
	. '<input type="hidden" name="prefixe" value="'.$a['prefixe'].'" />'
	. atelier_debut_textarea($texte) . atelier_fin_textarea()
	.'<textarea style="border:1px solid #000;" name="contenu" rows="50" cols="75">'.entites_html($contenu).'</textarea>'

	. ("<div align='right'><input class='fondo' type='submit' value='"
	. _T('atelier:bouton_enregistrer_fichier')
	. "' /></div>");

	$arg = $a['id_projet']; 

	return generer_action_auteur("atelier_edit_fichier", $arg, '', $form, " method='post' name='formulaire'");

}

?>
