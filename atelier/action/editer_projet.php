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

	// Ajoute ou retire les auteurs
	$q = sql_select('id_auteur','spip_auteurs');
	while ($r = sql_fetch($q)) {
		$check = _request('auteur_'.$r['id_auteur']);
		if ($check == 'yes') sql_insertq('spip_auteurs_projets',array('id_auteur' => $r['id_auteur'],'id_projet' => $id_projet));
		else sql_delete('spip_auteurs_projets','id_projet='.$id_projet.' AND id_auteur='.$r['id_auteur']);
	}

	// création de l'arborescence
	if (_request('arbo')) {
		switch (_request('type')) {
			case 'plugin' : projets_creer_arbo_plug(&$rapport); break;
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

	$return = mkdir('./'.$repertoire_squelettes_alternatifs.'/'._request('prefixe'));
	if ($return) $rapport .= ' commande : mkdir ./'.$repertoire_squelettes_alternatifs.'/'._request('prefixe').' - r&eacute;ussite <br />';
	else $rapport .= ' commande : mkdir ./'.$repertoire_squelettes_alternatifs.'/'._request('prefixe').' - echec <br />';

	$return = mkdir('./'.$repertoire_squelettes_alternatifs.'/'._request('prefixe').'/css');
	if ($return) $rapport .= ' commande : mkdir ./'.$repertoire_squelettes_alternatifs.'/'._request('prefixe').'/css'.' - r&eacute;ussite <br />';
	else $rapport .= ' commande : mkdir ./'.$repertoire_squelettes_alternatifs.'/'._request('prefixe').'/css'.' - echec <br />';

	$return = mkdir('./'.$repertoire_squelettes_alternatifs.'/'._request('prefixe').'/lang');
	if ($return) $rapport .= ' commande : mkdir ./'.$repertoire_squelettes_alternatifs.'/'._request('prefixe').'/lang'.' - r&eacute;ussite <br />';
	else $rapport .= ' commande : mkdir ./'.$repertoire_squelettes_alternatifs.'/'._request('prefixe').'/lang'.' - echec <br />';

	$return = mkdir('./'.$repertoire_squelettes_alternatifs.'/'._request('prefixe').'/images');
	if ($return) $rapport .= ' commande : mkdir ./'.$repertoire_squelettes_alternatifs.'/'._request('prefixe').'/images'.' - r&eacute;ussite <br />';
	else $rapport .= ' commande : mkdir ./'.$repertoire_squelettes_alternatifs.'/'._request('prefixe').'/images'.' - echec <br />';

	$return = mkdir('./'.$repertoire_squelettes_alternatifs.'/'._request('prefixe').'/javascript');
	if ($return) $rapport .= ' commande : mkdir ./'.$repertoire_squelettes_alternatifs.'/'._request('prefixe').'/javascript'.' - r&eacute;ussite <br />';
	else $rapport .= ' commande : mkdir ./'.$repertoire_squelettes_alternatifs.'/'._request('prefixe').'/javascript'.' - echec <br />';

	$return = mkdir('./'.$repertoire_squelettes_alternatifs.'/'._request('prefixe').'/formulaires');
	if ($return) $rapport .= ' commande : mkdir ./'.$repertoire_squelettes_alternatifs.'/'._request('prefixe').'/formulaires'.' - r&eacute;ussite <br />';
	else $rapport .= ' commande : mkdir ./'.$repertoire_squelettes_alternatifs.'/'._request('prefixe').'/formulaires'.' - echec <br />';

	$return = mkdir('./'.$repertoire_squelettes_alternatifs.'/'._request('prefixe').'/modeles');
	if ($return) $rapport .= ' commande : mkdir ./'.$repertoire_squelettes_alternatifs.'/'._request('prefixe').'/modeles'.' - r&eacute;ussite <br />';
	else $rapport .= ' commande : mkdir ./'.$repertoire_squelettes_alternatifs.'/'._request('prefixe').'/modeles'.' - echec <br />';
}

function projets_creer_arbo_plug(&$rapport) {
	$return = mkdir(_DIR_PLUGINS._request('prefixe'));
	if ($return) $rapport .= ' commande : mkdir '. _DIR_PLUGINS._request('prefixe').' - r&eacute;ussite <br />';
	else $rapport .= ' commande : mkdir '. _DIR_PLUGINS._request('prefixe').' - echec <br />';

	$return = mkdir(_DIR_PLUGINS._request('prefixe').'/exec');
	if ($return) $rapport .= ' commande : mkdir '. _DIR_PLUGINS._request('prefixe').'/exec'.' - r&eacute;ussite <br />';
	else $rapport .= ' commande : mkdir '. _DIR_PLUGINS._request('prefixe').'/exec'.' - echec <br />';

	$return = mkdir(_DIR_PLUGINS._request('prefixe').'/action');
	if ($return) $rapport .= ' commande : mkdir '. _DIR_PLUGINS._request('prefixe').'/action'.' - r&eacute;ussite <br />';
	else $rapport .= ' commande : mkdir '. _DIR_PLUGINS._request('prefixe').'/action'.' - echec <br />';

	$return = mkdir(_DIR_PLUGINS._request('prefixe').'/inc');
	if ($return) $rapport .= ' commande : mkdir '. _DIR_PLUGINS._request('prefixe').'/inc'.' - r&eacute;ussite <br />';
	else $rapport .= ' commande : mkdir '. _DIR_PLUGINS._request('prefixe').'/inc'.' - echec <br />';

	$return = mkdir(_DIR_PLUGINS._request('prefixe').'/lang');
	if ($return) $rapport .= ' commande : mkdir '. _DIR_PLUGINS._request('prefixe').'/lang'.' - r&eacute;ussite <br />';
	else $rapport .= ' commande : mkdir '. _DIR_PLUGINS._request('prefixe').'/lang'.' - echec <br />';
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
