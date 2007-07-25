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
 
// Balise de traitement des donn�es du formulaire
function balise_FORMULAIRE_ADHERENT_dyn() {

	//On r�cup�re les champs
	$nom=_request('nom');
	$prenom=_request('prenom');
	$mail=_request('mail');
	$sexe=_request('sexe');
	$rue=_request('rue');
	$cp=_request('cp');
	$ville=_request('ville');
	$telephone=_request('telephone');
	$portable=_request('portable');
	$categorie=_request('categorie');
	$commentaire=_request('commentaire');
	$publication=_request('publication');
	$naissance=_request('naissance');
	$profession=_request('profession');
	$societe=_request('societe');
	$utilisateur1=_request('utilisateur1');
	$utilisateur2=_request('utilisateur2');
	$utilisateur3=_request('utilisateur3');
	$utilisateur4=_request('utilisateur4');
	$bouton=_request('bouton');
	
	//echo "le bouton -> $bouton " ;
	
	if ($bouton=='Confirmer'){	
		//on envoit des emails si tout est contr�l� et confirm�
		
		$nomasso=lire_config('association/nom');
		$adresse=lire_config('association/email');
		$expediteur=$nomasso.'<'.$adresse.'>';		
		$entete .= "Reply-To: ".$data['mail']."\n";     					 // r�ponse automatique � Association
		$entete .= "MIME-Version: 1.0\n";
		$entete .= "Content-Type: text/plain; charset=$charset\n";	// Type Mime pour un message au format HTML
		$entete .= "Content-Transfer-Encoding: 8bit\n";
		$entete .= "X-Mailer: PHP/" . phpversion();         			// mailer
		//$entetes.= "Content-Type: text/html; charset=iso-8859-1\n"; 
		//$entetes.= "X-Sender: < ".$data['mail'].">\n";   } 
		//$entetes .= "X-Priority: 1\n";                							// Message urgent ! 
		//$entetes .= "X-MSMail-Priority: High\n";         					// d�finition de la priorit�
		//$entetes .= "Return-Path: < webmaster@ >\n"; 					// En cas d' erreurs 
		//$entetes .= "Errors-To: < webmaster@ >\n";    					// En cas d' erreurs 
		//$entetes .= "cc:  \n"; 											// envoi en copie � �
		//$entetes .= "bcc: \n";          										// envoi en copie cach�e � �
		$sujet='Demande d\'adh&eacute;sion';
		
		//au webmaster
		$message = "Un nouveau membre vient de demander son adh&eacute;sion :\n\nNOM : ".$nom."\nPr&eacute;nom : ".$prenom."\nEmail :".$mail."\nAdresse: ".$rue." ".$cp." ".$ville
		."\nT&eacute;l&eacute;phone: ".$telephone
		."\n\nCat&eacute;gorie ".$categorie
		."\n\nCommentaire: ".$commentaire;
		envoyer_mail ( $adresse, $sujet, $message, $from = $expediteur, $headers = $entetes );
		
		//au demandeur
		$adresse= $mail;
		$message= "Bonjour ".$prenom."\n\n\nVous venez de demander votre inscription &agrave; l'association ".$nomasso."\nNous allons prendre contact avec vous tr&egrave;s rapidement.\n\nAvec nos remerciements. \n\n\nLe bureau de ".$nomasso."\r\n";
		envoyer_mail ( $adresse, $sujet, $message, $from = $expediteur, $headers = $entetes );
		
		//on enregistre les donn�es dans la table
		spip_query ( " INSERT INTO spip_asso_adherents (nom, prenom, email,  rue, cp, ville, telephone, portable, categorie, statut, remarques, publication, naissance, profession, societe, utilisateur1, utilisateur2, utilisateur3, utilisateur4,creation) 
		VALUES ("._q($nom).", "._q($prenom).",  "._q($mail).",  "._q($rue).", "._q($cp).", "._q($ville).", "._q($telephone).", "._q($portable).", "._q($categorie).", "._q(prospect).", "._q($commentaire).", "._q($publication).", "._q($naissance).", "._q($profession).", "._q($societe).", "._q($utilisateur1).", "._q($utilisateur2).", "._q($utilisateur3).", "._q($utilisateur4).", NOW() ) ");	
		
		/*
		//dire merci		
		
		$id_adherent = spip_insert_id();
		//echo "id -> $id ";
		$valeur = spip_fetch_array(spip_query("SELECT cotisation, libelle FROM spip_asso_categories WHERE valeur='$categorie'") );
		//var_dump($valeur);
		
		return array (
		'formulaires/formulaire_adherent_merci',0, 
		array (
			'nom'		=> $nom,
			'prenom'	=> $prenom,
			'mail'		=> $mail,
			'rue'	=> $rue,
			'cp'		=> $cp,
			'ville'		=> $ville,
			'telephone'=> $telephone,
			'commentaire'=> $commentaire,
			'categorie'=> $valeur['libelle'],
			'valeur'=> $valeur['cotisation']  // montant
			)
		);
		
		// insertion du formulaire de paiement
		return inclure_balise_dynamique(
					array(
						'formulaires/formulaire_adherent_paiement',0,
						array(
							'nom'		=> $nom,
							'prenom'	=> $prenom,
							'sexe'		=> $sexe ,
							'mail'		=> $mail,
							'rue'		=> $rue,
							'cp'		=> $cp,
							'ville'		=> $ville,
							'telephone'=> $telephone,
							'categorie'=> $valeur['libelle'],
							'valeur'=> $valeur['cotisation'], // montant
							'id_adherent'=> $id_adherent, // reference
							'texte_bouton'=> 'Payer par carte de credit'
						)
					),
					false
				);
		
		// fin paiement
		*/	
	}
	else {
		if ($bouton=='Valider' OR $bouton=='Retour'){
			
			//On contr�le les donn�es du formulaire			
			$erreur = "non" ;	 // si pas d'erreur
			
			//email invalide
			if (!email_valide($mail) || empty($mail) ){
				$erreur_email='Adresse courriel invalide !';
				$bouton='Soumettre';
			}
			//donnees manquantes
			if ( empty($nom) ){
				$erreur_nom='Nom manquant !';
				$erreur = "oui" ;
			}
			if ( empty($prenom) ){
				$erreur_prenom='Prenom manquant !';
				$erreur = "oui" ;
			}
			if ( empty($rue) ){
				$erreur_rue='Adresse manquante !';
				$erreur = "oui" ;
			}
			if ( empty($cp)  ){
				$erreur_cp='Code postal manquant !';
				$erreur = "oui" ;
			}
			if ( empty($ville) ){
				$erreur_ville='Ville manquante !';
				$erreur = "oui" ;
			}	
			
			if ($erreur == "oui" OR $bouton=='Retour'){
				//echo "le bouton -> $bouton car $erreur_email $erreur_nom $erreur_prenom $erreur_rue $erreur_cp $erreur_ville" ;
				//echo $categorie ;
				
				//on retourne les infos � un formulaire de correction		
				return inclure_balise_dynamique(
					array(
						'formulaires/formulaire_adherent_previsu',0,
						array(
							'nom'			=> $nom,
							'prenom'		=> $prenom,
							'sexe'			=> $sexe ,
							'mail'			=> $mail,
							'rue'			=> $rue,
							'cp'			=> $cp,
							'ville'			=> $ville,
							'telephone'	=> $telephone,
							'portable' 		=> $portable,
							'categorie'		=> $categorie,
							'commentaire'=> $commentaire,
							'naissance' 	=> $naissance,
							'profession' 	=> $profession,
							'societe' 		=> $societe,
							'utilisateur1' 	=> $utilisateur1,
							'utilisateur2' 	=> $utilisateur2,
							'utilisateur3' 	=> $utilisateur3,
							'utilisateur4' 	=> $utilisateur4,
							'secteur'		=> $secteur,
							'publication'	=> $publication,
							'bouton'		=> "Valider",
							'erreur_email' => $erreur_email,
							'erreur_nom' => $erreur_nom,
							'erreur_prenom' => $erreur_prenom,
							'erreur_rue' => $erreur_rue,
							'erreur_cp' => $erreur_cp,
							'erreur_ville' => $erreur_ville,
						)
					),
					false
				);
			}
			else {
				
				$valeur = spip_fetch_array(spip_query("SELECT cotisation, libelle, valeur FROM spip_asso_categories WHERE valeur='$categorie'") );
				// si non on demande confirmation des donn�es
				
				return inclure_balise_dynamique(
					array(
						'formulaires/formulaire_adherent_confirmation',0,
						array(
							'nom'		=> $nom,
							'prenom'	=> $prenom,
							'sexe'		=> $sexe ,
							'mail'		=> $mail,
							'rue'		=> $rue,
							'cp'		=> $cp,
							'ville'		=> $ville,
							'telephone'=> $telephone,
							'categorie'	=> $valeur['valeur'],
							'valeur'		=> $valeur['cotisation'], // montant
							'libelle'		=> $valeur['libelle'],
							'commentaire'=> $commentaire,
							'bouton'	=> "Confirmer",
						)
					),
					false
				);	
			}			
		}
	}		
	
	//formulaire d'adhesion
	return array (
		'formulaires/formulaire_adherent',0, 
		array (
			'nom'		=> $nom,
			'prenom'	=> $prenom,
			'mail'		=> $mail,
			'rue'		=> $rue,
			'cp'		=> $cp,
			'ville'		=> $ville,
			'telephone'=> $telephone,
			'portable'	=> $portable,
			'commentaire'=> $commentaire
			'naissance' 	=> $naissance,
			'profession' 	=> $profession,
			'societe' 		=> $societe,
			'utilisateur1' 	=> $utilisateur1,
			'utilisateur2' 	=> $utilisateur2,
			'utilisateur3' 	=> $utilisateur3,
			'utilisateur4' 	=> $utilisateur4,
			'secteur'		=> $secteur,
			'publication'	=> $publication,
			)
		);
	
}
?>