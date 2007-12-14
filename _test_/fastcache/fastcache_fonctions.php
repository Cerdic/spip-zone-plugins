<?php

# balise #FASTCACHE pour noter les pages cachables
if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_FASTCACHE_dist($p) {
	$p->code = "'<'.'?php header(\"' . "
		. "'X-Fast-Cache: yes'"
		. " . '\"); ?'.'>'";
	$p->interdire_scripts = false;
	return $p;
}

?>
