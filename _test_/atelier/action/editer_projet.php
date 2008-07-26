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

function action_editer_projet_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
	if (!$id_auteur) redirige_par_entete('./');

	$rapport = '';

	if (!$id_projet = intval($arg)) {
		$id_projet = insert_projet();
	}

	// Enregistre l'envoi dans la BD
	$err = projets_set($id_projet);

	// création de l'arborescence
	if (_request('arbo')) {
		switch (_request('type')) {
			case 'plugin' : projets_creer_arbo_plug(); break;
			case 'squelette' : projets_creer_arbo_ske(&$rapport); break;
		}
	}

        $redirect = parametre_url(urldecode(generer_url_ecrire('projets',"id_projet=$id_projet")),
				'rapport', $rapport, '&') . $err;

	include_spip('inc/headers');
	redirige_par_entete($redirect);
}

function projets_creer_arbo_ske(&$rapport) {
	global $repertoire_squelettes_alternatifs; // plugin switcher

	$rapport .= " commande : mkdir ./$repertoire_squelettes_alternatifs/"._request('prefixe')."<br />";
	exec('mkdir ./'.$repertoire_squelettes_alternatifs.'/'._request('prefixe'),&$output,$return_var);
	foreach($output as $ligne) $rapport.=$ligne.'<br />';

	$rapport .= " commande : mkdir ./$repertoire_squelettes_alternatifs/"._request('prefixe').'/lang'."<br />";
	exec('mkdir ./'.$repertoire_squelettes_alternatifs.'/'._request('prefixe').'/lang',&$output,$return_var);
	foreach($output as $ligne) $rapport.=$ligne.'<br />';

	$rapport .= " commande : mkdir ./$repertoire_squelettes_alternatifs/"._request('prefixe').'/css'."<br />";
	exec('mkdir ./'.$repertoire_squelettes_alternatifs.'/'._request('prefixe').'/css',&$output,$return_var);
	foreach($output as $ligne) $rapport.=$ligne.'<br />';

	$rapport .= " commande : mkdir ./$repertoire_squelettes_alternatifs/"._request('prefixe').'/images'."<br />";
	exec('mkdir ./'.$repertoire_squelettes_alternatifs.'/'._request('prefixe').'/images',&$output,$return_var);
	foreach($output as $ligne) $rapport.=$ligne.'<br />';

	$rapport .= " commande : mkdir ./$repertoire_squelettes_alternatifs/"._request('prefixe').'/javascript'."<br />";
	exec('mkdir ./'.$repertoire_squelettes_alternatifs.'/'._request('prefixe').'/javascript',&$output,$return_var);
	foreach($output as $ligne) $rapport.=$ligne.'<br />';

	$rapport .= " commande : mkdir ./$repertoire_squelettes_alternatifs/"._request('prefixe').'/formulaires'."<br />";
	exec('mkdir ./'.$repertoire_squelettes_alternatifs.'/'._request('prefixe').'/formulaires',&$output,$return_var);
	foreach($output as $ligne) $rapport.=$ligne.'<br />';

	$rapport .= " commande : mkdir ./$repertoire_squelettes_alternatifs/"._request('prefixe').'/modeles'."<br />";
	exec('mkdir ./'.$repertoire_squelettes_alternatifs.'/'._request('prefixe').'/modeles',&$output,$return_var);
	foreach($output as $ligne) $rapport.=$ligne.'<br />';
}

function projets_creer_arbo_plug() {
	exec('mkdir '._DIR_PLUGINS._request('prefixe'));
	exec('mkdir '._DIR_PLUGINS._request('prefixe').'/exec');
	exec('mkdir '._DIR_PLUGINS._request('prefixe').'/action');
	exec('mkdir '._DIR_PLUGINS._request('prefixe').'/inc');
	exec('mkdir '._DIR_PLUGINS._request('prefixe').'/lang');
}

function insert_projet() {

	include_spip('base/abstract_sql');

	sql_insertq('spip_projets', array('descriptif' => ''));

	$ret = sql_fetsel(
		array('MAX(id_projet) as id_projet'),
		array('spip_projets')
	);

	return $ret['id_projet'];
}

function projets_set($id_projet) {
	$err = '';

	$c = array();
	foreach (array('titre', 'descriptif', 'type', 'prefixe') as $champ)
	       $c[$champ] = _request($champ);
			
	revision_projet($id_projet, $c);

	return $err;
}

function revision_projet($id_projet, $c=false) {
	include_spip('inc/autoriser');
	include_spip('inc/filtres');

	// Ces champs seront pris nom pour nom (_POST[x] => spip_articles.x)
	$champs_normaux = array('titre', 'descriptif','type','prefixe');

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

	spip_query("UPDATE spip_projets SET ".join(', ', $update)." WHERE id_projet=$id_projet");

}
?>
