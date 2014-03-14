<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function phpexcel_lancer_exemple($exemple) {

	sous_repertoire(_DIR_TMP, 'phpexcel');
	include_spip("lib/examples/$exemple");

}

?>