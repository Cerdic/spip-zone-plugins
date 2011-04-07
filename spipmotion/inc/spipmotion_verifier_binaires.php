<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans SPIP
 *
 * Auteurs :
 * Quentin Drouet (kent1)
 * 2008-2010 - Distribué sous licence GNU/GPL
 *
 */

/**
 * Fonction de vérification que les binaires utilisés soient correctement installés
 * et exécutables
 * 
 * -* ffmpeg
 * -* ffmpeg2theora
 * -* flvtool2
 * -* qt-faststart
 * -* le script spipmotion.sh
 * -* la class ffmpeg-php
 * -* mediainfo
 *
 * Si le safe_mode est activé, on l'inscrit dans les metas ainsi que son exec_dir
 * afin de retrouver le script spipmotion.sh qui doit s'y trouver
 * 
 * Note : Les codes de retour normaux d'une application sont :
 * -* 0 en cas de réussite
 * -* 1 en cas d'échec (l'application est là mais retourne une erreur)
 * -* 127 en cas d'absence de l'application
 * 
 * @param unknown_type $valeurs
 * @param boolean $notif : On notifie ou pas?
 */
function inc_spipmotion_verifier_binaires_dist($valeurs='',$notif=false){
	spip_log('SPIPmotion : Vérification des binaires','spipmotion');
	$erreurs = array();
	
	/**
	 * On vérifie que safe_mode soit activé ou pas
	 */
	$safe_mode = @ini_get('safe_mode');
	if($safe_mode == 1){
		ecrire_config('spipmotion_safe_mode', 'oui');
		$safe_mode_path = @ini_get('safe_mode_exec_dir');
		ecrire_config('spipmotion_safe_mode_exec_dir', $safe_mode_path);
	}else{
		effacer_config('spipmotion_safe_mode');
		effacer_config('spipmotion_safe_mode_exec_dir');
	}
	
	if(!$valeurs)
		$valeurs = lire_config('spipmotion');

	/**
	 * Tester ffmpeg2theora
	 */
	exec('ffmpeg2theora',$retour_ffmpeg2theora,$retour_ffmpeg2theora_int);
	if($retour_ffmpeg2theora_int != 0){
		ecrire_config('spipmotion_ffmpeg2theora_casse', 'oui');
		$erreurs[] = 'ffmpeg2theora';
	}else{
		effacer_config('spipmotion_ffmpeg2theora_casse');
	}

	/**
	 * Tester flvtool2
	 */
	exec('flvtool2',$retour_flvtool,$retour_flvtool_int);
	if($retour_flvtool_int != 0){
		ecrire_config('spipmotion_flvtool_casse', 'oui');
		$erreurs[] = 'flvtool2';
	}else{
		effacer_config('spipmotion_flvtool_casse');
	}

	/**
	 * Tester qt-faststart
	 */
	exec('qt-faststart',$retour_qt_faststart,$retour_qt_faststart_int);
	if($retour_qt_faststart_int != 0){
		ecrire_config('spipmotion_qt-faststart_casse', 'oui');
		$erreurs[] = 'qt-faststart';
	}else{
		effacer_config('spipmotion_qt-faststart_casse');
	}
	
	/**
	 * Tester mediainfo
	 * MediaInfo n'est pas indispensable au bon fonctionnement
	 * On n'envoie pas de mail de notification
	 * On ne bloquera pas les encodages
	 */
	exec('mediainfo --help',$retour_mediainfo,$retour_mediainfo_int);
	if(!in_array($retour_mediainfo_int,array(0,255))){
		ecrire_config('spipmotion_mediainfo_casse', 'oui');
	}else{
		effacer_config('spipmotion_mediainfo_casse');
	}

	/**
	 * Tester le script spipmotion.sh présent dans script_bash/
	 * Si le safe_mode est activé, il doit se trouver dans le répertoire des scripts autorisés
	 */
	if($safe_mode == 1){
		$spipmotion_sh = $safe_mode_path.'/spipmotion.sh';
	}else{
		$spipmotion_sh = find_in_path('script_bash/spipmotion.sh');
	}
	exec($spipmotion_sh." --help",$retour_spipmotionsh,$retour_spipmotionsh_int);
	if($retour_spipmotionsh_int != 0){
		ecrire_config('spipmotion_spipmotionsh_casse', 'oui');
		$erreurs[] = 'spipmotion.sh';
	}else{
		effacer_config('spipmotion_spipmotionsh_casse');
	}

	/**
	 * Tester ffmpeg
	 */
	if($valeurs['chemin'] != ''){
		exec($spipmotion_sh." --p ".$valeurs['chemin']." --info '-version'",$retour_ffmpeg,$retour_int_ffmpeg);
		if($retour_int_ffmpeg != 0){
			ecrire_config('spipmotion_ffmpeg_casse', 'oui');
			$erreurs[] = 'ffmpeg';
		}else{
			effacer_config('spipmotion_ffmpeg_casse');
		}
	}else{
		exec($spipmotion_sh." --info -version",$retour_ffmpeg,$retour_int_ffmpeg);
		if($retour_int_ffmpeg != 0){
			ecrire_config('spipmotion_casse', 'oui');
			$erreurs[] = 'ffmpeg';
		}else{
			if($GLOBALS['meta']['spipmotion_casse'] == 'oui'){
				effacer_config('spipmotion_casse');
			}
		}
	}
	if (!class_exists('ffmpeg_movie')) {
		ecrire_config('spipmotion_ffmpeg-php_casse', 'oui');
		$erreurs[] = 'ffmpeg-php';
	}else{
		effacer_config('spipmotion_ffmpeg-php_casse');
	}

	if(count($erreurs) > 0){
		ecrire_config('spipmotion_casse', 'oui');
	}else{
		effacer_config('spipmotion_casse');
	}

	if($notif){
		if ($notifications = charger_fonction('notifications', 'inc')) {
			$notifications('spipmotion_verifier_binaires', 1,
				array(
					'erreurs' => $erreurs
				)
			);
		}
	}
	return $erreurs;
}
?>