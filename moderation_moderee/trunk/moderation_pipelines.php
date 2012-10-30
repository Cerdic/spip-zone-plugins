<?php
include_spip('inc/config');
include_spip('inc/session');
function moderation_pre_edition($flux){
	if ($flux['args']['table']=='spip_forum'
		AND $flux['args']['action']=='instituer'){
			// Pour publier direct les auteurs configurés par modération modérée
				global $visiteur_session;
				if ($visiteur_session) {
					$moderation_plug_admin=lire_config("moderation/admin");
					$moderation_plug_redac=lire_config("moderation/redac");
					$moderation_plug_visit=lire_config("moderation/visit");
					$autstat = $visiteur_session['statut'];
					if ($autstat == '0minirezo' AND $moderation_plug_admin == 'on') {
						$flux['data']['statut']='publie';
					}
					else if ($autstat == '1comite' AND $moderation_plug_redac == 'on') {
						$flux['data']['statut']='publie';
					}
					else if ($autstat == '6forum' AND $moderation_plug_visit == 'on')  {
						$flux['data']['statut']='publie';		
					}
				} 	
	}
	return $flux;
}
?>