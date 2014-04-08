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

function action_rechercher_livre_dist() {

	include_spip('inc/filtres');
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$titre = _request('titre');
	$auteur = _request('auteur');
	$illustrateur = _request('illustrateur');
	$edition = _request('edition');
	$prix_vente = _request('prix_vente');
	$isbn = _request('isbn');

	$par = '';
	if ($titre) $par .= '&titre='.$titre;
	if ($auteur) $par .= '&auteur='.$auteur;
	if ($illustrateur) $par .= '&illustrateur='.$illustrateur;
	if ($edition) $par .= '&edition='.$edition;
	if ($prix_vente) $par .= '&prix_vente='.$prix_vente;
	if ($isbn) $par .= '&isbn='.$isbn;

	$url = generer_url_ecrire("rechercher_livres");
	if ($par) {
		$url = $url.$par;
		$url = attribut_html($url);
		$url = str_replace('#38;','',$url); // bidouille
	}


	redirige_par_entete($url);

}

?>
