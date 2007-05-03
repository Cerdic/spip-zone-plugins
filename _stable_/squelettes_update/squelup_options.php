<?php
if (_DIR_RESTREINT AND $auteur_session['statut'] == '0minirezo') {

	$GLOBALS['svn_up_dir_skel'] = "squelettes";
	if (isset($GLOBALS['dossier_squelettes'])
		&& $GLOBALS['dossier_squelettes']!=""){
		$skel = explode(':',$GLOBALS['dossier_squelettes']);
		$skel = $GLOBALS['svn_up_dir_skel'][0];
		if (is_dir($skel))
			$GLOBALS['svn_up_dir_skel'] = $skel;
	}
	if (!($GLOBALS['svn_up_dir_skel']
	AND is_dir($GLOBALS['svn_up_dir_skel'].'/.svn')
	AND is_writable($GLOBALS['svn_up_dir_skel'])
	))
		$GLOBALS['svn_up_dir_skel'] = NULL;

if (_request('var_svn')=='update') {

	if (_request('username')!==NULL){
		include_spip('inc/meta');
		ecrire_meta('squelette_up_svn_username',_request('username'));
		ecrire_metas();
	}
	if (_request('password')!==NULL){
		include_spip('inc/meta');
		ecrire_meta('squelette_up_svn_password',_request('password'));
		ecrire_metas();
	}
	$user = "";
	if (isset($GLOBALS['meta']['squelette_up_svn_username'])){
		$user .= "--username "
		 . escapeshellarg($GLOBALS['meta']['squelette_up_svn_username']);
		if (isset($GLOBALS['meta']['squelette_up_svn_password'])){
			$user .= " --password "
			 .escapeshellarg($GLOBALS['meta']['squelette_up_svn_password']);
		}
	}

	$out = array();
	exec("svn $user update ".$GLOBALS['svn_up_dir_skel'],$out);
	if (!$GLOBALS['svn_update_result'] = end($out))
		$GLOBALS['svn_update_result'] = 'erreur svn';
}

}
?>
