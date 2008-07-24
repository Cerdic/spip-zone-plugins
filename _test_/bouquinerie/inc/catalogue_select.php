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

// recupere les données d'un catalogue necessaire pour composer le formulaire d'édition
// new=oui catalogue à créer si on valide le formulaire

function inc_catalogue_select_dist($id_catalogue) {
	if (is_numeric($id_catalogue)) {
		$row = sql_fetsel("*", "spip_catalogues", "id_catalogue=$id_catalogue");
	}  
  	else {  
		// si id_catalogue n'est pas numérique alors c'est une demande de création
		$row['titre'] = _T('bouq:titre_nouveau_catalogue');
		$row['id_catalogue'] = 0;
	}

	return $row;
}

?>
