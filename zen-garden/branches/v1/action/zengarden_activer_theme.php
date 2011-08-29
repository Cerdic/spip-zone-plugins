<?php
/**
 * Plugin Zen-Garden pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

function action_zengarden_activer_theme_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$arg = $securiser_action();

	if (strncmp('defaut:',$arg,7) == 0){
		$dir_theme = _DIR_THEMES . substr($arg,7);
		$flux = pipeline('zengarden_activer_theme', array('args' => array('dir' =>$dir_theme, 'action'=>'effacer'), 'data' => true));
		if ($flux) {
			include_spip('inc/meta');
			effacer_meta("zengarden_theme");
		}
	}
	elseif (strncmp('apercu:',$arg,7) == 0){
		$theme = substr($arg,7);
		$dir_theme = _DIR_THEMES . $theme;
		if (is_dir($dir_theme)) {
			$flux = pipeline('zengarden_activer_theme', array('args' => array('dir' =>$dir_theme, 'action'=>'apercevoir'), 'data' => true));
			if ($flux) {
				include_spip('inc/cookie');
				spip_setcookie('spip_zengarden_theme', $theme);
			}
		}
	}
	elseif (strncmp('activation:',$arg,11) == 0) {
		$theme = substr($arg,11);
		$dir_theme = _DIR_THEMES . $theme;
		if (is_dir($dir_theme)) {
			$flux = pipeline('zengarden_activer_theme', array('args' => array('dir' => $dir_theme, 'action'=>'activer'), 'data' => true));
			if ($flux) {
				include_spip('inc/meta');
				ecrire_meta("zengarden_theme", $theme);
			}
		}
	}
}

?>
