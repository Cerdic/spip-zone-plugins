<?php
function moderation_vip($flux){
include_spip('inc/session');
	if ($flux['args']['table']=='spip_forum'
		AND $flux['args']['action']=='instituer'){
			// Pour publier direct les auteurs configurés par modération modérés
				global $visiteur_session;
				if ($visiteur_session) {
					$autstat = $visiteur_session['statut'];
					if ($autstat == '0minirezo' AND defined('_MOD_MOD_ADMIN')) {
						$flux['data']['statut']='publie';
					}
					else if ($autstat == '1comite' AND defined('_MOD_MOD_REDAC')) {
						$flux['data']['statut']='publie';
					}
					else if ($autstat == '6forum' AND defined('_MOD_MOD_VISIT'))  {
						$flux['data']['statut']='publie';		
					}
				} 	
	}
	return $flux;
}
?>