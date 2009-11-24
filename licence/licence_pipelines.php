<?php
/*
 * Plugin Licence
 * (c) 2007-2009 fanouch
 * Distribue sous licence GPL
 *
 */

function licence_affiche_milieu($flux) {

	if ($flux['args']['exec'] == 'articles'){
		include_spip('inc/licence');
		$flux['data'] .= licence_formulaire_article($flux['args']['id_article'],$flux['args']['id_licence']);
	}
	return $flux;
}


?>
