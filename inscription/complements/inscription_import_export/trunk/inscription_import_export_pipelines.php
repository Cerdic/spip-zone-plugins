<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function inscription_import_export_affiche_droite($flux){
	include_spip('inc/autoriser');
	if(
		((preg_match('/^inscription3/',$flux['args']['exec']))
		|| (preg_match('/^auteur/',$flux['args']['exec']))
		|| ((preg_match('/^i3_/',$flux['args']['exec'])) && ($flux['args']['exec'] != 'inscription_import'))
		|| (($flux['args']['exec'] == 'cfg') && ((_request('cfg') == 'inscription3') || preg_match('/^i2_/',_request('cfg')))))
		&& autoriser('webmestre')
	){
		$flux['data'] .= recuperer_fond('prive/inscription_import_importer',$flux['args']);
	}
	return $flux;
}
?>