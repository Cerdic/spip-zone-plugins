<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
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
	$data = sql_fetsel("m.sexe, m.nom_famille, m.prenom, m.validite, m.id_asso, c.libelle",'spip_asso_membres as m LEFT JOIN spip_asso_categories as c ON m.categorie=c.id_categorie', "m.id_auteur=$id_auteur");

	if ((!$full AND ($id_auteur !== intval($GLOBALS['visiteur_session']['id_auteur']))) OR !$data) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip('inc/association_coordonnees');
		$nom_membre = association_calculer_nom_membre($data['sexe'], $data['prenom'], $data['nom_famille']);
		$validite=$data['validite'];
		$adresses = association_recuperer_adresses_string(array($id_auteur));
		$emails = association_recuperer_emails_string(array($id_auteur));
		$telephones = association_recuperer_telephones_string(array($id_auteur));

		$categorie = $data['libelle']?$data['libelle']:_T('asso:pas_de_categorie_attribuee');

		$statut = sql_getfetsel('statut', 'spip_auteurs', 'id_auteur='.$id_auteur);
		switch($statut)	{
			case "0minirezo":
				$statut='auteur'; break;
			case "1comite":
				$statut='auteur'; break;
			default :
				$statut='visiteur'; break;
		}
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
		association_onglets(_T('asso:titre_onglet_membres'));
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo '<div class="infos"><div class="numero"><a href="'.generer_url_ecrire('auteur_infos','id_auteur='.$id_auteur).'" title="'._T('asso:adherent_label_modifier_'.$statut).'">'._T('asso:adherent_libelle_numero_'.$statut);
		echo '<p>';
		echo $id_auteur;
		echo '</p></a></div></div>';

		$nom = htmlspecialchars($nom_membre);
		$coord = '';
		if ($full) {
			$adh = generer_url_ecrire('edit_adherent',"id=$id_auteur");
			$nom = "<a href='$adh' title=\"" .
			  _T('asso:adherent_label_modifier_membre') .
			  "\">" .
			  $nom .
			  "</a>";
			$coord = '<div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">';
			if ($adresses[$id_auteur]) $coord .= '<br />' . $adresses[$id_auteur] . '<br/>';
			if ($emails[$id_auteur]) $coord .= '<br/>' . $emails[$id_auteur];
			if ($telephones[$id_auteur]) $coord .=  '<br/>'.$telephones[$id_auteur];
			$coord .= "<p>".$categorie."</p></div>";

		}
		$coord .= "<div style='font-weight: bold; text-align:center' class='verdana1 spip_xx-small'><p>"._T('asso:adherent_libelle_date_validite')."<br/>".affdate($validite)."</p></div>";

		echo '<br /><div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">'.$nom."</div>".$coord;
		if ($GLOBALS['association_metas']['id_asso'] == 'on') {
			
			$id_asso = ($data['id_asso'])?_T('asso:adherent_libelle_reference_interne').'<br/>'.$data['id_asso']:_T('asso:pas_de_reference_interne_attribuee');
			echo '<p style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">'.$id_asso."</p>";
		}

		// Afficher les champs extras
		echo '<div style="text-align: center" class="verdana1 spip_xx-small">'.pipeline('afficher_contenu_objet', array ('args'=>array('type'=>'asso_membre', 'id_objet'=>$id_auteur, 'contexte'=>array()), 'data'=>'')).'</div>';

		echo '<br/><div style="text-align:center;">'.association_date_du_jour().'</div>';	
		 echo fin_boite_info(true);
		
		 echo association_retour();

		 echo debut_droite("",true);
		
		 debut_cadre_relief(  "", false, "", $titre = $nom_membre);

		// Liste des groupes
		$query = sql_select('g.id_groupe as id_groupe, g.nom as nom', 'spip_asso_groupes g LEFT JOIN spip_asso_groupes_liaisons l ON g.id_groupe=l.id_groupe', 'l.id_auteur='.$id_auteur, '', 'g.nom');
		if (sql_count($query)) {
			echo '<div class="cadre_padding">'._T('asso:groupes_dp');
			if ($row=sql_fetch($query)) {
				echo ' <a href="'.generer_url_ecrire('voir_groupe', 'id='.$row['id_groupe']).'">'.$row['nom'].'</a>';
			}
			while ($row=sql_fetch($query)) {
				echo ', <a href="'.generer_url_ecrire('voir_groupe', 'id='.$row['id_groupe']).'">'.$row['nom'].'</a>';
			}
			echo '.</div>';
		}


		// FICHE HISTORIQUE COTISATIONS
		echo '<fieldset><legend>'._T('asso:adherent_titre_historique_cotisations').'</legend>';
		echo _T('asso:liens_vers_les_justificatifs'), ' ', voir_adherent_recus($id_auteur), '<br /><br />';
		/* si on a l'autorisation admin, on ajoute un bouton pour ajouter une cotisation */
		if ($full) {
			echo '<a href="'.generer_url_ecrire('ajout_cotisation', 'id='.$id_auteur).'">'._T('asso:adherent_label_ajouter_cotisation').'</a>';
		}
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

			if ($r = voir_adherent_ventes($id_auteur))
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
			$critere='id_emprunteur='._q($id_auteur);
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
