<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_email_charger_dist($id_email='new', $objet='', $id_objet='', $retour=''){
	$valeurs = formulaires_editer_objet_charger('email', $id_email, '', '', $retour, '');
	$valeurs['objet'] = $objet;
	$valeurs['id_objet'] = $id_objet;
	$valeurs['type'] = sql_getfetsel('type', 'spip_emails_liens', 'objet='.sql_quote($objet).' AND id_objet='.intval($id_objet).' AND id_email='.intval($id_email) );
	return $valeurs;
}

function formulaires_editer_email_verifier_dist($id_email='new', $objet='', $id_objet='', $retour=''){
	$erreurs = formulaires_editer_objet_verifier('email', $id_email, array('email') );
	if ( !count($erreurs) AND $format = strtolower(_request('format')) ) { // les champs obligatoires sont renseignes (donc pas d'erreur) et on a specifie un format de mel...
		$adresse = trim(_request('email'));
		switch ($format) {
			case 'smtp': // SMTP:compte@domaine.tld (RFC5321+5322+6531)
			case 'internet': // (c'est l'equivalent utilise par la RFC )
			case 'inet': // (c'est l'abreviation pour Internet, utilise par Lotus par exemple)
				include_spip('inc/filtres'); // filtre "email_valide"
				$invalidite = !email_valide($adresse);
				break;
			case 'x400': // X400:C=CC;A= ;P=mygot;O=Exchange;S=John;G=Prenom; (RFC1685+1664+1801)
				$invalidite = !( preg_match('/A=([^;]+);/', $adresse) OR (preg_match('/P=([^;]+);/', $adresse) AND strpos($adresse, 'A= ')) ); // Il faut le "ADministration Management Domain Name" (clef A) qui vaut un espace quand on n'utilise que le "PRivate Management Domain Name"
				$dest1 = preg_match('/O=([^;]+);/', $adresse) AND (strpos($adresse, 'OU4=')?(strpos($adresse, 'OU3=')?(strpos($adresse, 'OU2=')?(strpos($adresse, 'OU1=')?TRUE:FALSE):TRUE):TRUE):TRUE); // indiquer le "Organization" et optionnellement "Organizational Unit 1/2/3/4" (mais dans l'ordre : pas de 3 sans 2...)
				$dest2 = preg_match('/CN=([^;]+);/', $adresse) OR (preg_match('/G=([^;]+);/', $adresse) AND preg_match('/S=([^;]+);/', $adresse)); // Une fois dans la place, il faut bien un recipiendaire dont on donne le "Common Name" (Prenom Famille) ou le "Surname" (nom de amille) et le le "Given name" (prenom usuel) qui le composent (les "Initials" et le "generation Qualifier" sont bien facultatifs, tandis que bien que non exige, le "Given name" est souvent discriminant)
				$invalidite &= !( preg_match('/CN=([^;][A-Z]{2});/', $adresse) AND ($dest1 OR $dest2) ); // Outre la passerelle de messagerie, il faut indiquer le "Country code ISO" et le destinataire.
				break; // Quand present, on peut profiter directement des parametres "DDA:rfc-822=name(a)org;DDA:acp-plad=org" ou '(a)' est pour '@' dans l'adresse SMTP selon la RFC 822 et ses successeurs
			case 'ldap': // DN:cn=Prenom Famille,uid=Identifiant,dc=Domaine,dc=tld
				$invalidite = !( preg_match('/dc=([^,][a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]),dc=([a-z]{2,})[,$]/', $adresse) OR preg_match('/c=([^,][A-Z]{2})[,$]/', $adresse) );  // Il faut au moins deux elements (nom et extension) de "Domain Controler" ou "Country code ISO" (mais dans ce cas ne faudrait-il pas obligatoirement les "STate"/"Locality" et/ou "Organization" en plus ?) http://en.wikipedia.org/wiki/LDAP#Usage
				$invalidite &= !( preg_match('/cn=([^,]+)[,$]/', $adresse) OR preg_match('/uid=([^,]+)[,$]/', $adresse) ); // Il faut le "Canonical Name" (Prenom Initial-eventelle Famille) qui est relatif ou le "User IDentifier" qui est absolu et unique
				break; // On pourrait peut-etre simplement envoyer le "Distinguished Name of the entry" a un serveur et voir s'il y a une reponse (i.e. si l'entree existe...) http://en.wikipedia.org/wiki/LDAP#LDAP_URLs
			case 'x500': // X500:/o=Multinationale/ou=filiale France/cn=Recipients of  /cn=compte_utilisateur (exemple de ce que produit Outlook/Exchange)
			case 'ex': // (autre nom en reference a "microsoft EXchange" ou c'est tres utilise http://social.technet.microsoft.com/forums/en-US/exchangesvrdeploylegacy/thread/9911853d-67c9-48ec-af8a-7fd72832d778/ )
			case 'ad': // (autre nom en reference a "microsoft Active Directory" qui est bien un annuaire X.500 http://www.experts-exchange.com/Software/Server_Software/Email_Servers/Exchange/A_9650-NDRs-and-the-legacyExchangeDN.html )
				$invalidite = !preg_match('#/o=([[:alnum:]]+)[/$]#', $adresse) ; // Il faut un structure racine
				$invalidite &= !( preg_match('#/cn=([[:alnum:]]+)[/$]#', $adresse) OR preg_match('#/ou=([[:alnum:]]+)[/$]#', $adresse) ); // Il faut un "Canonical Name" ou un "Organization Unit"
				break; // On pourrait peut-etre simplement envoyer la chaine un serveur et voir s'il y a une reponse (i.e. si l'entree existe...)
			case 'ms': // MS:domainin/domain/user
				$invalidite = !stripos($adresse, '/'); // Un dernier "slash"
				$invalidite = !substr_count($adresse, '/', 2); // Au moins un "slash" et un "domain" de plus de 2 caracteres. en fait faudrait une belle regex pour s'assurer qu'on ne depasse pas la longueur maximale et qu'il n'y a pasde caractere prohibe http://support.microsoft.com/kb/909264
				break; // Il s'agit "domain" NT de windows server : S'il correspond au FQDN alors l'adresse de courriel pourrait etre user@domainin.domain tout simplement ! http://msdn.microsoft.com/en-us/library/windows/desktop/ms675915%28v=vs.85%29.aspx http://support.microsoft.com/kb/244670
			case 'ccmail': // CCMAIL:Name, user at domain
				$invalidite = !strpos($adresse, ' at '); //
				break; // Pourquoi ne pas indiquer le "Full Qualified Domain Name" et remplacer ' at ' par '@' tout simplement ?
			case '': // a partir d'ici mettre vos cas rajoutes si vous surchargez la verif
				$invalidite = FALSE; // cette variable recoit le resultat d'echec de votre validation
				break;
			default: // Pour le reste on fait rien...
				$invalidite = TRUE; // mais on rale au format incompris...
				break; //
		}
		if ($invalidite)
			$erreurs['email'] = _T('spip:info_email_invalide'); // informer que "Adresse email invalide."
	}
	return $erreurs;
}

function formulaires_editer_email_traiter_dist($id_email='new', $objet='', $id_objet='', $retour=''){
	// si redirection demandee, on refuse le traitement en ajax
	if ($retour) refuser_traiter_formulaire_ajax();
	return formulaires_editer_objet_traiter('email', $id_email, '', '', $retour, '');
}

?>