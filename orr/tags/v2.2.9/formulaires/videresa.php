<?php

function formulaires_videresa_charger_dist($idresa, $id_ressource, $jourj='', $vue=''){
	$valeurs = array();
	return $valeurs;
}

function formulaires_videresa_verifier_dist($idresa, $id_ressource, $jourj, $vue){
    include_spip('inc/autoriser');
	$erreurs = array();
	if ($idresa != intval($idresa) OR $id_ressource != intval($id_ressource))
		$erreurs['message_erreur'] = _T("orr:supprimer_parametre_incorrect").' 1';
	// puisque l'autorisation teste sur la ressource, vérifier que l'id_ressource est bien lié avec l'id_resa...
	if (!sql_countsel('spip_orr_reservations_liens', array("objet = 'orr_ressource'", "id_orr_reservation = $idresa", "id_objet = $id_ressource")))
		$erreurs['message_erreur'] = _T("orr:supprimer_parametre_incorrect").' 2';
	if (!autoriser('supprimer', 'orr_reservation', $id_ressource))
		$erreurs['message_erreur'] = _T("orr:suppression_autorisation_interdite");
	return $erreurs;
}

function formulaires_videresa_traiter_dist($idresa, $id_ressource, $jourj, $vue){
	$retour=array();
//	list($date,$heure) = explode(' ',$date_debut);
	$efface= sql_delete("spip_orr_reservations", "id_orr_reservation = $idresa");
	if (!$efface){
		$retour['message_erreur'] = "L'effacement de la réservation n° $idresa à échoué ";
	}
	$retour['message_ok'] = "La réservation n° $idresa est supprimée";
	$retour['redirect'] = "spip.php?page=orr&jourj=$jourj&vue=$vue";
	return $retour;
}

?>
