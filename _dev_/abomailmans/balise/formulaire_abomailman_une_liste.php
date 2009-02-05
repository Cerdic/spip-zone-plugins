<?php
/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007
 * $Id$
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
		 if (false === strpos($liste, '@@')) {
			foreach($listes as $liste_join) {
				if (abomailman_mail ($prenom . " " . $nom, $email, $liste_join, $liste_join)) {
					 $liste_confirme  .= " <b>". $liste ."</b><br>";
				}		
			 }
		}
		else {
			spip_log("on s'abonne à sympa","abomailmans");
			    $temp = explode ("@@", $liste);
			    $email_liste_sympa = $temp[1];
			    $sympa_join = $temp[0];
			    $temp2 = explode ("@", $email_liste_sympa);
			    $proprio_liste = $temp2[0] . '-request@' . $temp2[1];
			    $objet = (empty($abonnement)) ? 'UNSUBSCRIBE' : 'SUBSCRIBE';
	                $sujet = $objet . " " . $email_liste_sympa . ' ';
      	          $sujet .= (empty($desabonnement)) ? $nom : '';
                if (abomailman_mail ($nom, $email, $sympa_join, $sympa_join, $sujet)) {
                    $liste_confirme  .= " <b>". $email_liste_sympa ."</b><br>";
                }

                // DEBUG : envoie une copie du vrai mail
//                $contenu_mail = 'from_nom : ' . $nom . "\n";
//                $contenu_mail .= 'from_email : ' . $email . "\n";
//                $contenu_mail .= 'to_nom : ' . $sympa_join . "\n";
//                $contenu_mail .= 'to_email : ' . $sympa_join . "\n";
//                $contenu_mail .= 'subject : ' . $sujet . "\n";
//                $email_debug = 't.bothorel@free.fr';
//                abomailman_mail ('abomailmanstest', $email_debug, '', $email_debug ,'mailman pour SYMPA plugin debug', $contenu_mail);		    		
                // Fin debug
		}
		$liste_confirme .= "<br >\n" . _T("abomailmans:message_confirm_suite") .
		  "<br />";
		$rslt = array(
			"id_abomailman" => "NULL",
			"message"		=> $liste_confirme
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