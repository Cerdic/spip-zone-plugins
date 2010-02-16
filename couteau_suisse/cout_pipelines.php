<?php

if(!defined("_ECRIRE_INC_VERSION")) return;

// attention, ici il se peut que le plugin ne soit pas initialise (cas des .js/.css par exemple)
if(defined('_LOG_CS')) cs_log("inclusion de cout_pipelines.php");

// fonction d'erreur indispensable a tous les pipelines
function cs_deferr($f) {
	spip_log(_L("Pipeline CS : fonction '$f' non definie !"));
}

/***********
 * INSTALL *
 ***********/

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

/*********
 * PRIVE *
 *********/

// ajout d'un onglet sur la page de configuration de SPIP
function couteau_suisse_ajouter_onglets($flux){
	include_spip('inc/autoriser');
	$arg = $flux['args']=='configuration' || $flux['args']=='plugins';
	// si on est admin...
	if($arg && autoriser('configurer', 'cs'))
		$flux['data']['couteau_suisse']= new Bouton(find_in_path('img/couteau-24.gif'), _T('couteau:titre'), generer_url_ecrire('admin_couteau_suisse'));
	return $flux;
}

// ajout d'une icone sur la page de configuration des plugins
// ce code ne sert a rien puisque le pipeline 'affiche_gauche' n'est pas pris en compte dans 'admin_plugin'...
function couteau_suisse_affiche_gauche($flux){
/*
	if(_request('exec') == 'admin_plugin')
		$flux['data'] .= 
			icone_horizontale(_T('couteau:titre'), generer_url_ecrire('admin_couteau_suisse'), find_in_path('img/couteau-24.gif'), '', false);
*/
	global $cs_metas_pipelines;
	if(isset($cs_metas_pipelines['affiche_gauche']))
		eval($cs_metas_pipelines['affiche_gauche']);
	return $flux;
}
function couteau_suisse_affiche_droite($flux){
	global $cs_metas_pipelines;
	if(isset($cs_metas_pipelines['affiche_droite']))
		eval($cs_metas_pipelines['affiche_droite']);
	return $flux;
}
function couteau_suisse_affiche_milieu($flux){
	global $cs_metas_pipelines;
	if(isset($cs_metas_pipelines['affiche_milieu']))
		eval($cs_metas_pipelines['affiche_milieu']);
	return $flux;
}
function couteau_suisse_boite_infos($flux){
	global $cs_metas_pipelines;
	if(isset($cs_metas_pipelines['boite_infos']))
		eval($cs_metas_pipelines['boite_infos']);
	return $flux;
}
function couteau_suisse_pre_boucle($flux){
	global $cs_metas_pipelines;
	if(isset($cs_metas_pipelines['pre_boucle']))
		eval($cs_metas_pipelines['pre_boucle']);
	return $flux;
}

function couteau_suisse_header_prive($flux_){
	global $cs_metas_pipelines;
	$flux = '';
	if(isset($cs_metas_pipelines['header_prive']))
		eval($cs_metas_pipelines['header_prive']);
	if(isset($cs_metas_pipelines['header'])) {
		// si une compilation est necessaire...
		if(strpos($cs_metas_pipelines['header'], '<cs_html>')!==false) cs_compile_header();
		$flux .= $cs_metas_pipelines['header'];
	}
	$flux = strlen(trim($flux))
		?"\n<!-- Debut header du Couteau Suisse -->\n$flux<!-- Fin header du Couteau Suisse -->\n\n"
		:$flux =  "\n<!-- Rien dans les metas du Couteau Suisse -->\n\n";
	return $flux_.$flux;
}


/**********
 * PUBLIC *
 **********/

function couteau_suisse_affichage_final($flux){
	global $cs_metas_pipelines;
	if(isset($cs_metas_pipelines['affichage_final']))
		eval($cs_metas_pipelines['affichage_final']);
	// nettoyage des separateurs et differentes sentinelles
	return preg_replace(',<span class=\'csfoo \w+\'></span>,', '', $flux);
}

function couteau_suisse_insert_head($flux_){
	global $cs_metas_pipelines;
	$flux = '';
	if(isset($cs_metas_pipelines['insert_head']))
		eval($cs_metas_pipelines['insert_head']);
	if(isset($cs_metas_pipelines['header'])) {
		// si une compilation est necessaire...
		if(strpos($cs_metas_pipelines['header'], '<cs_html>')!==false) cs_compile_header();
 		$flux .= $cs_metas_pipelines['header'];
	}
	$flux = strlen(trim($flux))
		?"\n<!-- Debut header du Couteau Suisse -->\n$flux<!-- Fin header du Couteau Suisse -->\n\n"
		:$flux =  "\n<!-- Rien dans les metas du Couteau Suisse -->\n\n";
	return $flux_.$flux;
}

/********
 * TYPO *
 ********/

function couteau_suisse_nettoyer_raccourcis_typo($flux){
	global $cs_metas_pipelines;
	if(isset($cs_metas_pipelines['nettoyer_raccourcis_typo']))
		eval($cs_metas_pipelines['nettoyer_raccourcis_typo']);
	return $flux;
}

function couteau_suisse_pre_propre($flux){
	global $cs_metas_pipelines;
	if(isset($cs_metas_pipelines['pre_propre']))
		eval($cs_metas_pipelines['pre_propre']);
	return $flux;
}

function couteau_suisse_pre_typo($flux){
	global $cs_metas_pipelines;
	if(isset($cs_metas_pipelines['pre_typo']))
		eval($cs_metas_pipelines['pre_typo']);
	return $flux;
}

function couteau_suisse_post_propre($flux){
	global $cs_metas_pipelines;
	if(isset($cs_metas_pipelines['post_propre']))
		eval($cs_metas_pipelines['post_propre']);
	include_spip('cout_lancement');
	cs_trace_balises_html($flux);
	return $flux;
}

function couteau_suisse_post_typo($flux){
	global $cs_metas_pipelines;
	if(isset($cs_metas_pipelines['post_typo']))
		eval($cs_metas_pipelines['post_typo']);
	return $flux;
}

/********
 * BASE *
 *******/

function couteau_suisse_pre_edition($flux){
	global $cs_metas_pipelines;
	if(isset($cs_metas_pipelines['pre_edition']))
		eval($cs_metas_pipelines['pre_edition']);
	return $flux;
}

function couteau_suisse_post_edition($flux){
	global $cs_metas_pipelines;
	if(isset($cs_metas_pipelines['post_edition']))
		eval($cs_metas_pipelines['post_edition']);
	return $flux;
}

/**********
 * DIVERS *
 *********/

function couteau_suisse_creer_chaine_url($flux){
	global $cs_metas_pipelines;
	if(isset($cs_metas_pipelines['creer_chaine_url']))
		eval($cs_metas_pipelines['creer_chaine_url']);
	return $flux;
}

// le contenu du sous-menu est gere par les lames elles-memes
function couteau_suisse_bt_toolbox($params) {
	global $cs_metas_pipelines;
	if(!isset($cs_metas_pipelines['bt_toolbox'])) return $params;
	$flux = '';
	eval($cs_metas_pipelines['bt_toolbox']);
	$tableau_formulaire = '
 <table class="spip_barre" style="width: auto; padding: 1px!important; border-top: 0px;" summary="">'
	. str_replace(array('@@champ@@','@@span@@'), array($params['champ'], 'span style="vertical-align:75%;"'), $flux) . '
 </table>';
	$params['flux'] .= produceWharf('couteau_suisse', '', $tableau_formulaire);
	return $params;
}

// bouton principal du Couteau Suisse
function couteau_suisse_bt_gadgets($params) {
	global $cs_metas_pipelines;
	if(!isset($cs_metas_pipelines['bt_toolbox'])) return $params;
	$params['flux'] .= bouton_barre_racc("swap_couche('".$GLOBALS['numero_block']['couteau_suisse']."','');", _DIR_PLUGIN_COUTEAU_SUISSE."/img/couteau-24.gif", _T('couteauprive:raccourcis_barre'), $params['help']);
	return $params;
}

function couteau_suisse_porte_plume_barre_pre_charger($flux){
	global $cs_metas_pipelines;
	if (isset($cs_metas_pipelines['porte_plume_barre_pre_charger']))
		eval($cs_metas_pipelines['porte_plume_barre_pre_charger']);
	$barres = pipeline('porte_plume_cs_pre_charger', array());
	$r = array(
		"id" => 'couteau_suisse_drop',
		"name" => _T('couteau:pp_couteau_suisse_drop'),
		"className" => 'couteau_suisse_drop',
		"replaceWith" => '',
		"display" => true,
	);
	foreach($barres as $barre=>$menu) {
		$r["dropMenu"] = $menu;
		$flux[$barre]->ajouterApres('grpCaracteres', $r);
	}
	return $flux;
}

function couteau_suisse_porte_plume_lien_classe_vers_icone($flux){
	global $cs_metas_pipelines;
	if (isset($cs_metas_pipelines['porte_plume_lien_classe_vers_icone'])) {
		$flux['couteau_suisse_drop'] = 'couteau-19.png';
		// chemin des icones-typo de couleur
		_chemin(_chemin(sous_repertoire(_DIR_VAR, 'couteau-suisse')));
		eval($cs_metas_pipelines['porte_plume_lien_classe_vers_icone']);
	}
	return $flux;
}

// pipeline maison : bouton sous un drop Couteau Suisse
function couteau_suisse_porte_plume_cs_pre_charger($flux){
	global $cs_metas_pipelines;
	if (isset($cs_metas_pipelines['porte_plume_cs_pre_charger']))
		eval($cs_metas_pipelines['porte_plume_cs_pre_charger']);
	return $flux;
}

// pipeline maison : pre-affichage de la description d'un outil
// flux['outil'] est l'id de l'outil, $flux['actif'] est l'etat de l'outil, flux['texte'] est le texte de description
function couteau_suisse_pre_description_outil($flux) {
	global $cs_metas_pipelines;
	$id = &$flux['outil']; $texte = &$flux['texte'];
	if(isset($cs_metas_pipelines['pre_description_outil']))
		eval($cs_metas_pipelines['pre_description_outil']);
	return $flux;
}

// callback pour la fonction cs_compile_pipe()
function cs_compile_header_callback($matches) {
if(defined('_LOG_CS')) cs_log(" -- compilation d'un header. Code CSS : $matches[1]");
	return cs_recuperer_code($matches[1]);
}

// recherche et compilation par SPIP du contenu d'un fichier .html : <cs_html>contenu</cs_html>
function cs_compile_header() {
	global $cs_metas_pipelines;
//if(defined('_LOG_CS')) cs_log(" -- recherche de compilations CSS necessaires.");
	$cs_metas_pipelines['header'] = preg_replace_callback(',<cs_html>(.*)</cs_html>,Ums', 'cs_compile_header_callback', $cs_metas_pipelines['header']);
	// sauvegarde en metas !
	ecrire_meta('tweaks_pipelines', serialize($cs_metas_pipelines));
	ecrire_metas();
}

/**
 * recupere le resultat du calcul d'une compilation de code de squelette (marcimat)
 * $coucou = $this->recuperer_code('[(#AUTORISER{ok}|oui)coucou]');
 */
function cs_recuperer_code(&$code) {//, $contexte=array(), $options = array(), $connect=''){
	$fond = _DIR_CS_TMP . md5($code);
	$base = $fond . '.html';
	if(!file_exists($base) OR $GLOBALS['var_mode']=='recalcul')
		ecrire_fichier($base, $code);
	include_spip('public/assembler');
	$fond = str_replace('../', '', $fond);
//	return recuperer_fond($fond, array('fond'=>$fond));
	$f = inclure_page($fond, array('fond'=>$fond));
	return $f['texte'];
}


/*
if(defined('_LOG_CS')) cs_log("INIT : cout_pipelines, lgr=" . strlen($cs_metas_pipelines['pipelines']));
if(!$GLOBALS['cs_pipelines']) include_once(_DIR_CS_TMP.'pipelines.php');
if(defined('_LOG_CS')) cs_log(' -- sortie de cout_pipelines... cs_pipelines = ' . intval($GLOBALS['cs_pipelines']));
*/
?>