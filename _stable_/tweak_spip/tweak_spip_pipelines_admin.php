<?php
include_spip('cout_lancement');

function tweak_spip_ajouter_onglets($flux){
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) 
		if ($flux['args']=='configuration')
			$flux['data']['tweak_spip']= new Bouton(find_in_path('img/couteau-24.gif'), _T('cout:titre'), generer_url_ecrire("tweak_spip_admin"));
	return $flux;
}

function tweak_spip_header_prive($flux){
	global $cout_metas_pipelines;
	if (isset($cout_metas_pipelines['header_prive']))
		eval($cout_metas_pipelines['header_prive']);
	if (isset($cout_metas_pipelines['header']))
		$flux .= "\n<!-- Debut header du Couteau Suisse -->\n" . join("\n", $cout_metas_pipelines['header']) . "\n<!-- Fin header du Couteau Suisse -->\n\n";
		else $flux .= "\n<!-- Rien pour le Couteau Suisse -->\n";
	return $flux;
}

function tweak_spip_install($action){
cout_log("tweak_spip_install($action)");
	include_spip('inc/meta');
	switch ($action){
		case 'test':
			return isset($GLOBALS['meta']['tweaks_actifs']);
			break;
		case 'install':
			break;
		case 'uninstall':
			foreach(array_keys($GLOBALS['meta']) as $meta) 
				if(strpos($meta, 'tweaks_') === 0) effacer_meta($meta);
			ecrire_metas();
			if (@file_exists($f=sous_repertoire(_DIR_TMP, "tweak-spip"))) {
				include_spip('inc/getdocument');
				effacer_repertoire_temporaire($f);
			}
			break;
	}
}	


?>