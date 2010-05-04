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
	
function exec_action_activites(){
		
	include_spip('inc/autoriser');
	if (!autoriser('configurer')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		
		$url_action_activites=generer_url_ecrire('action_activites');
		
		$id_activite=intval($_POST['id_activite']);
		$id_evenement=intval($_POST['id_evenement']);
		$id_membre=intval($_POST['id_membre']);
		$date=$_POST['date'];
		$nom=$_POST['nom'];
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
		
		$action=$_POST['agir'];
		$url_retour=$_POST['url_retour'];
		
		//AJOUT INSCRIPTION
		if ($action=="ajoute") {
			$n = activites_insert($date, $id_evenement, $non_membres, $inscrits, $email, $telephone, $adresse, $montant, $commentaire);
			spip_log("insertion activite numero: $n");
			header ('location:'.$url_retour);
		}
		
		//MODIFICATION INSCRIPTION
		if ($action=="modifie") {
		  sql_updateq('spip_asso_activites',array(
			"date" => $date,
			"id_evenement" => $id_evenement,
			"nom" => $nom,
			"id_adherent" => $id_membre,
			"membres" => $membres,
			"non_membres" => $non_membres,
			"inscrits" => $inscrits,
			"email" => $email,
			"telephone" => $telephone,
			"adresse" => $adresse,
			"montant" => $montant,
			"date_paiement" => $date_paiement,
			"statut" => $statut,
			"commentaire" => $commentaire),
			     "id_activite=$id_activite");
			header ('location:'.$url_retour);
			exit;
		}
		
		//AJOUT PAIEMENT
		if ($action=="paie") {
			$n = activites_paiement_insert($date_paiement, $journal, $montant, $id_activite, $nom, $commentaire, $statut, $inscrits, $nom_membres, $membres, $id_membre);
			spip_log("insertion paiement activite numero: $n");
			header ('location:'.$url_retour);
			exit;
		}	
		
		//SUPPRESSION PROVISOIRE INSCRIPTIONS
		if (isset($_POST['delete'])) {
			$url_retour = $_SERVER['HTTP_REFERER'];
			$delete_tab=(isset($_POST["delete"])) ? $_POST["delete"]:array();
			$count=count ($delete_tab);
			
			$commencer_page = charger_fonction('commencer_page', 'inc');
			echo $commencer_page(_L('Gestion pour Association')) ;
			association_onglets();
			
			debut_gauche();
			
			debut_boite_info();
			echo association_date_du_jour();	
			fin_boite_info();
			
			
			$res=association_icone(_T('asso:bouton_retour'),  $url_retour, "retour-24.png");	
			echo bloc_des_raccourcis($res);
			
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
				$id = intval($drop_tab[$i]);
				sql_delete('spip_asso_activites', "id_activite=$id");
				sql_delete('spip_asso_comptes', "id_journal=$id AND imputation=".lire_config('association/pc_activites'));
			}
			header ('location:'.$url_retour);
			exit;
		}
	}
}

function activites_paiement_insert($date_paiement, $journal, $montant, $id_activite, $nom, $commentaire, $statut, $inscrits, $nom_membres, $membres, $id_membre)
{
	sql_updateq('spip_asso_activites', array(
		"nom" => $nom,
		"id_adherent" => $id_membre,
		"membres" => $membres,
		"non_membres" => $non_membres,
		"inscrits" => $inscrits,
		"montant" => $montant,
		"date_paiement" => $date_paiement,
		"statut" => $statut,
		"commentaire" => $commentaire),
		   "id_activite=$id_activite");

	$justification=_T('asso:activite_justification_compte_inscription',array('id_activite' => $id_activite, 'nom' => $nom));

	return sql_insertq('spip_asso_comptes', array(
		'date' => $date_paiement,
		'journal' => $journal,
		'recette' => $montant,
		'justification' => $justification,
		'imputation' => lire_config('association/pc_activites'),
		'id_journal' => $id_activite));
}

function activites_insert($date, $id_evenement, $non_membres, $inscrits, $email, $telephone, $adresse, $montant, $commentaire)
{
	return sql_insertq('spip_asso_activites', array(
		'date' => $date,
		'id_evenement' => $id_evenement,
		'nom' => $nom,
		'id_adherent' => $id_membre,
		'membres' => $membres,
		'non_membres' => $non_membres,
		'inscrits' => $inscrits,
		'email' => $email,
		'telephone' => $telephone,
		'adresse' => $adresse,
		'montant' => $montant,
		'commentaire' => $commentaire));
}
?>
