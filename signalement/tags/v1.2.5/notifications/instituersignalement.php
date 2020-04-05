<?php
/**
 * Plugin Signalement
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * © 2012 - Distribue sous licence GNU/GPL
 *
 * Notification lors du post d'un signalement
 *
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * cette notification s'execute quand un signalement est poste,
 *
 * @param string $quoi
 * @param int $id_signalement
 */
function notifications_instituersignalement_dist($quoi, $id_signalement, $options) {
	// ne devrait jamais se produire
	if ($options['statut'] == $options['statut_ancien']) {
		spip_log("statut inchange",'notifications');
		return;
	}
	
	/**
	 * A la republication, on ne relance pas la notif, c'était certainement une erreur
	 */
	if (($options['statut'] != 'publie') OR ($options['statut'] == 'publie' &&  !in_array($options['statut_ancien'],array('prop','refuse'))))
		return false;
	
	$t = sql_fetsel("*", "spip_signalements", "id_signalement=".intval($id_signalement));

	if (!$t)
		return;
	
	$t['auteur'] = sql_getfetsel('nom','spip_auteurs','id_auteur='.intval($t['id_auteur']));
	
	include_spip('inc/texte');
	include_spip('inc/filtres');

	// Qui va-t-on prevenir ?
	$destinataires = array();

	// 1. Les auteurs de l'objet lie au signalement
	// seulement s'ils ont le droit de le moderer
	$result = sql_select("auteurs.*","spip_auteurs AS auteurs, spip_auteurs_liens AS lien","lien.objet=".sql_quote($t['objet'])." AND lien.id_objet=".intval($t['id_objet'])." AND auteurs.id_auteur=lien.id_auteur");

	while ($qui = sql_fetch($result)) {
		if ($qui['email'])
			$destinataires[] = $qui['email'];
	}

	$destinataires = pipeline('notifications_destinataires',
		array(
			'args'=>array('quoi'=>$quoi,'id'=>$id_signalement,'options'=>$options)
		,
			'data'=>$destinataires)
	);

	$options['signalement'] = $t;
	
	// Nettoyer le tableau
	// Ne pas ecrire au posteur du message !
	notifications_nettoyer_emails($destinataires,array($t['email_auteur']));

	// preparer le calcul des liens de moderation
	$moderations = array();
	foreach(array('publie','refuse') as $statut){
		if ($statut!==$t['statut']){
			$moderations["url_moderer_$statut"] = "$id_signalement-$statut-".$t['statut'];
		}
	}
	include_spip("inc/securiser_action");
	$action = 'instituer_signalement_paremail';
	$pass = secret_du_site();

	//
	// Envoyer les emails
	//
	$email_notification_signalement = charger_fonction('email_notification_signalement','inc');
	foreach ($destinataires as $email) {
		// ajouter les liens de moderation par statut
		$contexte = array('notification_email'=>$email);
		foreach($moderations as $k=>$arg){
			$arg = "$arg-$email";
			$hash = _action_auteur("$action-$arg", '', $pass, 'alea_ephemere');
			$contexte[$k] = generer_url_action($action, "arg=$arg&hash=$hash", true, true);
		}

		$texte = $email_notification_signalement($t, $email, $contexte);
		notifications_envoyer_mails_texte_ou_html($email, $texte);
	}

}
?>