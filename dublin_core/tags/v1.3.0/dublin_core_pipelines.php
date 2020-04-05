<?php
function dublin_core_insert_head($flux){
	// Si Zpip, on connait le type de page
	 if (defined('_DIR_PLUGIN_Z')) {
		if ($GLOBALS['page']['contexte']['type'] == 'article'){
			$flux .= recuperer_fond('dublin_core_article', array('id_article'=>$GLOBALS['page']['contexte']['id_article']));
		}
	// Sinon, on regarde juste si on dispose d'un id_article dans l'environnement
	} else {
		if ($GLOBALS['page']['contexte']['id_article']>0){
			$flux .= recuperer_fond('dublin_core_article', array('id_article'=>$GLOBALS['page']['contexte']['id_article']));
		}
	}
	return $flux;
}

?>