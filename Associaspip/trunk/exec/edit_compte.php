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

function exec_edit_compte() {
	sinon_interdire_acces(autoriser('editer_compta', 'association'));
	include_spip ('association_modules');
/// INITIALISATIONS
	$id_compte = association_passeparam_id('compte');
/// AFFICHAGES_LATERAUX (connexes)
	echo association_navigation_onglets('titre_onglet_comptes', 'comptes');
/// AFFICHAGES_LATERAUX : INTRO : info compte
	echo association_tablinfos_intro('', 'compte', $id_compte);
/// AFFICHAGES_LATERAUX : RACCOURCIS
	echo association_navigation_raccourcis(array(
		array('informations_comptables', 'grille-24.png', array('comptes', "id=$id_compte"), array('gerer_compta', 'association') ),
	) );
/// AFFICHAGES_CENTRAUX (corps)
	debut_cadre_association('compts.gif', 'modification_des_comptes');
/// AFFICHAGES_CENTRAUX : FORMULAIRE
	echo recuperer_fond('prive/editer/editer_asso_compte', array (
		'id_compte' => $id_compte
	));
/// AFFICHAGES_CENTRAUX : FIN
	fin_page_association();
}

?>