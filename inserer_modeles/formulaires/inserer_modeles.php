<?php

function formulaires_inserer_modeles_charger_dist() {
	include_spip('inc/inserer_modeles');
	$contexte = array();
	
	if (!_request('formulaire_modele') || _request('annuler')) {
		$contexte['_liste_formulaires_modeles'] = inserer_modeles_lister_formulaires_modeles();
	} else {
		$infos_modele = charger_infos_formulaire_modele(_request('formulaire_modele'));
		$contexte['formulaire_modele'] = _request('formulaire_modele');
		$contexte['nom'] = _T_ou_typo($infos_modele['nom']);
		$contexte['logo'] = $infos_modele['logo'];
		$contexte['_saisies'] = $infos_modele['parametres'];
		if (_request('code_modele'))
			$contexte['code_modele'] = _request('code_modele');
	}
	
	return $contexte;
}

function formulaires_inserer_modeles_verifier_dist() {
	$erreurs = array();
	
	if (_request('choisir') && !_request('formulaire_modele'))
		$erreurs['message_erreur'] = _T('inserer_modeles:erreur_choix_modele');
	
	if (_request('inserer')) {
		include_spip('inc/saisies');
		include_spip('inc/inserer_modeles');
		$infos = charger_infos_formulaire_modele(_request('formulaire_modele'));
		$erreurs = saisies_verifier($infos['parametres']);
	}
	
	return $erreurs;
}

function formulaires_inserer_modeles_traiter_dist() {
	if (_request('inserer')) {
		include_spip('inc/saisies');
		include_spip('inc/inserer_modeles');
		$infos = charger_infos_formulaire_modele(_request('formulaire_modele'));
		$champs = saisies_lister_champs($infos['parametres'],false);
		$code = '<'._request('modele');
		$code .= _request('id_modele') ? _request('id_modele') : '';
		$code .= _request('classe') ? '|'._request('classe') : '';
		foreach ($champs as $champ)
			if($champ != 'modele' && $champ != 'classe' && $champ != 'id_modele')
				$code .= _request($champ) ? "|$champ="._request($champ) : '';
		$code .= '>';
		set_request('code_modele',$code);
		return array('message_ok' => _T('inserer_modeles:message_copier_code'));
	}
}

?>