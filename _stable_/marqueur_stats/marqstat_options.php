<?php

$GLOBALS['spip_pipelines']['insert_body'] = '';

function marqstat_affichage_final($texte){
	global $html;
	if (!isset($GLOBALS['meta']['marqstat_flag_insert_body'])
		AND $html) {
		include_spip('marqstat_fonctions');
		$code = marqstat_get_code();
		if (strlen($code))
			$texte=preg_replace(",(</body>),i","$code\n</body>",$texte);
	}
	return $texte;
}

?>