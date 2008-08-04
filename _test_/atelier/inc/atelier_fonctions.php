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

function atelier_recuperer_nom_auteur($id_auteur) {
	$r = sql_fetsel('nom','spip_auteurs',"id_auteur=$id_auteur");
	return $r['nom'];
}

function atelier_recuperer_versions($id_projet) {
	$r = sql_fetsel('versions','spip_projets',"id_projet=$id_projet");
	if ($r['versions'] != '') $versions = explode ('/',$r['versions']);
	return $versions;
}

function atelier_recuperer_taches($id_projet, $version) {
	$taches = array();
	$q = sql_select('id_tache, titre, etat',
			'spip_taches',
			'id_projet='.$id_projet.' AND version="'.$version.'"',
			'',
			'etat');

	while ($r = sql_fetch($q))
		$taches[] = '<a href="'.generer_url_ecrire('taches','id_tache='.$r['id_tache']).'">'.$r['titre'].'</a></td><td style="text-align:right;">'.$r['etat'];
	return $taches;
}

function atelier_recuperer_taches_ouvertes($id_projet, $version) {
	$taches = array();
	$q = sql_select('id_tache, titre, etat',
			'spip_taches',
			'id_projet='.$id_projet.' AND version="'.$version.'" AND etat="ouverte"',
			'',
			'etat');

	while ($r = sql_fetch($q))
		$taches[] = '<a href="'.generer_url_ecrire('taches','id_tache='.$r['id_tache']).'">'.$r['titre'].'</a>';
	return $taches;
}

function atelier_recuperer_taches_fermees($id_projet, $version) {
	$taches = array();
	$q = sql_select('id_tache, titre, etat',
			'spip_taches',
			'id_projet='.$id_projet.' AND version="'.$version.'" AND etat="fermee"',
			'',
			'etat');

	while ($r = sql_fetch($q))
		$taches[] = '<a href="'.generer_url_ecrire('taches','id_tache='.$r['id_tache']).'">'.$r['titre'].'</a>';
	return $taches;
}
?>
