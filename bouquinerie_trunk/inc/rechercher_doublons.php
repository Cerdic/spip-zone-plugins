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

/* retourne une structure de type :

['titre']
['rows'][index]['livres'] => Array ('id_livre','titre',$par) 

*/

function inc_rechercher_doublons_dist($par) {

	$r = array('titre' => $par, 'rows' => array());
	$row = array('livres' => array());

	if ($par) {
		$q = sql_query('SELECT livres_1.id_livre, livres_1.titre, livres_1.'.$par.' FROM spip_livres as livres_1
				 WHERE EXISTS (SELECT * FROM spip_livres as livres_2
						 WHERE  livres_1.id_livre <> livres_2.id_livre
						 AND  livres_1.'.$par.' = livres_2.'.$par.'
						) 
				AND livres_1.'.$par.' <> ""
 				ORDER BY '.$par
				);

		$tag = '';

		while ($e = sql_fetch($q)){
			$livre = array();

			if ($tag == '') $tag = $e[$par];
			if ($tag != $e[$par]) {
				array_push(&$r['rows'],$row);
				$row = array('livres' => array());
				$tag = $e[$par];
			}

			$livre['id_livre'] = $e['id_livre'];
			$livre['titre'] = $e['titre'];
			$livre[$par] = $e[$par];

			array_push(&$row['livres'],$livre);
		}
	}
	else return false;
	
	return $r;
}

?>
