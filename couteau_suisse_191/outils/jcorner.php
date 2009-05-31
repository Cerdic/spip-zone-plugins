<?php
function jcorner_installe() {
cs_log("jcorner_installe()");
	if(!defined('_jcorner_CLASSES')) return;

	// on decode les balises entrees dans la config
	$classes = preg_split("/[\r\n]+/", trim(_jcorner_CLASSES));
	$code = array();
	foreach ($classes as $class) {
		list($class,) = explode('//', $class);
		if (preg_match('/^\s*([\'"]?)(.*?)\\1\s*=(.*)$/', $class, $regs)) {
			// forme avec commande jQuery
			$a = trim($regs[2]); $b = trim($regs[3]);
			$b = preg_match('/^(=*)\s*\.*(.*)$/', $b, $regs2)?trim($regs2[2]):'';
			$not = !strlen($regs2[1])?'.not(".jc_done").addClass("jc_done")':'';
			if(strlen($a) && strlen($b)) $code[] = "jQuery(\"$a\", this)$not.$b;";
		} elseif (preg_match('/^\s*([\'"]?)(.+)\\1\s*$/', $class, $regs)) {
			// forme simple avec coins arrondis
			$a = trim($regs[2]);
			if (strlen($a)) $code[] = "jQuery(\"$a\", this).not('.jc_done').addClass('jc_done').corner();";
		}
	}
	// sauvegarde en meta : le code jQuery
	ecrire_meta('cs_jcorner', join("\n\t", $code));
	ecrire_metas();
//print_r($classes); print_r($code);
}


function jcorner_insert_head($flux) {
	// le code jQuery est-il dispo ?
	if (!isset($GLOBALS['meta']['cs_jcorner'])) jcorner_installe();
	return $flux . "<script type=\"text/javascript\"><!--\nfunction jcorner_init() {\n\tif(typeof jQuery.fn.corner!='function') return;\n\t" . $GLOBALS['meta']['cs_jcorner'] . "\n}\n// --> </script>\n";
}

?>