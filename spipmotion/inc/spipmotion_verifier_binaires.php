<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * Quentin Drouet (kent1)
 * 2008-2010 - Distribué sous licence GNU/GPL
 *
 */

/**
 * Vérifier que les binaires utilisés soient correctement installés
 * -* ffmpeg
 * -* ffmpeg2theora
 * -* flvtool2
 * -* qt-faststart
 * -* le script spipmotion.sh
 * -* la class ffmpeg-php
 *
 * Note : Les codes de retour normaux d'une application sont :
 * -* 0 en cas de réussite
 * -* 1 en cas d'échec (l'application est là mais retourne une erreur)
 * -* 127 en cas d'absence de l'application
 * @param unknown_type $valeurs
 */
function inc_spipmotion_verifier_binaires_dist($valeurs='',$notif=false){
	$erreurs = array();

	spip_log('Verification des binaires','spipmotion');
	if(!$valeurs)
		$valeurs = lire_config('spipmotion');

	/**
	 * Tester ffmpeg
	 */
	if($valeurs['chemin'] != ''){
		exec($valeurs['chemin'].' --help',$retour,$retour_int);
		if($retour_int != 0){
			ecrire_config('spipmotion_ffmpeg_casse', 'oui');
			$erreurs[] = 'ffmpeg';
		}else{
			effacer_config('spipmotion_ffmpeg_casse');
		}
	}else{
		exec('ffmpeg --help',$retour,$retour_int);
		spip_log($retour,'test_binaires');
		spip_log($retour_int,'test_binaires');
		if($retour_int != 0){
			ecrire_config('spipmotion_casse', 'oui');
			$erreurs[] = 'ffmpeg';
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
	 * Tester le script spipmotion.sh présent dans script_bash/
	 */
	exec(find_in_path('script_bash/spipmotion.sh').' --help',$retour_spipmotionsh,$retour_spipmotionsh_int);
	if($retour_spipmotionsh_int != 0){
		ecrire_config('spipmotion_spipmotionsh_casse', 'oui');
		$erreurs[] = 'spipmotion.sh';
	}else{
		effacer_config('spipmotion_spipmotionsh_casse');
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
			spip_log('notifications verifier_binaires','spipmotion');
			spip_log($erreurs,'spipmotion');
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