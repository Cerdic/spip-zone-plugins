<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/autoriser');
include_spip ('inc/navigation_modules');
include_spip ('inc/voir_adherent');
	
function exec_voir_adherent(){
		
	$id_auteur= intval($_GET['id']);
	$full = autoriser('associer', 'adherents');
	
	if ((!$full AND ($id_auteur !== $GLOBALS['visiteur_session']['id_auteur'])) OR !$data = sql_fetsel("*",_ASSOCIATION_AUTEURS_ELARGIS, "id_auteur=$id_auteur")) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$indexation = $GLOBALS['association_metas']['indexation'];
		$id_asso=$data['id_asso'];
		$nom_famille=$data['nom_famille'];
		$prenom=$data['prenom'];
		$validite=$data['validite'];
		$adresse = $data['adresse'];
		$cp = $data['code_postal'];
		$ville = $data['ville'];
		$telephone = $data["telephone"];
  		$mobile = $data["mobile"];

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo '<div style="font-weight: bold; text-align: center;" class="verdana1 spip_xx-small">'.propre(_T('asso:adherent_libelle_numero')).'<br />';
		echo '<span class="spip_xx-large">';
		if($indexation=="id_asso"){echo $id_asso;} else {echo $id_auteur;}
		echo '</span></div>';

		$nom = htmlspecialchars($nom_famille.' '.$prenom);
		if ($full) {
			$adh = generer_url_ecrire('edit_adherent',"id=$id_auteur");
			$nom = "<a href='$adh' title=\"" .
			  _T('asso:adherent_label_modifier_membre') .
			  "\">" .
			  $nom .
			  "</a>";

			$coord =  '<br /><div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">' . $adresse . '<br />' . $cp . ' ' . $ville . '<br/>' . $telephone . '<br />' . $mobile .  "</div>\n";

		} else $coord = '';

		echo '<br /><div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">', $nom, "</div>\n", $coord;

		echo '<br /><div style="text-align:center;">'.association_date_du_jour().'</div>';	
		 echo fin_boite_info(true);
		
		 echo association_retour();

		 echo debut_droite("",true);
		
		 debut_cadre_relief(  "", false, "", $titre = $nom_famille.' '.$prenom);

		 echo _L('Liens_vers_les_justificatifs'), ' ', voir_adherent_recus($id_auteur), '<br /><br />';

		// FICHE HISTORIQUE COTISATIONS
		echo '<fieldset><legend>'._T('asso:adherent_titre_historique_cotisations').'</legend>';
		echo voir_adherent_cotisations($id_auteur, $full);

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
			$critere='id_acheteur='. (($indexation !=='id_asso') ? $id_auteur : sql_quote($id_asso));

			if ($r = voir_adherent_ventes($critere))
			  echo '<fieldset><legend>'._T('asso:adherent_titre_historique_ventes').'</legend>', $r, '</fieldset>';
		}
		// FICHE HISTORIQUE DONS
		if ($GLOBALS['association_metas']['dons']=="on"){
			if ($r = voir_adherent_dons($id_auteur, $full))
				echo '<fieldset><legend>'._T('asso:adherent_titre_historique_dons').'</legend>', $r, '</fieldset>';
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

?>
