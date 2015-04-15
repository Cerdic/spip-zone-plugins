<?php

/**
var_dump($flux);
die ("plouf $message_ok");
 * Proposer un traitement par defaut pour les #FORMULAIRE_CONFIGURER_XXX
 *
 * @param array $flux
 * @return array
 */


function mailchimp_formulaire_traiter($flux)
{

	// on recupere d'abord le nom du formulaire .
	// car c'est un pipeline donc tout formulaire passe dedans ( prive ou public)
	$formulaire = $flux['args']['form'];
	#spip_log(__LINE__);

	//dans notre cas c'est le formulaire mesabonnes (du plugin mes_abonnes) qui nous interesse
	if ($formulaire=="mesabonnes"){
		// necessaire pour utiliser les autorisations
		include_spip('inc/autoriser');
		include_spip('inc/mailchimp');

		//on verifie que les parametres du plugin mailchimp sont initialisées

		// 2 cas possibles : inscription ou desinscription
		$id_abonne = $flux['data']['id_abonne'];

		$statut = sql_getfetsel('statut', 'spip_mesabonnes', 'id_abonne='.intval($id_abonne));
		$email = sql_getfetsel('email', 'spip_mesabonnes', 'id_abonne='.intval($id_abonne));

		if ($statut=='publie'){
			$flux['data'] = mailchimp_subscribe($email,$flux['data']);
		} // $statut=='subscribe'

		else if ($statut=='poubelle'){
			$flux['data'] = mailchimp_unsubscribe($email,$flux['data']);
		}
		else
		{
			spip_log(__LINE__);
			// ne doit pas arriver normallement
		}

	}

	return $flux ;
}
