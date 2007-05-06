<?php
include_spip('cout_lancement');

/***********
 * INSTALL *
 ***********/

function couteau_suisse_install($action){
cs_log("couteau_suisse_install($action)");
	include_spip('inc/meta');
	switch ($action){
		case 'test':
			return isset($GLOBALS['meta']['tweaks_actifs']);
			break;
		case 'install':
			break;
		case 'uninstall':
			foreach(array_keys($GLOBALS['meta']) as $meta) {
				if(strpos($meta, 'tweaks_') === 0) effacer_meta($meta);
				if(strpos($meta, 'cs_') === 0) effacer_meta($meta);
			}
			ecrire_metas();
			if (@file_exists($f=sous_repertoire(_DIR_TMP, "couteau-suisse"))) {
				include_spip('inc/getdocument');
				effacer_repertoire_temporaire($f);
			}
			break;
	}
}

/*********
 * PRIVE *
 *********/

function couteau_suisse_ajouter_onglets($flux){
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"])
		if ($flux['args']=='configuration')
			$flux['data']['couteau_suisse']= new Bouton(find_in_path('img/couteau-24.gif'), _T('cout:titre'), generer_url_ecrire("admin_couteau_suisse"));
	return $flux;
}

function couteau_suisse_header_prive($flux){
	global $cs_metas_pipelines;
	if (isset($cs_metas_pipelines['header_prive']))
		eval($cs_metas_pipelines['header_prive']);
	if (isset($cs_metas_pipelines['header']))
		$flux .= "\n<!-- Debut header du Couteau Suisse -->\n" . join("\n", $cs_metas_pipelines['header']) . "\n<!-- Fin header du Couteau Suisse -->\n\n";
		else $flux .= "\n<!-- Rien pour le Couteau Suisse -->\n";
	return $flux;
}

/**********
 * PUBLIC *
 **********/

function couteau_suisse_affichage_final($flux){
	global $cs_metas_pipelines;
	if (isset($cs_metas_pipelines['affichage_final']))
		eval($cs_metas_pipelines['affichage_final']);
	return $flux;
}

function couteau_suisse_insert_head($flux){
	global $cs_metas_pipelines;
	if (isset($cs_metas_pipelines['insert_head']))
		eval($cs_metas_pipelines['insert_head']);
	if (isset($cs_metas_pipelines['header']))
		$flux .=  "\n<!-- Debut header du Couteau Suisse -->\n" . join("\n", $cs_metas_pipelines['header']) . "<!-- Fin header du Couteau Suisse -->\n\n";
		else $flux .=  "\n<!-- Rien pour le Couteau Suisse -->\n";
	return $flux;
}

/********
 * TYPO *
 ********/

function couteau_suisse_nettoyer_raccourcis_typo($flux){
	global $cs_metas_pipelines;
	if (isset($cs_metas_pipelines['nettoyer_raccourcis_typo']))
		eval($cs_metas_pipelines['nettoyer_raccourcis_typo']);
	return $flux;
}

function couteau_suisse_pre_propre($flux){
	global $cs_metas_pipelines;
	if (isset($cs_metas_pipelines['pre_propre']))
		eval($cs_metas_pipelines['pre_propre']);
	return $flux;
}

function couteau_suisse_pre_typo($flux){
	global $cs_metas_pipelines;
	if (isset($cs_metas_pipelines['pre_typo']))
		eval($cs_metas_pipelines['pre_typo']);
	return $flux;
}

function couteau_suisse_post_propre($flux){
	global $cs_metas_pipelines;
	if (isset($cs_metas_pipelines['post_propre']))
		eval($cs_metas_pipelines['post_propre']);
	return $flux;
}

function couteau_suisse_post_typo($flux){
	global $cs_metas_pipelines;
	if (isset($cs_metas_pipelines['post_typo']))
		eval($cs_metas_pipelines['post_typo']);
	return $flux;
}

?>