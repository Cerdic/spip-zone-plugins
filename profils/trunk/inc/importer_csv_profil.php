<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function inc_importer_csv_profil_dist($id_profil, $profil, $envoyer_notification) {
	// À partir de chaque colonne, on va générer les request nécessaires pour lancer le traitement du formulaire
	$champs_auteur = array();
	$champs_organisation = array();
	$champs_contact = array();
	$champs_coordonnees = array();
	
	foreach ($profil as $champ => $valeur) {
		// Si c'est bien un objet attendu
		if (preg_match('%^(auteur|organisation|contact)_%', $champ, $trouver)) {
			$objet = $trouver[1];
			$champ = str_replace($objet.'_', '', $champ);
			
			// Si c'est une coordonnée pour cet objet
			if (
				preg_match('%^(adresses|numeros|emails)_%', $champ, $trouver)
				and $coordonnee = $trouver[1]
				and $champ = str_replace($coordonnee.'_', '', $champ)
				and preg_match('%^([a-zA-Z0-9]+?)_%', $champ, $trouver)
				and is_string($type = $trouver[1])
				and $champ = str_replace($type.'_', '', $champ)
			) {
				if ($type == '0') {
					$type = 0;
				}
				
				$champs_coordonnees[$objet][$coordonnee][$type][$champ] = $valeur;
			}
			// Sinon on met directement la valeur pour cet objet
			else {
				${"champs_$objet"}[$champ] = $valeur;
			}
		}
	}
	
	// On place tout ça en request
	set_request('auteur', $champs_auteur);
	set_request('organisation', $champs_organisation);
	set_request('contact', $champs_contact);
	set_request('coordonnees', $champs_coordonnees);
	set_request('envoyer_notification', $envoyer_notification);
	
	$traiter = charger_fonction('traiter', 'formulaires/profil');
	$retours = $traiter('new', $id_profil, '', true); // On force en mode admin pour être sûr, car dans des jobs
}
