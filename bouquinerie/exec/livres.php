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

function exec_livres_dist() {
	// récupere les paramètres et lance la fonction de traitement des paramètres
	exec_livres_args(intval(_request('id_livre')) // id du livre a visualiser
	);
}

function exec_livres_args($id_livre) {

	$livre_select = charger_fonction('livre_select','inc');
	$row = $livre_select($id_livre);

	livres($id_livre,$row);
}

function livres($id_livre,$row) {

	include_spip('inc/bouq');
	include_spip('inc/bouq_presentation');

	$nom_page = debut_page_bouq(_T('bouq:livres'),"livres");
	if (!bouq_autoriser()) exit;

	echo debut_gauche($nom_page,true);

	bouq_afficher_raccourcis();

	echo '<br />';

	cadre_gauche_bouq('Actions',array(
		'<a href="'.generer_url_ecrire("livres_edit",'id_livre='.$id_livre).'">'._T('bouq:editer_livre').'</a>',
		'<a href="'.generer_url_action("supprimer_livre",'id_livre='.$id_livre).'">'._T('bouq:supprimer_livre').'</a>'
		));

	echo '<br />';
	bouq_afficher_infos();
  
	echo debut_droite($nom_page,true);

	echo debut_cadre_trait_couleur('',true);
	echo gros_titre($row['titre'],'',false);

	echo '<br /';
	echo debut_cadre_couleur();
	echo '<b>'._T('bouq:date_creation').'</b>'. '&nbsp;&nbsp;' . $row['date_ajout'] . '<br />';
	echo '<b>'._T('bouq:id_livre'). '&nbsp;&nbsp;' . $row['id_livre'].'</b><br />';
	echo '<b>'._T('bouq:catalogue').'</b>&nbsp;&nbsp;&nbsp;'.bouq_afficher_catalogue($row['id_catalogue']);
	echo fin_cadre_couleur(true);
	echo '<br />';

	if ($row['url_image']) {
		echo '<table><tr><td>';
		 echo '<img src="'.$row['url_image'].'" /></td><td>';
	}

	
	if ($row['auteur']) echo _T('bouq:texte_auteur'). '&nbsp;:&nbsp;' . $row['auteur'] . '<br />';
	if ($row['id_reference']) {
		echo _T('bouq:texte_reference') . '&nbsp;:&nbsp;' . $row['id_reference'];
		if ($row['type_import'] == 'priceminister')
			echo ' / <a href="http://www.priceminister.com/offer/buy/'.$row['id_reference'].'/">lien priceminister</a>';
			echo ' / <a href="'.generer_action_auteur('rechercher_image',$row['id_livre']).'">Rechercher une image</a>';
		echo '<br />';
	}
	if ($row['illustrateur']) echo _T('bouq:texte_illustrateur') . '&nbsp;:&nbsp;' . $row['illustrateur'] . '<br />';
	if ($row['edition']) echo _T('bouq:texte_edition') . '&nbsp;:&nbsp;' . $row['edition'] . '<br />';
	echo '<br />';

	if ($row['prix_vente']) echo _T('bouq:texte_prix_vente') . '&nbsp;:&nbsp;' . $row['prix_vente'] . '<br />';
	if ($row['isbn']) echo _T('bouq:texte_isbn'). '&nbsp;:&nbsp;' . $row['isbn'] . '<br />';
	echo '<br />';

	if ($row['statut']) {
		 echo _T('bouq:texte_statut') . '&nbsp;:&nbsp;' . $row['statut'] . '<br />';
		echo '<br />';
	}

	if ($row['etat_livre']) echo _T('bouq:texte_etat_livre'). '&nbsp;:&nbsp;' . $row['etat_livre'] . '<br />';
	if ($row['format']) echo _T('bouq:texte_format') . '&nbsp;:&nbsp;' . $row['format'] . '<br />';
	if ($row['etat_jaquette']) echo _T('bouq:texte_etat_jaquette') . '&nbsp;:&nbsp;' . $row['etat_jaquette'] . '<br />';
	if ($row['reliure']) echo _T('bouq:texte_reliure'). '&nbsp;:&nbsp;' . $row['reliure'] . '<br />';
	if ($row['type_livre']) echo _T('bouq:texte_type_livre'). '&nbsp;:&nbsp;' . $row['type_livre'] . '<br />';
	echo '<br />';

	if ($row['lieu_edition']) echo _T('bouq:etat_jaquette') . '&nbsp;;&nbsp;' . $row['etat_jaquette'] . '<br />';
	if ($row['annee_edition']) echo _T('bouq:annee_edition') . '&nbsp;;&nbsp;' . $row['annee_edition'] . '<br />';
	if ($row['num_edition']) echo _T('bouq:num_edition') . '&nbsp;;&nbsp;' . $row['num_edition'] . '<br />';
	echo '<br />';

	if ($row['inscription']) echo _T('bouq:texte_inscription').' : <p>' . $row['inscription'] . '</p>';
	if ($row['remarque']) echo _T('bouq:texte_remarque').' : <p>' . $row['remarque'] . '</p>';
	if ($row['commentaire']) echo _T('bouq:texte_commentaire').' : <p>' . $row['commentaire'] . '</p>';
	echo '<br />';


	if ($row['prix_achat']) echo _T('bouq:texte_prix_achat').'</b>' . '&nbsp;&nbsp;' . $row['prix_achat'] . '<br />';
	if ($row['lieu']) echo _T('bouq:texte_lieu').'</b>' . '&nbsp;&nbsp;' . $row['lieu'] . '<br />';
	if ($row['num_facture']) echo _T('bouq:texte_num_facture').'</b>' . '&nbsp;&nbsp;' . $row['num_facture'] . '<br />';

	if ($row['url_image']) {
	echo '</td></tr></table>';
	}
	echo '<br />';


	echo fin_cadre_trait_couleur(true);

	echo fin_gauche(); 
	fin_page_bouq();
}

?>
