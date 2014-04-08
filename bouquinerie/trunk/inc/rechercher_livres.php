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

function inc_rechercher_livres_dist($titre='',$auteur='',$illustrateur='',$edition='',$prix_vente='',$isbn='') {

	$livres = array();

	$where = '';
	if ($titre) $where .= 'AND titre LIKE "%'.$titre.'%" ';
	if ($auteur) $where .= 'AND auteur LIKE "%'.$auteur.'%" ';
	if ($illustrateur) $where .= 'AND illustrateur LIKE "%'.$illustrateur.'%" ';
	if ($edition) $where .= 'AND edition LIKE "%'.$edition.'%" ';
	if ($prix_vente) $where .= 'AND prix_vente LIKE "%'.$prix_vente.'%" ';
	if ($isbn) $where .= 'AND isbn LIKE "%'.$isbn.'%" ';

	// on retire les 4 permiers caractères (AND )

	$where = substr($where,4);

	if ($where) {
		$q = sql_select('id_livre, titre','spip_livres',$where);
		while ($r= sql_fetch($q)) {
			$livre = array('id_livre' => $r['id_livre'], 'titre' => $r['titre']);
			array_push(&$livres,$livre);
		}
	}
	return $livres;

}

?>
