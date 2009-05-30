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

function action_editer_catalogue_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// si id_catalogue n'est pas un nombre, c'est une creation 
	// mais on verifie qu'on a toutes les données qu'il faut.
	if (!$id_catalogue = intval($arg)) {
		$id_catalogue = insert_catalogue();
	}

	// Enregistre l'envoi dans la BD
	$err = catalogues_set($id_catalogue);

/*	$redirect = parametre_url(urldecode(_request('redirect')),
		'id_catalogue', $id_catalogue, '&') . $err;*/
	$redirect = urldecode(_request('redirect')) . $err;

	redirige_par_entete($redirect);
}

// Appelle toutes les fonctions de modification d'un catalogue
// $err est de la forme '&trad_err=1'
function catalogues_set($id_catalogue, $c=false) {
	$err = '';

	$c = array();
	foreach (array('titre', 'descriptif') as $champ)
	       $c[$champ] = _request($champ);
			
	revision_catalogue($id_catalogue, $c);

	return $err;
}

function insert_catalogue() {

	include_spip('base/abstract_sql');

	// ajout du nouveau catalogue dans la BD

	sql_insertq('spip_catalogues', array('descriptif' => ''));

	$ret = sql_fetsel(
		array('MAX(id_catalogue) as id_catalogue'),
		array('spip_catalogues')
	);

	$id_catalogue = $ret['id_catalogue'];

	return $id_catalogue;
}

function revision_catalogue($id_catalogue, $c=false) {
	include_spip('inc/autoriser');
	include_spip('inc/filtres');

	// Ces champs seront pris nom pour nom (_POST[x] => spip_articles.x)
	$champs_normaux = array('titre', 'descriptif');

	// ne pas accepter de titre vide
	if (_request('titre', $c) === '')
		$c = set_request('titre', _T('ecrire:info_sans_titre'), $c);

	$champs = array();
	foreach ($champs_normaux as $champ) {
		$val = _request($champ, $c);
		if ($val !== NULL)
			$champs[$champ] = corriger_caracteres($val);
	}

	$update = array();
	foreach ($champs as $champ => $val)
		$update[] = $champ . '=' . _q($val);

	if (!count($update)) return;

	spip_query("UPDATE spip_catalogues SET ".join(', ', $update)." WHERE id_catalogue=$id_catalogue");

}

?>
