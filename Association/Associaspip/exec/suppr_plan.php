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

function exec_suppr_plan() {
	if (!autoriser('configurer_compta', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip ('inc/navigation_modules');
		list($id_plan, $plan) = association_passeparam_id('plan', 'asso_plan');
		onglets_association('plan_comptable', 'association');
		// info
		$infos['entete_code'] = $plan['code'];
		$infos['solde_initial'] = association_formater_prix($plan['solde_anterieur']);
		$infos['entete_date'] = association_formater_date($plan['date_anterieure']);
		$infos['entete_utilise'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_comptes',"imputation='$plan[code]' OR journal='$plan[code]'")) );
		echo association_totauxinfos_intro($plan['intitule'], 'plan', $id_plan, $infos );
		// datation et raccourcis
		raccourcis_association('');
		debut_cadre_association('plan_compte.png', 'suppression_de_compte');
		echo association_bloc_suppression('plan', $id_plan,'plan_comptable');
		fin_page_association();
	}
}

?>