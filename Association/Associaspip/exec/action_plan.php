<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip ('inc/navigation_modules');

function exec_action_plan()
{
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$id_plan = intval(_request('id'));
		onglets_association('plan_comptable');
		// info
		$plan = sql_fetsel('*', 'spip_asso_plan', "id_plan=$id_plan");
		$infos['entete_code'] = $plan['code'];
		$infos['solde_initial'] = association_prixfr($plan['solde_anterieur']);
		$infos['entete_date'] = association_datefr($plan['date_anterieure']);
		$infos['entete_utilise'] = _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_comptes',"imputation='$plan[code]' OR journal='$plan[code]'")) );
		echo totauxinfos_intro($plan['intitule'], 'plan', $id_plan, $infos );
		// datation et raccourcis
		icones_association('');
		debut_cadre_association('euro-39.gif', 'suppression_de_compte');
		echo bloc_confirmer_suppression('plan', $id_plan,'plan');
		fin_page_association();
	}
}

?>