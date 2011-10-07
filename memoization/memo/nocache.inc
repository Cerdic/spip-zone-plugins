<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

class MCacheBackend_nocache extends MCacheBackend {
	function size() { return 0; }
}

?>
