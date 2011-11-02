<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

$config = @unserialize($GLOBALS['meta']['del2seen']);

define('_SEENTHIS_LOGIN', $config['login']);
define('_SEENTHIS_PASS', $config['pass']);

?>