<?php
include_spip('inc/meta');
include_spip('inc/session');
function moderation_vip($flux){
	if ($flux['args']['table']=='spip_forum'
		AND $flux['args']['action']=='instituer'  AND $flux["data"]["statut"]!="prive"){
			// Pour publier direct les auteurs configurés par modération modérée
				global $visiteur_session;
				if ($visiteur_session) {
					$moderation_plug_admin=$GLOBALS['meta']["moderation_plug_admin"];
					$moderation_plug_redac=$GLOBALS['meta']["moderation_plug_redac"];
					$moderation_plug_visit=$GLOBALS['meta']["moderation_plug_visit"];
					$autstat = $visiteur_session['statut'];
					if ($autstat == '0minirezo' AND $moderation_plug_admin == 'oui') {
						$flux['data']['statut']='publie';
					}
					else if ($autstat == '1comite' AND $moderation_plug_redac == 'oui') {
						$flux['data']['statut']='publie';
					}
					else if ($autstat == '6forum' AND $moderation_plug_visit == 'oui')  {
						$flux['data']['statut']='publie';		
					}
				} 	
	}
	return $flux;
}
?>