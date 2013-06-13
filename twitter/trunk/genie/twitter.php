<?php
/*
 * Plugin spip|twitter
 * (c) 2009-2013
 *
 * envoyer et lire des messages de Twitter
 * distribue sous licence GNU/LGPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Alerte sur les articles publies post-dates
 *
 * @param int $last
 * @return int
 */
function genie_twitter_dist($last) {
	$cfg = @unserialize($GLOBALS['meta']['microblog']);
	// si le site utilise les articles postdates
	// et que l'on a configurer pour alerter a la publication uniquement
	// il faut surveiller les articles publies
	// $last est la date de la dernier occurence du cron, si vaut zero on ne fait rien
	if ($GLOBALS['meta']["post_dates"]=='non'
	AND $cfg['evt_publierarticlesfutur']=='publication'
	AND $last){
		include_spip('inc/abstract_sql');
		$deja_annonces = explode(',',$GLOBALS['meta']['twitter_annonces']);
		$deja_annonces = array_map('intval',$deja_annonces);

		$res = sql_select("id_article,statut","spip_articles",
			array(
				"statut='publie'",
				"date>".sql_quote(date("Y-m-d H:i:s",$last)),
				"date<=".sql_quote(date("Y-m-d H:i:s")),
				sql_in('id_article',$deja_annonces,"NOT")
			));
		include_spip('inc/twitter_notifications');
		include_spip('inc/twitter');
		while($row = sql_fetch($res)){
			$status = twitter_annonce('instituerarticle',array('id_article'=>$row['id_article']));
			envoyer_tweet($status,array('objet'=>'article','id_objet'=>$row['id_article']));
		}
		// raz des annonces deja faites
		include_spip('inc/meta');
		ecrire_meta('twitter_annonces','0');
	}

	return 1;
}

/**
 * Ajouter la tache cron pour tweeter les articles post-dates, chaque heure
 * @param $taches_generales
 * @return mixed
 */
function twitter_taches_generales_cron($taches_generales){
	if ($GLOBALS['meta']["post_dates"]=='non'
		AND	$cfg = @unserialize($GLOBALS['meta']['microblog'])
		AND $cfg['evt_publierarticlesfutur']=='publication'){
		// surveiller toutes les heures les publications post-dates
		$taches_generales['twitter'] = 3600;
	}
	return $taches_generales;
}

?>