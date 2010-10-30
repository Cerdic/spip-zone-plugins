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
	
function exec_action_ressources(){
		
	$id_ressource=intval(_request('id'));
	include_spip('inc/autoriser');
	if (!autoriser('configurer') OR !$id_ressource) {
			include_spip('inc/minipres');
			echo minipres();

	} else exec_action_ressources_args($id_ressource);
}

function exec_action_ressources_args($id_ressource)
{
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
	echo association_retour();
	echo debut_droite("",true);
	echo debut_cadre_relief(  "", false, "", $titre = _T('asso:ressources_titre_suppression_ressources'));
	echo '<p><strong>'._T('asso:ressources_danger_suppression',array('id_ressource' => $id_ressource)).'</strong></p>';

	$res = '<div style="float:right;"><input type="submit" value="'._T('asso:bouton_confirmer').'" class="fondo" /></div>';

	echo redirige_action_post('supprimer_ressources', $id_ressource, 'ressources', '', $res);
	
	echo fin_cadre_relief(true);
	echo fin_page_association();
}
?>
