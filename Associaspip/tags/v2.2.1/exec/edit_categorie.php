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

function exec_edit_categorie() {
	sinon_interdire_acces(autoriser('editer_profil', 'association'));
	include_spip ('association_modules');
/// INITIALISATIONS
	$id_categorie = association_passeparam_id('categorie');
/// AFFICHAGES_LATERAUX (connexes)
	echo association_navigation_onglets('categories_de_cotisations', 'association');
/// AFFICHAGES_LATERAUX : INTRO : info categorie
	$infos['entete_utilisee'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_membres', "id_categorie=$id_categorie"), ));
	echo association_tablinfos_intro('', 'categorie', $id_categorie, $infos );
/// AFFICHAGES_LATERAUX : RACCOURCIS
	echo association_navigation_raccourcis(array(
		array('categories_de_cotisations', 'grille-24.png', array('categories', "id=$id_categorie"), array('editer_profil', 'association') ),
	) );
/// AFFICHAGES_CENTRAUX (corps)
	debut_cadre_association('cotisation.png', 'categories_de_cotisations');
/// AFFICHAGES_CENTRAUX : FORMULAIRE
	echo recuperer_fond('prive/editer/editer_asso_categorie', array (
		'id_categorie' => $id_categorie
	));
/// AFFICHAGES_CENTRAUX : FIN
	fin_page_association();
}

?>