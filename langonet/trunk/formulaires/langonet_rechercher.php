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
			$modules_choisis[] = reset(explode(':', $_valeurs));
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
	$langonet_rechercher = charger_fonction('langonet_rechercher_'.$type,'inc');

	// Verification et formatage des resultats de la recherche
	$retour = array();
	$resultats = $langonet_rechercher($pattern, $correspondance, $modules);
	if ($resultats['erreur']) {
		$retour['message_erreur'] = $resultats['erreur'];
	}
	else {
		$retour['message_ok']['resume'] = _T('langonet:message_ok_item_trouve', array('pattern' => $resultats['pattern']));
		$retour['message_ok']['total'] = $resultats['total'];
		$retour['message_ok']['trouves'] = $resultats['trouves'];
	}
	$retour['editable'] = true;
	return $retour;
}

function formater_recherche($resultats) {
	include_spip('inc/layer');
	
	$texte = '';
	$total = 0;
	foreach ($resultats['item_trouve'] as $_pertinence => $_trouves) {
		if ($_trouves) {
			$total += count($_trouves);
			// On démarre un groupe d'items trouves avec un message
			$suffixe = (count($_trouves) == 1 ? '_1' : '_n');
			$texte .= '<div style="margin-bottom: 20px">' . "\n" .
			          '<div class="success">' . "\n" .
			          _T('langonet:message_ok_item_trouve_' . $_pertinence .
			          $suffixe, array('sous_total'=>count($_trouves))) . "\n" .
			          '</div>' . "\n";
			foreach ($_trouves as $_item => $_infos) {
				$texte .= bouton_block_depliable($_item . ' (' .
				          count($_infos['fichier']) . ')', false) .
				          debut_block_depliable(false) .
				          "<p style=\"padding-left:2em;\">  " .
				          _T('langonet:texte_item_defini_ou')."\n<br />";
				foreach ($_infos['fichier'] as $_index => $_fichier_def) {
					$texte .= "\t" . '<span style="font-weight:bold;padding-left:2em;">' .
					          $_fichier_def . "</span><br />\n" .
					          "\t" . '<span style="padding-left:3em;padding-right:0.5em;"><em>' .
							  '<span style="color: #aaa">&#10078;</span> ' . $_infos['traduction'][$_index] . ' <span style="color: #aaa">&#10077;</span>' .
							  "</em></span><br />\n";
				}
				$texte .= "</p>\n" .
				          fin_block();
			}
			$texte .= '</div>' . "\n";
		}
	}
	
	// Tout s'est bien passe on renvoie le message ok et les resultats de la verification
	$retour['message_ok']['resume'] = _T('langonet:message_ok_item_trouve', array('pattern' => $resultats['pattern']));
	$retour['message_ok']['total'] = $total;
	$retour['message_ok']['trouves'] = $texte;

	return $retour;
}

?>