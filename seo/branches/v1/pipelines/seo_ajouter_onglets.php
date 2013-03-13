<?php
/**
 * BouncingOrange SPIP SEO plugin
 *
 * @category   SEO
 * @package    SPIP_SEO
 * @author     Pierre ROUSSET (p.rousset@gmail.com)
 * @copyright  Copyright (c) 2009 BouncingOrange (http://www.bouncingorange.com)
 * @license    http://opensource.org/licenses/gpl-2.0.php  General Public License (GPL 2.0)
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function seo_ajouter_onglets($flux){

	global $connect_statut, $connect_toutes_rubriques;

	// seul les administrateurs globaux ont acces au bouton de configuration
	if ($connect_statut && $connect_toutes_rubriques){
		if ($flux['args']=='configuration'){
			$flux['data']['seo'] = new Bouton(_DIR_PLUGIN_SEO . "img_pack/seo-24.png", _T("seo:seo"), generer_url_ecrire('seo_config'));
		}
	}

	return ($flux);
}

?>
