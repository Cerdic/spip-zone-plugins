<?php
/*
 * Plugin FBLogin / gestion du login FB
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */

/**
 * Fonction appellee dans le pipeline notifications
 * Permet de notifier FB pour ecrire dans le feednews
 *
 * @param array $flux
 * @return array
 */
function fblogin_notifications($flux){
	$quoi = $flux['args']['quoi'];
	
	// gerer le relai des news sur fb
	// les news a publier sont listees dans la globale $fb_notification_news
	// format :
	// 'quoi' => array ('sujet'=>'chaine langue titre', 'texte'=>'chaine langue descriptif')
	$liste_news = isset($GLOBALS['fb_notification_news'])?$GLOBALS['fb_notification_news']:array();

	if (isset($liste_news[$quoi])){
		$id = $flux['args']['id'];
		$options = $flux['args']['options'];

		$qui = isset($options['id_auteur'])?intval($options['id_auteur']):intval($GLOBALS['visiteur_session']['id_auteur']);
		// trouver les destinataires
		if ($qui == $GLOBALS['visiteur_session']['id_auteur']
			&& defined('_FB_API_KEY')
			&& isset($GLOBALS['visiteur_session']['fb_session'])){

			try {
				include_spip('inc/fb_lib');
				include_spip('inc/filtres');
				$api_client = new FacebookRestClient(_FB_API_KEY, _FB_SECRET, $GLOBALS['visiteur_session']['fb_session']['session_key']);
				$uid = $api_client->users_getLoggedInUser();
				
				$infos = array('uid'=>$uid,'nom_site'=>$GLOBALS['meta']['nom_site'],'url_site'=>$GLOBALS['meta']['adresse_site']);

				// remplacer par une methode generique enrichissable
				$sujet = isset($liste_news[$quoi]['sujet'])?_T($liste_news[$quoi]['sujet'],$infos):'';
				$texte = isset($liste_news[$quoi]['texte'])?_T($liste_news[$quoi]['texte'],$infos):'';
			
				if ($sujet) {
					$res = $api_client->feed_publishActionOfUser($sujet,$texte);
					spip_log("Notification mini news :".var_export($res,true).": $sujet / $texte",'fblogin');
				}
			}
			// Une exception est levee uniquement si une erreur est trouvee
			catch (Exception $e) {
				spip_log('Exception api FB, pipeline fblogin_notifications','fblogin');
			}
		}
	}

	return $flux;
}

?>
