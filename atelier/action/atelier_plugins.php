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

function action_atelier_plugins_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
	if (!$id_auteur) redirige_par_entete('./');

	$id_projet = $arg;

	$r = sql_fetsel('prefixe','spip_projets','id_projet='.$id_projet);
	$prefixe = $r['prefixe'];

	$creer_repertoire = _request('creer_repertoire');

	if(isset($creer_repertoire)) {
		$action = "creer_repertoire";
	}

	if (isset($action)) {
		$fonction = 'atelier_'.$action;
		if (function_exists($fonction)) {
			call_user_func($fonction,$prefixe);
		}
	}

        $redirect = parametre_url(urldecode(generer_url_ecrire('projets')),
				'id_projet', $id_projet, '&') . $err;

	include_spip('inc/headers');
	redirige_par_entete($redirect);
}

function atelier_creer_repertoire($prefixe) {
	mkdir(_DIR_PLUGINS.'/'.$prefixe);
}

?>
