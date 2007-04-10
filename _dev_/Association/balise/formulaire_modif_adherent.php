<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('base/abstract_sql');
include_spip('inc/mail');
include_spip('inc/charsets');
include_spip('inc/lang');
include_spip('inc/headers');
include_spip('public/assembler');
include_spip('balise/formulaire_modif_adherent');

// Balise independante du contexte ici
function balise_FORMULAIRE_MODIF_ADHERENT ($p) 
{return calculer_balise_dynamique($p, 'FORMULAIRE_MODIF_ADHERENT', array());}

//function balise_FORMULAIRE_MODIF_ADHERENT_stat($args, $filtres) {
	// Si le moteur n'est pas active, pas de balise
	//if ($GLOBALS['meta']["activer_moteur"] != "oui")
		//return '';

	// filtres[0] doit etre un script (a revoir)
	//else
	  //return array($filtres[0], $args[0]);
//}
 
// Balise de traitement des données du formulaire
function balise_FORMULAIRE_MODIF_ADHERENT_dyn() {

	//On récupère les champs
	$nom=_request('nom');
	$prenom=_request('prenom');
	$mail=_request('mail');
	$rue=_request('adresse');
	$cp=_request('cp');
	$ville=_request('ville');
	$telephone=_request('telephone');
	$bouton=_request('bouton');
	
	if ($bouton=='Confirmer'){	
				
		//enregistrement dans la table
		spip_query ( " UPDATE spip_asso_adherents SET nom='$nom', prenom='$prenom', email='$mail',  rue='$rue', cp='$cp', ville='$ville', telephone='$telephone' ");	
		
	}
	else {
		if ($bouton=='Soumettre'){
			
			//On contrôle les données du formulaire			
			$bouton='Confirmer';	 // si pas d'erreur
			
			//email invalide
			if ( $mail != email_valide($mail) || empty($mail) ){
				$erreur_email='Adresse courriel invalide !';
				$bouton='Soumettre';
			}
			//donnees manquantes
			if ( empty($nom) ){
				$erreur_nom='Nom manquant !';
				$bouton='Soumettre';
			}
			if ( empty($prenom) ){
				$erreur_prenom='Prenom manquant !';
				$bouton='Soumettre';
			}
			if ( empty($rue) ){
				$erreur_rue='Rue manquante !';
				$bouton='Soumettre';
			}
			if ( empty($cp)  ){
				$erreur_cp='Code postal manquant !';
				$bouton='Soumettre';
			}
			if ( empty($ville) ){
				$erreur_ville='Ville manquante !';
				$bouton='Soumettre';
			}	
			
			//on retourne les infos à un formulaire de previsualisation		
			return inclure_balise_dynamique(
				array(
					'formulaires/formulaire_modif_adherent_previsu',0,
					array(
						'nom'		=> $nom,
						'prenom'	=> $prenom,
						'mail'		=> $mail,
						'adresse'	=> $rue,
						'cp'		=> $cp,
						'ville'		=> $ville,
						'telephone'=> $telephone,
						'bouton'	=> $bouton,
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
	}		
	
	//On retourne au formulaire adhérent
	return array (
		'formulaires/formulaire_modif_adherent',0, 
		array (
			'nom'		=> $nom,
			'prenom'	=> $prenom,
			'mail'		=> $mail,
			'adresse'	=> $rue,
			'cp'		=> $cp,
			'ville'		=> $ville,
			'telephone'=> $telephone,
			)
		);
	
}
?>