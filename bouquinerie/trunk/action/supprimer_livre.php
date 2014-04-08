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

function action_supprimer_livre_dist() {

//	$securiser_action = charger_fonction('securiser_action', 'inc');
//	$arg = $securiser_action();

	// si id_livre n'est pas un nombre, erreur
	if ($id_livre = intval(_request('id_livre'))) {

		$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
		if (!$id_auteur) redirige_par_entete('./');
		sql_query("DELETE FROM spip_livres WHERE id_livre=$id_livre");
		sql_query("DELETE FROM spip_livres_catalogues WHERE id_livre=$id_livre");

	}

	redirige_par_entete(generer_url_ecrire("admin_bouquinerie"));
}

?>
