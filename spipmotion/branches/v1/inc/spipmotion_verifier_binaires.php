<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans SPIP
 *
 * Auteurs :
 * Quentin Drouet (kent1)
 * 2008-2013 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction de vérification que les binaires utilisés soient correctement installés
 * et exécutables ainsi que quelques éléments de configuration de PHP :
 * 
 * -* l'état du safe_mode;
 * -* ffmpeg;
 * -* ffmpeg2theora;
 * -* flvtool2;
 * -* flvtool++;
 * -* qt-faststart;
 * -* le script spipmotion.sh;
 * -* le script spipmotion_vignette.sh;
 * -* la class ffmpeg-php;
 * -* mediainfo;
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
		ecrire_meta('spipmotion_safe_mode', 'oui','','spipmotion_metas');
		$safe_mode_path = @ini_get('safe_mode_exec_dir');
		ecrire_meta('spipmotion_safe_mode_exec_dir', $safe_mode_path,'','spipmotion_metas');
	}else{
		effacer_meta('spipmotion_safe_mode','spipmotion_metas');
		effacer_meta('spipmotion_safe_mode_exec_dir','spipmotion_metas');
	}
	
	if(!$valeurs)
		$valeurs = lire_config('spipmotion',array());

	if(!function_exists('exec')){
		ecrire_config('spipmotion_exec_casse', 'oui');
		$erreurs[] = 'exec';
	}
	else{
	
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
		 * Tester flvtool++
		 */
		exec('flvtool++',$retour_flvtoolplus,$retour_flvtoolplus_int);
		if($retour_flvtoolplus_int != 0){
			ecrire_config('spipmotion_flvtoolplus_casse', 'oui');
			$erreurs[] = 'flvtool++';
		}else{
			effacer_config('spipmotion_flvtoolplus_casse');
		}
		
		if(!in_array('flvtool2',$erreurs) OR !in_array('flvtool++',$erreurs)){
			foreach($erreurs as $erreur=>$soft){
				if(in_array($soft,array('flvtool2','flvtool++'))){
					unset($erreurs[$erreur]);
				}
			}
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
		 * Tester les scripts spipmotion.sh et spipmotion_vignette.sh présents dans script_bash/
		 * Si le safe_mode est activé, il doivent se trouver dans le répertoire des scripts autorisés
		 */
		if($safe_mode == 1){
			$spipmotion_sh = $safe_mode_path.'/spipmotion.sh';
			$spipmotion_vignette_sh = $safe_mode_path.'/spipmotion_vignette.sh';
		}else{
			$spipmotion_sh = find_in_path('script_bash/spipmotion.sh');
			$spipmotion_vignette_sh = find_in_path('script_bash/spipmotion_vignette.sh');
		}
		exec($spipmotion_sh." --help",$retour_spipmotionsh,$retour_spipmotionsh_int);
		if($retour_spipmotionsh_int != 0){
			ecrire_config('spipmotion_spipmotionsh_casse', 'oui');
			$erreurs[] = 'spipmotion.sh';
		}else{
			effacer_config('spipmotion_spipmotionsh_casse');
		}
		
		exec($spipmotion_vignette_sh." --help",$retour_spipmotion_vignettesh,$retour_spipmotion_vignettesh_int);
		if($retour_spipmotion_vignettesh_int != 0){
			ecrire_config('spipmotion_spipmotion_vignette_sh_casse', 'oui');
			$erreurs[] = 'spipmotion_vignette.sh';
		}else{
			effacer_config('spipmotion_spipmotion_vignette_sh_casse');
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
			spip_log($retour_ffmpeg,'test');
			if($retour_int_ffmpeg != 0){
				ecrire_config('spipmotion_casse', 'oui');
				$erreurs[] = 'ffmpeg';
			}else{
				if($GLOBALS['meta']['spipmotion_casse'] == 'oui'){
					effacer_config('spipmotion_casse');
				}
			}
		}
		
		/**
		 * Tester ffmpeg2theora
		 */
		exec('ffmpeg2theora',$retour_ffmpeg2theora,$retour_ffmpeg2theora_int);
		if($retour_ffmpeg2theora_int != 0){
			ecrire_config('spipmotion_ffmpeg2theora_casse', 'oui');
			//$erreurs[] = 'ffmpeg2theora';
		}else{
			effacer_config('spipmotion_ffmpeg2theora_casse');
		}
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