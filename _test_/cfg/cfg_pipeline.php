<?php
function cfg_header_prive($texte) {
	$texte.= '<link rel="stylesheet" type="text/css" href="' . _DIR_PLUGIN_CFG . 'css/cfg.css" />' . "\n";
	return $texte;
}
?>
