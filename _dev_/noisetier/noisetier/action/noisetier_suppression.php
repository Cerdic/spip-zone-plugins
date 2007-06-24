<?php

function action_noisetier_suppression_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	$arg = explode('-',$arg);
	$id_noisette = $arg[1];
	if ($id_noisette AND $arg[0]=='suppression') 
		spip_query("DELETE FROM spip_noisettes WHERE id_noisette=$id_noisette");

	$redirect = str_replace("&amp;","&",urldecode(_request('redirect')));
	if ($redirect==NULL) $redirect="";
	if ($redirect)
		redirige_par_entete(str_replace("&amp;","&",urldecode($redirect)));
}

?>