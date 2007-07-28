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
	
	function exec_prets(){
		global $connect_statut, $connect_toutes_rubriques;
		
		debut_page(_T('asso:prets_titre_liste_reservations'), "", "");
		
		$url_prets = generer_url_ecrire('prets');
		$url_edit_pret=generer_url_ecrire('edit_pret');
		$url_action_prets=generer_url_ecrire('action_prets');
		$id_ressource=$_REQUEST['id'];
		
		association_onglets();
		
		debut_gauche();
		
		debut_boite_info();
		$query = spip_query ( "SELECT * FROM spip_asso_ressources WHERE id_ressource='$id_ressource'" ) ;
		while ($data = spip_fetch_array($query)) {
			$statut=$data['statut'];
			echo '<div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">'._T('asso:ressources_num').'<br />';
			echo '<span class="spip_xx-large">'.$data['id_ressource'].'</span></div>';
			echo '<p>'._T('asso:ressources_libelle_code').': '.$data['code'].'<br />';
			echo $data['intitule'];
			echo '</p>';
		}
		fin_boite_info();
		
		//include_spip('inc/raccourcis_modules');
		
		if ($connect_statut == '0minirezo') {
			debut_raccourcis();
			if ($statut=="ok") {
				icone_horizontale(_T('asso:prets_nav_ajouter'), generer_url_ecrire("edit_pret","action=ajoute&id=$id_ressource"), "fiche-perso-24.gif","cree.gif");
			}
			icone_horizontale(_T('asso:bouton_retour'), generer_url_ecrire("ressources","id=$id_ressource"), _DIR_PLUGIN_ASSOCIATION."/img_pack/livredor.png","rien.gif");	
			fin_raccourcis();
		}	
		
		debut_droite();
		debut_cadre_relief(  "", false, "", $titre =_T('asso:prets_titre_liste_reservations'));
		
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
		$query = spip_query ( "SELECT * FROM spip_asso_prets WHERE id_ressource='$id_ressource' ORDER BY date_sortie DESC" ) ;
		while ($data = spip_fetch_array($query)) {
			echo '<tr style="background-color: #EEEEEE;">';	
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;">';
			switch($data['statut']){
				case "ok": $puce= "verte"; break;
				case "du": $puce= "rouge"; break;
				case "attendu": $class="orange"; break;
				case "annule": $class="poubelle"; break;	   
			}
			echo '<img src="/dist/images/puce-'.$puce.'.gif"></td>';			
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;">'.$data['id_pret'].'</td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right">'.association_datefr($data['date_sortie']).'</td>';
			$id_emprunteur=$data['id_emprunteur'];
			$sql=spip_query( "SELECT * FROM spip_asso_adherents WHERE ".lire_config('association/indexation')."='$id_emprunteur' ");
			$auteur=spip_fetch_array($sql);
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;">'.$auteur['nom'].' '.$auteur['prenom'].'</td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;">'.$data['duree'].'</td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right">';
			if ($data['date_retour']==0) { echo '&nbsp';} else {echo association_datefr($data['date_retour']);}
			echo '</td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:center;"><a href="'.$url_action_prets.'&action=supprime&id_pret='.$data['id_pret'].'&id_ressource='.$id_ressource.'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/poubelle-12.gif" title="'._T('asso:prets_nav_annuler').'"></a></td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:center;"><a href="'.$url_edit_pret.'&action=modifie&id='.$data['id_pret'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/edit-12.gif" title="'._T('asso:prets_nav_editer').'"></a></td>';
			echo'  </tr>';
		}     
		echo'</table>';
		
		fin_cadre_relief();  
		fin_page();
	}
?>
