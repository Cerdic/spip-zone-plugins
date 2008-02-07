<?php
// definition des constantes 1.9.3 pour les SPIP anterieurs
if (!defined('_DIR_LOG')){
	define('_DIR_LOG',defined('_DIR_TMP')?_DIR_TMP:_DIR_SESSION);
}
if (!defined('_FILE_LOG_SUFFIX')){
	define('_FILE_LOG_SUFFIX','.log');
}
if (!defined('_FILE_LOG')){
	define('_FILE_LOG','spip');
}
?>
