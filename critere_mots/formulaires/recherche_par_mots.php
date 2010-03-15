<?php

function formulaires_recherche_par_mots_charger_dist($filtre_groupes = NULL, $lien = NULL){
	if ($GLOBALS['spip_lang'] != $GLOBALS['meta']['langue_site'])
		$lang = $GLOBALS['spip_lang'];
	else
		$lang='';
		
	$mots = _request('mots');
	
	return 
		array(
			'action' => ($lien ? $lien : generer_url_public(self())), # action specifique, ne passe pas par Verifier, ni Traiter
			'filtre_groupes' => $filtre_groupes,
			'lang' => $lang,
			'id_groupe'=>_request('id_groupe'),
			'mots'=>$mots
		);
}

function critere_mots_enleve_mot_de_liste($listemots, $id_mot) {
	unset($listemots[array_search($id_mot,$listemots)]);
	return $listemots;
}

?>