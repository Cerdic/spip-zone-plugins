<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// compatibilite SPIP 1.91
if (!defined('_DIR_PLUGIN_COUTEAU_SUISSE')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_COUTEAU_SUISSE',(_DIR_PLUGINS.end($p)));
}

// attention, ici il se peut que le plugin ne soit pas initialise (cas des .js/.css par exemple)
cs_log("inclusion de cout_pipelines.php");

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
				if(!defined('_SPIP19300')) echo '<br/>';
				echo debut_cadre_enfonce('', true),
					icone_horizontale(_T('cout:titre'), generer_url_ecrire('admin_couteau_suisse'), find_in_path('img/couteau-24.gif'), '', false),
					fin_cadre_enfonce(true);
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
			ecrire_metas();
			if (@file_exists(_DIR_CS_TMP)) {
				include_spip('inc/getdocument');
				effacer_repertoire_temporaire(_DIR_CS_TMP);
			}
			@unlink(_DIR_RSS_TMP);
			break;
	}
}

/*********
 * PRIVE *
 *********/

// ajout d'un onglet sur la page de configuration de SPIP
function couteau_suisse_ajouter_onglets($flux){
	// si on est admin...
	if ($flux['args']=='configuration' && cout_autoriser())
		$flux['data']['couteau_suisse']= new Bouton(find_in_path('img/couteau-24.gif'), _T('cout:titre'), generer_url_ecrire('admin_couteau_suisse'));
	return $flux;
}

// ajout d'une icone sur la page de configuration des plugins
// ce code ne sert a rien puisque le pipeline 'affiche_gauche' n'est pas pris en compte dans 'admin_plugin'...
function couteau_suisse_affiche_gauche($flux){
/*
	if (_request('exec') == 'admin_plugin')
		$flux['data'] .= 
			icone_horizontale(_T('cout:titre'), generer_url_ecrire('admin_couteau_suisse'), find_in_path('img/couteau-24.gif'), '', false);
*/
	return $flux;
}
function couteau_suisse_affiche_droite($flux){
	global $cs_metas_pipelines;
	if (isset($cs_metas_pipelines['affiche_droite']))
		eval($cs_metas_pipelines['affiche_droite']);
	return $flux;
}
function couteau_suisse_affiche_milieu($flux){
	global $cs_metas_pipelines;
	if (isset($cs_metas_pipelines['affiche_milieu']))
		eval($cs_metas_pipelines['affiche_milieu']);
	return $flux;
}

function couteau_suisse_header_prive($flux){
	global $cs_metas_pipelines;
	if (isset($cs_metas_pipelines['header_prive']))
		eval($cs_metas_pipelines['header_prive']);
	if (isset($cs_metas_pipelines['header']))
		$flux .= "\n<!-- Debut header du Couteau Suisse -->\n"
			. join("\n", $cs_metas_pipelines['header'])
			. "\n<!-- Fin header du Couteau Suisse -->\n\n";
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

// le contenu du sous-menu est gere par les lames elles-memes
function couteau_suisse_BT_toolbox($params) {
	global $cs_metas_pipelines;
	if (!isset($cs_metas_pipelines['BT_toolbox'])) return $params;
	$flux = '';
	eval($cs_metas_pipelines['BT_toolbox']);
	$tableau_formulaire = '
 <table class="spip_barre" style="width: auto; padding: 1px!important; border-top: 0px;" summary="">'
	. str_replace(array('@@champ@@','@@span@@'), array($params['champ'], 'span style="vertical-align:75%;"'), $flux) . '
 </table>';
	$params['flux'] .= produceWharf('couteau_suisse', '', $tableau_formulaire);
	return $params;
}

// bouton principal du Couteau Suisse
function couteau_suisse_BT_gadgets($params) {
	global $cs_metas_pipelines;
	if (!isset($cs_metas_pipelines['BT_toolbox'])) return $params;
	$params['flux'] .= bouton_barre_racc("swap_couche('".$GLOBALS['numero_block']['couteau_suisse']."','');", _DIR_PLUGIN_COUTEAU_SUISSE."/img/couteau-24.gif", _T('desc:raccourcis_barre'), $params['help']);
	return $params;
}

/*
cs_log("INIT : cout_pipelines, lgr=" . strlen($cs_metas_pipelines['pipelines']));
if (!$GLOBALS['cs_pipelines']) include_once(_DIR_CS_TMP.'pipelines.php');
cs_log(' -- sortie de cout_pipelines... cs_pipelines = ' . intval($GLOBALS['cs_pipelines']));
*/
?>