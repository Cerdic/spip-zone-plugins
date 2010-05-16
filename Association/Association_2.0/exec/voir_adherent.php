<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007-2008
	* Bernard Blazin & Fran�ois de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip ('inc/navigation_modules');
	
function exec_voir_adherent(){
		
	include_spip('inc/autoriser');
	if (!autoriser('configurer')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$id_auteur= intval($_GET['id']);
		$indexation = $GLOBALS['asso_metas']['indexation'];
		$query = sql_select("*",_ASSOCIATION_AUTEURS_ELARGIS, "id_auteur=$id_auteur");
		while ($data = sql_fetch($query)) { 
			$id_asso=$data['id_asso'];
			$nom_famille=$data['nom_famille'];
			$prenom=$data['prenom'];
			$validite=$data['validite'];
		}
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
		//debut_page(_T(), "", "");
		
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo '<div style="font-weight: bold; text-align: center;" class="verdana1 spip_xx-small">'.propre(_T('asso:adherent_libelle_numero')).'<br />';
		echo '<span class="spip_xx-large">';
		if($indexation=="id_asso"){echo $id_asso;} else {echo $id_auteur;}
		echo '</span></div>';
		echo '<br /><div style="font-weight: bold; text-align: center;" class="verdana1 spip_xx-small">'.$nom_famille.' '.$prenom.'</div>';
		echo '<br /><div style="text-align:center;">'.association_date_du_jour().'</div>';	
		 echo fin_boite_info(true);
		
		
		 echo association_retour();

		echo debut_droite("",true);
		
		debut_cadre_relief(  "", false, "", $titre = _T('asso:adherent_titre_historique_membre'));
		
		// FICHE HISTORIQUE COTISATIONS
		echo '<fieldset><legend>'._T('asso:adherent_titre_historique_cotisations').'</legend>';
		echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo "<tr style='background-color: #DBE1C5;'>\n";
		echo '<td style="text-align:right;"><strong>'._T('asso:adherent_entete_id').'</strong></td>';
		echo '<td><strong>'._T('asso:adherent_entete_date').'</strong></td>';
		echo '<td style="text-align:right;"><strong>'._T('asso:adherent_entete_paiement').'</strong></td>';
		echo '<td><strong>'._T('asso:adherent_entete_justification').'</strong></td>';
		echo '<td><strong>'._T('asso:adherent_entete_journal').'</strong></td>';
		echo '</tr>';
		
		$query = sql_select("*", "spip_asso_comptes", "id_journal=$id_auteur ", '', "date DESC" );
		while ($data = sql_fetch($query)) {
			echo '<tr style="background-color: #EEEEEE;">';
			echo '<td class="arial11 border1" style="text-align:right;">'.$data['id_compte']."</td>\n";
			echo '<td class="arial11 border1">'.association_datefr($data['date'])."</td>\n";
			echo '<td class="arial11 border1" style="text-align:right;">'.$data['recette'].' &euro;</td>';
			echo '<td class="arial11 border1">'.propre($data['justification'])."</td>\n";
			echo '<td class="arial11 border1">'.$data['journal']."</td>\n";
			echo '</tr>';
		}
		echo '</table>';
		echo '</fieldset>';
		
		// FICHE HISTORIQUE ACTIVITES	
		if ($GLOBALS['asso_metas']['activites']=="on"){
			echo '<fieldset><legend>'._T('asso:adherent_titre_historique_activites').'</legend>';
			echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
			echo "<tr style='background-color: #DBE1C5;'>\n";
			echo '<td style="text-align:right;"><strong>'._T('asso:adherent_entete_id')."</strong></td>\n";
			echo '<td><strong>'._T('asso:adherent_entete_date')."</strong></td>\n";
			echo '<td><strong>'._T('asso:adherent_entete_activite')."</strong></td>\n";
			echo '<td style="text-align:right;"><strong>'._T('asso:adherent_entete_inscrits')."</strong></td>\n";
			echo '<td><strong>'._T('asso:adherent_entete_statut')."</strong></td>\n";
			echo '<td><strong>&nbsp;</strong></td>';
			echo '</tr>';
			$critere='id_adherent='.$id_auteur;
			if($indexation=='id_asso'){$critere='id_adherent='._q($id_asso);} 
			$query = sql_select("*", "spip_asso_activites", $critere, '', "date DESC" );			
			while ($data = sql_fetch($query)) {
				$id_evenement=$data['id_evenement'];
				echo '<tr style="background-color: #EEEEEE;">';
				echo '<td class="arial11 border1" style="text-align:right;">'.$data['id_activite']."</td>\n";
				$sql = sql_select("*", "spip_evenements", "id_evenement=$id_evenement" );
				while ($evenement = sql_fetch($sql)) {
					$date = substr($evenement['date_debut'],0,10);
					//echo '<td class="arial11 border1">'.association_datefr($date)."</td>\n";
					echo '<td class="arial11 border1">'.association_datefr($date)."</td>\n";
					echo '<td class="arial11 border1">'.$evenement['titre']."</td>\n";
				}
				echo '<td class="arial11 border1" style="text-align: right;">'.$data['inscrits']."</td>\n";
				echo '<td class="arial11 border1">'.$data['statut']."</td>\n";
				echo '<td class="arial11 border1" style="text-align: center;">', association_bouton(_T('asso:adherent_bouton_maj_inscription'), 'edit-12.gif', 'edit_activite', 'id='.$data['id_activite']), "</td>\n";
				echo '</tr>';
			}
			echo '</table>';
			echo '</fieldset>';
		}
		
		// FICHE HISTORIQUE VENTES
		if ($GLOBALS['asso_metas']['ventes']=="on"){
			echo '<fieldset><legend>'._T('asso:adherent_titre_historique_ventes').'</legend>';
			echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
			echo "<tr style='background-color: #DBE1C5;'>\n";
			echo '<td style="text-align:right;"><strong>'._T('asso:vente_entete_id')."</strong></td>\n";
			echo '<td><strong>'._T('asso:vente_entete_date')."</strong></td>\n";
			echo '<td><strong>'._T('asso:vente_entete_article')."</strong></td>\n";
			echo '<td style="text-align:right;"><strong>'._T('asso:vente_entete_quantites')."</strong></td>\n";
			echo '<td><strong>'._T('asso:vente_entete_date_envoi')."</strong></td>\n";
			echo "<td><strong>&nbsp;</strong></td>\n";
			echo '</tr>';
			$critere='id_acheteur='.$id_auteur;
			if($indexation=='id_asso'){$critere='id_acheteur='._q($id_asso);} 
			$query = sql_select("*", "spip_asso_ventes", $critere, '', "date_vente DESC" );			
			while ($data = sql_fetch($query)) {
				echo '<tr style="background-color: #EEEEEE;">';
				echo '<td class="arial11 border1" style="text-align:right;">'.$data['id_vente']."</td>\n";
				echo '<td class="arial11 border1" style="text-align:right;">'.association_datefr($data['date_vente'])."</td>\n";
				echo '<td class="arial11 border1">'.$data['article']."</td>\n";
				echo '<td class="arial11 border1" style="text-align:right;">'.$data['quantite']."</td>\n";
				echo '<td class="arial11 border1" style="text-align:right;">'.association_datefr($data['date_envoi'])."</td>\n";
				echo '<td class="arial11 border1" style="text-align:center;">', association_bouton(_T('asso:adherent_bouton_maj_vente'), 'edit-12.gif', 'edit_vente','id='.$data['id_vente']), "</td>\n";
				echo '</tr>';
			}
			echo '</table>';
			echo '</fieldset>';
		}
		// FICHE HISTORIQUE DONS
		if ($GLOBALS['asso_metas']['dons']=="on"){
			echo '<fieldset><legend>'._T('asso:adherent_titre_historique_dons').'</legend>';
			echo _T('asso:a_developper');
			echo '</fieldset>';
		}
		// FICHE HISTORIQUE PRETS
		if ($GLOBALS['asso_metas']['prets']=="on"){
			echo '<fieldset><legend>'._T('asso:adherent_titre_historique_prets').'</legend>';
			echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
			echo "<tr style='background-color: #DBE1C5;'>\n";
			echo '<td>&nbsp;</td>';
			echo '<td style="text-align:right;"><strong>'._T('asso:pret_entete_id')."</strong></td>\n";
			echo '<td><strong>'._T('asso:pret_entete_article')."</strong></td>\n";
			echo '<td style="text-align:right;"><strong>'._T('asso:pret_entete_date_sortie')."</strong></td>\n";
			echo '<td style="text-align:right;"><strong>'._T('asso:pret_entete_date_retour')."</strong></td>\n";
			echo '<td><strong>&nbsp;</strong></td>';
			echo '</tr>';
			if($indexation=='id_asso'){$critere='id_emprunteur='._q($id_asso);} else {$critere='id_emprunteur='._q($id_auteur);}
			$query = sql_select("*", "spip_asso_prets AS P LEFT JOIN spip_asso_ressources AS R ON P.id_ressource=R.id_ressource", $critere, '', "id_pret DESC" );			
			while ($data = sql_fetch($query)) {
				switch($data['statut']){
				case "ok": $puce= "verte"; break;
				case "reserve": $puce= "rouge"; break;
				case "suspendu": $puce="orange"; break;
				case "sorti": $puce="poubelle"; break;	   
				}
				echo "\n<tr style='background-color: #EEEEEE;'>";
				echo '<td class="arial11 border1">';
				echo '<img src="' . _DIR_PLUGIN_ASSOCIATION_ICONES . 'puce-'.$puce. ".gif\" /></td>\n";
				echo '<td class="arial11 border1" style="text-align:right;">'.$data['id_pret']."</td>\n";
				echo '<td class="arial11 border1">'.$data['intitule']."</td>\n";
				echo '<td class="arial11 border1" style="text-align:right;">'.association_datefr($data['date_sortie'])."</td>\n";
				echo '<td class="arial11 border1" style="text-align:right;">';
				if($data['date_retour']=="0000-00-00"){echo '&nbsp;';} else {echo association_datefr($data['date_retour']);}
				echo "</td>\n";
				echo '<td class="arial11 border1" style="text-align:center">' . association_bouton(_L('adherent_bouton_maj_pret'), 'edit-12.gif', 'edit_pret', 'agir=modifie&id_pret='.$data['id_pret']) . "</td>\n";
				echo '</tr>';
			}
			echo '</table>';
			echo '</fieldset>';
		}
		
		echo fin_cadre_relief(true);
		echo fin_gauche(), fin_page();
	} 
}
?>
