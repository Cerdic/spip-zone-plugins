<?php
/**
 * Utilisations de pipelines par Espace Privé en Admin Panel Bootstrap
 *
 * @plugin     Espace Privé en Admin Panel Bootstrap
 * @copyright  2017
 * @author     VisionInfo
 * @licence    GNU/GPL
 * @package    SPIP\Admin_panel\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function admin_panel_header_prive($flux){
       $flux = admin_panel_insert_head($flux);
       return $flux;
   }
    
   function admin_panel_insert_head($flux){
       $flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_ADMIN_PANEL .'prive/theme/admin_panel_style_prive.css" type="text/css" />';
       return $flux;
   }


