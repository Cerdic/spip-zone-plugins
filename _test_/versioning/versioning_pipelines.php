<?php
function versioning_header_prive($texte) {
	$texte.= '<link rel="stylesheet" type="text/css" href="' . _DIR_PLUGIN_VERSIONING . 'css/versioning.css" />' . "\n";
	return $texte;
}

?>