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

#		if ($statut=="ok")
 {
			echo bloc_des_raccourcis(association_icone(_T('asso:prets_nav_ajouter'), generer_url_ecrire('edit_pret','agir=ajoute&id_pret='.$id_ressource), 'livredor.png', 'creer.gif'));
			
		}
		echo association_retour();
		echo debut_droite("",true);
		echo debut_cadre_relief(  "", false, "", $titre =_T('asso:prets_titre_liste_reservations'));
		
		echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo "<tr style='background-color: #DBE1C5'>";
		echo '<td>&nbsp;</td>';
		echo '<td>'._T('asso:entete_id').'</td>';
		echo '<td><strong>'._T('asso:prets_entete_date_sortie').'</strong></td>';
		echo '<td><strong>'._T('asso:prets_entete_nom').'</strong></td>';
		echo '<td><strong>'._T('asso:prets_entete_duree').'</strong></td>';
		echo '<td><strong>'._T('asso:prets_entete_date_retour').'</strong></td>';
		echo '<td colspan="2" style="text-align:center;"><strong>'._T('asso:entete_action').'</strong></td>';
		echo'  </tr>';
		$index = $GLOBALS['asso_metas']['indexation'];
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
			$id_emprunteur=intval($data['id_emprunteur']);

			$auteur=sql_fetsel("*", "spip_asso_adherents", "$index=$id_emprunteur");
			echo '<td class="arial11 border1">'.$auteur['nom'].' '.$auteur['prenom'].'</td>';
			echo '<td class="arial11 border1" style="text-align:right;">'.$data['duree'].'</td>';
			echo '<td class="arial11 border1" style="text-align:right">';
			if ($data['date_retour']==0) { echo '&nbsp';} else {echo association_datefr($data['date_retour']);}
			echo '</td>';
			echo '<td class="arial11 border1" style="text-align:center;">'. association_bouton(_T('asso:prets_nav_annuler'), 'poubelle-12.gif', 'action_prets', 'id_pret='.$data['id_pret'].'&id_ressource='.$id_ressource) . "</td>\n";
			echo '<td class="arial11 border1" style="text-align:center;">' . association_bouton(_T('asso:prets_nav_editer'), 'edit-12.gif', 'edit_pret', 'id_pret='.$data['id_pret']) . "</td>\n"; 
			echo'  </tr>';
		}     
		echo'</table>';
		
		fin_cadre_relief();  
		echo fin_gauche(), fin_page();
	}
}
?>
