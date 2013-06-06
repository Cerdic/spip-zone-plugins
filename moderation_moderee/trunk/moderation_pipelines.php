<?php
include_spip('inc/config');
include_spip('inc/session');
function moderation_pre_edition($flux){
	if ($flux['args']['table']=='spip_forum'
		AND $flux['args']['action']=='instituer' AND $flux["data"]["statut"]!="prive"){
			// Pour publier direct les auteurs configurés par modération modérée
				global $visiteur_session;
				if ($visiteur_session){
					       if (lire_config("moderation/".$visiteur_session['statut']) == 'on') {
						        $flux['data']['statut']='publie';
                      }
				} 	
	}
	return $flux;
}
?>