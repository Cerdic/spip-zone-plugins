<?php
/*
 * Plugin Couteau Kiss
 * (c) 2010 Cedric Morin
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

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
	$squelettes = array_unique($squelettes);
	if (isset($GLOBALS['dossier_squelettes_sav'])){
		$squelettes = array_diff($squelettes,explode(":",$GLOBALS['dossier_squelettes_sav']));
	}
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
function ck_verifier_options($init = false){
	if (!file_exists($f=((defined('_ROOT_CWD')?_ROOT_CWD:'')._DIR_TMP."ck_options.php"))
	  AND
		($init OR isset($GLOBALS['meta']['ck_options']))) {
		// vider la meta auparavant, au cas ou le code php serait corrompu
		// si le code est valide, il sera remis dans la meta
		if (isset($GLOBALS['meta']['ck_options'])){
			$code = $GLOBALS['meta']['ck_options'];
			include_spip('inc/meta');
			effacer_meta('ck_options');
		}
		elseif($init){
			$c = formulaires_configurer_ck_charger_dist();
			$code = ck_produire_code($c);
		}
		ck_produire_options($code);
	}
}

/**
 * Produire le code a partir d'un tableau ou des requests
 * @param array|null $c
 * @return string
 */
function ck_produire_code($c=null){
	$code = "";
	// backuper un eventuel dossier_squelettes en dur pour ne pas le doublonner dans la config
	$code .= "if (isset(\$GLOBALS['dossier_squelettes'])) \$GLOBALS['dossier_squelettes_sav']=\$GLOBALS['dossier_squelettes'];\n";
	if ($d = _request('dossier_squelettes',$c)){
		$d = ck_recupere_dossier_squelette($d,true);
		// ne pas ecraser la globale avec une valeur vide, ca ne sert a rien
		// de plus on concatene
		if ($d){
			$value = addslashes($d);
			$code .= "\$GLOBALS['dossier_squelettes'] = (isset(\$GLOBALS['dossier_squelettes'])?rtrim(\$GLOBALS['dossier_squelettes'],':').':':'').'$value';\n";
		}
	}

	// pas la peine en SPIP 3 : c'est pas defaut
	if (intval($GLOBALS['spip_version_branche'])<3
		AND _request('supprimer_numero',$c)){
		$code .= "\$GLOBALS['table_des_traitements']['TITRE'][]= 'typo(supprimer_numero(%s), \"TYPO\", \$connect)';\n";
		$code .= "\$GLOBALS['table_des_traitements']['NOM'][]= 'typo(supprimer_numero(%s), \"TYPO\", \$connect)';\n";
	}

	$code .= ck_code_globale('toujours_paragrapher',_request('toujours_paragrapher',$c)?'true':'false');

	// on ne surcharge autobr uniquement si inhibe
	if(_request('no_autobr',$c))
		$code .= ck_code_constante('_AUTOBR',"''");

	$code .= ck_code_globale('forcer_lang',_request('forcer_lang',$c)?'true':'false');
	if ($v = _request('no_set_html_base',$c) OR !_SET_HTML_BASE){
		$code .= ck_code_constante('_SET_HTML_BASE',$v?'false':'true');
	}
	if (_request($s=_request('introduction_suite',$c)))
		$code .= ck_code_constante('_INTRODUCTION_SUITE',"'".addslashes($s)."'");


	// cache
	if (strlen($cs = _request('cache_strategie',$c))){
		if ($cs==-1) $code .= "if (\$_SERVER['REQUEST_TIME']<".(time()+24*3600).") ";
		$code .= ck_code_constante('_NO_CACHE',intval($cs));
	}

	$code .= ck_code_globale('derniere_modif_invalide',_request('derniere_modif_invalide',$c)?'true':'false');
	$code .= ck_code_constante('_DUREE_CACHE_DEFAUT',intval(_request('cache_duree',$c)));
	$code .= ck_code_constante('_DELAI_CACHE_resultats',intval(_request('cache_duree_recherche',$c)));
	$code .= ck_code_globale('quota_cache',intval(_request('cache_taille',$c)));


	// taille des logo et images
	if ($t = _request('image_seuil_document',$c))
		$code .= ck_code_constante('_LARGEUR_MODE_IMAGE',intval($t));
	if ($t = _request('logo_max_size',$c))
		$code .= ck_code_constante('_LOGO_MAX_SIZE',intval($t));
	if ($t = _request('logo_max_width',$c))
		$code .= ck_code_constante('_LOGO_MAX_WIDTH',intval($t));
	if ($t = _request('logo_max_height',$c))
		$code .= ck_code_constante('_LOGO_MAX_HEIGHT',intval($t));
	if ($t = _request('docs_max_size',$c))
		$code .= ck_code_constante('_DOC_MAX_SIZE',intval($t));
	if ($t = _request('imgs_max_size',$c))
		$code .= ck_code_constante('_IMG_MAX_SIZE',intval($t));
	if ($t = _request('imgs_max_width',$c))
		$code .= ck_code_constante('_IMG_MAX_WIDTH',intval($t));
	if ($t = _request('imgs_max_height',$c))
		$code .= ck_code_constante('_IMG_MAX_HEIGHT',intval($t));


	// ecrire
	if ($t = _request('longueur_login_mini',$c))
		$code .= ck_code_constante('_LOGIN_TROP_COURT',intval($t)-1);
	if ($t = _request('nb_objets_tranches',$c))
		$code .= ck_code_constante('_TRANCHES',intval($t));
	if (!$t = _request('compacte_head_ecrire',$c))
		$code .= ck_code_constante('_INTERDIRE_COMPACTE_HEAD_ECRIRE','true');
	if (!$t = _request('inhiber_javascript_ecrire',$c))
		$code .= ck_code_globale('filtrer_javascript',1);

	return $code;
}

/**
 *
 * @return array
 */
function formulaires_configurer_ck_charger_dist(){
	ck_verifier_options();
	$valeurs = array(
		'_dossier_squelettes_sav' => isset($GLOBALS['dossier_squelettes_sav'])?$GLOBALS['dossier_squelettes_sav']:'',
		'dossier_squelettes' => ck_recupere_dossier_squelette($GLOBALS['dossier_squelettes']),
		'supprimer_numero' => preg_match(",supprimer_numero,",reset($GLOBALS['table_des_traitements']['TITRE']))?1:0,
		'toujours_paragrapher' => $GLOBALS['toujours_paragrapher']?1:0,
		'forcer_lang' => $GLOBALS['forcer_lang']?1:0,
		'no_set_html_base' => defined('_SET_HTML_BASE')?(_SET_HTML_BASE==false):0,
		'introduction_suite' => defined('_INTRODUCTION_SUITE')?_INTRODUCTION_SUITE:'',
		'no_autobr' => defined('_AUTOBR')?(_AUTOBR?false:true):false,

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
	if ($d = _request('dossier_squelettes')){
		$d = explode(":",$d);
		foreach($d as $s){
			$s = trim($s);
			if (strncmp($s,"/",1)==0 OR strpos($s,"../")!==false)
				$erreurs['dossier_squelettes'] = _T('ck:erreur_dossier_squelette_invalide');
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

	$code = ck_produire_code();
	$file = ck_produire_options($code);

	// on relance le hit via un refuser
	refuser_traiter_formulaire_ajax();

	// ne pas reinjecter dans la saisie
	set_request('dossier_squelettes');

	$res = array('editable'=>true,'message_ok'=>_T('ck:message_ok',array('file'=>joli_repertoire($file))));
	return $res;
}


?>