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

// Enregistre une revision de catalogue
function revision_catalogue ($id_catalogue, $c=false) {

	include_spip('inc/modifier.php');
	modifier_contenu('catalogue', $id_catalogue,
 	        array(
 	            'nonvide' => array('titre' => _T('info_sans_titre')),
 	            'invalideur' => $invalideur,
 	            'indexation' => $indexation,
 	            'date_modif' => 'date_modif' // champ a mettre a NOW() s'il y a modif
 	        ),
 	        $c);
 	
	return ''; // pas d'erreur
}

?>
