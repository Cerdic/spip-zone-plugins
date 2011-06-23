<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function forumsectorise_pre_insertion($flux) {
	$conf_forumsectorise = lire_config('forumsectorise');
	if (($flux['args']['table'] == 'spip_articles') &&
		 ($conf_forumsectorise['id_secteur'] > 0) &&
		 ($flux['data']['id_secteur'] == $conf_forumsectorise['id_secteur'])) {
		$flux['data']['accepter_forum'] = $conf_forumsectorise['type'];
	}
	return $flux;
}


?>