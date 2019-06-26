<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function visiteurs_connectes_compter() {
	return count(preg_files(_DIR_TMP.'visites/', '.', 100000));
}
