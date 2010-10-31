<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007-2008
	* Bernard Blazin & François de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/autoriser');
include_spip ('inc/navigation_modules');
	
function exec_voir_adherent(){
		
	$id_auteur= intval($_GET['id']);
	if (!autoriser('configurer') OR !$data = sql_fetsel("*",_ASSOCIATION_AUTEURS_ELARGIS, "id_auteur=$id_auteur")) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$indexation = $GLOBALS['association_metas']['indexation'];
		$id_asso=$data['id_asso'];
		$nom_famille=$data['nom_famille'];
		$prenom=$data['prenom'];
		$validite=$data['validite'];
		$adh = generer_url_ecrire('edit_adherent',"id=$id_auteur");
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
		echo '<br /><div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">',
			"<a href='$adh' title=\"",
			_T('asso:adherent_label_modifier_membre'),
			"\">",
			htmlspecialchars($nom_famille.' '.$prenom),
			 "</a></td></div>\n";
		echo '<br /><div style="text-align:center;">'.association_date_du_jour().'</div>';	
		 echo fin_boite_info(true);
		
		 echo association_retour();

		 echo debut_droite("",true);
		
		 debut_cadre_relief(  "", false, "", $titre = $nom_famille.' '.$prenom);

		// FICHE HISTORIQUE COTISATIONS
		echo '<fieldset><legend>'._T('asso:adherent_titre_historique_cotisations').'</legend>';
		echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo "<tr style='background-color: #DBE1C5;'>\n";
		echo '<th style="text-align:right;">'._T('asso:adherent_entete_id').'</th>';
		echo '<th>'._T('asso:adherent_entete_journal').'</th>';
		echo '<th>'._T('asso:adherent_entete_date').'</th>';
		echo '<th>'._T('asso:adherent_entete_justification').'</th>';
		echo '<th style="text-align:right;">'._T('asso:montant').'</th>';
		echo '</tr>';
		
		$query = sql_select("*", "spip_asso_comptes", "id_journal=$id_auteur ", '', "date DESC" );
		while ($data = sql_fetch($query)) {
		  voir_adherent_paiement($data['id_compte'], $data['date'], $data['recette'], $data['justification'], $data['journal']);
		}
		echo '</table>';
		echo '</fieldset>';
		
		// FICHE HISTORIQUE ACTIVITES	
		if ($GLOBALS['association_metas']['activites']=="on"){
			echo '<fieldset><legend>'._T('asso:adherent_titre_historique_activites').'</legend>';
			echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
			echo "<tr style='background-color: #DBE1C5;'>\n";
			echo '<th style="text-align:right;">'._T('asso:adherent_entete_id')."</th>\n";
			echo '<th>'._T('asso:adherent_entete_date')."</th>\n";
			echo '<th>'._T('asso:adherent_entete_activite')."</th>\n";
			echo '<th style="text-align:right;">'._T('asso:adherent_entete_inscrits')."</th>\n";
			echo '<th>'._T('asso:adherent_entete_statut')."</th>\n";
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
		if ($GLOBALS['association_metas']['ventes']=="on"){
			echo '<fieldset><legend>'._T('asso:adherent_titre_historique_ventes').'</legend>';
			echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
			echo "<tr style='background-color: #DBE1C5;'>\n";
			echo '<th style="text-align:right;">'._T('asso:vente_entete_id')."</th>\n";
			echo '<th>'._T('asso:vente_entete_date')."</th>\n";
			echo '<th>'._T('asso:vente_entete_article')."</th>\n";
			echo '<th style="text-align:right;">'._T('asso:vente_entete_quantites')."</th>\n";
			echo '<th>'._T('asso:vente_entete_date_envoi')."</th>\n";
			echo "<td><strong>&nbsp;</strong></td>\n";
			echo '</tr>';
			$critere='id_acheteur='.$id_auteur;
			if($indexation=='id_asso'){$critere='id_acheteur='._q($id_asso);} 
			$query = sql_select("*", "spip_asso_ventes", $critere, '', "date_vente DESC" );			
			while ($data = sql_fetch($query)) {
			  voir_adherent_vente($data['id_vente'], $data['article'], $data['quantite'], $data['date_vente'], $data['date_envoi']);
			}
			echo '</table>';
			echo '</fieldset>';
		}
		// FICHE HISTORIQUE DONS
		if ($GLOBALS['association_metas']['dons']=="on"){
			echo '<fieldset><legend>'._T('asso:adherent_titre_historique_dons').'</legend>';
			echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
			echo "<tr style='background-color: #DBE1C5;'>\n";
			echo '<th style="text-align:right;">'._T('asso:adherent_entete_id').'</th>';
			echo '<th>'._T('asso:adherent_entete_journal').'</th>';
			echo '<th>'._T('asso:adherent_entete_date').'</th>';
			echo '<th>'._T('asso:adherent_entete_justification').'</th>';
			echo '<th style="text-align:right;">'._T('asso:montant').'</th>';
			$query = sql_select("*", "spip_asso_dons", 'id_adherent='.$id_auteur, '', "date_don DESC" );			
			foreach(voir_adherent_dons($id_auteur) as $data) {
				voir_adherent_paiement($data['id_don'], $data['date_don'], $data['argent'], $data['justification'], $data['journal']);
			}
			echo '</table></fieldset>';
		}
		// FICHE HISTORIQUE PRETS
		if ($GLOBALS['association_metas']['prets']=="on"){
			echo '<fieldset><legend>'._T('asso:adherent_titre_historique_prets').'</legend>';
			echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
			echo "<tr style='background-color: #DBE1C5;'>\n";
			echo '<th>&nbsp;</th>';
			echo '<th style="text-align:right;">'._T('asso:entete_id')."</th>\n";
			echo '<th>'._T('asso:vente_entete_article')."</th>\n";
			echo '<th style="text-align:right;">'._T('asso:prets_entete_date_sortie')."</th>\n";
			echo '<th style="text-align:right;">'._T('asso:prets_entete_date_retour')."</th>\n";
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
				echo '<td class="arial11 border1" style="text-align:center">' . association_bouton(_T('asso:adherent_bouton_maj_operation'), 'edit-12.gif', 'edit_pret', 'agir=modifie&id_pret='.$data['id_pret']) . "</td>\n";
				echo '</tr>';
			}
			echo '</table>';
			echo '</fieldset>';
		}
		
		echo fin_cadre_relief(true);
		echo fin_page_association();
	} 
}

function voir_adherent_paiement($id, $date, $montant, $justification, $journal)
{
	echo '<tr style="background-color: #EEEEEE;">';
	echo '<td class="arial11 border1" style="text-align:right;">'.$id."</td>\n";
	echo '<td class="arial11 border1">'.$journal."</td>\n";
	echo '<td class="arial11 border1">'.association_datefr($date)."</td>\n";
	echo '<td class="arial11 border1">'.propre($justification)."</td>\n";
	echo '<td class="arial11 border1" style="text-align:right;">'.$montant.' &euro;</td>';
	echo '</tr>';
}

function voir_adherent_vente($id, $article, $quantite, $date_vente, $date_envoi)
{
	echo '<tr style="background-color: #EEEEEE;">';
	echo '<td class="arial11 border1" style="text-align:right;">'.$id."</td>\n";
	echo '<td class="arial11 border1" style="text-align:right;">'.association_datefr($date_vente)."</td>\n";
	echo '<td class="arial11 border1">'.$article."</td>\n";
	echo '<td class="arial11 border1" style="text-align:right;">'.$quantite."</td>\n";
	echo '<td class="arial11 border1" style="text-align:right;">'.association_datefr($date_envoi)."</td>\n";
	echo '<td class="arial11 border1" style="text-align:center;">', association_bouton(_T('asso:adherent_bouton_maj_vente'), 'edit-12.gif', 'edit_vente','id='.$id), "</td>\n";
	echo '</tr>';
}

function voir_adherent_dons($id_auteur)
{
	return sql_allfetsel("*", 
			     "spip_asso_dons AS D LEFT JOIN spip_asso_comptes AS C ON C.id_journal=D.id_don",
			     'C.imputation=' . sql_quote($GLOBALS['association_metas']['pc_dons']) . ' AND '. 'id_adherent='.$id_auteur, 
			     '',
			     "D.date_don DESC" );			
}
?>
