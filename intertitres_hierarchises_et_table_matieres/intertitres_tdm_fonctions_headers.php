<?php
function IntertitresTdm_header_prive($texte) {
	$texte.= '<link rel="stylesheet" type="text/css" href="' . find_in_path('css/intertitres_prives.css') . '" />' . "\n";
	return $texte;
}
function IntertitresTdm_insert_head($texte) {
	$texte.= '<link rel="stylesheet" type="text/css" href="' . find_in_path('css/intertitres_publics.css') . '" />' . "\n";
	return $texte;
}
?>