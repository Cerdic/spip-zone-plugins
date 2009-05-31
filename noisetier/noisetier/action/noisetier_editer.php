<?php

function action_noisetier_editer_dist() {
	include_spip('inc/filtres');
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	$arg = explode('-',$arg);
	$id_noisette = $arg[1];
	if ($id_noisette AND $arg[0]=='texte') {
		$titre = _q(corriger_caracteres(_request('titre')));
		$descriptif = _q(corriger_caracteres(_request('descriptif')));
		spip_query("UPDATE spip_noisettes SET titre=$titre, descriptif=$descriptif WHERE id_noisette=$id_noisette");
	}

	$redirect = str_replace("&amp;","&",urldecode(_request('redirect')));
	if ($redirect==NULL) $redirect="";
	if ($redirect)
		redirige_par_entete(str_replace("&amp;","&",urldecode($redirect)));
}

?>