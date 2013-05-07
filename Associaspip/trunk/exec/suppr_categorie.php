<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function exec_suppr_categorie() {
	$r = association_controle_id('categorie', 'asso_categories', 'editer_profil');
	if ($r) {
		list($id_categorie, $categorie) = $r;
		exec_suppr_categorie_args($id_categorie, $categorie);
	}
}

function exec_suppr_categorie_args($id_categorie, $categorie) {
	include_spip ('association_modules');
	echo association_navigation_onglets('categories_de_cotisations', 'association');
	// INTRO : resume ressource
	$infos['entete_code'] = association_formater_code($categorie['valeur'], 'x-spip_asso_categories');
	$infos['entete_duree'] = association_formater_duree($categorie['duree'], 'M');
	$infos['entete_montant'] = association_formater_prix($categorie['prix_cotisation'], 'subscription');
	$infos['entete_utilise'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_membres', "id_categorie=$id_categorie"), ));
	echo '<div class="hproduct">'. association_tablinfos_intro('<span class="n">'.$categorie['libelle'].'</span>', 'categorie', $id_categorie, $infos ) .'</div>';
	// datation et raccourcis
	echo association_navigation_raccourcis(array(
		array('categories_de_cotisations', 'grille-24.png', array('categories', "id=$id_categorie"), array('editer_profil', 'association')),
	) );
	debut_cadre_association('cotisation.png', 'categories_de_cotisations');
	echo association_form_suppression('categorie', $id_categorie);
	fin_page_association();
}

?>