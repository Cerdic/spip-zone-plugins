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

function return_version() {
	$version = lire_meta('bouq_version');
	return $version;
}

function generer_url_livres($p) {
	$type = "livre";
	$_id = interprete_argument_balise(1,$p);
	if (!$_id) $_id = champ_sql('id_' . $type, $p);
	if ($s = $p->id_boucle) $s = $p->boucles[$s]->sql_serveur;
	$s = addslashes($s);
	if ($s)	return "'./?"._SPIP_PAGE."=$type&amp;id_$type=' . $_id . '&amp;connect=$s'";
	else return "'./?"._SPIP_PAGE."=$type&amp;id_$type=' . $_id";
}

function balise_BOUQUINERIE_VERSION($p) {
	$p->code ='return_version()';
	$p->statut = 'php';
	return $p;
}

function balise_URL_LIVRE($p) {

	$p->code = generer_url_livres($p);
	$p->interdire_scripts = false;
	return $p;
}

?>
