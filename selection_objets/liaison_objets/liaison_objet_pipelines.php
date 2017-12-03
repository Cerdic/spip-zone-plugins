<?php

if (!defined("_ECRIRE_INC_VERSION"))
	return;

function liaison_objet_header_prive($flux) {
	$flux .= '<link rel="stylesheet" href="' . find_in_path('css/lo_admin.css').' type="text/css" media="all" />';
	return $flux;
}

function liaison_objet_affiche_gauche($flux) {
	include_spip('inc/config');
	$exec = $flux["args"]["exec"];

	$args = $flux['args'];

	$objets_liaison = lire_config('liaison_objet/liaison_rubrique_objet', array());

	if (in_array($exec, $objets_liaison)) {
		$e = trouver_objet_exec($exec);
		$objet = $e['type'];
		$id_table_objet = $e['id_table_objet'];
		if (!$objet) {
			$objet = $exec;
			$id_table_objet = 'id_' . objet;
		}
		$table = table_objet_sql($objet);
		$contexte['id_objet'] = $flux["args"][$id_table_objet] ? $flux["args"][$id_table_objet] : _request($id_table_objet);
		$contexte['objet'] = $objet;
		$objets_cibles = lire_config('liaison_objet/objets_cible', array());

		$contexte['langue'] = array($args['lang']);
		if ($objet == 'rubrique' OR $objet == 'article') {
			$contexte['langue'] = sql_getfetsel('lang', $table, $id_table_objet . '=' . $contexte['id_objet']);
		}
		if ($objet == 'rubrique') {
			if (!$trad_rub = test_plugin_actif('tradrub'))
				$contexte['langue'] = lire_config('langues_multilingue');
			elseif (!$contexte['langue']) {
				if (!$trad_rub = test_plugin_actif('tradrub'))
					$contexte['langue'] = lire_config('langues_multilingue');
			}
		}
		$contexte['objets_cibles'] = $objets_cibles;
		$flux["data"] .= recuperer_fond("prive/squelettes/navigation/affiche_gauche", $contexte);
	}

	return $flux;
}

function liaison_objet_affiche_milieu($flux = "") {
	include_spip('inc/config');
	$exec = $flux["args"]["exec"];
	//Exception pour les documents
	if ($exec == 'document_edit')
		$exec = 'document';
	$objets_cibles = lire_config('liaison_objet/objets_cible', array());

	if (in_array($exec, $objets_cibles)) {
		$e = trouver_objet_exec($exec);
		$objet = $e['type'];
		$id_table_objet = $e['id_table_objet'];
		if (!$objet) {
			$objet = $exec;
			$id_table_objet = 'id_' . objet;
		}

		$table = table_objet_sql($objet);
		$args = $flux["args"];

		$tables = lister_tables_objets_sql();

		$id_objet = $args['id_' . $objet];
		if ($objet == 'site')
			$id_objet = $args['id_syndic'];
		$data = $flux["data"];
		$special = array(
			'article',
			'rubrique'
		);
		if (in_array($objet, $special)) {
			$choisies = picker_selected(lire_config('liaison_objet/liaison_' . $objet . '_dest', array()), $objet);
		}
		else {
			$choisies = lire_config('liaison_objet/liaison_' . $objet . '_dest', array());
		}
		if (in_array($id_objet, $choisies) OR !$choisies) {
			$contexte = array(
				'id_objet_dest' => $id_objet,
				'objet_dest' => $objet
			);

			$contexte['langue'] = array($args['lang']);

			if ($tables[$table]['field']['lang']) {
				$contexte['langue'] = array(sql_getfetsel('lang', $table, 'id_' . $objet . '=' . $id_objet));
			}
			elseif ($objet == 'document') {
				$contexte['langue'] = array();
			}
			if ($objet == 'rubrique') {
				if (!$trad_rub = test_plugin_actif('tradrub')) {
					if ($langues_multilingue = lire_config('langues_multilingue')) {
						$contexte['langue'] = explode(',', lire_config('langues_multilingue'));
					}
					else {
						$contexte['langue'] = array($args['lang']);
					}
				}
			}
			if ($objet == 'auteur') {
				$contexte['langue'] = '';
			}

			$flux["data"] .= recuperer_fond('prive/objets/liste/liaison_interface', $contexte);
		}
	}
	return $flux;
}

function liaison_objet_formulaire_charger($flux) {
	$form = $flux['args']['form'];
	if ($form == 'configurer_liaison_objet') {
		//emprunté de a2a  preparé les type_liens pour le formulaire
		include_spip('inc/config');
		if (!$cfg['type_liens'] = _request('type_liens')) {
			$types_lien = lire_config('liaison_objet/type_liens', array());
			$flux['data']['type_liens'] = '';
			foreach ($types_lien as $key => $value) {
				if ($key)
					$flux['data']['type_liens'] .= "$key,$value\n";
			}

		}

		//également les  type_liens_OBJET

		$objets_cibles = lire_config('liaison_objet/objets_cible', array());

		foreach ($objets_cibles as $objet) {
			if ($types_lien = lire_config('liaison_objet/type_liens_' . $objet)) {
				$flux['data']['type_liens_' . $objet] = '';
				foreach ($types_lien as $key => $value) {
					if ($key)
						$flux['data']['type_liens_' . $objet] .= "$key,$value\n";
				}
			}

		}
	}
	return $flux;
}

function liaison_objet_formulaire_traiter($flux) {
	// intervenir sur la configuration du plugin
	$form = $flux['args']['form'];
	if ($form == 'configurer_liaison_objet') {
		include_spip('inc/config');
		$cfg = lire_config('liaison_objet');
		$cfg['type_liens'] = types_liaisons2array(_request('type_liens'));

		if (!$objets_cibles = $cfg['objets_cible'])
			$objets_cibles = array();

		foreach ($objets_cibles as $objet) {
			if (_request('type_liens_' . $objet))
				$cfg['type_liens_' . $objet] = types_liaisons2array(_request('type_liens_' . $objet));
		}

		ecrire_config('liaison_objet', $cfg);
	}
	return $flux;
}

function liaison_objet_jqueryui_plugins($scripts) {
	$scripts[] = 'jquery.ui.autocomplete';
	$scripts[] = "jquery.ui.widget";
	$scripts[] = "jquery.ui.mouse";
	$scripts[] = "jquery.ui.sortable";
	return $scripts;
}

function types_liaisons2array($type) {
	$tableau = array();
	$lignes = explode("\n", $type);
	foreach ($lignes as $l) {
		$donnees = explode(',', $l);
		if ($donnees[1])
			$tableau[trim($donnees[0])] = trim($donnees[1]);
		else
			$tableau[trim($donnees[0])] = '';
	}

	return $tableau;
}
?>
