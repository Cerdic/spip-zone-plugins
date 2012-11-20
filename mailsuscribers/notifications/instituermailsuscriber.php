<?php
/**
 * Plugin mailsuscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

// Fonction appelee par divers pipelines
// http://doc.spip.org/@notifications_instituerarticle_dist
function notifications_instituermailsuscriber_dist($quoi, $id_mailsuscriber, $options) {

	// ne devrait jamais se produire
	if ($options['statut'] == $options['statut_ancien']) {
		spip_log("statut inchange",'notifications');
		return;
	}

	include_spip('inc/texte');

	$modele = "";
	if ($options['statut'] == 'valide') {
		$modele = "notifications/mailsuscriber_suscribe";
	}
	elseif ($options['statut_ancien'] == 'valide') {
		$modele = "notifications/mailsuscriber_unsuscribe";
	}
	elseif($options['statut'] == 'prop'){
		$modele = "notifications/mailsuscriber_confirm";
	}

	if ($modele){
		$destinataires = sql_allfetsel("email","spip_mailsuscribers","id_mailsuscriber=".intval($id_mailsuscriber));
		$destinataires = array_map('reset',$destinataires);

		$destinataires = pipeline('notifications_destinataires',
			array(
				'args'=>array('quoi'=>$quoi,'id'=>$id_mailsuscriber,'options'=>$options)
			,
				'data'=>$destinataires)
		);

		$envoyer_mail = charger_fonction('envoyer_mail','inc'); // pour nettoyer_titre_email
		$texte = recuperer_fond($modele,array('id_mailsuscriber'=>$id_mailsuscriber));

		notifications_envoyer_mails($destinataires, $texte);
	}
}

?>
