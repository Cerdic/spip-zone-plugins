<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function exec_edit_don() {
	if (!autoriser('gerer_dons', 'association')) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		include_spip ('inc/navigation_modules');
		$id_don = association_passeparam_id('don');
		onglets_association('titre_onglet_dons', 'dons');
		// INTRO : resume don
		echo association_totauxinfos_intro('', 'don', $id_don);
		// datation et raccourcis
		raccourcis_association('');
		debut_cadre_association('dons-24.gif', 'dons_titre_mise_a_jour');
		echo recuperer_fond('prive/editer/editer_asso_dons', array (
			'id_don' => $id_don
		));
		fin_page_association();
	}
}

?>