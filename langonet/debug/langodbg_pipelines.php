<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function langodbg_declarer_operations_langonet($operations) {

	$operations['debug'] = array('debug_regexp');

	return $operations;
}
