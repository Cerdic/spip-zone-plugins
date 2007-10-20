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
	
	include_spip('inc/presentation');
	include_spip ('inc/navigation_modules');
	
	function exec_ressources(){
		global $connect_statut, $connect_toutes_rubriques;
		
		include_spip ('inc/acces_page');
		
		$url_ressources = generer_url_ecrire('ressources');
		$url_edit_ressource=generer_url_ecrire('edit_ressource');
		$url_action_ressources=generer_url_ecrire('action_ressources');
		$url_prets=generer_url_ecrire('prets');
		
		debut_page(_T('asso:ressources_titre_liste_ressources'), "", "");
		
		association_onglets();
		
		debut_gauche();
		
		debut_boite_info();
		echo '<p>'._T('asso:ressources_info').'</p>';
		fin_boite_info();
		
		debut_raccourcis();
		icone_horizontale(_T('asso:ressources_nav_ajouter'), generer_url_ecrire('edit_ressource','action=ajoute'),'../'._DIR_PLUGIN_ASSOCIATION.'/fiche-perso-24.gif','cree.gif');	
		fin_raccourcis();
		
		debut_droite();
		debut_cadre_relief(  "", false, "", $titre = _T('asso:ressources_titre_liste_ressources'));
		
		echo "<table border=0 cellpadding=2 cellspacing=0 width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo "<tr bgcolor='#DBE1C5'>";
		echo '<td>&nbsp;</td>';
		echo '<td><strong>'._T('asso:ressources_entete_intitule').'</strong></td>';
		echo '<td><strong>'._T('asso:ressources_entete_code').'</strong></td>';
		echo '<td><strong>'._T('asso:ressources_entete_montant').'</strong></td>';
		echo '<td colspan="3" style="text-align:center;"><strong>'._T('asso:entete_action').'</strong></td>';
		echo'  </tr>';
		$query = spip_query ( "SELECT * FROM spip_asso_ressources ORDER BY id_ressource" ) ;
		while ($data = spip_fetch_array($query)) {
			echo '<tr style="background-color: #EEEEEE;">';		
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;">';
			switch($data['statut']){
				case "ok": $puce= "verte"; break;
				case "reserve": $puce= "rouge"; break;
				case "suspendu": $puce="orange"; break;
				case "sorti": $puce="poubelle"; break;	   
			}
			echo '<img src="/dist/images/puce-'.$puce.'.gif"></td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;">'.$data['intitule'].'</td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;">'.$data['code'].'</td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;">'.number_format($data['pu'], 2, ',', ' ').'</td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:center;"><a href="'.$url_prets.'&id='.$data['id_ressource'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/voir-12.gif" title="'._T('asso:prets_nav_gerer').'"></a></td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:center;"><a href="'.$url_action_ressources.'&action=supprime&id='.$data['id_ressource'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/poubelle-12.gif" title="'._T('asso:ressources_nav_supprimer').'"></a></td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:center;"><a href="'.$url_edit_ressource.'&action=modifie&id='.$data['id_ressource'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/edit-12.gif" title="'._T('asso:ressources_nav_editer').'"></a></td>';
			echo'  </tr>';
		}     
		echo'</table>';
		
		fin_cadre_relief();  
		fin_page();
	}
?>
