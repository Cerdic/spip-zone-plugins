<?php
/**
 * Utilisations de pipelines par Interface de traduction pour objets
 *
 * @plugin     Interface de traduction pour objets
 * @copyright  2019
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Interface_traduction_objets\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Perermet de compléter ou modifier le résultat de la compilation d’un squelette donné.
 *
 * @param array $flux
 *   Les données du pipeline
 *
 * @return array
 *   Les données du pipeline
 */
function interface_traduction_objets_recuperer_fond($flux) {
	$contexte = $flux['args']['contexte'];
	$fond = $flux['args']['fond'];

	// Enlever le lien traduction dans le formulaire traduire
	if ($fond == 'formulaires/traduire') {
		$flux['data']['texte'] = preg_replace('/(<div\sclass="new_trad)([^<]|<.+>.*<\/.+>)+(<\/div>)/i', '', $flux['data']['texte']);
	}

	//Insertion des onglets de langue
	if (strpos($fond, 'prive/squelettes/contenu/') !== false and
		$objet = _request('exec') and
		$objet_exec = trouver_objet_exec($objet) and
		$table_objet = $objet_exec['table_objet_sql'] and
		$trouver_table = charger_fonction('trouver_table', 'base') and
		$desc = $trouver_table($table_objet) and
		isset($desc['field']['lang']) and
		isset($desc['field']['id_trad']) and
		isset($desc['field']['langue_choisie']) and
		$id_table_objet = $objet_exec['id_table_objet'] and
		$id_objet = $contexte[$id_table_objet] and
		$fond == 'prive/squelettes/contenu/' . $objet and
		$config = explode(',' ,$GLOBALS['meta']['desactiver_interface_traduction']) and
		!in_array($table_objet, $config)
	) {
		$langues_dispos = explode(',', $GLOBALS['meta']['langues_multilingue']);
		$select = ['id_trad', 'lang', $id_table_objet];

		$id_parent_table = '';
		if (isset($desc['field']['id_rubrique'])) {
			if ($objet != 'rubrique') {
				$id_parent_table = 'id_rubrique';
			}
			else {
				$id_parent_table = 'id_parent';
			}
			$select[] = $id_parent_table;
		}

		$donnees_objet = sql_fetsel(
			$select,
			$table_objet,
			$id_table_objet . '=' . $contexte[$id_table_objet]);

		$lang_objet = $donnees_objet['lang'];
		$id_trad = $donnees_objet['id_trad'];
		$langues_traduites = [];

		if ($id_trad > 0) {
			$langues_traduites[$lang_objet] = $id_objet;
			$traductions = sql_allfetsel(
				'lang,' . $id_table_objet,
				$table_objet,
				'id_trad=' . $id_trad . ' AND ' . $id_table_objet . '!=' . $id_objet);

			foreach ($traductions AS $traduction) {
				$langues_traduites[$traduction['lang']] = $traduction[$id_table_objet];
			}
		}
		else {
			$id_trad = $id_objet;
		}

		$contexte['objet'] = $objet;
		$contexte['id_objet'] = $id_objet;

		// Si secteur par langue, on établit l'id_parent.
		if (test_plugin_actif('secteur_langue')) {
			$contexte['id_parent'] = isset($donnees_objet['id_rubrique']) ?
				$donnees_objet['id_rubrique'] :
				(isset($donnees_objet['id_parent']) ? $donnees_objet['id_parent'] : '');
		}
		$contexte['id_table_objet'] = $id_table_objet;
		$contexte['langues_dispos'] = $langues_dispos;
		$contexte['lang_objet'] = $lang_objet;
		$contexte['id_trad'] = $id_trad;
		$contexte['langues_traduites'] = $langues_traduites;


		$barre_langue = recuperer_fond("prive/inclure/barre_traductions_objet", $contexte, array('ajax' => true));
		$flux['data']['texte'] = str_replace('</h1>', '</h1>' . $barre_langue, $flux['data']['texte']);
	}

	// Liste compacte des objets traduits
	if ($exec = _request('exec') and
		$exec != 'recherche' and
		strpos($fond, 'prive/objets/liste/') !== false and
		$segments = explode('/', $fond) and
		$objets = $segments[3] and
		$objet = objet_type($objets) and
		$exec != $objet and
		$table_objet_sql = table_objet_sql($objet) and
		$id_table_objet = id_table_objet($objet) and
		$tables_spip = lister_tables_spip() and
		isset($tables_spip[$table_objet_sql]) and
		$trouver_table = charger_fonction('trouver_table', 'base') and
		$desc = $trouver_table($table_objet_sql) and
		isset($desc['field']['lang']) and
		isset($desc['field']['id_trad']) and
		isset($desc['field']['langue_choisie']) and
		$config = explode(',' ,$GLOBALS['meta']['desactiver_liste_compacte']) and
		!in_array($table_objet_sql, $config)) {

		// Détermine si la liste est de type sections.
		if (test_plugin_actif('secteur_langue') and isset($contexte['id_rubrique'])) {
			$contexte['type'] = 'sections';
		}

		// S'il existe un squelette compacte pour l'objet on le prend.
		if (find_in_path('prive/objets/liste/compacte/' . $objets . '.html')) {
			$liste_compacte = recuperer_fond('prive/objets/liste/compacte/' . $objets, $contexte, ['ajax'=>'oui']);
		}
		// Sinon on prend le générique.
		else {
			$contexte['objets'] = $objets;
			$contexte['objet'] = $objet;
			$contexte['table_objet_sql'] = $table_objet_sql;
			$contexte['id_table_objet'] = $id_table_objet;
			$contexte['champs'] = $desc['field'];
			$contexte['voir'] = _request('voir');


			/*
			* Affichage de champs supplémentaires
			*/

			// Les auteurs liés s'il y en a en moins un.
			$auteur = sql_getfetsel('id_auteur', 'spip_auteurs_liens', 'objet LIKE' . sql_quote($objet));
			if ($auteur) {
				$contexte['champ_auteur'] = TRUE;
			}

			// Existence d'un champ date.
			$champ_date = '';
			if (isset($desc['date']) and $desc['date']) {
				$champ_date = $desc['date'];
			} elseif (isset($desc['field']['date'])) {
				$champ_date = 'date';
			}
			if ($champ_date) {
				$contexte['champ_date'] = $champ_date;
			}

			// Existence d'un champ rang.
			if (isset($desc['field']['rang'])) {
				$contexte['champ_rang'] = 'rang';
			}

			$liste_compacte = recuperer_fond('prive/objets/liste/objets_compacte', $contexte, ['ajax'=>'oui']);
		}
		$flux['texte'] = $liste_compacte;
	}

	return $flux;
}


function interface_traduction_objets_header_prive($flux) {
	$flux .= '<link rel="stylesheet" href="' . find_in_path('css/interface_traduction_objets_styles.css') . '" type="text/css" media="all" />';

	return $flux;
}

/*Ajoute la langue de traduction dans le chargement du formulaire edition_rubrique*/
function interface_traduction_objets_formulaire_charger($flux) {
	$form = $flux['args']['form'];
	$segments = explode('_', $form);

	if ($segments[0] == 'editer' and
		_request('new') == 'oui') {

		if (!$flux['data']['lang_dest'] = _request('lang_dest') and $id_parent = _request('id_parent')) {
			$flux['data']['lang_dest'] = sql_getfetsel('lang', 'spip_rubriques', 'id_rubrique=' . $id_parent);
		}

			// pour afficher la liste des trad sur la base de l'id_trad en base
		if (isset($flux['data']['lang_dest'])) {
			$flux['data']['_hidden'] .= '<input type="hidden" name="lang_dest" value="' . $flux['data']['lang_dest'] . '"/>';
		}
	}

	if ($form == 'traduire') {
		// Rendre le changement de la langue possible aunsi que le changement de la référence des traductions
		$flux['data']['_langue'] = $flux['data']['langue'];
		$flux['data']['editable'] = TRUE;
	}


	return $flux;
}

/*Prise en compte de la langue de traduction dans le traitement du formulaire edition_article*/
function interface_traduction_objets_pre_insertion($flux) {
	if ($lang = _request('lang_dest')) {
		$flux['data']['lang'] = $lang;
		$flux['data']['langue_choisie'] = 'oui';
		$flux['data']['id_trad'] = _request('lier_trad');
	}
	return $flux;
}