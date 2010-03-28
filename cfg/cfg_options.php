<?php

/**
 * Plugin générique de configuration pour SPIP
 *
 * @license    GNU/GPL
 * @package    plugins
 * @subpackage cfg
 * @category   outils
 * @copyright  (c) toggg, marcimat 2007-2008
 * @link       http://www.spip-contrib.net/
 * @version    $Id$
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


// Compatibilite 1.9.2
if (version_compare($GLOBALS['spip_version_code'],'1.9300','<'))
	include_spip('inc/compat_cfg');
	
// inclure les fonctions lire_config(), ecrire_config() et effacer_config()
include_spip('inc/cfg_config');

// signaler le pipeline de notification
$GLOBALS['spip_pipeline']['cfg_post_edition'] = "";
$GLOBALS['spip_pipeline']['editer_contenu_formulaire_cfg'] = "";



// droit du bouton d'amin aux webmestres
function autoriser_cfg_bouton($faire,$quoi,$id,$qui,$options) {
	// si on est admin
	return autoriser('configurer','cfg');
}

?>