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

function action_installer_base_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	include_spip('base/create');
	include_spip('base/abstract_sql');
	creer_base();

	include_spip('inc/meta'); // inutile, utiliser la valeur de plugin.xml
	ecrire_meta('bouq_version', '0.1');
	ecrire_meta('BaseBouq', 'oui');
	ecrire_metas();
                          
	redirige_par_entete(rawurldecode(generer_url_ecrire('admin_bouquinerie')));
}

?>
