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
		include_spip ('inc/navigation_modules');
		list($id_categorie, $categorie) = $r;
		onglets_association('categories_de_cotisations', 'association');
		// INTRO : resume ressource
		$infos['entete_code'] = association_formater_code($categorie['valeur'], 'x-spip_asso_categories');
		$infos['entete_duree'] = association_formater_duree($categorie['duree'], 'M');
		$infos['entete_montant'] = association_formater_prix($categorie['prix_cotisation'], 'subscription');
		$infos['entete_utilise'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_membres', "categorie=$id_categorie"), ));
		echo '<div class="hproduct">'. association_totauxinfos_intro('<span class="n">'.$categorie['libelle'].'</span>', 'categorie', $id_categorie, $infos ) .'</div>';
		// datation et raccourcis
		echo association_navigation_raccourcis('categories');
		debut_cadre_association('cotisation.png', 'categories_de_cotisations');
		echo association_bloc_suppression('categorie', $id_categorie);
		fin_page_association();
	}
}

?>
