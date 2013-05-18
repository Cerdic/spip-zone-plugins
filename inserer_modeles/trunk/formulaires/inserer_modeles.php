<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_inserer_modeles_charger_dist($formulaire_modele,$modalbox,$env) {
	include_spip('inc/inserer_modeles');
	$env = unserialize($env);
	$contexte = array();
	// Toujours transmettre les id_(article/rubrique/breve...)
	foreach ($env as $var => $val)
		if (substr($var,0,3)=='id_' && is_numeric($val))
			$contexte[$var] = $val;
	if ((!_request('formulaire_modele') && $formulaire_modele=='') || _request('annuler')) {
		$contexte['_liste_formulaires_modeles'] = inserer_modeles_lister_formulaires_modeles();
	} else {
		if ($formulaire_modele!='')
			$contexte['ne_pas_afficher_bouton_annuler'] = 'on';
		if ($formulaire_modele=='')
			$formulaire_modele = _request('formulaire_modele');
		$infos_modele = charger_infos_formulaire_modele($formulaire_modele);
		include_spip('inc/saisies');
		$champs_saisies = saisies_charger_champs($infos_modele['parametres']);
		// On charge les valeurs éventuellement passées par l'url
		foreach($champs_saisies as $champ => $val) {
			if (_request($champ))
				$champs_saisies[$champ] = _request($champ);
		}
		$contexte = array_merge($contexte,$champs_saisies);
		
		$contexte['formulaire_modele'] = $formulaire_modele;
		$contexte['_nom'] = _T_ou_typo($infos_modele['nom']);
		$contexte['logo'] = $infos_modele['logo'];
		$contexte['_saisies'] = $infos_modele['parametres'];
		if (_request('_code_modele'))
			$contexte['_code_modele'] = _request('_code_modele');
		if (_request('_js_inserer_code'))
			$contexte['_js_inserer_code'] = _request('_js_inserer_code');
	}
	
	if ($modalbox!='') {
		$contexte['modalbox'] = 'oui';
		$_modalbox_retour = url_absolue(generer_url_public('inserer_modeles','',true));
		if (substr($formulaire_modele,-5)=='.yaml')
				$formulaire_modele = substr($formulaire_modele,0,-5);
		$_modalbox_retour = parametre_url($_modalbox_retour,'formulaire_modele',$formulaire_modele,'&');
		// Il faut aussi transmettre les id ici
		foreach ($env as $var => $val)
			if (substr($var,0,3)=='id_' && is_numeric($val))
				$_modalbox_retour = parametre_url($_modalbox_retour,$var,$val,'&');
		// Dans le cas ou une saisie ouvre une nouvelle modalbox, il faut transmettre le param modalbox au retour
		// sinon le bouton submit ne fermera pas la modalbox
		$_modalbox_retour = parametre_url($_modalbox_retour,'modalbox','oui','&');
		$contexte['_modalbox_retour'] = $_modalbox_retour;
	}
	
	return $contexte;
}

function formulaires_inserer_modeles_verifier_dist($formulaire_modele,$modalbox,$env) {
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

function formulaires_inserer_modeles_traiter_dist($formulaire_modele,$modalbox,$env) {
	if (_request('inserer')) {
		include_spip('inc/saisies');
		include_spip('inc/inserer_modeles');
		$infos = charger_infos_formulaire_modele(_request('formulaire_modele'));
		$champs = saisies_lister_champs($infos['parametres'],false);
		if(isset($infos['traiter']))
			$f = charger_fonction($infos['traiter'],'formulaires',true);
		else $f=false;
		if ($f)
			$code = $f($champs);
		else {
			$code = '<'._request('modele');
			if (_request('id_modele') && _request('id_modele')!='')
				$code .= _request('id_modele');
			if (_request('variante') && _request('variante')!='')
				$code .= '|'._request('variante');
			if (_request('classe') && _request('classe')!='')
				$code .= '|'._request('classe');
			if (_request('align') && _request('align')!='')
				$code .= '|'._request('align');
			foreach ($champs as $champ) {
				if($champ != 'modele' && $champ != 'variante' && $champ != 'classe' && $champ != 'id_modele' && $champ != 'align' && _request($champ) && _request($champ)!='') {
					if($champ == _request($champ))
						$code .= "|$champ";
					// On transforme les tableaux en une liste
					elseif (is_array(_request($champ)))
						$code .= "|$champ=".implode(',',_request($champ));
					else
						$code .= "|$champ="._request($champ);
				}
			}
			$code .= '>';
		}
		set_request('_code_modele',$code);
		
		// Dans la colonne de gauche, on peut peut présupposer du champs dans lequel on veut insérer le modèle (chapeau, texte, ps).
		// On ne fait donc pas d'insertion automatique.
		if ($modalbox!='') {
			return array('message_ok' => _T('inserer_modeles:message_code_insere'));
		} else {
			// js pour inserer la balise dans le texte
			$codejs = "barre_inserer('".texte_script($code)."', $('textarea[name=texte]')[0]);";
			set_request('_js_inserer_code',$codejs);
			return array('message_ok' => _T('inserer_modeles:message_inserer_code'));
		}
	}
}

?>
