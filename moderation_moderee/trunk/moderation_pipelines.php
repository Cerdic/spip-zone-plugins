<?php
include_spip('inc/config');
include_spip('inc/session');
function moderation_pre_edition($flux) {
	if ($flux['args']['table']=='spip_forum'
		AND $flux['args']['action']=='instituer' 
		AND
		!in_array(
			$flux["data"]["statut"], 
			array("prive","privrac","privadm")
		)
	) {
		// Pour publier direct les auteurs configur�s par mod�ration mod�r�e
		global $visiteur_session;
		if ($visiteur_session){
			if (lire_config("moderation/".$visiteur_session['statut']) == 'on') {
				var_dump($flux["data"]);
				$flux['data']['statut']='publie';
			}
		} 	
	}
	return $flux;
}
