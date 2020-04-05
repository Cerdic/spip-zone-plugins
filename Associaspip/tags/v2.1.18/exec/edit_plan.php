<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');
include_spip ('inc/navigation_modules');

function exec_edit_plan(){

	include_spip('inc/autoriser');
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {

		$url_asso = generer_url_ecrire('association');
		$url_plan = generer_url_ecrire('plan_comptable');
		$url_action_plan=generer_url_ecrire('action_plan');

		$id_plan= intval(_request('id_plan'));

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:edition_plan_comptable')) ;
		association_onglets();
		echo debut_gauche("",true);
		echo debut_boite_info(true);
		echo association_date_du_jour();
		echo fin_boite_info(true);
		echo association_retour();
		echo debut_droite("",true);

		echo recuperer_fond("prive/editer/editer_asso_plan", array (
			'id_plan' => $id_plan
		));
		echo fin_page_association();
	}
}
?>
