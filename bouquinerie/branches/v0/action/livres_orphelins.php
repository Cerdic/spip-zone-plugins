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

function action_livres_orphelins_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
	if (!$id_auteur) redirige_par_entete('./');

	if ($id_catalogue = intval(_request('id_catalogue'))) {
		sql_update("spip_livres",array('id_catalogue' => $id_catalogue),"id_catalogue=0");
		$q = sql_select("id_livre","spip_livres");
		while ($r = sql_fetch($q))
			sql_insertq("spip_livres_catalogues",array('id_livre' => $r['id_livre'], 'id_catalogue' => $id_catalogue));
	}

	redirige_par_entete(generer_url_ecrire("admin_bouquinerie"));
}

?>
