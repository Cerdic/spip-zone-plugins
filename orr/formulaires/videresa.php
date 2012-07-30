<?php

function formulaires_videresa_charger_dist($idresa,$date_debut){
	$valeurs = array(
		//~ "idresa" => $idresa,
	);
	return $valeurs;
}

function formulaires_videresa_verifier_dist($idresa,$date_debut){
	$erreurs = array();
	return $erreurs;
}

function formulaires_videresa_traiter_dist($idresa,$date_debut){
	$idresa = sql_quote($idresa);
	$retour=array();
	list($date,$heure) = explode(' ',$date_debut);
	$efface= sql_delete("spip_orr_reservations", "id_orr_reservation = $idresa");
	if (!$efface){
		$retour['message_erreur'] = "L'effacement de la réservation n° $idresa à échoué ";
	}
	$retour['message_ok'] = "Bravo, vous avez supprimé définitivement la réservation n° $idresa !";
	$retour['redirect'] = "spip.php?page=affichage_orr&jourj=$date";
	return $retour;
}

?>
