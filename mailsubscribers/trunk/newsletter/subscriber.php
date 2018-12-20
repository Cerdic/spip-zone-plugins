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
 * @param string $email
 *   champ obligatoire
 * @param array $options
 *   array :
 *     array listes : liste(s) pour l'unsubscribe
 * @return bool|array
 *   false si n'existe pas
 *   array :
 *     string email
 *     string nom
 *     deprecated array listes
 *     string lang
 *     string status : on|pending|off
 *     array subscriptions
 *     string url_unsubscribe : url de desabonnement
 */
function newsletter_subscriber_dist($email, $options = array()) {

	// chercher si un tel email est deja en base
	$infos = sql_fetsel("email,nom,'' as listes,lang,'' as status,jeton,id_mailsubscriber", 'spip_mailsubscribers',
		'email=' . sql_quote($email) . " OR email=" . sql_quote(mailsubscribers_obfusquer_email($email)));
	if ($infos) {
		$id_mailsubscriber = $infos['id_mailsubscriber'];
		$infos = mailsubscribers_informe_subscriber($infos);
		$infos['url_admin'] = generer_url_entite($id_mailsubscriber, 'mailsubscriber');
		if ($infos['email'] !== $email) {
			$infos['url_admin'] = parametre_url($infos['url_admin'], 'email', $email);
		}

		// si on est dans le contexte d'une liste unique connue, modifier l'url_unsubscribe
		if (isset($options['listes'])
			and is_array($options['listes'])
			and count($options['listes'])==1
		  and $id_et_segment = reset($options['listes'])
			and $id_et_segment = explode('+', $id_et_segment)
			and $id = reset($id_et_segment)
			and $id = mailsubscribers_normaliser_nom_liste($id)
		  and isset($infos['subscriptions'][$id]['url_unsubscribe'])){
			$infos['url_unsubscribe'] = $infos['subscriptions'][$id]['url_unsubscribe'];
		}
		return $infos;
	}

	return false;
}
