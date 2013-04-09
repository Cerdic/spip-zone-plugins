<?php
/**
 * GetID3
 * Gestion des métadonnées de fichiers sonores et vidéos directement dans SPIP
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info), BoOz
 * 2008-2013 - Distribué sous licence GNU/GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction de vérification que les binaires utilisés soient correctement installés
 * et exécutables
 * -* safe mode;
 * -* vorbiscomment;
 * -* metaflac;
 * 
 * Note : Les codes de retour normaux d'une application sont :
 * -* 0 en cas de réussite
 * -* 1 en cas d'échec (l'application est là mais retourne une erreur)
 * -* 127 en cas d'absence de l'application
 * 
 * @param boolean $notif
 * 		On notifie ou pas?
 * @return array $erreurs
 * 		La liste des erreurs
 */
function inc_getid3_verifier_binaires_dist($notif=false){
	$erreurs = array();
	
	$tags_write = array('mp3','mp2','mpc','ogg','flac');
	$tags_impossible = array();

	/**
	 * Si on est en safe mode, ne fonctionne pas
	 */
	if (preg_match('#(1|ON)#i', ini_get('safe_mode'))) {
		ecrire_config('getid3_safe_mode', 'oui');
		$erreurs[] = 'safe_mode';
		$tags_impossible[] = array('ogg','flac');
	}else{
		/**
		 * Tester vorbiscomment
		 */
		exec('vorbiscomment --help',$retour,$retour_int);
		if($retour_int != 0){
			ecrire_config('getid3_vorbiscomment_casse', 'oui');
			$erreurs[] = 'vorbiscomment';
			$tags_impossible[] = 'ogg';
		}else{
			effacer_config('getid3_vorbiscomment_casse');
		}
	
		/**
		 * Tester metaflac
		 */
		exec('metaflac --help',$retour_metaflac,$retour_metaflac_int);
		if($retour_metaflac_int != 0){
			ecrire_config('getid3_metaflac_casse', 'oui');
			$erreurs[] = 'metaflac';
			$tags_impossible[] = 'flac';
		}else{
			effacer_config('getid3_metaflac_casse');
		}
	}

	if(count($erreurs) > 0)
		ecrire_config('getid3_casse', 'oui');
	else
		effacer_config('getid3_casse');

	/**
	 * Ecriture dans la configuration des formats sur lesquels on peut écrire les tags
	 */
	$tags_write = array_diff($tags_write,$tags_impossible);
	ecrire_config('getid3_write',serialize($tags_write));
	
	if((count($erreurs) > 0) && $notif){
		if ($notifications = charger_fonction('notifications', 'inc')) {
			$notifications('getid3_verifier_binaires', 1,
				array(
					'erreurs' => $erreurs
				)
			);
		}
	}
	return $erreurs;
}
?>