<?php
/**
 * Plugin mailsuscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('mailsuscribers_fonctions');

/**
 * Decrit les informations d'un inscrit
 * Pour retirer une liste il faut desinscrire
 *
 * @param $email
 *   champ obligatoire
 * @return bool|array
 *   false si n'existe pas
 *   array :
 *     email : string
 *     nom : string
 *     listes : array
 *     lang : string
 *     status : on|pending|off
 *     url_unsuscribe : url de desabonnement
 */
function newsletter_suscriber_dist($email){

	// chercher si un tel email est deja en base
	$infos = sql_fetsel('email,nom,listes,lang,statut,jeton','spip_mailsuscribers','email='.sql_quote($email));
	if ($infos){
		$infos['listes'] = explode(",",$infos['listes']);
		if ($infos['statut']=='valide')
			$infos['status']=='on';
		elseif (in_array($infos['statut'],array('prepa','prop'))){
			$infos['status']=='pending';
		}
		else {
			$infos['status']=='off';
		}
		unset($infos['statut']);

		$infos['url_unsuscribe'] = mailsuscriber_url_unsuscribe($infos['email'],$infos['jeton']);
		unset($infos['jeton']);

		return $infos;
	}

	return false;
}