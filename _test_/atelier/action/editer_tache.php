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

function action_editer_tache_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
	if (!$id_auteur) redirige_par_entete('./');


	if (!$id_tache = intval($arg)) {
		$id_tache = insert_tache();
	}

	// Enregistre l'envoi dans la BD
	$err = taches_set($id_tache);

        $redirect = parametre_url(urldecode(generer_url_ecrire('taches')),
				'id_tache', $id_tache, '&') . $err;

	include_spip('inc/headers');
	redirige_par_entete($redirect);
}

function insert_tache() {

	include_spip('base/abstract_sql');

	sql_insertq('spip_taches', array('descriptif' => ''));

	$ret = sql_fetsel(
		array('MAX(id_tache) as id_tache'),
		array('spip_taches')
	);

	return $ret['id_tache'];
}

function taches_set($id_tache) {
	$err = '';

	$c = array();
	foreach (array('titre', 'descriptif', 'id_projet','id_auteur','etat') as $champ)
	       $c[$champ] = _request($champ);

	revision_tache($id_tache, $c);

	return $err;
}

function revision_tache($id_tache, $c=false) {
	include_spip('inc/autoriser');
	include_spip('inc/filtres');

	// Ces champs seront pris nom pour nom (_POST[x] => spip_taches.x)
	$champs_normaux = array('titre', 'descriptif','id_projet','id_auteur','etat');

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

	spip_query("UPDATE spip_taches SET ".join(', ', $update)." WHERE id_tache=$id_tache");

}
?>
