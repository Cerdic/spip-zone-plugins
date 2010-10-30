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
		
	include_spip('inc/autoriser');
	if (!autoriser('configurer')) {
		include_spip('inc/minipres');
		echo minipres();
	} else exec_action_dons_args(intval(_request('id')));
}


function exec_action_dons_args($id_don) {
	// A ameliorer: redecrire le don
	$data = !$id_don ? '' : sql_fetsel('*', 'spip_asso_dons', "id_don=$id_don");
	if (!$data) {
		include_spip('inc/minipres');
		echo minipres(_T('zxml_inconnu_id') . $id_don);
	} else {

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
		association_onglets();
		echo debut_gauche("",true);
		echo debut_boite_info(true);
		echo association_date_du_jour();	
		echo fin_boite_info(true);
		echo association_retour();
		echo debut_droite("", true);
		echo debut_cadre_relief(  "", false, "", $titre = _T('asso:action_sur_les_dons'));
		$res = '<p><strong>' . _T('asso:vous_vous_appretez_a_effacer_le_don') . $id_don . '</strong></p>';

		$res .= '<p style="float:right;"><input type="submit" value="'._T('asso:bouton_confirmer').'" class="fondo" /></p>';

		echo redirige_action_post('supprimer_dons', $id_don, 'dons', '', "<div>$res</div>");
		fin_cadre_relief();  
		echo fin_page_association(); 
	}
} 
?>
