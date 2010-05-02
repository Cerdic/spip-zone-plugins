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

function exec_action_comptes(){
		
	include_spip('inc/autoriser');
	if (!autoriser('configurer')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		
		$id_compte= intval(_request('id'));
		
		//SUPPRESSION PROVISOIRE OPERATION
		if (_request('agir') == "supprime") {
			
			$url_retour = $_SERVER['HTTP_REFERER'];
			
			$commencer_page = charger_fonction('commencer_page', 'inc');
			echo $commencer_page(_T('Gestion pour Association')) ;

			association_onglets();
			echo debut_gauche('', true);
			
			echo debut_boite_info(true);
			echo association_date_du_jour();	
			echo fin_boite_info(true);
			
			echo bloc_des_raccourcis(association_icone(_T('asso:bouton_retour'),  $url_retour, "retour-24.png"));
			echo debut_droite('', true);
			
			debut_cadre_relief(  "", false, "", $titre = _T('Op&eacute;rations comptables'));
			echo '<p><strong>' . _L('Vous vous appr&ecirc;tez &agrave; effacer la ligne de compte n&deg; '. $id_compte . '&nbsp;:') . '</strong></p>';

			$res = action_comptes_ligne("id_compte=$id_compte");
			$res .= '<p style="float:right;"><input type="submit" value="'._T('asso:bouton_confirmer').'" class="fondo"></p>';

			echo redirige_action_post('supprimer_comptes', $id_compte, 'comptes', '', $res);
			fin_cadre_relief();  
			
			fin_page();
			exit;
		}
		
		//---------------------------- 
		//VALIDATION PROVISOIRE COMPTE		
		if (isset($_POST['valide'])) {
			
			$url_retour = $_SERVER['HTTP_REFERER'];
			
			$commencer_page = charger_fonction('commencer_page', 'inc');
			echo $commencer_page(_T('Gestion pour Association')) ;
			
			association_onglets();
			
			echo debut_gauche('', true);
			
			echo debut_boite_info(true);
			echo association_date_du_jour();	
			echo fin_boite_info(true);
			
			echo bloc_des_raccourcis(association_icone(_T('asso:bouton_retour'),  $url_retour, "retour-24.png"));	
			
			echo debut_droite('', true);
			
			debut_cadre_relief("", false, "", $titre = _L('Op&eacute;rations comptables'));
			echo '<p>' . _L('Vous vous appr&ecirc;tez &agrave; valider les op&eacute;rations&nbsp;:') .  '</p>';

			$res = action_comptes_ligne(sql_in("id_compte", $_POST['valide']));
			$res .= '<p>' . _L('Apr&egrave;s confirmation vous ne pourrez plus modifier ces op&eacute;rations !') . '</p>';
			
			$res .= '<p style="float:right;"><input name="submit" type="submit" value="'._T('asso:bouton_confirmer').'" class="fondo" /></p>';

			// count est du bruit de fond de secu
			echo redirige_action_post('valider_comptes', count($_POST['valide']), 'comptes', "", $res);

			fin_cadre_relief();  
			
			fin_page();
		}
	} 
}

function action_comptes_ligne($where)
{
	$res = '';
	$query = sql_select('*', 'spip_asso_comptes', $where);
	while($data = sql_fetch($query)) {
		$res .= '<tr>';
		$res .= '<td><strong>'.association_datefr($data['date']).'</strong>';
		$res .= '<td><strong>'.propre($data['justification']).'</strong>';
		$res .= '<td>';
		$res .= '<input type=checkbox name="definitif[]" value="'.$data['id_compte'].'" checked="checked" />';
	}
	return "<table>$res</table>";
}
?>
