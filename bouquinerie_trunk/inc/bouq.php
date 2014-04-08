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

function bouq_verifier_base() {
	if (!$GLOBALS['meta']['BaseBouq']) return false;
	else  return true;
}

function bouq_verifier_catalogue() {
	$row = sql_fetsel("*", "spip_catalogues", "");
	if ($row) return true;
	return false;
}

function bouq_verifier_livres() {
	$row = sql_fetsel("*", "spip_livres", "");
	if ($row) return true;
	return false;
}

function bouq_verifier_livres_orphelins() {
	$row = sql_fetsel("*","spip_livres","id_catalogue=0");
	if ($row) return true;
	return $false;
}

function bouq_afficher_catalogue($id_catalogue) {

	$row = sql_fetsel("titre, descriptif", "spip_catalogues", "id_catalogue=$id_catalogue");
	return $row['titre'] .' / '. $row['descriptif'];
}

function bouq_autoriser() {
	if ($admin AND $connect_statut != "0minirezo") {
		echo _T('avis_non_acces_page');
		return false;
	}
	return true;
}

function get_nombre_livres($id_catalogue) {
	$r = sql_fetsel('count(*) as nombre','spip_livres_catalogues',"id_catalogue=$id_catalogue");
	return $r['nombre'];
}

function set_langue(&$afficher_langue,&$langue_defaut) {
	if (($GLOBALS['meta']['multi_rubriques'] == 'oui'
		AND (!isset($GLOBALS['id_rubrique'])))
		OR $GLOBALS['meta']['multi_articles'] == 'oui') {
		
		$afficher_langue = true;

		if (isset($GLOBALS['langue_rubrique'])) $langue_defaut = $GLOBALS['langue_rubrique'];
		else $langue_defaut = $GLOBALS['meta']['langue_site'];
	} else $afficher_langue = $langue_defaut = '';
}
?>
