<?php
/**
 * Utilisations de pipelines par Documentation technique
 *
 * @plugin     Documentation technique
 * @copyright  2013
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Doc_tech\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
	

function doc_tech_header_prive($flux){
	$flux .= '<link rel="stylesheet" href="' . _DIR_PLUGIN_DOC_TECH .'css/style_prive_doc_tech.css" type="text/css" media="all" />';
	return $flux;
}

?>