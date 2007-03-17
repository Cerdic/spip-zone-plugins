<?php
include_spip('tweak_spip');
/*
function tweak_spip_affiche_droite($flux){
	return tweak_pipeline('affiche_droite', $flux);
}
function tweak_spip_affiche_gauche($flux){
	return tweak_pipeline('affiche_gauche', $flux);
}
function tweak_spip_affiche_milieu($flux){
	return tweak_pipeline('affiche_milieu', $flux);
}
function tweak_spip_ajouter_boutons($flux){
	return tweak_pipeline('ajouter_boutons', $flux);
}
*/
function tweak_spip_ajouter_onglets($flux){
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) 
		if ($flux['args']=='configuration')
			$flux['data']['tweak_spip']= new Bouton("administration-24.gif", _T('tweak:titre'), generer_url_ecrire("tweak_spip_admin"));
	return $flux;
}
/*
function tweak_spip_body_prive($flux){
	return tweak_pipeline('body_prive', $flux);
}
function tweak_spip_exec_init($flux){
	return tweak_pipeline('exec_init', $flux);
}
*/
function tweak_spip_header_prive($flux){
	$flux .= "\n<!-- Debut header TWEAKS -->\n"
		. tweak_insert_header('css')
		. tweak_insert_header('js');
	global $tweaks_metas_pipes;
	if (isset($tweaks_metas_pipes['header_prive']))
		eval($tweaks_metas_pipes['header_prive']);
	return $flux."\n<!-- Fin header TWEAKS -->\n\n";
}

function tweak_spip_install($action){
tweak_log("tweak_spip_install($action)");
	include_spip('inc/meta');
	switch ($action){
		case 'test':
			return isset($GLOBALS['meta']['tweaks_actifs']);
			break;
		case 'install':
			break;
		case 'uninstall':
/*			foreach(array_keys($GLOBALS['meta']) as $meta) 
				if(strpos($meta, 'tweaks_') !== false) effacer_meta($meta);
			ecrire_metas();*/
			include_spip('inc/getdocument');
			effacer_repertoire_temporaire(_DIR_TMP."tweak-spip");
			break;
	}
}	


?>