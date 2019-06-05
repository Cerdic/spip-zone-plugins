<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

// Cette balise renvoie le tableau des membres definis dans la config du plugin HCERES
function balise_AERES_MEMBRES_dist($p) {
		$p->code = "aeres_lister_membres()";
	return $p;
}

function aeres_lister_membres() {
	static $ret = NULL;
	if (is_null($ret)) {
		if (isset($GLOBALS['meta']['aeres'])) {
			$config = unserialize($GLOBALS['meta']['aeres']);
			$ret = (isset($config['membres'])) ? explode(';',$config['membres']) : array();
		}
		else
			$ret = array();
	}
	return $ret;
}

