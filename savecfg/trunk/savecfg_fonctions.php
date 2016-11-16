<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function savecfg_afficher_tout($flux) {
	if (($flux['args']['exec'] == 'cfg' and _request('cfg')) or (strpos($flux['args']['exec'],
				'configurer_') !== false)
	) {
		include_spip('inc/presentation');
		$flux['data'] = recuperer_fond('prive/formulaires_savecfg') . recuperer_fond('prive/formulaire_savecfg_import') . $flux['data'];
	}

	return $flux;
}
