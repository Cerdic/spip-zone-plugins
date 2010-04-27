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
	
	function exec_ressources(){
		
		include_spip('inc/autoriser');
		if (!autoriser('configurer')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}
		
		$url_ressources = generer_url_ecrire('ressources');
		$url_ajout_ressource=generer_url_ecrire('edit_ressource','agir=ajoute');
		$url_edit_ressource=generer_url_ecrire('edit_ressource','agir=modifie');
		$url_action_ressources=generer_url_ecrire('action_ressources');
		$url_prets=generer_url_ecrire('prets');
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:ressources_titre_liste_ressources')) ;
		
		
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo '<p>'._T('asso:ressources_info').'</p>';
		echo '<img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'puce-verte.gif"> Libre<br />';
		echo '<img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'puce-orange.gif"> En suspend<br />';
		echo '<img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'puce-rouge.gif"> R&eacute;s&eacute;rv&eacute;<br />';
		echo '<img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'puce-poubelle.gif"> Supprim&eacute;';
		echo fin_boite_info(true);
		
		
		$res=icone_horizontale(_T('asso:ressources_nav_ajouter'), $url_ajout_ressource,_DIR_PLUGIN_ASSOCIATION_ICONES.'ajout_don.png','rien.gif',false );
			
		echo bloc_des_raccourcis($res);
		echo debut_droite("",true);
		echo debut_cadre_relief(  "", false, "", $titre = _T('asso:ressources_titre_liste_ressources'));
		
		echo "<table border=0 cellpadding=2 cellspacing=0 width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo "<tr bgcolor='#DBE1C5'>";
		echo '<td>&nbsp;</td>';
		echo '<td><strong>'._T('asso:ressources_entete_intitule').'</strong></td>';
		echo '<td><strong>'._T('asso:ressources_entete_code').'</strong></td>';
		echo '<td><strong>'._T('asso:ressources_entete_montant').'</strong></td>';
		echo '<td colspan="4" style="text-align:center;"><strong>'._T('asso:entete_action').'</strong></td>';
		echo'  </tr>';
		$query = spip_query ( "SELECT * FROM spip_asso_ressources ORDER BY id_ressource" ) ;
		while ($data = spip_fetch_array($query)) {
			echo '<tr style="background-color: #EEEEEE;">';		
			echo '<td class="arial11 border1">';
			switch($data['statut']){
				case "ok": $puce= "verte"; break;
				case "reserve": $puce= "rouge"; break;
				case "suspendu": $puce="orange"; break;
				case "sorti": $puce="poubelle"; break;	   
			}
			echo '<img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'puce-'.$puce.'.gif"></td>';
			echo '<td class="arial11 border1">'.$data['intitule'].'</td>';
			echo '<td class="arial11 border1">'.$data['code'].'</td>';
			echo '<td class="arial11 border1" style="text-align:center;">'.number_format($data['pu'], 2, ',', ' ').'</td>';
			
			
			echo '<td class="'.$class. ' border1"></td>';
			echo '<td class="'.$class. ' border1"><a href="'.$url_prets.'&id='.$data['id_ressource'].'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'voir-12.png" title="'._T('asso:prets_nav_gerer').'"></a></td>';
			//echo '<td class="arial11 border1" style="text-align:center;"><a href="'.$url_prets.'&id='.$data['id_ressource'].'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'voir-12.gif" title="'._T('asso:prets_nav_gerer').'"></a></td>';
			echo '<td class="arial11 border1" style="text-align:center;"><a href="'.$url_action_ressources.'&agir=supprime&id='.$data['id_ressource'].'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'poubelle-12.gif" title="'._T('asso:ressources_nav_supprimer').'"></a></td>';
			echo '<td class="arial11 border1" style="text-align:center;"><a href="'.$url_edit_ressource.'&id='.$data['id_ressource'].'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'edit-12.gif" title="'._T('asso:ressources_nav_editer').'"></a></td>';
			echo'  </tr>';
		}     
		echo'</table>';
		
		fin_cadre_relief();  
		echo fin_gauche(), fin_page();
	}
?>
