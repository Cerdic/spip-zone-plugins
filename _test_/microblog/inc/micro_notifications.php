<?php

/*****************************************************************\
 * spip|microblog
 *                      (c) Fil 2009
 *
 * envoyer des micromessages depuis SPIP vers twitter ou laconica
 * distribue sous licence GNU/LGPL
 *
\*****************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;



/*
 * Buzzer les notifications
 */

function Microblog_notifications($x) {
  include_spip('inc/filtres_mini');
  include_spip('inc/texte');

	$status = null;
	$cfg = @unserialize($GLOBALS['meta']['microblog']);
	switch($x['args']['quoi']) {
		case 'forumposte':      // post forums
			if ($cfg['evt_forumposte']
			AND $id = intval($x['args']['id'])) {
				$url = url_absolue(generer_url_entite($id, 'forum'));
				$t = sql_fetsel('titre,texte', 'spip_forum', 'id_forum='.$id);
				$titre = couper(typo($t['titre'].' | '.$t['texte']),
					120 - strlen('#forum  ') - strlen($url));
				$status = "$titre #forum $url";
			}
			break;
		
		case 'instituerarticle':    // publier | proposer articles
		if ($id = intval($x['args']['id'])
			AND ( ($cfg['evt_publierarticles'] 
				AND $x['args']['options']['statut'] == 'publie') // publier
			OR ($cfg['evt_proposerarticles'] 
				AND $x['args']['options']['statut'] == 'prop' 
				AND $x['args']['options']['statut_ancien'] != 'publie') )  // proposer
		) {
			$espace_lien = ($x['args']['options']['statut'] == 'publie' ? true : false);  // lien notifié vers public | privé
        $url = str_replace('amp;','',url_absolue(generer_url_entite($id, 'article', '', '', $espace_lien)));
			$t = sql_fetsel('titre,descriptif,texte', 'spip_articles', 'id_article='.$id);
			$etat = str_replace(array('prop','publie'),
				array(_T('microblog:propose'),_T('microblog:publie')),
				$x['args']['options']['statut']
			);
			$titre = couper(typo($t['titre']
				.' | '.$etat
				.' | '.($t['descriptif'] != '' ? $t['descriptif'].' | ' : '')
				.$t['texte']),
				120 - strlen($url));
			$status = "$titre $url";
		}
		break;
	}

	if (!is_null($status)) {
		include_spip('inc/microblog');
echo $status;
die;    
		microblog($status);
	}

	return $x;
}

