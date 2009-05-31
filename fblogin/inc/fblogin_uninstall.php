<?php
/*
 * Plugin FBLogin / gestion du login FB
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */

/**
 * Desinstallation du plugin
 *
 */
function inc_fblogin_uninstall_dist(){
	$redirect = '';
	// charger l'auth fb dans la session si necessaire
	if (defined('_FB_API_KEY') 
		AND _request('fb_sig_api_key') == _FB_API_KEY
		AND $uid = _request('fb_sig_user')){
		
		include_spip('base/abstract_sql');
		$res = sql_select('id_auteur','spip_auteurs','fb_uid='.sql_quote($uid));
		if ($row = sql_fetch($res)){
			$session = charger_fonction('session','inc');
			$session($row['id_auteur']);
		}
	}
}

?>
