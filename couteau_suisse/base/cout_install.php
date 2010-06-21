<?php

function couteau_suisse_install($action){
if(defined('_LOG_CS')) cs_log("couteau_suisse_install($action)");
	include_spip('inc/meta');
	switch ($action){
		case 'test':
			// affichage d'un lien ici, puisque le pipeline 'affiche_gauche' n'est pas pris en compte dans 'admin_plugin'...
			if(!defined('_SPIP20100') && _request('exec') == 'admin_plugin') {
				if(!defined('_SPIP19300')) echo '<br />';
				include_spip('inc/presentation');
				echo debut_cadre_enfonce('', true),
					icone_horizontale(_T('couteau:titre'), generer_url_ecrire('admin_couteau_suisse'), find_in_path('img/couteau-24.gif'), '', false),
					fin_cadre_enfonce(true);
			}
			return isset($GLOBALS['meta']['tweaks_actifs']);
			break;
		case 'install':
			break;
		case 'uninstall':
			// effacement de toutes les metas du Couteau Suisse
			foreach(array_keys($GLOBALS['meta']) as $meta) {
				if(strpos($meta, 'tweaks_') === 0) effacer_meta($meta);
				if(strpos($meta, 'cs_') === 0) effacer_meta($meta);
			}
			ecrire_metas(); # Pour SPIP 1.92
			// effacement des repertoires temporaires
			include_spip('inc/getdocument');
			foreach(array(_DIR_CS_TMP, _DIR_VAR.'couteau-suisse') as $dir) 
				if(@file_exists($dir)) effacer_repertoire_temporaire($dir);
			// fichier RSS temporaire
			include_spip('cout_define');
			@unlink(_CS_TMP_RSS);
			// retrait de l'inclusion eventuelle dans config/mes_options.php
			include_spip('cout_utils');
			cs_verif_FILE_OPTIONS(false, true);
			break;
	}
}

function cout_install_pack($pack, $redirige=false) {
	global $metas_vars, $metas_outils;
	$pack = &$GLOBALS['cs_installer'][$pack];
	if(is_string($pack) && function_exists($pack)) $pack = $pack();
	effacer_meta('tweaks_actifs');
	$metas_vars = $metas_outils = array();
	foreach(preg_split('%\s*[,|]\s*%', $pack['outils']) as $o) $metas_outils[trim($o)]['actif'] = 1;
	if(is_array($pack['variables'])) foreach($pack['variables'] as $i=>$v) $metas_vars[$i] = $v;
	ecrire_meta('tweaks_actifs', serialize($metas_outils));
	ecrire_meta('tweaks_variables', serialize($metas_vars));
	// tout recompiler
	if($redirige) cout_exec_redirige();
}

// redirige vers la page exec en cours en vue une reinitialisation du Couteau Suisse
// si $arg==false alors la redirection ne se fera pas (procedure d'installation par exemple)
function cout_exec_redirige($arg='', $recompiler=true) {
	if($recompiler) {
		ecrire_metas();
		cs_initialisation(true);
		include_spip('inc/invalideur');
		suivre_invalideur("1"); # tout effacer
		purger_repertoire(_DIR_SKELS);
		purger_repertoire(_DIR_CACHE);
	}
	if($arg!==false) {
		include_spip('inc/headers');
		redirige_par_entete(generer_url_ecrire(_request('exec'), $arg, true));
	}
}

?>