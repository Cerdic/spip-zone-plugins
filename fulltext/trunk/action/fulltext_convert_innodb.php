<?php
/**
 * Plugin Fulltext
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_fulltext_convert_innodb_dist($table = null) {
	$fulltext_convert_engine = charger_fonction('fulltext_convert_engine', 'action');
	$fulltext_convert_engine($table, 'innodb');
}
