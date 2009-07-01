<?php
/**
 * Plugin Zen-Garden pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

function action_zengarden_activer_theme_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$arg = $securiser_action();

	if ($arg=='-1'){
		include_spip('inc/meta');
		effacer_meta("zengarden_theme");
	}
	elseif (strncmp('preview:',$arg,8)==0){
		include_spip('inc/cookie');
		spip_setcookie('spip_zengarden_theme',substr($arg,8));
	}
	elseif (is_dir(_DIR_THEMES . $arg)) {
		include_spip('inc/meta');
		ecrire_meta("zengarden_theme",$arg);
	}
}

?>