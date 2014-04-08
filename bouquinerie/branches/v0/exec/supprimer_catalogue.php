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


function exec_supprimer_catalogue_dist() {

	include_spip('inc/bouq_presentation');
	$nom_page = debut_page_bouq(_T('bouq:titre_supprimer_catalogue'),"supprimer_catalogue");

	if (!bouq_autoriser()) exit;

	$id_catalogue =  intval(_request('id_catalogue'));
	include_spip('inc/supprimer_catalogue');
	$supprimer_catalogue = charger_fonction('supprimer_catalogue','inc');

	echo debut_gauche($nom_page,true);

	bouq_afficher_raccourcis();

	echo '<br />';

	bouq_afficher_infos();

	echo debut_droite($nom_page,true);
	
	echo debut_boite_alerte();
	echo '<p>'._T('bouq:attention_supprimer_catalogue').'</p>';
	echo '<p>Ce catalogue contient <b>'.get_nombre_livres($id_catalogue).'</b> livres.</p>';

	echo $supprimer_catalogue($id_catalogue);
	echo fin_boite_alerte();

	echo fin_gauche();
	echo fin_page_bouq();
}

?>
