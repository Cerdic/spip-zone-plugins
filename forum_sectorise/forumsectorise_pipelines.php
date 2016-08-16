<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function forumsectorise_pre_insertion($flux) {
	include_spip('inc/plugin');
	include_spip('inc/config');

	$conf_forumsectorise = lire_config('forumsectorise');

	if (($flux['args']['table'] == table_objet_sql('article')) &&
		 in_array($flux['data']['id_secteur'], $conf_forumsectorise['ident_secteur'])) {
		$flux['data']['accepter_forum'] = $conf_forumsectorise['type'];
	}
	return $flux;
}


?>