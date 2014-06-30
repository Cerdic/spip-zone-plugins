<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip("action/editer_objet");
include_spip('inc/mailsubscribers');
include_spip('inc/config');
include_spip('inc/filtres');
include_spip('inc/autoriser');

/**
 * Inscrit un subscriber par son email
 * si le subscriber existe deja, on met a jour les informations (nom, listes, lang)
 * l'ajout d'une inscription a une liste est cumulatif : si on appelle plusieurs fois la fonction avec le meme email
 * et plusieurs listes differentes, l'inscrit sera sur chaque liste
 * Pour retirer une liste il faut desinscrire
 *
 * Quand aucune liste n'est indiquee :
 *   si l'email n'est inscrit a rien, on l'inscrit a la liste generale 'newsletter'
 *   si l'email est deja inscrit, on ne change pas ses inscriptions, mais on modifie ses informations (nom, lang)
 *
 * @param $email
 *   champ obligatoire
 * @param array $options
 *   nom : string
 *   listes : array (si non fourni, inscrit a la liste generale 'newsletter')
 *   lang : string
 *   force : bool permet de forcer une inscription sans doubleoptin (passe direct en valide)
 *   graceful : bool permet a contrario de ne pas inscrire quelqu'un qui s'est desabonne (utilise lors de l'import en nombre, l'utilisateur est ignore dans ce cas)
 *   invite_email_from : text . utilisé par le formulaire #NEWSLETTER_INVITE, permet de renseigner la personne qui invite à s'inscrire à la newsletter
 *   invite_email_text : text . utilisé par le formulaire #NEWSLETTER_INVITE, permet de renseigner le message personnalisé d'invitation
 * @return bool
 *   true si inscrit comme demande, false sinon
 */
function newsletter_subscribe_dist($email,$options = array()){

	if (!$email = trim($email)) return false;
	// on abonne pas un email invalide ou obfusque !
	if (!email_valide($email) OR mailsubscribers_test_email_obfusque($email)){
		spip_log("email invalide pour abonnement : $email","mailsubscribers."._LOG_INFO_IMPORTANTE);
		return false;
	}

	$set = array();
	foreach (array('lang', 'nom','invite_email_from','invite_email_text') as $k){
		if (isset($options[$k]))
			$set[$k] = $options[$k];
	}
	if (isset($options['listes'])
	  AND is_array($options['listes'])){
		$set['listes'] = array_map('mailsubscribers_normaliser_nom_liste',$options['listes']);
		$set['listes'] = implode(',',$set['listes']);
	}

	// chercher si un tel email est deja en base
	$row = sql_fetsel('*','spip_mailsubscribers','email='.sql_quote($email)." OR email=".sql_quote(mailsubscribers_obfusquer_email($email)));

	// Si c'est une creation d'inscrit
	if (!$row){
		if (isset($options['invite_email_from']) AND strlen($options['invite_email_from'])){
	    spip_log("Invitation ". $options['invite_email_from'] . " invite $email a s'inscrire " ,"mailsubscribers."._LOG_INFO_IMPORTANTE);
		}
  	else {
			spip_log("Inscription liste $email " ,"mailsubscribers."._LOG_INFO_IMPORTANTE);
	  }
		// on utilise pas objet_inserer car email unique et on ne veut pas passer par etape insertion email='' qui peut echouer
		// en cas de doublon
		$set['email'] = $email;
		if (!isset($set['lang']))
			$set['lang'] = $GLOBALS['meta']['langue_site'];
		if (!isset($set['listes']))
			$set['listes'] = mailsubscribers_normaliser_nom_liste();
		// statut et date par defaut
		$set['statut'] = 'prepa';
		$set['date'] = date('Y-m-d H:i:s');

		if ($id = objet_inserer("mailsubscriber",0,$set)){
			$row = sql_fetsel('*','spip_mailsubscribers','id_mailsubscriber='.intval($id));
			if ($row['email']!==$set['email']){
				// securite car $set pas forcement pris en charge dans objet_inserer
				autoriser_exception("modifier","mailsubscriber",$row['id_mailsubscriber']);
				autoriser_exception("instituer","mailsubscriber",$row['id_mailsubscriber']);
				objet_modifier("mailsubscriber",$row['id_mailsubscriber'],$set);
				autoriser_exception("modifier","mailsubscriber",$row['id_mailsubscriber'],false);
				autoriser_exception("instituer","mailsubscriber",$row['id_mailsubscriber'],false);
				$row = sql_fetsel('*','spip_mailsubscribers','id_mailsubscriber='.intval($id));
			}
			$set = array();
		}

		else {
			spip_log("Impossible de creer un mailsubscriber : ".var_export($set,true),"mailsubscribers."._LOG_ERREUR);
			return false;
		}
	}
	else {
		$set['email'] = $email; // si email obfusque
		// si on est graceful et que l'inscrit s'est deja desabonne, on ne fait rien
		if ($row['statut']=='refuse'
		  AND isset($options['graceful'])
		  AND $options['graceful']==true)
			return false;

		$row['listes'] = explode(',',$row['listes']);
		// si on etait en refuse, il faut considerer qu'on est abonne a rien
		if ($row['statut']=='refuse'){
			$row['listes'] = array();
		}
		if (!isset($set['listes'])){
			// filtrer les listes de newsletter pour voir si l'abonne est abonne a quelque chose
			$listes = array_map('mailsubscribers_filtre_liste',$row['listes']);
			$listes = array_filter($listes);
			// sinon l'abonner a la liste par defaut
			if (!count($listes))
				$set['listes'] = mailsubscribers_normaliser_nom_liste();
		}
		// si c'est un inscrit existant faire les mises a jour des listes si besoins
		if (isset($set['listes'])){
			$set['listes'] = array_merge($row['listes'],explode(',',$set['listes']));
			$set['listes'] = array_map('trim',$set['listes']);
			$set['listes'] = array_unique($set['listes']);
			$set['listes'] = array_filter($set['listes']);
			$set['listes'] = implode(",",$set['listes']);
			if (!$set['listes'])
				$set['listes'] = mailsubscribers_normaliser_nom_liste();
		}

		// si deja en prop, on le repasse sauvagement en prepa pour forcer un re-envoi de mail de confirmation
		if ($row['statut']=='prop'){
			sql_updateq("spip_mailsubscribers",array('statut'=>'prepa'),'id_mailsubscriber='.intval($row['id_mailsubscriber']));
		}
	}

	// si pas deja valide
	if ($row['statut']!=='valide'){
		// changer le statut en prop (doubleoptin) ou valide (simpleoptin)
		if (
			(isset($options['force']) AND $options['force'])
			OR !lire_config('mailsubscribers/double_optin',0)){

			$set['statut'] = 'valide';
		}
		else {
			$set['statut'] = 'prop';
		}
	}
	if (count($set)){
		autoriser_exception("modifier","mailsubscriber",$row['id_mailsubscriber']);
		autoriser_exception("instituer","mailsubscriber",$row['id_mailsubscriber']);
		objet_modifier("mailsubscriber",$row['id_mailsubscriber'],$set);
		autoriser_exception("modifier","mailsubscriber",$row['id_mailsubscriber'],false);
		autoriser_exception("instituer","mailsubscriber",$row['id_mailsubscriber'],false);
	}

	return true;
}
