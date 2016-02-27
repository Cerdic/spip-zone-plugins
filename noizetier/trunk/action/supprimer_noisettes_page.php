<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_supprimer_noisettes_page_dist($page = null) {
	if (is_null($page)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$page = $securiser_action();
	}

	if ($page) {
		include_spip('noizetier_fonctions');
		
		if (strpos($page, '|') !== false) {
			$page = explode('|', $page);
			$page = array('objet' => $page[0], 'id_objet' => $page[1]);
		}
		noizetier_supprimer_noisettes_page($page);
	}
}
