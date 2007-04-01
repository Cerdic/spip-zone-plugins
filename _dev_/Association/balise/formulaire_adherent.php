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
function balise_FORMULAIRE_ADHERENT ($p) 
{return calculer_balise_dynamique($p, 'FORMULAIRE_ADHERENT', array());}

//function balise_FORMULAIRE_ADHERENT_stat($args, $filtres) {
	// Si le moteur n'est pas active, pas de balise
	//if ($GLOBALS['meta']["activer_moteur"] != "oui")
		//return '';

	// filtres[0] doit etre un script (a revoir)
	//else
	  //return array($filtres[0], $args[0]);
//}
 
// Balise de traitement des données du formulaire
function balise_FORMULAIRE_ADHERENT_dyn() {

	//On récupère les champs
	$nom=_request('nom');
	$prenom=_request('prenom');
	$mail=_request('mail');
	$rue=_request('adresse');
	$cp=_request('cp');
	$ville=_request('ville');
	$telephone=_request('telephone');
	$commentaire=_request('commentaire');
	$previsu=_request('previsu');
	$valide=_request('valide');
	
	if ($valide){	
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
		$sujet='Demande d\'adh&eacute;sion';
		
		//au webmaster
		$message = "Un nouveau membre vient de s'inscrire: ".$prenom." ".$nom."\nSon email :".$mail."\nIl sera membre:".$case_radio."\nSon adresse: ".$mail." ".$cp." ".$ville."\nSon num&eacute;ro de t&eacute;l&eacute;phone: ".$telephone."\nSon message: ".$text;
		//mail("$adresse","$sujet","$message", "$headers");
		envoyer_mail ( $adresse, $sujet, $message, $from = $expediteur, $headers = $entetes );
		
		//au demandeur
		$adresse= $mail;
		$message= "Bonjour ".$prenom."\n\n\nVous venez de demander votre inscription &agrave; l'association ".$nomasso."\nNous allons prendre contact avec vous tr&egrave;s rapidement.\n\nAvec nos remerciements. \n\n\nLe bureau de ".$nomasso."\r\n";
		envoyer_mail ( $adresse, $sujet, $message, $from = $expediteur, $headers = $entetes );
		//mail("$adresse1","$sujet","$message1","$headers");
		
		//enregistrement dans la table
		spip_query ( " INSERT INTO spip_asso_adherents (nom, prenom, email,  rue, cp, ville, telephone, statut, commentaire, creation) VALUES ('$nom', '$prenom',  '$mail',  '$rue', '$cp', '_$ville', '$telephone','prospect', '$commentaire', CURRENT_DATE() ) ");	
		
	}
	else {
		if ($previsu){
			
			//On contrôle les données du formulaire
			
			
			
			//on retourne les infos à un formulaire de previsualisation
			
			return inclure_balise_dynamique(
				array(
					'formulaires/formulaire_adherent_previsu',0,
					array(
						'nom'		=> $nom,
						'prenom'	=> $prenom,
						'mail'		=> $mail,
						'adresse'	=> $rue,
						'cp'		=> $cp,
						'ville'		=> $ville,
						'telephone'=> $telephone,
						'commentaire'=> $commentaire
					)
				),
				false
			);
		}
	}		
	
	//On retourne au formulaire d'adhesion
	return array (
		'formulaires/formulaire_adherent',0, 
		array (
			'nom'		=> $nom,
			'prenom'	=> $prenom,
			'mail'		=> $mail,
			'adresse'	=> $rue,
			'cp'		=> $cp,
			'ville'		=> $ville,
			'telephone'=> $telephone,
			'commentaire'=> $commentaire
			)
		);
	
}
?>