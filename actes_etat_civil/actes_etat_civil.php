<?php
function actes_etat_civil_inclure_css($flux) {
	if (find_in_path('css/actes_etat_civil.css')){
		$flux .= '<!-- plugin etat_civil -->'."\n";
		$flux .= '<link href="'.find_in_path('css/actes_etat_civil.css').'" rel="stylesheet" type="text/css" />'."\n";
	}
	return $flux;
}
?>