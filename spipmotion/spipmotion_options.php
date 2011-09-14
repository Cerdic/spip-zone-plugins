<?php

if(!defined('_DIR_LIB_FLOT')){
	define('_DIR_LIB_FLOT','lib/flot');
}

if(!is_array($GLOBALS['spipmotion_metas'])){
	$inc_meta = charger_fonction('meta', 'inc');
	$inc_meta('spipmotion_metas');
}
?>
