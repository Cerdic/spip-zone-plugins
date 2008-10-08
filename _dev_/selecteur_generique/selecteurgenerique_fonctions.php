<?php

function selecteurgenerique_verifier_js($flux){
    if(strpos($flux,'autocomplete')===FALSE){
		$autocompleter = find_in_path('javascript/jquery.autocomplete.js');
		$autocompletecss = find_in_path('jquery.autocomplete.css');
		$flux .= "<script type='text/javascript' src='$autocompleter'></script>";
		$flux .= "<link rel='stylesheet' href='$autocompletecss' type='text/css' media='all' />";
	};
	return $flux;
}
?>