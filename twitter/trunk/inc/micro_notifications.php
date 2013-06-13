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

/*
 * Buzzer les notifications
 */

function twitter_notifications($x) {
  include_spip('inc/filtres_mini');
  include_spip('inc/texte');

	$status = null;
	$cfg = @unserialize($GLOBALS['meta']['microblog']);
	switch($x['args']['quoi']) {
		case 'forumposte':      // post forums
			if ($cfg['evt_forumposte']
			AND $id = intval($x['args']['id'])) {
				// ne pas poster si le forum est valide et config forum valide activee
				if (sql_getfetsel("statut","spip_forum","id_forum=".intval($id))!="publie"
					OR !$cfg['evt_forumvalide']){
					$status = twitter_annonce('forumposte',array('id_forum'=>$id));
					envoyer_microblog($status,array('objet'=>'forum','id_objet'=>$id));
				}
			}
			break;
		case 'forumvalide':      // forum valide
			if ($cfg['evt_forumvalide']
			AND $id = intval($x['args']['id'])) {
				$status = twitter_annonce('forumvalide',array('id_forum'=>$id));
				envoyer_microblog($status,array('objet'=>'forum','id_objet'=>$id));
			}
			break;

		case 'instituerarticle':    // publier | proposer articles
		if ($id = intval($x['args']['id'])
			AND (
				// publier
				($cfg['evt_publierarticles']
					AND $x['args']['options']['statut'] == 'publie'
					AND $x['args']['options']['statut_ancien'] != 'publie'
					AND ($GLOBALS['meta']["post_dates"]=='oui'
						OR strtotime($x['args']['options']['date'])<=time()
						OR $cfg['evt_publierarticlesfutur']!='publication'
					)
				)
			OR 
				// proposer
				($cfg['evt_proposerarticles']
				AND $x['args']['options']['statut'] == 'prop' 
				AND $x['args']['options']['statut_ancien'] != 'publie'
				)
			)
		) {
			// si on utilise aussi le cron pour annoncer les articles post-dates
			// noter ceux qui sont deja annonces ici (pour eviter double annonce)
			if ($x['args']['options']['statut'] == 'publie'
			  AND $GLOBALS['meta']["post_dates"]=='non'
				AND $cfg['evt_publierarticlesfutur']=='publication'
			){
				include_spip('inc/meta');
				ecrire_meta('microblog_annonces',$GLOBALS['meta']['microblog_annonces'].','.$id);
			}

			// en cas d'attente, on note la date du plus vieux, et on ajoute l'attente
			$heure = time()+60;
			if (($attente = 60*intval($cfg['attente'])) > 0) {
				$vieux = $GLOBALS['meta']['microblog_vieux'];
				if ($vieux AND $vieux>$heure-$attente) {
					$heure = $vieux + $attente;
				}
				ecrire_meta('microblog_vieux', $heure);
			}

			$status = twitter_annonce('instituerarticle',array('id_article'=>$id));
			envoyer_microblog($status,array('objet'=>'article','id_objet'=>$id), $heure);
		}
		break;
	}

	return $x;
}

function twitter_annonce($quoi,$contexte){
	return trim(recuperer_fond("modeles/microblog_$quoi",$contexte));
}

function envoyer_microblog($status,$liens=array(), $heure = null){
	// un status vide ne provoque pas d'envoi
	if (!is_null($status) AND strlen($status)) {
		if (!function_exists('job_queue_add')){
			include_spip('inc/microblog');
			microblog($status);
		}
		else {
			if ($heure === null)
				$heure = time() + 60;
			$id_job = job_queue_add('microblog',"microblog : $status",array($status),'inc/microblog',true, $heure);
			if ($liens)
				job_queue_link($id_job,$liens);
		}
	}
}

?>
