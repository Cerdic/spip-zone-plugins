<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;
function formulaires_importer_contacts_charger_importateurcontacts_dist($fournisseur){
	if(_request('fournisseur') == 'email_simple')
		return array('email_simple' => '');
	if(_request('fournisseur') == 'email_liste')
		return array('email_liste' => '');
	return array();
}

function formulaires_importer_contacts_verifier_importateurcontacts_dist($fournisseur){
	$erreurs = array();
	$contacts = array();
	$email_simple = _request('email_simple');
	$email_liste = _request('email_liste');
	
	if (!$email_simple && !$email_liste){
		$erreurs['email_liste'] = _T('info_obligatoire');
		$erreurs['email_simple'] = _T('info_obligatoire');
	}
	else if($email_simple){
		$email = importateurcontacts_parse_email($email_simple);
		if (!$email) {
			$erreurs['email_simple'] = _T('info_email_invalide');
		}
		else{
			$contacts[] = $email;
		}
	}
	else if($email_liste){
		$lines = explode("\n", trim($email_liste));
		$emails = array();
		foreach($lines as $email){
			if(strlen($email) > 0){
				$email_valide = importateurcontacts_parse_email($email);
				if(!$email_valide){
					$erreurs['email_liste'] = _T('info_email_invalide').' "'.$email.'"';
					break;
				}else{
					$contacts[] = $email_valide;
				}
			}
		}
	}
	if(count($erreurs) == 0){
		set_request('contacts',$contacts);
		spip_log($contacts,'test.'._LOG_ERREUR);
	}
	
	return $erreurs;
}

/**
 * Parser l'email
 * Gérer deux cas possibles :
 * -* email@domaine.tld Nom de la personne
 * -* Nom de la personne <email@domaine.tld>
 */
function importateurcontacts_parse_email($email){
	if(!is_string($email))
		return false;
	
	$email_explode = preg_split("/[\s]+/",$email,2);
	spip_log($email_explode,'test.'._LOG_ERREUR);
	
	if(email_valide($email_explode[0])){
		return array(
				'email'=> $email_explode[0],
				'nom' => (isset($email_explode[1]) && strlen(trim($email_explode[1])) > 0) ? $email_explode[1] : $email_explode[0]
			);
	}else{
		$email_name = preg_match('/^(?:"?([^@"]+)"?\s)?<?([^>]+@[^>]+)>?$/',$email,$matches);
		if(isset($matches[1]) && isset($matches[2]))
		return array(
				'email'=> $matches[2],
				'nom' => $matches[1]
			);
	}
	return false;
}
?>