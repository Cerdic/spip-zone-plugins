<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_importer_contacts_charger_openinviter_dist($fournisseur){
	return array(
		'email' => '',
		'password' => ''
	);
}

function formulaires_importer_contacts_verifier_openinviter_dist($fournisseur){
	$erreurs = array();
	
	$email = _request('email');
	$password = _request('password');
	
	// L'email est obligatoire
	if (!$email){
		$erreurs['email'] = _T('info_obligatoire');
	}
	elseif ($fournisseur['type'] == 'webmail' and include_spip('inc/filtres') and !email_valide($email)){
		$erreurs['email'] = _T('info_email_invalide');
	}
	
	// Le mot de passe est obligatoire
	if (!$password){
		$erreurs['password'] = _T('info_obligatoire');
	}
	
	// S'il n'y a pas d'erreurs on peut essayer de s'authentifier
	if (!$erreurs){
		$inviter = openinviter_authentification($fournisseur['nom_plugin'], $email, $password, $erreurs);
	}
	
	// S'il n'y a toujours pas d'erreurs, récupère les contacts
	if (!$erreurs){
		$contacts_openinviter = $inviter->getMyContacts();
		
		// S'il y a un problème
		if ($contacts_openinviter === false or !is_array($contacts_openinviter)){
			$erreurs['message_erreur'] = _T('openinviter:erreur_generale');
		}
		else{
			// On éteint le plugin
			$inviter->stopPlugin();
			
			// On nettoie les résultats
			$contacts = array();
			foreach ($contacts_openinviter as $email=>$nom){
				$email = trim($email);
				if ($email) $contacts[] = array(
					'nom' => $nom,
					'email' => $email
				);
			}
			
			// Et on met les contacts dans l'environnement
			set_request('contacts', $contacts);
		}
	}
	
	return $erreurs;
}

function openinviter_authentification($nom_plugin, $email, $password, &$erreurs){
	include_spip('OpenInviter/openinviter');
	$inviter = new OpenInviter();
	
	if (!$inviter){
		$erreurs['message_erreur'] = _T('openinviter:erreur_generale');
	}
	
	// On démarre le plugin choisi
	$inviter->startPlugin($nom_plugin);
	
	// S'il y a une erreur de connexion au plugin
	$erreur_eventuelle = $inviter->getInternalError();
	if (!empty($erreur_eventuelle)){
		if (include_spip('inc/autoriser') and autoriser('configurer')) $erreurs['message_erreur'] = $erreur_eventuelle;
		else $erreurs['message_erreur'] = _T('openinviter:erreur_generale');
	}
	// Sinon s'il y a une erreur d'authentification au plugin avec les infos fournies
	elseif (!$inviter->login($email, $password)){
		$erreur_eventuelle = $inviter->getInternalError();
		$erreurs['email'] = $erreur_eventuelle ? $erreur_eventuelle : _T('openinviter:erreur_authentification');
	}
	
	return $inviter;
}

?>
