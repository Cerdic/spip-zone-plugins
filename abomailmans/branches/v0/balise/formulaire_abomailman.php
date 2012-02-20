<?php
/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007
 * $Id$
*/

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite


function balise_FORMULAIRE_ABOMAILMAN($p) {
	 return calculer_balise_dynamique($p,'FORMULAIRE_ABOMAILMAN', array('id_abomailman'));}

function balise_FORMULAIRE_ABOMAILMAN_stat($args, $filtres) {
	return (array($args[1])); }

function balise_FORMULAIRE_ABOMAILMAN_dyn($id_abomailman) {
	include_spip ("inc/abomailmans");

	$nom = _request('nom');
	$email = _request('email');
	$listes = _request('listes', true);
	$abonnement = _request('abonnement');
	$desabonnement = _request('desabonnement');

	
	if (($abonnement or $desabonnement) && $email && $listes) {
	    if ($abonnement) {
	       $liste_confirme  = _T("abomailmans:message_confirmation_a")."<br>";
	    } else {
	       $liste_confirme  = _T("abomailmans:message_confirmation_d")."<br>";
	    }
		foreach($listes as $liste_join) {

		// Pour l'instant la partie gauche des @@@ ($listes[0]) ne sert pas
            // mais est necessaire si on veut afficher le nom de la liste dans
            // le message de confirmation au lieu de son email.
            $liste = explode('@@@', $liste_join);

            // 1er cas : c'est une liste MAILMAN
            if (false === strpos($liste[1], '@@')) {
                $liste[1] = (empty($abonnement)) ? str_replace('-join', '-leave', $liste[1]) : $liste[1];
			    if (abomailman_mail ($nom, $email, $liste[1], $liste[1])) {
				    $liste_confirme  .= " <b>". $liste[1] ."</b><br>";
			    }

			    // DEBUG : envoie une copie du vrai mail
//                $contenu_mail = 'from_nom : ' . $nom . "\n";
//                $contenu_mail .= 'from_email : ' . $email . "\n";
//                $contenu_mail .= 'to_nom : ' . $liste[1] . "\n";
//                $contenu_mail .= 'to_email : ' . $liste[1] . "\n";
//                $contenu_mail .= 'subject : ' . "\n";
//                $email_debug = 't.bothorel@free.fr';
//                abomailman_mail ('abomailmanstest', $email_debug, '', $email_debug ,'mailman plugin debug', $contenu_mail); 
                // FIN DEBUG
            }

            // 2eme cas : c'est une liste SYMPA (présence de deux @ à suivre)
			else {
			    $temp = explode ("@@", $liste[1]);
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
		 }
		$liste_confirme .= "<br >\n" . _T("abomailmans:message_confirm_suite") .
		  "<br />";
		$rslt = array(
			"id_abomailman" => "NULL",
			"message"		=> $liste_confirme
		);
	}
	elseif (($abonnement or $desabonnement) && !$email) {
		$message =_T("abomailmans:email_oublie");
		$rslt = array(
			"id_abomailman" => $id_abomailman,
			"message"		=> $message
		);
	}
	elseif (($abonnement or $desabonnement) && !$listes) {
	    $message =_T("abomailmans:liste_oublie");
        $rslt = array(
            "id_abomailman" => $id_abomailman,
            "message"       => $message
        );
	}
	return array('formulaires/formulaire_abomailman',0, $rslt);
}



?>