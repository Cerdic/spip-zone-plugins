<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

$cfg = @unserialize($GLOBALS['meta']['memoization']);
if ($cfg['pages'] == 'file')
	require_once _DIR_RESTREINT.'public/cacher.php';
else
	require_once dirname(__FILE__).'/cacher-memo.php';


?>
