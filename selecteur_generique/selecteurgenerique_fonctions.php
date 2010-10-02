<?php

function selecteurgenerique_verifier_js($flux){
	$contenu = "";
    if(strpos($flux,'jquery.autocomplete.js')===FALSE){
		$autocompleter = find_in_path('javascript/jquery.autocomplete.js');
		$autocompletecss = find_in_path('iautocompleter.css');
		$contenu .= "
<script type='text/javascript' src='$autocompleter'></script>
<link rel='stylesheet' href='$autocompletecss' type='text/css' media='all' />
";
	};
	return $contenu;
}
?>