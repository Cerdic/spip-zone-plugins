<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function genie_maj__zotspip_dist($t) {
	include_spip('inc/zotspip');
	return zotspip_maj();
}

?>
