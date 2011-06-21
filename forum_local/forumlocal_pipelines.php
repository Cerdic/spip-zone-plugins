<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function forumlocal_pre_insertion($flux) {
	$conf_forumlocal = lire_config('forumlocal');
	if (($flux['args']['table'] == 'spip_articles') &&
		 ($conf_forumlocal['id_secteur'] > 0) &&
		 ($flux['data']['id_secteur'] == $conf_forumlocal['id_secteur'])) {
		$flux['data']['accepter_forum'] = $conf_forumlocal['type'];
	}
	return $flux;
}


?>