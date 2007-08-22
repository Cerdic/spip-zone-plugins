<?php
/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007
*/
if (!defined("_ECRIRE_INC_VERSION")) return;	#securite


function balise_FORMULAIRE_ABOMAILMAN_UNE_LISTE($p) {
	 return calculer_balise_dynamique($p,'FORMULAIRE_ABOMAILMAN_UNE_LISTE', array('id_abomailman'));}

function balise_FORMULAIRE_ABOMAILMAN_UNE_LISTE_stat($args, $filtres) {
	return (array($args[1])); }

function balise_FORMULAIRE_ABOMAILMAN_UNE_LISTE_dyn($id_abomailman) {
	include_spip ("inc/abomailmans");

	$nom = _request('nom');
	$prenom = _request('prenom');
	$email = _request('email');
	$listes = _request('listes', true);
	$abonnement = _request('abonnement');

	if ($abonnement && $email) {
		foreach($listes as $liste_join) {
			if (abomailman_mail ($prenom . " " . $nom, $email, $liste_join, $liste_join)) {
				$liste_confirme  .= _T("abomailmans:message_confirmation") ." <b>". $liste_join ."</b><br>";
			}		
		 }

		$rslt = array(
			"id_abomailman" => "NULL",
			"liste_confirme"		=> $liste_confirme
		);
	}
	else {
		if ($abonnement && !$email) $message =_T("abomailmans:email_oublie");
			$rslt = array(
				"id_abomailman" => $id_abomailman,
				"message"		=> $message
			);
	}
	return array('formulaires/formulaire_abomailman_une_liste',0, $rslt);
}



?>