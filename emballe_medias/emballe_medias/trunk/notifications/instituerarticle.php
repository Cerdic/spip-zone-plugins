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
	spip_log('notifications_instituerarticle','test.'._LOG_ERREUR);
	$t = sql_fetsel("*", "spip_articles", "id_article=".intval($id_article));
	if (!$t)
		return;
	
	$type = 'article';
	
	// ne devrait jamais se produire
	if ($options['statut'] == $options['statut_ancien']) {
		spip_log("statut inchange",'notifications');
		return;
	}

	include_spip('inc/texte');

	$modele = "";
	$id_secteur = $t['id_secteur'];
	$diogene = sql_fetsel('*','spip_diogenes','id_secteur='.intval($id_secteur).' AND objet IN ("article","emballe_media")');
	
	if(isset($diogene['id_diogene']) && $diogene['objet'] == 'emballe_media')
		$type = 'media';
	
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

		// preparer le calcul des liens de moderation
		$moderations = array();
		foreach(array('publie','refuse','poubelle') as $statut){
			if ($statut!==$t['statut'])
				$moderations["url_moderer_$statut"] = "$id_article-$statut-".$t['statut'];
		}
		include_spip("inc/securiser_action");
		$action = 'instituer_media_paremail';
		$pass = secret_du_site();
		
		$email_notification_forum = charger_fonction('email_notification_forum','inc');
		$contexte = array('id_article'=>$id_article,"id"=>$id_article);
		foreach ($destinataires as $email) {
			// ajouter les liens de moderation par statut
			$contexte['notification_email'] = $email;
			foreach($moderations as $k=>$arg){
				$arg = "$arg-$email";
				$hash = _action_auteur("$action-$arg", '', $pass, 'alea_ephemere');
				$contexte[$k] = generer_url_action($action, "arg=$arg&hash=$hash", true, true);
			}
			$texte = recuperer_fond($modele,$contexte);
			notifications_envoyer_mails_texte_ou_html($email, $texte);
		}
	}
}

?>