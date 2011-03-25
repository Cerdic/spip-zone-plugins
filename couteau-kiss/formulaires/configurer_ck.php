<?php
/*
 * Plugin Couteau Kiss
 * (c) 2010 Cedric Morin
 * Distribue sous licence GPL
 *
 */

include_spip('public/interfaces');
include_spip('inc/presenter_liste');

function ck_rtrim_dir($d){
	return rtrim($d,'/');
}
function ck_recupere_dossier_squelette($d,$raw=false){
	$squelettes = $d;
	$squelettes = explode(':',$squelettes);
	$squelettes = array_map('ck_rtrim_dir',$squelettes);
	$squelettes = array_diff($squelettes,array('squelettes',''));
	if ($raw)
		return implode(':',$squelettes);
	array_push($squelettes, 'squelettes');
	$squelettes = implode(':',$squelettes);
	return $squelettes;
}

/**
 * Produire le fichier PHP et une copie en meta
 *
 * @param string $code
 * @return string
 */
function ck_produire_options($code){
	// appliquer et verifier que ca ne plante pas !
	eval($code);

	// et enregistrer dans le fichier le cas echeant
	$file = _DIR_TMP."ck_options.php";
	ecrire_fichier($file, "<"."?php\n$code\n?>");

	// sauvegarder dans une meta pour recuperer les options apres vidage de tmp/
	include_spip('inc/meta');
	ecrire_meta('ck_options',$code);
	return $file;
}

/**
 * Verifier l'existence du fichier PHP, et sinon le restaurer avec la copie en meta
 */
function ck_verifier_options(){
	if (!file_exists($f=((defined('_ROOT_CWD')?_ROOT_CWD:'')._DIR_TMP."ck_options.php"))
	  AND isset($GLOBALS['meta']['ck_options'])) {
		// vider la meta auparavant, au cas ou le code php serait corrompu
		// si le code est valide, il sera remis dans la meta
		$code = $GLOBALS['meta']['ck_options'];
		include_spip('inc/meta');
		effacer_meta('ck_options');
		ck_produire_options($code);
	}
}

/**
 *
 * @return array
 */
function formulaires_configurer_ck_charger_dist(){
	ck_verifier_options();
	$valeurs = array(
		'dossier_squelettes' => ck_recupere_dossier_squelette($GLOBALS['dossier_squelettes']),
		'supprimer_numero' => preg_match(",supprimer_numero,",reset($GLOBALS['table_des_traitements']['TITRE']))?1:0,
		'toujours_paragrapher' => $GLOBALS['toujours_paragrapher']?1:0,
		'forcer_lang' => $GLOBALS['forcer_lang']?1:0,
		'no_set_html_base' => defined('_SET_HTML_BASE')?(_SET_HTML_BASE==false):0,
		'introduction_suite' => defined('_INTRODUCTION_SUITE')?_INTRODUCTION_SUITE:'',

		'cache_strategie' => (defined('_NO_CACHE') AND strlen(_NO_CACHE))?(_NO_CACHE==0?0:-1):'',
		'derniere_modif_invalide' => $GLOBALS['derniere_modif_invalide'],
		'cache_duree' => defined('_DUREE_CACHE_DEFAUT')?_DUREE_CACHE_DEFAUT:24*3600,
		'cache_duree_recherche' => defined('_DELAI_CACHE_resultats')?_DELAI_CACHE_resultats:600,
		'cache_taille' => $GLOBALS['quota_cache'],

		'image_seuil_document' => defined('_LARGEUR_MODE_IMAGE')?_LARGEUR_MODE_IMAGE:'',
		'logo_max_size' => _LOGO_MAX_SIZE?_LOGO_MAX_SIZE:'',
		'logo_max_width' => _LOGO_MAX_WIDTH?_LOGO_MAX_WIDTH:'',
		'logo_max_height' => _LOGO_MAX_HEIGHT?_LOGO_MAX_HEIGHT:'',
		'docs_max_size' => _DOC_MAX_SIZE?_DOC_MAX_SIZE:'',
		'imgs_max_size' => _IMG_MAX_SIZE?_IMG_MAX_SIZE:'',
		'imgs_max_width' => _IMG_MAX_WIDTH?_IMG_MAX_WIDTH:'',
		'imgs_max_height' => _IMG_MAX_HEIGHT?_IMG_MAX_HEIGHT:'',

		'longueur_login_mini' => _LOGIN_TROP_COURT+1,
		'nb_objets_tranches' => _TRANCHES,
		'compacte_head_ecrire' => defined('_INTERDIRE_COMPACTE_HEAD_ECRIRE')?(_INTERDIRE_COMPACTE_HEAD_ECRIRE?0:1):1,
		'inhiber_javascript_ecrire' => $GLOBALS['filtrer_javascript']==1?0:1,

	);
	return $valeurs;
}


function formulaires_configurer_ck_verifier_dist(){
	$erreurs = array();
	$cache_strategie = _request('cache_strategie');
	if ($cache_strategie!=-1){
		if (!$t = _request('cache_taille')
		  OR !$t=intval($t)
			OR $t<10){
			$erreurs['cache_taille'] = _T('ck:erreur_cache_taille_mini');
		}
	}
	return $erreurs;
}


function ck_code_constante($name,$value){
	return "if (!defined('$name')) define('$name',$value);\n";
}
function ck_code_globale($name,$value){
	return "\$GLOBALS['$name']=$value;\n";
}

function formulaires_configurer_ck_traiter_dist(){
	$code = "";

	// public
	if ($d = _request('dossier_squelettes')){
		$d = ck_recupere_dossier_squelette($d,true);
		$code .= ck_code_globale('dossier_squelettes',"'".addslashes($d)."'");
	}
	set_request('dossier_squelettes');

	if (_request('supprimer_numero')){
		$code .= "\$GLOBALS['table_des_traitements']['TITRE'][]= 'typo(supprimer_numero(%s), \"TYPO\", \$connect)';\n";
		$code .= "\$GLOBALS['table_des_traitements']['NOM'][]= 'typo(supprimer_numero(%s), \"TYPO\", \$connect)';\n";
	}

	$code .= ck_code_globale('toujours_paragrapher',_request('toujours_paragrapher')?'true':'false');
	$code .= ck_code_globale('forcer_lang',_request('forcer_lang')?'true':'false');
	if ($v = _request('no_set_html_base') OR !_SET_HTML_BASE){
		$code .= ck_code_constante('_SET_HTML_BASE',$v?'false':'true');
	}
	if (_request($s=_request('introduction_suite')))
		$code .= ck_code_constante('_INTRODUCTION_SUITE',"'".addslashes($s)."'");


	// cache
	if (strlen($c = _request('cache_strategie'))){
		if ($c==-1) $code .= "if (\$_SERVER['REQUEST_TIME']<".(time()+24*3600).") ";
		$code .= ck_code_constante('_NO_CACHE',intval($c));
	}
	$code .= ck_code_globale('derniere_modif_invalide',_request('derniere_modif_invalide')?'true':'false');
	$code .= ck_code_constante('_DUREE_CACHE_DEFAUT',intval(_request('cache_duree')));
	$code .= ck_code_constante('_DELAI_CACHE_resultats',intval(_request('cache_duree_recherche')));
	$code .= ck_code_globale('quota_cache',intval(_request('cache_taille')));


	// taille des logo et images
	if ($t = _request('image_seuil_document'))
		$code .= ck_code_constante('_LARGEUR_MODE_IMAGE',intval($t));
	if ($t = _request('logo_max_size'))
		$code .= ck_code_constante('_LOGO_MAX_SIZE',intval($t));
	if ($t = _request('logo_max_width'))
		$code .= ck_code_constante('_LOGO_MAX_WIDTH',intval($t));
	if ($t = _request('logo_max_height'))
		$code .= ck_code_constante('_LOGO_MAX_HEIGHT',intval($t));
	if ($t = _request('docs_max_size'))
		$code .= ck_code_constante('_DOC_MAX_SIZE',intval($t));
	if ($t = _request('imgs_max_size'))
		$code .= ck_code_constante('_IMG_MAX_SIZE',intval($t));
	if ($t = _request('imgs_max_width'))
		$code .= ck_code_constante('_IMG_MAX_WIDTH',intval($t));
	if ($t = _request('imgs_max_height'))
		$code .= ck_code_constante('_IMG_MAX_HEIGHT',intval($t));
	

	// ecrire
	if ($t = _request('longueur_login_mini'))
		$code .= ck_code_constante('_LOGIN_TROP_COURT',intval($t)-1);
	if ($t = _request('nb_objets_tranches'))
		$code .= ck_code_constante('_TRANCHES',intval($t));
	if (!$t = _request('compacte_head_ecrire'))
		$code .= ck_code_constante('_INTERDIRE_COMPACTE_HEAD_ECRIRE','true');
	if (!$t = _request('inhiber_javascript_ecrire'))
		$code .= ck_code_globale('filtrer_javascript',1);

	$file = ck_produire_options($code);

	$res = array('editable'=>true,'message_ok'=>_T('ck:message_ok',array('file'=>joli_repertoire($file))));
	return $res;
}


?>