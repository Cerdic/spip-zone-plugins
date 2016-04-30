<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function vocabulaire_levenshtein_calculer($flux) {
	$get_lev = sql_allfetsel('mot', 'spip_vocabulaires', 'SOUNDEX(mot) = SOUNDEX('.sql_quote($flux['term']).')');
	$flux['mot'] = array_merge($flux['mot'], $get_lev);
	return $flux;
}
