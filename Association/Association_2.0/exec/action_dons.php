<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007
	* Bernard Blazin & François de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip ('inc/navigation_modules');

function exec_action_dons() {
		
	$id_don = intval(_request('id'));
	// A ameliorer: redecrire le don
	$data = !$id_don ? '' : sql_fetsel('*', 'spip_asso_dons', "id_don=$id_don");
	$url_retour = $_SERVER['HTTP_REFERER'];
				
	include_spip('inc/autoriser');
	if (!autoriser('configurer') OR !$data) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('Gestion pour Association')) ;
			association_onglets();
			
			echo debut_gauche("",true);
			
			echo debut_boite_info(true);
			echo association_date_du_jour();	
			echo fin_boite_info(true);
		
			$res=association_icone(_T('asso:bouton_retour'),  $url_retour, "retour-24.png");	
			echo bloc_des_raccourcis($res);
			
			echo debut_droite("", true);
			
			echo debut_cadre_relief(  "", false, "", $titre = _L('Action sur les dons'));
			$res = '<div align="center">';
			$res .= '<p><strong>' . _L('Vous vous appr&ecirc;tez &agrave; effacer le don ') . $id_don . '.</strong></p>';

			$res .= '<p style="float:right;"><input type="submit" value="'._T('asso:bouton_confirmer').'" class="fondo"></p>';
			$res .= '</div>';		

			echo redirige_action_post('supprimer_dons', $id_don, 'dons', '', $res);
			fin_cadre_relief();  
			echo fin_gauche(),fin_page(); 
	}
} 
?>
