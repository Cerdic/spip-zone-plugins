<?php
function jq_innerfade_inclure_headers($flux) {
	$flux .= '<!-- plugin innerfade -->'."\n";
	$flux .= '<script type="text/javascript" src="'.find_in_path('js/jquery.innerfade.js').'"></script>'."\n";
	return $flux;
}
?>