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

// compatibilite SPIP 1.91

if (!defined('_DIR_PLUGIN_ATELIER')){
  $p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
  define('_DIR_PLUGIN_ATELIER',(_DIR_PLUGINS.end($p)));
}

function atelier_ajouter_boutons($flux) {
	include_spip('inc/atelier_autoriser');
	if (atelier_autoriser())
		$flux['atelier']= new Bouton(find_in_path('images/atelier.png'), _T('atelier:titre'), generer_url_ecrire('atelier'));
	return $flux;
}
?>
