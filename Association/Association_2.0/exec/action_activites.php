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
		$id_activite=intval($_REQUEST['id_activite']);
		$id_evenement=intval($_REQUEST['id_evenement']);
		$id_membre=intval($_REQUEST['id_membre']);
		$date=$_REQUEST['date'];
		$nom=$_REQUEST['nom'];
		$membres=$_REQUEST['membres'];
		$non_membres=$_REQUEST['non_membres'];
		$inscrits=$_REQUEST['inscrits'];
		$email=$_REQUEST['email'];
		$telephone=$_REQUEST['telephone'];
		$adresse=$_REQUEST['adresse'];
		$montant=$_REQUEST['montant'];
		$date_paiement=$_REQUEST['date_paiement'];
		$journal=$_REQUEST['journal'];
		$statut=$_REQUEST['statut'];
		$commentaire=$_REQUEST['commentaire'];
		
		$action=$_REQUEST['agir'];
		$url_retour = $_REQUEST['url_retour'] ? $_REQUEST['url_retour'] : $_SERVER['HTTP_REFERER'];

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

		} elseif ($action=="paie") {
			$n = activites_paiement_insert($date_paiement, $journal, $montant, $id_activite, $nom, $commentaire, $statut, $inscrits, $nom_membres, $membres, $id_membre);
			spip_log("insertion paiement activite numero: $n");
			header ('location:'.$url_retour);

		} elseif (isset($_REQUEST['delete'])) {

			$commencer_page = charger_fonction('commencer_page', 'inc');
			echo $commencer_page(_T('asso:gestion_pour_association')) ;
			association_onglets();
			
			echo debut_gauche('', true);
			echo debut_boite_info(true);
			echo association_date_du_jour();	
			echo fin_boite_info(true);
			echo association_retour();
			echo debut_droite('', true);
			echo debut_cadre_relief("", true, "", _T('asso:activite_titre_inscriptions_activites'));

			if (is_array($_REQUEST["delete"])) {
				$count = count($_REQUEST["delete"]);
				echo '<p><strong>'._T('asso:activite_message_confirmation_supprimer',array('nombre' => $count, 'pluriel' => $count > 1 ? 's' : '')).'</strong></<p>';
				$res = '';
				for ( $i=0 ; $i < $count ; $i++ ) {
					$id = $_REQUEST["delete"][$i];
					$res .= "<input type='hidden' name='drop[]' value='$id' checked='checked' />\n";
				}
				$res .= "<div style='float:right;'><input type='submit' value='" . _T('asso:activite_bouton_confirmer') . "' class='fondo' /></a>\n";
				// count est du bruit de fond pour la secu
				echo generer_action_auteur('supprimer_activites', $count, $url_retour, $res, " method='post'");
			}
			fin_cadre_relief();
			echo fin_gauche(), fin_page();
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
