<?php

function formulaires_inserer_modeles_charger_dist($id_article,$id_rubrique,$id_breve,$formulaire_modele='',$modalbox='') {
	include_spip('inc/inserer_modeles');
	$contexte = array();
	
	if ((!_request('formulaire_modele') && $formulaire_modele=='') || _request('annuler')) {
		$contexte['_liste_formulaires_modeles'] = inserer_modeles_lister_formulaires_modeles();
	} else {
		if ($formulaire_modele=='')
			$formulaire_modele = _request('formulaire_modele');
		$infos_modele = charger_infos_formulaire_modele($formulaire_modele);
		include_spip('inc/saisies');
		$contexte = saisies_charger_champs($infos_modele['parametres']);
		$contexte['formulaire_modele'] = $formulaire_modele;
		$contexte['nom'] = _T_ou_typo($infos_modele['nom']);
		$contexte['logo'] = $infos_modele['logo'];
		$contexte['_saisies'] = $infos_modele['parametres'];
		if (_request('_code_modele'))
			$contexte['_code_modele'] = _request('_code_modele');
		if ($formulaire_modele!='')
			$contexte['ne_pas_afficher_bouton_annuler'] = 'on';
	}
	
	if (is_numeric($id_article))
		$contexte['id_article'] = $id_article;
	if (is_numeric($id_rubrique))
		$contexte['id_rubrique'] = $id_rubrique;
	if (is_numeric($id_breve))
		$contexte['id_breve'] = $id_breve;
	if ($modalbox!='')
		$contexte['modalbox'] = 'oui';
	
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
		if (_request('id_modele') && _request('id_modele')!='')
			$code .= _request('id_modele');
		if (_request('classe') && _request('classe')!='')
			$code .= '|'._request('classe');
		if (_request('align') && _request('align')!='')
			$code .= '|'._request('align');
		foreach ($champs as $champ) {
			if($champ != 'modele' && $champ != 'classe' && $champ != 'id_modele' && _request($champ) && _request($champ)!='') {
				if($champ == _request($champ))
					$code .= "|$champ";
				else
					$code .= "|$champ="._request($champ);
			}
		}
		$code .= '>';
		set_request('_code_modele',$code);
		return array('message_ok' => _T('inserer_modeles:message_copier_code'));
	}
}

?>