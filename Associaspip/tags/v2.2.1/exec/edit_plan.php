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

function exec_edit_plan() {
	sinon_interdire_acces(autoriser('gerer_compta', 'association'));
	include_spip ('association_modules');
/// INITIALISATIONS
	$id_plan = association_passeparam_id('plan');
/// AFFICHAGES_LATERAUX (connexes)
	echo association_navigation_onglets('plan_comptable', 'association');
/// AFFICHAGES_LATERAUX : INTRO : notice plan comptable
	echo propre(_T('asso:edit_plan'));
/// AFFICHAGES_LATERAUX : RACCOURCIS
	echo association_navigation_raccourcis(array(
		array('plan_comptable', 'grille-24.png', array('plan_comptable', "id=$id_plan"), array('gerer_compta', 'association') ),
	) );
/// AFFICHAGES_CENTRAUX (corps)
	debut_cadre_association('plan_compte.png', 'edition_plan_comptable');
/// AFFICHAGES_CENTRAUX : FORMULAIRE
	echo recuperer_fond('prive/editer/editer_asso_plan', array (
		'id_plan' => $id_plan,
	));
/// AFFICHAGES_CENTRAUX : FIN
	fin_page_association();
}

?>