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
	
function exec_prets(){
		
	include_spip('inc/autoriser');
	if (!autoriser('configurer')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		
		$url_prets = generer_url_ecrire('prets');
		$url_ajout_pret=generer_url_ecrire('edit_pret','agir=ajoute');
		$url_edit_pret=generer_url_ecrire('edit_pret','agir=modifie');
		$url_action_prets=generer_url_ecrire('action_prets');
		$url_retour = $_SERVER['HTTP_REFERER'];
		$id_ressource=intval($_REQUEST['id']);
		
		
	
		 $commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:prets_titre_liste_reservations')) ;
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		$data = sql_fetsel("*", "spip_asso_ressources", "id_ressource=$id_ressource" ) ;
		
		$statut=$data['statut'];
		echo '<div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">'._T('asso:ressources_num').'<br />';
		echo '<span class="spip_xx-large">'.$data['id_ressource'].'</span></div>';
		echo '<p>'._T('asso:ressources_libelle_code').': '.$data['code'].'<br />';
		echo $data['intitule'];
		echo '</p>';
		echo fin_boite_info(true);
		
		if ($statut=="ok") {
			$res=association_icone(_T('asso:prets_nav_ajouter'),  $url_ajout_pret.'&id_pret='.$id_ressource, 'livredor.png', 'creer.gif');
			echo bloc_des_raccourcis($res);
		}
		$res=association_icone(_T('asso:bouton_retour'),  $url_retour, "retour-24.png");	
		echo bloc_des_raccourcis($res);
		
		echo debut_droite("",true);
		echo debut_cadre_relief(  "", false, "", $titre =_T('asso:prets_titre_liste_reservations'));
		
		echo "<table border=0 cellpadding=2 cellspacing=0 width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo "<tr bgcolor='#DBE1C5'>";
		echo '<td>&nbsp;</td>';
		echo '<td>'._T('asso:entete_id').'</td>';
		echo '<td><strong>'._T('asso:prets_entete_date_sortie').'</strong></td>';
		echo '<td><strong>'._T('asso:prets_entete_nom').'</strong></td>';
		echo '<td><strong>'._T('asso:prets_entete_duree').'</strong></td>';
		echo '<td><strong>'._T('asso:prets_entete_date_retour').'</strong></td>';
		echo '<td colspan="2" style="text-align:center;"><strong>'._T('asso:entete_action').'</strong></td>';
		echo'  </tr>';
		$index = lire_config('association/indexation');
		if (!$index) $index = "id_adherent";
		$query = sql_select("*", "spip_asso_prets", "id_ressource=$id_ressource", '', "date_sortie DESC" ) ;
		while ($data = sql_fetch($query)) {
			echo '<tr style="background-color: #EEEEEE;">';	
			echo '<td class="arial11 border1">';
			$s = $data['statut'];
			switch($s) {
				case "du": $puce= "rouge"; break;
				case "attendu": $puce="orange"; break;
				case "annule": $puce="poubelle"; break;	   
				case "ok": default: $puce= "verte"; break;
			}
			echo http_img_pack('puce-'.$puce.'.gif', $s), '</td>';			
			echo '<td class="arial11 border1">'.$data['id_pret'].'</td>';
			echo '<td class="arial11 border1" style="text-align:right">'.association_datefr($data['date_sortie']).'</td>';
			$id_emprunteur=$data['id_emprunteur'];

			$auteur=sql_fetsel("*", "spip_asso_adherents", "$index=$id_emprunteur");
			echo '<td class="arial11 border1">'.$auteur['nom'].' '.$auteur['prenom'].'</td>';
			echo '<td class="arial11 border1" style="text-align:right;">'.$data['duree'].'</td>';
			echo '<td class="arial11 border1" style="text-align:right">';
			if ($data['date_retour']==0) { echo '&nbsp';} else {echo association_datefr($data['date_retour']);}
			echo '</td>';
			echo '<td class="arial11 border1" style="text-align:center;"><a href="'.$url_action_prets.'&agir=supprime&id_pret='.$data['id_pret'].'&id_ressource='.$id_ressource.'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'poubelle-12.gif" title="'._T('asso:prets_nav_annuler').'"></a></td>';
			echo '<td class="arial11 border1" style="text-align:center;"><a href="'.$url_edit_pret.'&id_pret='.$data['id_pret'].'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'edit-12.gif" title="'._T('asso:prets_nav_editer').'"></a></td>';
			echo'  </tr>';
		}     
		echo'</table>';
		
		fin_cadre_relief();  
		echo fin_gauche(), fin_page();
	}
}
?>
