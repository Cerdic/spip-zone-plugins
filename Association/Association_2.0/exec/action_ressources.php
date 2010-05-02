<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007
	* Bernard Blazin & Fran�ois de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
if (!defined("_ECRIRE_INC_VERSION")) return;

	include_spip('inc/presentation');
	include_spip ('inc/navigation_modules');
	
function exec_action_ressources(){
		
	$id_ressource=intval(_request('id'));
	include_spip('inc/autoriser');
	if (!autoriser('configurer') OR !$id_ressource) {
			include_spip('inc/minipres');
			echo minipres();

	} else {
		$url_retour=$_SERVER['HTTP_REFERER'];
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:ressources_titre_suppression_ressources')) ;
			
			association_onglets();
			
			echo debut_gauche("",true);
			
			echo debut_boite_info(true);
			$query = sql_select("*", "spip_asso_ressources", "id_ressource=$id_ressource" ) ;
			while ($data = sql_fetch($query)) {
				$statut=$data['statut'];
				echo '<div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">'._T('asso:ressources_num').'<br />';
				echo '<span class="spip_xx-large">'.$data['id_ressource'].'</span></div>';
				echo '<p>'._T('asso:ressources_libelle_code').': '.$data['code'].'<br />';
				echo $data['intitule'];
				echo '</p>';
			}
			echo fin_boite_info(true);
			
			
			$res=association_icone(_T('asso:bouton_retour'),  $url_retour, "retour-24.png");	
			echo bloc_des_raccourcis($res);
			
			echo debut_droite("",true);
			
			echo debut_cadre_relief(  "", false, "", $titre = _T('asso:ressources_titre_suppression_ressources'));
			echo '<p><strong>'._T('asso:ressources_danger_suppression',array('id_ressource' => $id_ressource)).'</strong></p>';

			$res = '<p style="float:right;"><input type="submit" value="'._T('asso:bouton_confirmer').'" class="fondo"></p>';
			echo redirige_action_post('supprimer_ressources', $id_ressource, 'ressources', '', $res);
			fin_cadre_relief();  
			
			echo fin_gauche(), fin_page();
		}
}
?>
