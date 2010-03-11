<?php

 /**
 * Formulaire de cotation
 * Licence GPL
 * Auteur Mohamed DRIA med.dria@gmail.com
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function cotation_header_prive($flux){
	$flux .= "\n<script type=\"text/javascript\" src=\"".find_in_path('javascript/cotation_sortable.js', false)."\"></script>";
	return $flux;
}

?>
