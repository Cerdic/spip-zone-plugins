<?php
/*
 * Plugin Notifications
 * (c) 2009-2012 SPIP
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// Fonction appelee par divers pipelines
// https://code.spip.net/@notifications_instituerarticle_dist
function notifications_instituerarticle_dist($quoi, $id_article, $options) {

	// ne devrait jamais se produire
	if ($options['statut'] == $options['statut_ancien']) {
		spip_log("statut inchange",'notifications');
		return;
	}

	include_spip('inc/texte');

	$modele = "";
	if ($options['statut'] == 'publie') {
		if ($GLOBALS['meta']["post_dates"]=='non'
			AND strtotime($options['date'])>time())
			$modele = "notifications/article_valide";
		else
			$modele = "notifications/article_publie";
	}

	if ($options['statut'] == 'prop' AND $options['statut_ancien'] != 'publie')
		$modele = "notifications/article_propose";

	if ($options['statut'] == 'refuse' AND in_array($options['statut_ancien'],array('prop','publie'))) {
		$modele = "notifications/article_refuse";
	}

	if ($modele){
		$destinataires = array();
		if ($GLOBALS['meta']["suivi_edito"] == "oui")
			$destinataires = explode(',',$GLOBALS['meta']["adresse_suivi"]);


		$destinataires = pipeline('notifications_destinataires',
			array(
				'args'=>array('quoi'=>$quoi,'id'=>$id_article,'options'=>$options)
			,
				'data'=>$destinataires)
		);

		$texte = email_notification_objet($id_article, "article", $modele);
		notifications_envoyer_mails($destinataires, $texte);
	}
}

?>