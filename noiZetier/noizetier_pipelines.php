<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function noizetier_header_prive($flux){
	$js = find_in_path('javascript/noizetier.js');
	$flux .= "\n<script type='text/javascript' src='$js'></script>\n";
	$css = generer_url_public('noizetier.css');
	$flux .= "\n<link rel='stylesheet' href='$css' type='text/css' />\n";
	return $flux;
}


/**
 * Pipeline recuperer_fond pour ajouter les noisettes
 *
 * @param array $flux
 * @return array
 */
function noizetier_recuperer_fond($flux){
	if (defined('_NOIZETIER_RECUPERER_FOND')?_NOIZETIER_RECUPERER_FOND:true) {
		include_spip('inc/noizetier');
		$fond = $flux['args']['fond'];
		$composition = $flux['args']['contexte']['composition'];
		// Si une composition est définie et si elle n'est pas déjà dans le fond, on l'ajoute au fond
		// sauf s'il s'agit d'une page de type page (les squelettes page.html assurant la redirection)
		if ($composition!='' AND noizetier_page_composition($fond)=='' AND noizetier_page_type($fond)!='page')
			$fond .= '-'.$composition;
		
		if (in_array($fond,noizetier_lister_blocs_avec_noisettes())) {
			$contexte = $flux['data']['contexte'];
			$contexte['bloc'] = substr($fond,0,strpos($fond,'/'));
			$complements = recuperer_fond('noizetier-generer-bloc',$contexte,array('raw'=>true));
			$flux['data']['texte'] .= $complements['texte'];
		}
	}
	return $flux;
}

/**
 * Pipeline compositions_lister_disponibles pour ajouter les compositions du noizetier
 *
 * @param array $flux
 * @return array
 */

function noizetier_compositions_lister_disponibles($flux){
	$noizetier_compositions = unserialize($GLOBALS['meta']['noizetier_compositions']);
	if (!is_array($noizetier_compositions))
		$noizetier_compositions = array();
	$type = $flux['args']['type'];
	$informer = $flux['args']['informer'];
	
	include_spip('inc/texte');
	foreach($noizetier_compositions as $t => $compos_type)
		foreach($compos_type as $c => $info_compo) {
			if($informer) {
				$noizetier_compositions[$t][$c]['nom'] = typo($info_compo['nom']);
				$noizetier_compositions[$t][$c]['description'] = propre($info_compo['description']);
				$noizetier_compositions[$t][$c]['icon'] = $info_compo['icon']!='' ? find_in_path($info_compo['icon']) : '';
			}
			else
				$noizetier_compositions[$t][$c] = 1;
			}
	
	if ($type=='' AND count($noizetier_compositions)>0) {
		if (!is_array($flux['data']))
			$flux['data'] = array();
		$flux['data'] = array_merge($flux['data'],$noizetier_compositions);
	}
	elseif (count($noizetier_compositions[$type])>0) {
		if (!is_array($flux['data'][$type]))
			$flux['data'][$type] = array();
		if (!is_array($noizetier_compositions[$type]))
			$noizetier_compositions[$type] = array();
		$flux['data'][$type] = array_merge($flux['data'][$type],$noizetier_compositions[$type]);
	}
	return $flux['data'];
}

/**
 * Pipeline styliser pour les compositions du noizetier de type page si celles-ci sont activées
 *
 * @param array $flux
 * @return array
 */
function noizetier_styliser($flux){
	if(defined('_NOIZETIER_COMPOSITIONS_TYPE_PAGE') AND _NOIZETIER_COMPOSITIONS_TYPE_PAGE) {
		$squelette = $flux['data'];
		$fond = $flux['args']['fond'];
		$ext = $flux['args']['ext'];
		// Si on n'a pas trouvé de squelette
		if (!$squelette) {
			$noizetier_compositions = unserialize($GLOBALS['meta']['noizetier_compositions']);
			// On vérifie qu'on n'a pas demandé une composition du noizetier de type page et qu'on appele ?page=type
			if (isset($noizetier_compositions['page'][$fond])) {
				$flux['data'] = substr(find_in_path("page.$ext"), 0, - strlen(".$ext"));
				$flux['args']['composition'] = $fond;
			}
		}
	}
	return $flux;
}

/**
 * Pipeline jqueryui_forcer pour demander au plugin l'insertion des scripts pour .sortable()
 *
 * @param array $plugins
 * @return array
 */
function noizetier_jqueryui_forcer($plugins){
	$plugins[] = "jquery.ui.core";
	$plugins[] = "jquery.ui.widget";
	$plugins[] = "jquery.ui.mouse";
	$plugins[] = "jquery.ui.sortable";
	return $plugins;
}


function noizetier_noizetier_lister_pages($flux){return $flux;}
function noizetier_noizetier_blocs_defaut($flux){return $flux;}
function noizetier_noizetier_config_export($flux){return $flux;}
function noizetier_noizetier_config_import($flux){return $flux;}

?>
