<?php

function rang_auteurs_header_prive($flux) {
	$flux .= "\n<script type='text/javascript'>trad_deplacer_element = '" . texte_script(_T('rang_auteurs:deplacer_element')) . "'</script>\n";
	$js = find_in_path('javascript/ordonner_liens.js');
	$flux .= "\n<script type='text/javascript' src='$js'></script>\n";
	return $flux;
}