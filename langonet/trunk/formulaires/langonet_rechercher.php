<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_langonet_rechercher_charger($type) {
	$legende = _T('langonet:titre_form_rechercher_'.$type);
	$explication = _T('langonet:info_rechercher_'.$type);
	$info_pattern = _T('langonet:info_pattern_'.$type.'_cherche');
	$info_modules = _T('langonet:info_modules_recherche_'.$type);
	$label_defaut = _T('langonet:label_defaut_modules_'.$type);

	$modules_fr = langonet_lister_modules('fr');

	$defaut_modules = _request('defaut_modules');
	$modules = _request('modules');
	if (($defaut_modules == 'oui')
	OR (!$defaut_modules AND !$modules)) {
		$modules_choisis = ($type == 'texte') ? array('ecrire', 'spip', 'public') : array_keys($modules_fr);
		$defaut_modules = 'oui';
	}
	else {
		$modules_choisis = array();
		foreach (_request('modules') as $_valeurs) {
			$m = explode(':', $_valeurs);
			$modules_choisis[] = reset($m);
		}
	}

	return array('type' => $type,
				'_legende' => $legende,
				'_explication' => $explication,
				'_info_pattern' => $info_pattern,
				'_info_modules' => $info_modules,
				'_label_defaut' => $label_defaut,
				'_modules' => $modules_fr,
				'_modules_choisis' => $modules_choisis,
				'defaut_modules' => $defaut_modules,
				'pattern' => _request('pattern'),
				'correspondance' => _request('correspondance'));
}

function formulaires_langonet_rechercher_verifier($type) {
	$erreurs = array();

	$obligatoires = array('pattern');
	if (!_request('defaut_modules'))
		$obligatoires[] = 'modules';
	foreach ($obligatoires as $_champ) {
		if (!_request($_champ)) {
			$erreurs[$_champ] = _T('langonet:message_nok_champ_obligatoire');
		}
	}

	return $erreurs;
}

function formulaires_langonet_rechercher_traiter($type) {

	// Recuperation des champs du formulaire
	$pattern = _request('pattern');
	$correspondance = _request('correspondance');

	if (_request('defaut_modules') == 'oui') {
		$modules_fr = langonet_lister_modules('fr');
		$modules = array();
		if ($type == 'texte') {
			$modules = array(
				'ecrire:' . $modules_fr['ecrire'],
				'public:' . $modules_fr['public'],
				'spip:' . $modules_fr['spip']);
		}
		else {
			foreach ($modules_fr as $_module => $_fichier) {
				$modules[] = "${_module}:${_fichier}";
			}
		}
	}
	else
		$modules = _request('modules');
	$langonet_rechercher = charger_fonction('rechercher_'.$type,'inc');

	// Verification et formatage des resultats de la recherche
	$retour = array();
	$resultats = $langonet_rechercher($pattern, $correspondance, $modules);
	if (isset($resultats['erreur'])) {
		$retour['message_erreur'] = $resultats['erreur'];
	}
	else {
		$retour['message_ok']['resume'] = _T('langonet:message_ok_item_trouve', array('pattern' => $pattern));
		$retour['message_ok']['total'] = $resultats['total'];
		$retour['message_ok']['trouves'] = $resultats['trouves'];
	}
	$retour['editable'] = true;
	return $retour;
}


function langonet_lister_modules($langue, $exclure_paquet=true) {
	$liste = array();

	foreach (preg_files(_DIR_RACINE, "/lang/[^/]+_${langue}\.php$") as $_fichier) {
		// On extrait le module
		if (preg_match(",/lang/([^/]+)_${langue}\.php$,i", $_fichier, $module)) {
			// On ajoute le module à la liste : l'index correspond au module et la valeur au dossier
			if (!$exclure_paquet OR ($exclure_paquet
			AND (strtolower(substr($module[1], 0, 7)) != 'paquet-'))) {
				$liste[$module[1]] = dirname($_fichier) . '/';
			}
		}
	}

	return $liste;
}

?>