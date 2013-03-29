<?php
/*
 * Plugin Notifications
 * (c) 2009-2012 SPIP
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// Fonction appelee par divers pipelines
// http://doc.spip.org/@notifications_instituerarticle_dist
function notifications_instituerarticle($quoi, $id_article, $options) {
	$type = 'article';
	
	// ne devrait jamais se produire
	if ($options['statut'] == $options['statut_ancien']) {
		spip_log("statut inchange",'notifications');
		return;
	}

	include_spip('inc/texte');

	$modele = "";
	$id_secteur = sql_getfetsel('id_secteur','spip_articles','id_article='.intval($id_article));
	$diogene = sql_fetsel('*','spip_diogenes','id_secteur='.intval($id_secteur).' AND objet IN ("article","emballe_media")');
	
	if(isset($diogene['id_diogene']) && $diogene['objet'] == 'emballe_media'){
		$type = 'media';
	}

	if ($options['statut'] == 'publie') {
		if ($GLOBALS['meta']["post_dates"]=='non'
			AND strtotime($options['date'])>time())
			$modele = "notifications/".$type."_valide";
		else
			$modele = "notifications/".$type."_publie";
	}
	if ($options['statut'] == 'prop' AND $options['statut_ancien'] != 'publie')
		$modele = "notifications/".$type."_propose";

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