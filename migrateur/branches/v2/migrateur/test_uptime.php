<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Test uptime (ssh ou non)
**/
function migrateur_test_uptime() {
	$source = migrateur_source();
	$cmd = $source->commande('uptime');
	if ($cmd) {
		exec("$cmd 2>&1", $output, $err);
		migrateur_log(implode("\n", $output));
	}
}
