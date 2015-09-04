<?php
/**
 * Plugin sociaux
 * Licence GPL3
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Inserer une CSS pour le contenu embed
 * @param $head
 * @return string
 */
function sociaux_insert_head_css($head){
  include_spip('inc/config');
 	if (lire_config('sociaux/css', 0)) {
      $head .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('css/sociaux.css').'" />'."\n";
	}
	return $head;
}


?>