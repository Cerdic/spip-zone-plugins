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
	if (!autoriser('gerer_compta', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip ('association_modules');
		echo association_navigation_onglets('plan_comptable', 'association');
		$id_plan = association_passeparam_id('plan');
		// Notice
		echo propre(_T('asso:edit_plan'));
		// datation et raccourcis
		echo association_navigation_raccourcis(array(
			'plan_comptable' => array('grille-24.png', array('plan_comptable', "id=$id_plan"), array('gerer_compta', 'association') ),
		) );
		debut_cadre_association('plan_compte.png', 'edition_plan_comptable');
		echo recuperer_fond('prive/editer/editer_asso_plan', array (
			'id_plan' => $id_plan
		));
		fin_page_association();
	}
}

?>
