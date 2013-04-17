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

function exec_edit_don() {
	sinon_interdire_acces(autoriser('editer_dons', 'association'));
	include_spip('association_modules');
/// INITIALISATIONS
	$id_don = association_passeparam_id('don');
	$id_auteur = association_recuperer_entier('id_auteur'); // a verifier si vraiment utilise
/// AFFICHAGES_LATERAUX (connexes)
	echo association_navigation_onglets('titre_onglet_dons', 'dons');
/// AFFICHAGES_LATERAUX : INTRO : resume don
	echo association_tablinfos_intro('', 'don', $id_don);
/// AFFICHAGES_LATERAUX : RACCOURCIS
	echo association_navigation_raccourcis(array(
		array('tous_les_dons', 'grille-24.png', array('dons', "id=$id_don"), array('voir_dons', 'association') ),
	) );
/// AFFICHAGES_CENTRAUX (corps)
	debut_cadre_association('dons-24.gif', 'ajouter_un_don');
/// AFFICHAGES_CENTRAUX : FORMULAIRE
	echo recuperer_fond('prive/editer/editer_asso_dons', array (
		'id_don' => $id_don,
		'id_auteur' => $id_auteur,
		'editable' => autoriser('editer_compta', 'association')
	));
/// AFFICHAGES_CENTRAUX : FIN
	fin_page_association();
}

?>