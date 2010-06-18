<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos et son directement dans spip
 *
 * Auteurs :
 * Quentin Drouet (kent1)
 *
 * 2008-2010 - Distribué sous licence GNU/GPL
 *
 * Fonctions spécifiques au squelette config_spipmotion.html
 **/

/**
 * Fonction de post-traitement du formulaire de configuration CFG
 * Vérifie principalement la présence du logiciel d'encodage
 */
function cfg_config_spipmotion_post_traiter(&$cfg){
	$modifs = $cfg->log_modif;
	if(preg_match('/chemin/', $modifs, $matches)){
		include_spip('inc/metas');
		$valeurs = $cfg->val;
		spip_log($valeurs,'spipmotion');
		if($valeurs['chemin'] != ''){
			exec($valeurs['chemin'],$retour,$retour_int);
			if($retour_int != 1){
				ecrire_config('spipmotion_casse', 'oui');
				$erreur = true;
			}else if($GLOBALS['meta']['spipmotion_casse'] == 'oui'){
				effacer_config('spipmotion_casse');
			}
		}else{
			exec('ffmpeg',$retour,$retour_int);
			if($retour_int != 1){
				ecrire_config('spipmotion_casse', 'oui');
				$erreur = true;
			}else{
				$config = lire_config('spipmotion');
				$config['chemin'] = 'ffmpeg';
				ecrire_meta('spipmotion',serialize($config));
				spip_log($config,'spipmotion');
				spip_log('on met juste "ffmpeg" comme chemin pour ffmpeg','spipmotion');
				if($GLOBALS['meta']['spipmotion_casse'] == 'oui'){
					effacer_config('spipmotion_casse');
				}
			}
		}

		if(!$erreur){
			/**
			 * On récupère les informations du nouveau ffmpeg
			 */
			$ffmpeg_infos = charger_fonction('ffmpeg_infos','inc');
			$ffmpeg_infos(true);
		}

		include_spip('inc/invalideur');
		suivre_invalideur('1');

		/**
		 * On force le rechargement de la page car on a récupéré de nouvelles infos sur ffmpeg
		 */
		$cfg->messages['redirect'] = self();
	}
}
?>