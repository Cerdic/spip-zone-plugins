<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// attention, ici il se peut que le plugin ne soit pas initialise (cas des .js/.css par exemple)
// et donc, pas de fonction cs_log !!
if(defined('_LOG_CS')) spip_log("COUTEAU-SUISSE. inclusion de cout_pipelines.php");

/***********
 * INSTALL *
 ***********/

function couteau_suisse_install($action){
cs_log("couteau_suisse_install($action)");
	include_spip('inc/meta');
	switch ($action){
		case 'test':
			// affichage d'un lien ici, puisque le pipeline 'affiche_gauche' n'est pas pris en compte dans 'admin_plugin'...
			if (_request('exec') == 'admin_plugin') {
				debut_cadre_enfonce();
				echo icone_horizontale(_T('cout:titre'), generer_url_ecrire('admin_couteau_suisse'), find_in_path('img/couteau-24.gif'), '', true);
				fin_cadre_enfonce();
			}
			return isset($GLOBALS['meta']['tweaks_actifs']);
			break;
		case 'install':
			break;
		case 'uninstall':
			foreach(array_keys($GLOBALS['meta']) as $meta) {
				if(strpos($meta, 'tweaks_') === 0) effacer_meta($meta);
				if(strpos($meta, 'cs_') === 0) effacer_meta($meta);
			}
			if(!defined('_SPIP19300')) ecrire_metas();
			if (@file_exists(_DIR_CS_TMP)) {
				include_spip('inc/getdocument');
				effacer_repertoire_temporaire(_DIR_CS_TMP);
			}
			break;
	}
}

/*********
 * PRIVE *
 *********/

// ajout d'un onglet sur la page de configuration de SPIP
function couteau_suisse_ajouter_onglets($flux){
	// si on est admin...
	if ($flux['args']=='configuration' && autoriser('configurer'))
		$flux['data']['couteau_suisse']= new Bouton(find_in_path('img/couteau-24.gif'), _T('cout:titre'), generer_url_ecrire('admin_couteau_suisse'));
	return $flux;
}

// ajout d'une icone sur la page de configuration des plugins
// ce code ne sert à rien puisque le pipeline 'affiche_gauche' n'est pas pris en compte dans 'admin_plugin'...
function couteau_suisse_affiche_gauche($flux){
/*
	if (_request('exec') == 'admin_plugin')
		$flux['data'] .= 
			icone_horizontale(_T('cout:titre'), generer_url_ecrire('admin_couteau_suisse'), find_in_path('img/couteau-24.gif'), '', true);
*/
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
/*
cs_log("appel de cout_pipelines : strlen=" . strlen($cs_metas_pipelines['pipelines']));
if (!$GLOBALS['cs_pipelines']) include_once(_DIR_CS_TMP.'pipelines.php');
cs_log(' -- appel cout_pipelines achevé... cs_pipelines = ' . intval($GLOBALS['cs_pipelines']));
*/
?>