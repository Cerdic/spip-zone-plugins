<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function inscription_import_export_affiche_droite($flux) {
	include_spip('inc/autoriser');
	if (((preg_match('/^inscription3/', $flux['args']['exec']))
		or (preg_match('/^auteur/', $flux['args']['exec']))
		or ((preg_match('/^i3_/', $flux['args']['exec'])) and ($flux['args']['exec'] != 'inscription_import'))
		or (($flux['args']['exec'] == 'cfg')
			and ((_request('cfg') == 'inscription3') or preg_match('/^i2_/', _request('cfg')))))
		and autoriser('webmestre')
	) {
		$flux['data'] .= recuperer_fond('prive/inscription_import_importer', $flux['args']);
	}
	return $flux;
}
