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

function exec_catalogues_dist() {
	// récupere les paramètres et lance la fonction de traitement des paramètres
	exec_catalogues_args(intval(_request('id_catalogue')) // id du catalogue a visualiser
	);
}

function exec_catalogues_args($id_catalogue) {

	$catalogue_select = charger_fonction('catalogue_select','inc');
	$row = $catalogue_select($id_catalogue);

	catalogues($id_catalogue,$row);
}

function catalogues($id_catalogue,$row) {

	include_spip('inc/bouq');
	include_spip('inc/bouq_presentation');

	$nom_page = debut_page_bouq(_T('bouq:titre_catalogues'),"catalogues");
                     
	if (!bouq_autoriser()) exit;

	echo debut_gauche($nom_page,true);

	bouq_afficher_raccourcis();

	echo '<br />';

	cadre_gauche_bouq(_T('bouq:actions'),array(
		'<a href="'.generer_url_ecrire("catalogues_edit",'id_catalogue='.$id_catalogue.'&url_retour='.generer_url_ecrire("catalogues","id_catalogue=$id_catalogue")).'">'._T('bouq:editer_catalogue').'</a>',
		'<a href="'.generer_url_ecrire("supprimer_catalogue",'id_catalogue='.$id_catalogue).'">'._T('bouq:supprimer_catalogue').'</a>'
		));

	echo '<br />';
	bouq_afficher_infos();

	echo debut_droite($nom_page,true);

	echo debut_cadre_trait_couleur('',true);
	echo gros_titre($row['titre'],'',false);

	echo '<br /';
	echo debut_cadre_couleur();
	echo '<b>'._T('bouq:id_catalogue'). '&nbsp;&nbsp;' . $row['id_catalogue'].'</b><br />';
	echo '<b>'._T('bouq:descriptif').'</b>&nbsp;&nbsp;'.$row['descriptif'];
	echo fin_cadre_couleur(true);
	echo '<br />';

	echo 'contient '.get_nombre_livres($row['id_catalogue']).' livres.<br />';

	include_spip('inc/afficher_livres');

	$afficher_livres = charger_fonction('afficher_livres','inc');
	$titre = _T('bouq:liste_livre');
	$requete = array('SELECT' => 'livres.id_livre, livres.titre ',
			 'FROM' => "spip_livres as livres",
			 'WHERE' => "livres.id_catalogue=$id_catalogue");

	echo $afficher_livres($titre,$requete);


	echo fin_cadre_trait_couleur(true);

	echo fin_gauche();
	fin_page_bouq();
}
?>
