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

// compatibilite SPIP 1.91

if (!defined('_DIR_PLUGIN_BOUQUINERIE')){
  $p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
  define('_DIR_PLUGIN_BOUQUINERIE',(_DIR_PLUGINS.end($p)));
}

// ajout d'un onglet sur la page de configuration de SPIP
function bouq_ajouter_boutons($flux){
	include_spip('inc/bouq');
	if (bouq_autoriser())
		$flux['bouquinerie']= new Bouton(find_in_path('images/bibliotheque.png'), _T('bouq:titre'), generer_url_ecrire('admin_bouquinerie'));
	return $flux;
}

?>
