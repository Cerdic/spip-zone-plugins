<?php

if (!defined('_ECRIRE_INC_VERSION')) return;
# memoization minimale (preferer le plugin memoization)
function cache_get($key) {
	return @unserialize(file_get_contents(_DIR_CACHE.$key));
}
function cache_set($key, $value) {
	return ecrire_fichier(_DIR_CACHE.$key, serialize($value));
}

?>
