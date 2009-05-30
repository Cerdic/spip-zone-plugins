<?php
/**
 * 
 * Insertion dans le pipeline i2_cfg_form
 * @return 
 * @param object $flux
 */
function i2_societes_i2_cfg_form($flux){
	$flux .= recuperer_fond('fonds/inscription2_societes');
	return $flux;
}

/**
 * 
 * Insertion dans le pipeline affiche_droite
 * Dans certaines pages définies, afficher le lien d'accès à la page des comptes utilisateurs
 * 
 * @return array Le même tableau qu'il reçoit en argument 
 * @param array $flux Un tableau donnant des informations sur le contenu passé au pipeline
 */

function i2_societes_affiche_droite($flux){
	if(((preg_match('/^inscription2/',$flux['args']['exec']))
		 || (preg_match('/^auteurs/',$flux['args']['exec']))
		 || (preg_match('/^i2_/',$flux['args']['exec']))
		 || (($flux['args']['exec'] == 'cfg') && ((_request('cfg') == 'inscription2') || preg_match('/^i2_/',_request('cfg'))))
		)
		 && ($flux['args']['exec'] != 'inscription2_adherents')){
    	$flux['data'] .= recuperer_fond('prive/i2_societes_affiche_droite');
	}
	return $flux;
}
?>