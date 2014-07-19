<?php
/**
 * Utilisations de pipelines par Requêteur SQL
 *
 * @plugin     Requêteur SQL
 * @copyright  2014
 * @author     David Dorchies
 * @licence    GNU/GPL
 * @package    SPIP\Requeteursql\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
	

function requeteursql_header_prive($texte){
	$texte .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('prive/requeteursql.css').'" media="all" />'."\n";
	return $texte;
}

?>
