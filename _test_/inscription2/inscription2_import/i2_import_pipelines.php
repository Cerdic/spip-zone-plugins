<?php
   function i2_import_affiche_droite($flux){
    	if(
			(preg_match('/^inscription2/',$flux['args']['exec']))
			|| (preg_match('/^auteurs/',$flux['args']['exec']))
			|| ((preg_match('/^i2_/',$flux['args']['exec'])) && ($flux['args']['exec'] != 'i2_import'))
			|| (($flux['args']['exec'] == 'cfg') && ((_request('cfg') == 'inscription2') || preg_match('/^i2_/',_request('cfg'))))
		){
	    	$flux['data'] .= recuperer_fond('prive/i2_import_importer');
		}
		return $flux;
    }
?>