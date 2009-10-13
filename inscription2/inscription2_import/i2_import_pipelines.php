<?php
   function i2_import_affiche_droite($flux){
   		include_spip('inc/autoriser');
    	if(
			((preg_match('/^inscription2/',$flux['args']['exec']))
			|| (preg_match('/^auteur/',$flux['args']['exec']))
			|| ((preg_match('/^i2_/',$flux['args']['exec'])) && ($flux['args']['exec'] != 'i2_import'))
			|| (($flux['args']['exec'] == 'cfg') && ((_request('cfg') == 'inscription2') || preg_match('/^i2_/',_request('cfg')))))
			&& autoriser('webmestre')
		){
	    	$flux['data'] .= recuperer_fond('prive/i2_import_importer',$flux['args']);
		}
		return $flux;
    }
?>