<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('base/abstract_sql');
include_spip('inc/mail');
include_spip('inc/charsets');
include_spip('inc/lang');
include_spip('inc/headers');
include_spip('public/assembler');
include_spip('balise/formulaire_adherent');

// Balise independante du contexte ici
function balise_FORMULAIRE_INSCRIPTION_ACTIVITE ($p) {
	return calculer_balise_dynamique($p, 'FORMULAIRE_INSCRIPTION_ACTIVITE', array());
}

//function balise_FORMULAIRE_INSCRIPTION_ACTIVITE_stat($args, $filtres) {
	// Si le moteur n'est pas active, pas de balise
	//if ($GLOBALS['meta']["activer_moteur"] != "oui")
		//return '';

	// filtres[0] doit etre un script (a revoir)
	//else
	  //return array($filtres[0], $args[0]);
//}
 
// Balise de traitement des données du formulaire
function balise_FORMULAIRE_INSCRIPTION_ACTIVITE_dyn() {

	//On récupère les champs
	$id_evenement=_request('id_evenement');
	$nom=_request('nom');
	$id_adherent=_request('id_adherent');
	$membres=_request('membres');
	$non_membres=_request('non_membres');
	$inscrits=_request('inscrits');
	$email=_request('email');
	$telephone=_request('telephone');
	$adresse=_request('adresse');
	$montant=_request('montant');
	$commentaire=_request('commentaire');
	$bouton=_request('bouton');
	
	if ($bouton=='Confirmer'){	
		//on envoit des emails
		
		$query = spip_query( " SELECT * FROM spip_asso_profil " );
		while ($data = spip_fetch_array($query)) {
			$nomasso=$data['nom'];
			$adresse=$data['mail'];
			$expediteur=$nomasso.'<'.$adresse.'>';
		}		
		$entete .= "Reply-To: ".$data['mail']."\n";     					 // réponse automatique à Association
		$entete .= "MIME-Version: 1.0\n";
		$entete .= "Content-Type: text/plain; charset=$charset\n";	// Type Mime pour un message au format HTML
		$entete .= "Content-Transfer-Encoding: 8bit\n";
		$entete .= "X-Mailer: PHP/" . phpversion();         			// mailer
		//$entetes.= "Content-Type: text/html; charset=iso-8859-1\n"; 
		//$entetes.= "X-Sender: < ".$data['mail'].">\n";   } 
		//$entetes .= "X-Priority: 1\n";                							// Message urgent ! 
		//$entetes .= "X-MSMail-Priority: High\n";         					// définition de la priorité
		//$entetes .= "Return-Path: < webmaster@ >\n"; 					// En cas d' erreurs 
		//$entetes .= "Errors-To: < webmaster@ >\n";    					// En cas d' erreurs 
		//$entetes .= "cc:  \n"; 											// envoi en copie à …
		//$entetes .= "bcc: \n";          										// envoi en copie cachée à …
		$sujet=_T('asso:activite_message_sujet',array('nomasso'=>$nomasso));
		
		$query = spip_query( " SELECT * FROM spip_evenements WHERE id_evenement=$id_evenement " );
		while ($data = spip_fetch_array($query)) {
			$activite=$data['titre'];
			$date=$data['date_debut'];
			$lieu=$data['lieu'];
		}
		
		//au webmaster
		$message = _T('asso:activite_message_webmaster',array(
			'nom' => $nom, 
			'activite' => $activite, 
			'inscrits' => $inscrits, 
			'commentaire'=>$commentaire
		));
		envoyer_mail ( $adresse, $sujet, $message, $from = $expediteur, $headers = $entetes );
		
		//au demandeur
		$adresse= $email;
		$message= _T('asso:activite_message_confirmation_inscription',array(
			'activite'=>$activite,
			'date'=>association_datefr($date),
			'lieu'=>$lieu,
			'nom'=>$nom,
			'id_adherent'=>$id_adherent,
			'membres'=>$membres,
			'non_membres'=>$non_membres,
			'inscrits'=>$inscrits,
			'email'=>$email,
			'telephone'=>$telephone,
			'adresse'=>$adresse,
			'montant'=>$montant,
			'commentaire'=>$commentaire,
			'nomasso'=>$nomasso
		));
		envoyer_mail ( $adresse, $sujet, $message, $from = $expediteur, $headers = $entetes );
		
		//enregistrement dans la table
		spip_query ( " INSERT INTO spip_asso_activites (id_evenement, nom, id_adherent, membres, non_membres, inscrits, email, date, telephone, montant, commentaire) VALUES ('$id_evenement','$nom', '$id_adherent', '$membres', '$non_membres', '$inscrits', '$email', NOW(), '$telephone', '$montant', '$commentaire') ");	
		
	}
	else {
		if ($bouton=='Soumettre'){
			
			//On contrôle les données du formulaire			
			$bouton='Confirmer';	 // si pas d'erreur
			
			//email invalide
			if ( $email != email_valide($email) || empty($email) ){
				$erreur_email='Adresse courriel invalide !';
				$bouton='Soumettre';
			}
			//donnees manquantes
			if ( empty($nom) ){
				$erreur_nom='Nom et prénom manquants !';
				$bouton='Soumettre';
			}
			if ( empty($inscrits) ){
				$erreur_inscrits='Nombre d\'inscrits manquant !';
				$bouton='Soumettre';
			}
			if ( empty($montant) ){
				$erreur_montant='Montant de la participation manquant !';
				$bouton='Soumettre';
			}
			
			//on retourne les infos à un formulaire de previsualisation		
			return inclure_balise_dynamique(
				array(
					'formulaires/formulaire_inscription_activite_previsu',0,
					array(
						'id_evenement'=> $id_evenement,
						'nom'		=> $nom,
						'id_adherent'=> $id_adherent,
						'membres'=> $membres,
						'non_membres'=> $non_membres,
						'inscrits'	=> $inscrits,
						'email'		=> $email,
						'telephone'	=> $telephone,
						'adresse'=> $adresse,
						'montant' => $montant,
						'commentaire'=> $commentaire,
						'bouton'=> $bouton,
						'erreur_email' => $erreur_email,
						'erreur_nom' => $erreur_nom,
						'erreur_inscrits' => $erreur_inscrits,
						'erreur_montant' => $erreur_montant,
					)
				),
				false
			);
		}
	}		
	
	//On retourne au formulaire d'inscription
	return array (
		'formulaires/formulaire_inscription_activite',0, 
		array (
			'id_evenement'=> $id_evenement,
			'nom'		=> $nom,
			'id_adherent'=> $id_adherent,
			'membres'=> $membres,
			'non_membres'=> $non_membres,
			'inscrits'	=> $inscrits,
			'email'		=> $email,
			'telephone'	=> $telephone,
			'adresse'=> $adresse,
			'montant' => $montant,
			'commentaire'=> $commentaire
			)
		);
	
}
?>