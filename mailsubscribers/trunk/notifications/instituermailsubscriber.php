<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

// Fonction appelee par divers pipelines
// http://doc.spip.org/@notifications_instituerarticle_dist
function notifications_instituermailsubscriber_dist($quoi, $id_mailsubscriber, $options) {

	// ne devrait jamais se produire
	if ($options['statut'] == $options['statut_ancien']) {
		spip_log("statut inchange",'notifications');
		return;
	}
	// desactivable
	if (isset($GLOBALS['notification_instituermailsubscriber_status']) AND !$GLOBALS['notification_instituermailsubscriber_status'])
		return;

	include_spip('inc/texte');

	$modele = "";
	if ($options['statut'] == 'valide') {
		$modele = "notifications/mailsubscriber_subscribe";
	}
	elseif ($options['statut_ancien'] == 'valide') {
		$modele = "notifications/mailsubscriber_unsubscribe";
	}
	elseif($options['statut'] == 'prop')
	{
		$row = sql_fetsel('*','spip_mailsubscribers','id_mailsubscriber='.intval($id_mailsubscriber));
		if (isset($row['invite_email_from']) AND strlen($row['invite_email_from'])){
			$modele = "notifications/mailsubscriber_invite_confirm";
		}
  	else {
			$modele = "notifications/mailsubscriber_confirm";
	  }
	}
	if ($modele){
		$destinataires = sql_allfetsel("email","spip_mailsubscribers","id_mailsubscriber=".intval($id_mailsubscriber));
		$destinataires = array_map('reset',$destinataires);

		$destinataires = pipeline('notifications_destinataires',
			array(
				'args'=>array('quoi'=>$quoi,'id'=>$id_mailsubscriber,'options'=>$options)
			,
				'data'=>$destinataires)
		);

		// precaution : enlever les adresses en "@example.org"
		foreach($destinataires as $k=>$email){
			if (preg_match(",@example.org$,i",$email))
				unset($destinataires[$k]);
		}

		if (count($destinataires)){
			$envoyer_mail = charger_fonction('envoyer_mail','inc'); // pour nettoyer_titre_email
			$texte = recuperer_fond($modele,array('id_mailsubscriber'=>$id_mailsubscriber));
			notifications_envoyer_mails($destinataires, $texte);
		}
	}
	if ($modele=="notifications/mailsubscriber_invite_confirm"){
		// Une fois la demande mail envoyée on réitinialise. On pet la trace de qui a invité la personne à la newsletter
		// mais c'est un moindre mal si la personne ne valide pas l'invitation et s'inscrit un an plus tard toute seule
		sql_updateq('spip_mailsubscribers',	array('invite_email_from' => '','invite_email_text'=>''), "id_mailsubscriber=".intval($id_mailsubscriber) );
	}
}

?>
