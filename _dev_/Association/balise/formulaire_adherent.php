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
	$rue=_request('adresse');
	$cp=_request('cp');
	$ville=_request('ville');
	$telephone=_request('telephone');
	$categorie=_request('categorie');
	$commentaire=_request('commentaire');
	$bouton=_request('bouton');
	
	//echo "le bouton -> $bouton " ;
	
	if ($bouton=='Confirmer'){	
		//on envoit des emails
		
		$query = spip_query( " SELECT * FROM spip_asso_profil WHERE id_profil=1" );
		while ($data = spip_fetch_array($query)) {
			$nomasso=$data['nom'];
			$adresse=$data['mail'];
			$expediteur=$nomasso.'<'.$adresse.'>';
		}		
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
		$message = "Un nouveau membre vient de demander son adh&eacute;sion :\n\nNOM : ".$nom."\nPr&eacute;nom : ".$prenom."\nEmail :<a href='mailto:'".$mail."'>".$mail."</a>\nAdresse: ".$rue." ".$cp." ".$ville
		."\nT&eacute;l&eacute;phone: ".$telephone
		."\n\nCat&eacute;gorie ".$categorie
		."\n\nCommentaire: ".$commentaire;
		envoyer_mail ( $adresse, $sujet, $message, $from = $expediteur, $headers = $entetes );
		
		//au demandeur
		$adresse= $mail;
		$message= "Bonjour ".$prenom."\n\n\nVous venez de demander votre inscription &agrave; l'association ".$nomasso."\nNous allons prendre contact avec vous tr&egrave;s rapidement.\n\nAvec nos remerciements. \n\n\nLe bureau de ".$nomasso."\r\n";
		envoyer_mail ( $adresse, $sujet, $message, $from = $expediteur, $headers = $entetes );
		
		//echo " INSERT INTO spip_asso_adherents (nom, prenom, email,  rue, cp, ville, telephone, statut, remarques, creation) 
		//VALUES ('$nom', '$prenom',  '$mail',  '$rue', '$cp', '$ville', '$telephone','prospect', '$commentaire', NOW() ) " ;
		
		
		if(!function_exists(_q)){
		function _q($a) {
			return (is_int($a)) ? strval($a) : ("'" . addslashes($a) . "'");
			}
		}
		
		//enregistrement dans la table
		spip_query ( " INSERT INTO spip_asso_adherents (nom, prenom, email,  rue, cp, ville, telephone, categorie, statut, remarques, creation) 
		VALUES ("._q($nom).", "._q($prenom).",  "._q($mail).",  "._q($rue).", "._q($cp).", "._q($ville).", "._q($telephone).", "._q($categorie).", "._q(prospect).", "._q($commentaire).", NOW() ) ");	
		
		$id_adherent = spip_insert_id();
		//echo "id -> $id ";
		$valeur = spip_fetch_array(spip_query("SELECT cotisation, libelle FROM spip_asso_categories WHERE valeur='$categorie'") );
		//var_dump($valeur);
		
		/*
		//dire merci
		return array (
		'formulaires/formulaire_adherent_merci',0, 
		array (
			'nom'		=> $nom,
			'prenom'	=> $prenom,
			'mail'		=> $mail,
			'adresse'	=> $rue,
			'cp'		=> $cp,
			'ville'		=> $ville,
			'telephone'=> $telephone,
			'commentaire'=> $commentaire,
			'categorie'=> $valeur['libelle'],
			'valeur'=> $valeur['cotisation']  // montant
			)
		);
		
		*/
		
		// insertion du formulaire de paiement
		return inclure_balise_dynamique(
					array(
						'formulaires/formulaire_adherent_paiement',0,
						array(
							'nom'		=> $nom,
							'prenom'	=> $prenom,
							'sexe'		=> $sexe ,
							'mail'		=> $mail,
							'adresse'	=> $rue,
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
				$erreur_rue='Rue manquante !';
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
			//on retourne les infos � un formulaire de previsualisation		
			return inclure_balise_dynamique(
				array(
					'formulaires/formulaire_adherent_previsu',0,
					array(
						'nom'		=> $nom,
						'prenom'	=> $prenom,
						'sexe'		=> $sexe ,
						'mail'		=> $mail,
						'adresse'	=> $rue,
						'cp'		=> $cp,
						'ville'		=> $ville,
						'telephone'=> $telephone,
						'categorie'=> $categorie,
						'commentaire'=> $commentaire,
						'bouton'	=> "Valider",
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
			}else{
			
			$valeur = spip_fetch_array(spip_query("SELECT cotisation, libelle, valeur FROM spip_asso_categories WHERE valeur='$categorie'") );
			
			return inclure_balise_dynamique(
					array(
						'formulaires/formulaire_adherent_confirmation',0,
						array(
							'nom'		=> $nom,
							'prenom'	=> $prenom,
							'sexe'		=> $sexe ,
							'mail'		=> $mail,
							'adresse'	=> $rue,
							'cp'		=> $cp,
							'ville'		=> $ville,
							'telephone'=> $telephone,
							'categorie'=> $valeur['valeur'],
							'valeur'=> $valeur['cotisation'], // montant
							'libelle'=> $valeur['libelle'],
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
			'adresse'	=> $rue,
			'cp'		=> $cp,
			'ville'		=> $ville,
			'telephone'=> $telephone,
			'commentaire'=> $commentaire
			)
		);
	
}
?>