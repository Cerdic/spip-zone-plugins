<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// Envoi le contenu par email
function notifications_modes_email_envoyer_dist($contact, $contenu){
	// S'il y a le plugin Facteur, on peut faire un truc plus propre
	if (defined('_DIR_PLUGIN_FACTEUR')){
		$corps = array(
			'texte' => $contenu['texte'],
		);
		// Si on a une version HTML
		if ($contenu['html'])
			$corps['html'] = $contenu['html'];
	}
	// Sinon c'est juste le texte
	else{
		$corps = $contenu['texte'];
	}
	
	$envoyer_mail = charger_fonction('envoyer_mail', 'inc/');
	return $envoyer_mail($contact, $contenu['court'], $corps);
}

// Renvoie une adresse e-mail ou rien
function notifications_modes_email_contact_dist($destinataire){
	include_spip('inc/filtres');
	
	// Si c'est déjà un mail
	if (email_valide($destinataire))
		return $destinataire;
	// Si c'est un id_auteur
	elseif (
		intval($destinataire) == $destinataire
		and $destinataire > 0
		and $email = sql_getfetsel('email', 'spip_auteurs', 'id_auteur = '.$destinataire)
	){
		return $email;
	}
	// Sinon rien
	else
		return null;
}

?>
