<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// TRAITEMENT PIPELINE :
//****************************************************
function image_cliquable_insert_head($texte)
{
	$texte .= '<script type="text/javascript" src="'.find_in_path('maphilight/jquery.maphilight.min.js').'"></script>'."\n";
	$texte .= '<script type="text/javascript">$(function() {$(".maphilight").maphilight(); });</script>'."\n";
	return $texte;


}


?>