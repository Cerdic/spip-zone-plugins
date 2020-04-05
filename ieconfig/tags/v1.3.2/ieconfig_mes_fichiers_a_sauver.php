<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function ieconfig_mes_fichiers_a_sauver($flux) {
	$tmp_ieconfig = defined('_DIR_TMP') ? _DIR_TMP . 'ieconfig/' : _DIR_RACINE . 'tmp/ieconfig/';
	if (@is_dir($tmp_ieconfig)) {
		$flux[] = $tmp_ieconfig;
	}

	return $flux;
}
