<?php

// necessaire de modifier le fichier /ecrire/exec/articles.php ligne 625 pour ajouter id='contenu_article' la div
// ticket correspondant ouvert sur Spip-Trac pour intיgration d'office dans la prochaine version 1.9.2 : http://trac.rezo.net/trac/spip/ticket/771
function tinymce_acti_pre_propre($flux) {
	global $exec;
	//$exec = $flux['args']['exec'];
	if(_DIR_RESTREINT=='' && ($exec=='articles' || $exec=='breves_voir')) {
		$flux = str_replace(array('src="images/', '&lt;', '&gt;'), array('src="../images/', '<', '>'), $flux);
	}
	return $flux;
}

?>