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
	
	function exec_action_activites(){
		global $connect_statut, $connect_toutes_rubriques;
		
		include_spip ('inc/acces_page'); 
		
		$url_action_activites=generer_url_ecrire('action_activites');
		
		$id_activite=$_POST['id_activite'];
		$id_evenement=$_POST['id_evenement'];
		$date=$_POST['date'];
		$nom=$_POST['nom'];
		$id_membre=$_POST['id_membre'];
		$membres=$_POST['membres'];
		$non_membres=$_POST['non_membres'];
		$inscrits=$_POST['inscrits'];
		$email=$_POST['email'];
		$telephone=$_POST['telephone'];
		$adresse=$_POST['adresse'];
		$montant=$_POST['montant'];
		$date_paiement=$_POST['date_paiement'];
		$journal=$_POST['journal'];
		$statut=$_POST['statut'];
		$commentaire=$_POST['commentaire'];
		
		$action=$_POST['action'];
		$url_retour=$_POST['url_retour'];
		
		//AJOUT INSCRIPTION
		if ($action=="ajoute"){
			spip_query( "INSERT INTO spip_asso_activites (date, id_evenement, nom, id_adherent, membres, non_membres, inscrits, email, telephone, adresse, montant, commentaire) VALUES ("._q($date).", "._q($id_evenement).", "._q($nom).", "._q($id_membre).", "._q($membres).", "._q($non_membres).", "._q($inscrits).", "._q($email).", "._q($telephone).", "._q($adresse).", "._q($montant).", "._q($commentaire)." )" );
			header ('location:'.$url_retour);
		}
		
		//MODIFICATION INSCRIPTION
		if ($action=="modifie") {
			spip_query("UPDATE spip_asso_activites SET date="._q($date).", id_evenement="._q($id_evenement).", nom="._q($nom).", id_adherent="._q($id_membre).", membres="._q($membres).", non_membres="._q($non_membres).", inscrits="._q($inscrits).", email="._q($email).", telephone="._q($telephone).", adresse="._q($adresse).", montant="._q($montant).", date_paiement="._q($date_paiement).", statut="._q($statut).", commentaire="._q($commentaire)." WHERE id_activite='$id_activite' ");
			header ('location:'.$url_retour);
			exit;
		}
		
		//AJOUT PAIEMENT
		if ($action=="paie") {
			spip_query("UPDATE spip_asso_activites SET nom="._q($nom).", id_adherent="._q($id_membre).", membres="._q($membres).", non_membres="._q($non_membres).", inscrits="._q($inscrits).", montant="._q($montant).", date_paiement="._q($date_paiement).", statut="._q($statut).", commentaire="._q($commentaire)." WHERE id_activite='$id_activite' ");
			$justification=_T('asso:activite_justification_compte_inscription',array('id_activite' => $id_activite, 'nom' => $nom));
			spip_query("INSERT INTO spip_asso_comptes (date, journal,recette,justification,imputation,id_journal) VALUES ("._q($date_paiement).","._q($journal).","._q($montant).","._q($justification).",".lire_config('association/pc_activites').","._q($id_activite).")");
			header ('location:'.$url_retour);
			exit;
		}	
		
		//SUPPRESSION PROVISOIRE INSCRIPTIONS
		if (isset($_POST['delete'])) {
			$url_retour = $_SERVER['HTTP_REFERER'];
			$delete_tab=(isset($_POST["delete"])) ? $_POST["delete"]:array();
			$count=count ($delete_tab);
			
			debut_page(_T('asso:titre_gestion_pour_association'), "", "");
			association_onglets();
			
			debut_gauche();
			
			debut_boite_info();
			echo association_date_du_jour();	
			fin_boite_info();
			
			debut_raccourcis();
			icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION."/img_pack/retour-24.png","rien.gif");	
			fin_raccourcis();
			
			debut_droite();
			
			debut_cadre_relief(  "", false, "", $titre = _T('asso:activite_titre_inscriptions_activites'));
			echo '<p><strong>'._T('asso:activite_message_confirmation_supprimer',array('nombre' => $count, 'pluriel' => $count > 1 ? 's' : '')).'</strong></<p>';
			echo '<table>';
			echo '<form action="'.$url_action_activites.'"  method="post">';
			for ( $i=0 ; $i < $count ; $i++ ) {
				$id = $delete_tab[$i];
				echo '<input type="hidden" name="drop[]" value="'.$id.'" checked>';
			}
			echo '<tr>';
			echo '<td><input name="url_retour" type="hidden" value="'.$url_retour.'">';
			echo '<input name="submit" type="submit" value="'._T('asso:activite_bouton_confirmer').'" class="fondo"></td></tr>';
			echo '</form>';
			echo '</table>';
			echo '</div>';
			
			fin_cadre_relief();
			fin_page();
		}
		
		//  SUPPRESSION DEFINITIVE INSCRIPTIONS
		if (isset($_POST['drop'])) {
			$drop_tab=(isset($_POST["drop"])) ? $_POST["drop"]:array();
			$count=count ($drop_tab);
			for ( $i=0 ; $i < $count ; $i++ ) {
				$id = $drop_tab[$i];
				spip_query("DELETE FROM spip_asso_activites WHERE id_activite='$id'");
				spip_query("DELETE FROM spip_asso_comptes WHERE id_journal='$id' AND imputation=".lire_config('association/pc_activites'));
			}
			header ('location:'.$url_retour);
			exit;
		}
	}
?>
