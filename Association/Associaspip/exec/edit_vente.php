<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010 Emmanuel Saint-James & Jeannot Lapin     (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip ('inc/navigation_modules');
include_spip ('inc/association_comptabilite');

function exec_edit_vente() {
		
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'ventes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {		
		$id_vente= intval(_request('id'));		

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page() ;
		
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		if ($id_vente) {
			echo '<div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">'._T('asso:vente_libelle_numero').'<br />';
			echo '<span class="spip_xx-large">';
			echo $id_vente;
			echo '</span></div><br />';
		}
		echo '<div>'.association_date_du_jour().'</div>';	
		
		echo fin_boite_info(true);	

		echo association_retour();
		
		echo debut_droite("",true);

		echo recuperer_fond("prive/editer/editer_asso_ventes", array (
			'id_vente' => $id_vente
		));

		echo fin_page_association(); 


		
	}  
}
?>
