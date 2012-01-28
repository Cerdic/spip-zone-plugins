<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function forumsectorise_pre_insertion($flux) {
	include_spip('inc/plugin');
	if(spip_version_compare($GLOBALS['spip_version_branche'],"2.1.99","<")) {
		$table = 'spip_articles' ;
	} else {
		$table = 'spip_article' ;
	}

	$conf_forumsectorise = lire_config('forumsectorise');
	if (($flux['args']['table'] == $table) &&
		 in_array($flux['data']['id_secteur'], $conf_forumsectorise['id_secteur'])) {
		$flux['data']['accepter_forum'] = $conf_forumsectorise['type'];
	}
	return $flux;
}


?>