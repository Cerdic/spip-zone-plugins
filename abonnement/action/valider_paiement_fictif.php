<?php

function action_valider_paiement_fictif_dist() {
	
	// ne rien faire si on est en prod !
	if (lire_config("abonnement/environnement") != "test") {
		spip_log('Petit malin ! (action/valider_paiement_fictif)' . $GLOBALS['ip'], 'abonnement');
		die("Page prot&eacute;g&eacute;e");
	}


	/*/
	/* Fonction a appeller dans le script de retour de la banque
	/* si ce script n'est pas dans le spip on peut utiliser les commandes suivantes pour demmarer spip
		# ou est l'espace prive de spip ?
		//chdir('..');
		//include('ecrire/inc_version.php');
	**/
	spip_log("Reception de paiement", 'abonnement');
	
	// on recupere les petites variables :
	$id_auteur = intval(_request('references'));
	$args = _request('args');
	$montant = intval(_request('montant'));
	$redirect = _request('redirect');
	$reponse_banque = _request('reponse_banque');

	list($type, $hash) = explode('-',$args);

	if ($reponse_banque == 'ok') {
		spip_log("Paiement OK", 'abonnement');
		$message = "paiement_ok";
		if ($type == 'article') {
			include_spip('action/activer_article');
			if (!abo_traiter_activer_article_hash($hash)) {
				spip_log("Erreur de traitement (article)", 'abonnement');
				$message = "erreur_site";
			}
		}
		elseif ($type == 'abonnement') {
			include_spip('action/activer_abonnement');
			if (!abo_traiter_activer_abonnement_hash($hash)) {
				spip_log("Erreur de traitement (abonnement)", 'abonnement');
				$message = "erreur_site";
			}
		}
	}
	else {
		spip_log("Erreur banque", 'abonnement');
		$message = "erreur_banque";
		// passer l'abo en "erreur_bank"
	}


	/*/
	/* Fin de ce qu'il faut recopier */

	include_spip('inc/headers');
	// on redirige sur la page de fin de transaction 
	$redirect = generer_url_public("validation_banque","","",true,"");
	$redirect = parametre_url($redirect,'message',$message,'&');
	redirige_par_entete($redirect);
	//redirige_par_entete(urldecode($redirect));
}

?>
