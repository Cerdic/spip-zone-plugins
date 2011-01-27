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

function action_modifier_activites() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_activite=$securiser_action();

	$nom = _request('nom');
	$membres = _request('membres');
	$non_membres = _request('non_membres');
	$inscrits = _request('inscrits');
	$montant = _request('montant');
	$date_paiement = _request('date_paiement');
	$statut = _request('statut');
	$commentaire = _request('commentaire');
	$email = _request('email');
	$telephone = _request('telephone');
	$adresse = _request('adresse');
	$date = _request('date');
	$id_evenement = _request('id_evenement');
	$id_adherent = _request('id_membre');

	include_spip('base/association');
	sql_updateq('spip_asso_activites',array(
			"nom" => $nom,
			"id_adherent" => $id_adherent,
			"membres" => $membres,
			"non_membres" => $non_membres,
			"inscrits" => $inscrits,
			"montant" => $montant,
			"date_paiement" => $date_paiement,
			"statut" => $statut,
			"commentaire" => $commentaire,
			"email" => $email,
			"telephone" => $telephone,
			"adresse" => $adresse,
			"date" => $date,
			"id_evenement" => $id_evenement),
		    "id_activite=$id_activite");
}
?>
