<?php
/**
 * Plugin Zeroclipboard
 * 
 * @package SPIP\Zeroclipboard\Pipelines
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline header_prive (SPIP)
 * 
 * On ajoute dans le head de l'espace privé le javascript calculé zeroclipboard.js.html
 * 
 * @param string $flux
 * 		Le contenu du head
 * @return string $flux
 * 		Le contenu du head modifié
 */
function zeroclipboard_header_prive($flux){
	$flux .= '
<script type="text/javascript" src="'.produire_fond_statique('zeroclipboard.js').'"></script>
';
	return $flux;
}

/**
 * Insertion dans le pipeline insert_head (SPIP)
 * 
 * On ajoute dans le contenu de la balise #INSERT_HEAD le javascript calculé zeroclipboard.js.html
 * 
 * @param string $flux
 * 		Le contenu de la balise
 * @return string $flux
 * 		Le contenu de la balise modifié
 */
function zeroclipboard_insert_head($flux){
	$flux .= '
<script type="text/javascript" src="'.produire_fond_statique('zeroclipboard.js',array('prive'=>'oui')).'"></script>
';
	return $flux;
}

/**
 * Insertion dans le pipeline jquery_plugins (SPIP)
 * 
 * On ajoute les deux js de zeroclipboard avec les autres plugins jQuery
 * 
 * @param array $plugins
 * 		Le tableau des plugins déjà présents
 * @return array $plugins
 * 		Le tableau des plugins modifié
 */
function zeroclipboard_jquery_plugins($plugins){
	if(!test_espace_prive())
		$plugins[] = _DIR_LIB_ZEROCLIPBOARD.'ZeroClipboard.js';
	else
		$plugins[] = str_replace(_DIR_RACINE,'',_DIR_LIB_ZEROCLIPBOARD).'ZeroClipboard.js';
	$plugins[] = 'javascript/spip_zeroclipboard.js';

	return $plugins;
}
?>