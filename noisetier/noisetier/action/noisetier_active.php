<?php

function action_noisetier_active_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	$arg = explode('-',$arg);
	$id_noisette = $arg[1];
	if ($id_noisette AND $arg[0]=='activer') 
		spip_query("UPDATE spip_noisettes SET actif='oui' WHERE id_noisette=$id_noisette");
	if ($id_noisette AND $arg[0]=='desactiver') 
		spip_query("UPDATE spip_noisettes SET actif='non' WHERE id_noisette=$id_noisette");
	
	$redirect = str_replace("&amp;","&",urldecode(_request('redirect')));
	if ($redirect==NULL) $redirect="";
	if ($redirect) $redirect = ancre_url($redirect,"noisette-$id_noisette");
	if ($redirect)
		redirige_par_entete(str_replace("&amp;","&",urldecode($redirect)));
}

?>