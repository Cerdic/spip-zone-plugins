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
	include_spip ('inc/navigation_modules');
	
	function exec_voir_adherent(){
		
		include_spip('inc/autoriser');
		if (!autoriser('configurer')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}
		
		$url_edit_compte = generer_url_ecrire('edit_compte');
		$url_edit_activite = generer_url_ecrire('edit_activite');
		$url_edit_pret = generer_url_ecrire('edit_pret','action=modifie');
		$url_retour = $_SERVER['HTTP_REFERER'];
		
		$id_auteur= intval($_GET['id']);
		$indexation = lire_config('association/indexation');
		$query = association_auteurs_elargis_select("*",'', "id_auteur=$id_auteur");
			while ($data = spip_fetch_array($query)) { 
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
		echo '<div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">'.propre(_T('asso:adherent_libelle_numero')).'<br />';
		echo '<span class="spip_xx-large">';
		if($indexation=="id_asso"){echo $id_asso;} else {echo $id_auteur;}
		echo '</span></div>';
		echo '<br /><div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">'.$nom_famille.' '.$prenom.'</div>';
		echo '<br /><div style="text-align:center;">'.association_date_du_jour().'</div>';	
		 echo fin_boite_info(true);
		
		
		$res=icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION."/img_pack/retour-24.png","rien.gif",false);	
		echo bloc_des_raccourcis($res);
		
		echo debut_droite("",true);
		
		debut_cadre_relief(  "", false, "", $titre = _T('asso:adherent_titre_historique_membre'));
		
		// FICHE HISTORIQUE COTISATIONS
		echo '<fieldset><legend>'._T('asso:adherent_titre_historique_cotisations').'</legend>';
		echo "<table border=0 cellpadding=2 cellspacing=0 width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo '<tr bgcolor="#DBE1C5">';
		echo '<td style="text-align:right;"><strong>'._T('asso:adherent_entete_id').'</strong></td>';
		echo '<td><strong>'._T('asso:adherent_entete_date').'</strong></td>';
		echo '<td style="text-align:right;"><strong>'._T('asso:adherent_entete_paiement').'</strong></td>';
		echo '<td><strong>'._T('asso:adherent_entete_justification').'</strong></td>';
		echo '<td><strong>'._T('asso:adherent_entete_journal').'</strong></td>';
		echo '</tr>';
		
		$query = spip_query ("SELECT * FROM spip_asso_comptes WHERE id_journal=$id_auteur ORDER BY date DESC" );
		while ($data = spip_fetch_array($query)) {
			echo '<tr style="background-color: #EEEEEE;">';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;">'.$data['id_compte'].'</td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;">'.association_datefr($data['date']).'</td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;">'.$data['recette'].' &euro;</td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;">'.$data['justification'].'</td>';
			echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;">'.$data['journal'].'</td>';
			echo '</tr>';
		}
		echo '</table>';
		echo '</fieldset>';
		
		// FICHE HISTORIQUE ACTIVITES	
		if (lire_config('association/activites')=="on"){
			echo '<fieldset><legend>'._T('asso:adherent_titre_historique_activites').'</legend>';
			echo "<table border=0 cellpadding=2 cellspacing=0 width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
			echo '<tr bgcolor="#DBE1C5">';
			echo '<td style="text-align:right;"><strong>'._T('asso:adherent_entete_id').'</strong></td>';
			echo '<td><strong>'._T('asso:adherent_entete_date').'</strong></td>';
			echo '<td><strong>'._T('asso:adherent_entete_activite').'</strong></td>';
			echo '<td style="text-align:right;"><strong>'._T('asso:adherent_entete_inscrits').'</strong></td>';
			echo '<td><strong>'._T('asso:adherent_entete_statut').'</strong></td>';
			echo '<td><strong>&nbsp;</strong></td>';
			echo '</tr>';
			$critere='id_adherent='.$id_auteur;
			if($indexation=='id_asso'){$critere='id_adherent='._q($id_asso);} 
			$query = spip_query ("SELECT * FROM spip_asso_activites WHERE ".$critere." ORDER BY date DESC" );			
			while ($data = spip_fetch_array($query)) {
				$id_evenement=$data['id_evenement'];
				echo '<tr style="background-color: #EEEEEE;">';
				echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;">'.$data['id_activite'].'</td>';
				$sql = spip_query ("SELECT * FROM spip_evenements WHERE id_evenement=$id_evenement" );
				while ($evenement = spip_fetch_array($sql)) {
					$date = substr($evenement['date_debut'],0,10);
					//echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;">'.association_datefr($date).'</td>';
					echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;">'.association_datefr($date).'</td>';
					echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;">'.$evenement['titre'].'</td>';
				}
				echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;">'.$data['inscrits'].'</td>';
				echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;">'.$data['statut'].'</td>';
				echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:center"><a href="'.$url_edit_activite.'&id='.$data['id_activite'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/edit-12.gif" title="'._T('asso:adherent_bouton_maj_inscription').'"></a></td>';
				echo '</tr>';
			}
			echo '</table>';
			echo '</fieldset>';
		}
		
		// FICHE HISTORIQUE VENTES
		if (lire_config('association/ventes')=="on"){
			echo '<fieldset><legend>'._T('asso:adherent_titre_historique_ventes').'</legend>';
			echo "<table border=0 cellpadding=2 cellspacing=0 width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
			echo '<tr bgcolor="#DBE1C5">';
			echo '<td style="text-align:right;"><strong>'._T('asso:vente_entete_id').'</strong></td>';
			echo '<td><strong>'._T('asso:vente_entete_date').'</strong></td>';
			echo '<td><strong>'._T('asso:vente_entete_article').'</strong></td>';
			echo '<td style="text-align:right;"><strong>'._T('asso:vente_entete_quantites').'</strong></td>';
			echo '<td><strong>'._T('asso:vente_entete_date_envoi').'</strong></td>';
			echo '<td><strong>&nbsp;</strong></td>';
			echo '</tr>';
			$critere='id_acheteur='.$id_auteur;
			if($indexation=='id_asso'){$critere='id_acheteur='._q($id_asso);} 
			$query = spip_query ("SELECT * FROM spip_asso_ventes WHERE ".$critere." ORDER BY date_vente DESC" );			
			while ($data = spip_fetch_array($query)) {
				echo '<tr style="background-color: #EEEEEE;">';
				echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;">'.$data['id_vente'].'</td>';
				echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;">'.association_datefr($data['date_vente']).'</td>';
				echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;">'.$data['article'].'</td>';
				echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;">'.$data['quantite'].'</td>';
				echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;">'.association_datefr($data['date_envoi']).'</td>';
				echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:center"><a href="'.$url_edit_vente.'&id='.$data['id_vente'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/edit-12.gif" title="'._T('asso:adherent_bouton_maj_vente').'"></a></td>';
				echo '</tr>';
			}
			echo '</table>';
			echo '</fieldset>';
		}
		// FICHE HISTORIQUE DONS
		if (lire_config('association/dons')=="on"){
			echo '<fieldset><legend>'._T('asso:adherent_titre_historique_dons').'</legend>';
			echo 'A d&eacute;velopper';
			echo '</fieldset>';
		}
		// FICHE HISTORIQUE PRETS
		if (lire_config('association/prets')=="on"){
			echo '<fieldset><legend>'._T('asso:adherent_titre_historique_prets').'</legend>';
			echo "<table border=0 cellpadding=2 cellspacing=0 width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
			echo '<tr bgcolor="#DBE1C5">';
			echo '<td>&nbsp;</td>';
			echo '<td style="text-align:right;"><strong>'._T('asso:pret_entete_id').'</strong></td>';
			echo '<td><strong>'._T('asso:pret_entete_article').'</strong></td>';
			echo '<td style="text-align:right;"><strong>'._T('asso:pret_entete_date_sortie').'</strong></td>';
			echo '<td style="text-align:right;"><strong>'._T('asso:pret_entete_date_retour').'</strong></td>';
			echo '<td><strong>&nbsp;</strong></td>';
			echo '</tr>';
			if($indexation=='id_asso'){$critere='id_emprunteur='._q($id_asso);} else {$critere='id_emprunteur='._q($id_auteur);}
			$query = spip_query ("SELECT * FROM spip_asso_prets LEFT JOIN spip_asso_ressources ON spip_asso_prets.id_ressource=spip_asso_ressources.id_ressource WHERE ".$critere." ORDER BY id_pret DESC" );			
			while ($data = spip_fetch_array($query)) {
				echo '<tr style="background-color: #EEEEEE;">';
				echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;">';
				echo '<img src="/dist/images/puce-'.$puce.'.gif"></td>';
				echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;">'.$data['id_pret'].'</td>';
				echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;">'.$data['intitule'].'</td>';
				echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;">'.association_datefr($data['date_sortie']).'</td>';
				echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right;">';
				if($data['date_retour']=="0000-00-00"){echo '&nbsp;';} else {echo association_datefr($data['date_retour']);}
				echo '</td>';
				echo '<td class="arial11" style="border-top: 1px solid #CCCCCC;text-align:center"><a href="'.$url_edit_pret.'&id='.$data['id_pret'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/edit-12.gif" title="'._T('asso:adherent_bouton_maj_pret').'"></a></td>';
				echo '</tr>';
			}
			echo '</table>';
			echo '</fieldset>';
		}
		
		echo fin_cadre_relief(true);
		 echo fin_gauche(), fin_page();
	} 
?>

