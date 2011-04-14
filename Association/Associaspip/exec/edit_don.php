<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010 Emmanuel Saint-James                     (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;
	
include_spip('inc/presentation');
include_spip ('inc/navigation_modules');
include_spip ('inc/association_comptabilite');

function exec_edit_don(){
		
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'dons')) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		$id_don= intval(_request('id'));


		$commencer_page = charger_fonction('commencer_page', 'inc');

		echo $commencer_page(_T('asso:dons_titre_mise_a_jour'));
		
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		if ($id_don) {
		  echo '<div style="font-weight: bold; text-align: center;" class="verdana1 spip_xx-small">DON<br><span class="spip_xx-large">'.$id_don.'</span></div>';
		}
		echo association_date_du_jour();
		echo fin_boite_info(true);
		
		echo association_retour();
		
		echo debut_droite("", true);
		

		echo recuperer_fond("prive/editer/editer_asso_dons", array (
			'id_don' => $id_don
		));

		echo fin_page_association();
	}
}

?>
