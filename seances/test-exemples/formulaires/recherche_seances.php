<?php
// chargement des valeurs par defaut des champs du formulaire
function formulaires_recherche_seances_charger_dist(){
	if ($GLOBALS['spip_lang'] != $GLOBALS['meta']['langue_site'])
		$lang = $GLOBALS['spip_lang'];
	else
		$lang='';

	return 
		array(
			'action' => generer_url_public('seances_recherche'),
			'id_rubrique' => _request('id_rubrique'),
			'id_article' => _request('id_article'),
			'lang' => $lang,
		);
}

?>
