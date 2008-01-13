<?php
function IntertitresTdm_header_prive($texte) {
	$texte.= '<link rel="stylesheet" type="text/css" href="' . find_in_path('css/headers_prive.css') . '" />' . "\n";
	return $texte;
}
?>