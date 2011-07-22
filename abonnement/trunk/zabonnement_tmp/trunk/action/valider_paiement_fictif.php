<?php

/**
 * Plugin abonnement pour Spip 2.0
 * Licence GPL (c) 2011
 */
/*
	[(#BOUTON_ACTION{<:paiement_fictif:>,
	[(#URL_ACTION_AUTEUR{valider_paiement_fictif,
	#ID_COMMANDE-encours-#ID_AUTEUR,
	#SELF})],ajax})] 
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_valider_paiement_fictif_dist($arg=null) {
	
	// ne rien faire si on est en prod !
	if (lire_config("abonnement/environnement") != "test") {
		spip_log('Petit malin ! (action/valider_paiement_fictif) ', 'abonnement');
		$arg=null;
		die("Page prot&eacute;g&eacute;e");
	}
	
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();

	//alm +
	list($id_commande, $statut, $id_auteur) = preg_split('/\W/', $arg);

	/*/
	/* Fonction a appeller dans le script de retour de la banque
	/* si ce script n'est pas dans le spip on peut utiliser les commandes suivantes pour demarrer spip
		# ou est l'espace prive de spip ?
		//chdir('..');
		//include('ecrire/inc_version.php');
	**/
	spip_log("valider_paiement_fictif auteur $id_auteur commande $id_commande $statut", 'abonnement');
	
	// on recupere les petites variables (envoi de la banque?)
	/*
	$id_auteur_banque = intval(_request('references'));
	$args = _request('args');
	$prix = intval(_request('prix'));
	$redirect = _request('redirect');
	*/
	$reponse_banque = 'ok';//_request('reponse_banque');
	
	if($reponse_banque == 'ok'){
	//TRAITEMENT POST PAIEMENT
	// on check les details de la commande, pour -eventuellement- en extraire les objets (article,produit,abonnement ...)
	// si oui alors on surcharge le traitement par defaut de la commande
		$commande_details = sql_allfetsel(
			'*',
			'spip_commandes_details',
			'id_commande = '.$id_commande
		);
	// Pour chaque detail>objet de la commande, on va traiter differemment > d'ou traitement todo pipeline
		if ($commande_details and is_array($commande_details)){
			foreach($commande_details as $detail){

				// si la banque est ok	
				if($reponse_banque=='ok'){
					$objet = $detail['objet'];
					$id_objet=$detail['id_objet'];	
					$statut_nouveau='paye';
					}		
				
				spip_log('action_valider_paiement_fictif pour'.$detail['id_objet'].' '.$detail['objet'].' paiement='.$statut_paiement,'abonnement');

			}
		}

	// maintenant on decide que la commande est paye (statut peut etre > attente,partiel,erreur) 
	//cf plugin commandes_paypal
	//ça bloque là
	
		include_spip('action/instituer_commande');
		$statut_commande='paye';
		action_instituer_commande($id_commande,$id_auteur,$statut_commande);
	
	//ici quelque soit le produit, l'abonnnement, l'article etc
	//on envoie le mail (de confirmation ou d'echec) de la commande? avec code d'acces au compte
	//au webmaster et au client
	//on en configure le contenu via config ? ou on salvatore?
	}

}

?>
