<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/mailsubscribers');
include_spip('mailsubscribers_fonctions');

/**
 * Decrit les informations d'un inscrit
 * Pour retirer une liste il faut desinscrire
 *
 * @param $email
 *   champ obligatoire
 * @return bool|array
 *   false si n'existe pas
 *   array :
 *     string email
 *     string nom
 *     array listes
 *     string lang
 *     string status : on|pending|off
 *     string url_unsubscribe : url de desabonnement
 */
function newsletter_subscriber_dist($email){

	// chercher si un tel email est deja en base
	$infos = sql_fetsel('email,nom,listes,lang,statut,jeton','spip_mailsubscribers','email='.sql_quote($email));
	if ($infos){
		if ($infos['statut']=='valide')
			$infos['status']='on';
		elseif (in_array($infos['statut'],array('prepa','prop'))){
			$infos['status']='pending';
		}
		else {
			$infos['status']='off';
		}
		unset($infos['statut']);

		$infos = mailsubscribers_informe_subscriber($infos);

		return $infos;
	}

	return false;
}
