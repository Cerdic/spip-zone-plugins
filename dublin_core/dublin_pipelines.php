<?php
function dublin_insert_head($flux){
	if ($GLOBALS['page']['contexte']['type'] == 'article'){
		$flux .= recuperer_fond('dublin_core_article', array('id_article'=>$GLOBALS['page']['contexte']['id_article']));				# interpreter le squelette spip en lui passant les bonnes informations
	} # si on dans une page article.html
	return $flux;	
}

?>