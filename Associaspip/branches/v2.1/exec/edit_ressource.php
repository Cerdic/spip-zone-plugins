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
	
function exec_edit_ressource(){
		
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'ressources')) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		$id_ressource = intval(_request('id'));

		$url_action_ressources=generer_url_ecrire('action_ressources');
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:ressources_titre_edition_ressources')) ;
		association_onglets(_T('asso:titre_onglet_prets'));
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo '<p>', _T('asso:gestion_des_emprunts_et_des_prets') . '</p>';
		echo fin_boite_info(true);
	
		echo association_retour();
		
		echo debut_droite("",true);
	
		echo recuperer_fond("prive/editer/editer_asso_ressources", array (
			'id_ressource' => $id_ressource
		));

		echo fin_page_association();
	}
}
?>
