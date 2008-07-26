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

function action_atelier_versions_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
	if (!$id_auteur) redirige_par_entete('./');


	$id_projet = intval($arg);

	$r = sql_fetsel('versions','spip_projets',"id_projet=$id_projet");
	if ($r['versions']) $versions = $r['versions'] . '/' . _request('version');
	else {
		$versions = _request('version');
	}


	sql_updateq('spip_projets',array('versions' => $versions),"id_projet=$id_projet");	
	
        $redirect = parametre_url(urldecode(generer_url_ecrire('atelier_roadmap')),
				'id_projet', $id_projet, '&') . $err;

	include_spip('inc/headers');
	redirige_par_entete($redirect);

}

?>
