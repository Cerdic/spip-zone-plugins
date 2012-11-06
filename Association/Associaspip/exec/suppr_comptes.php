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

function exec_suppr_comptes() {
	$r = association_controle_id('compte', 'asso_comptes', 'editer_compta', 'association');
	if ($r) {
		include_spip('inc/navigation_modules');
		list($id_compte, $row) = $r;
		onglets_association('titre_onglet_comptes', 'comptes');
		// info
		echo _T('asso:confirmation');
		// datation et raccourcis
		raccourcis_association('');
		debut_cadre_association('finances-32.jpg', 'operations_comptables');
		echo '<p><strong>', _T('asso:vous_vous_appretez_a_effacer_la_ligne_de_compte'),  ' ', $id_compte, '</strong></p>';

		$corps = association_formater_date($row['date_operation'])
			. ' <strong>'
			. propre($row['justification'])
			. "</strong> "
			. '<p class="boutons"><input type="submit" value="'
			. _T('asso:bouton_confirmer')
			. '" /></p>';

		echo redirige_action_post('supprimer_comptes', $id_compte, 'comptes', '', $corps);
		fin_page_association();
	}
}

?>