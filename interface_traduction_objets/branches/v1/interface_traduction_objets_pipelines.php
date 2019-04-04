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
	if (strpos($fond, 'prive/squelettes/contenu/') !== false AND
		$objet = _request('exec') AND
		$objet_exec = trouver_objet_exec($objet) AND
		$table_objet = $objet_exec['table_objet_sql'] AND
		$trouver_table = charger_fonction('trouver_table', 'base') AND
		$desc = $trouver_table($table_objet) AND
		isset($desc['field']['lang']) AND
		isset($desc['field']['id_trad']) AND
		isset($desc['field']['langue_choisie']) AND
		$id_table_objet = $objet_exec['id_table_objet'] AND
		$id_objet = $contexte[$id_table_objet] AND
		$fond == 'prive/squelettes/contenu/' . $objet AND
		$config = explode(',' ,$GLOBALS['meta']['desactiver_interface_traduction']) AND
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

		$id_trad_parent = '';
		if ($id_parent_table) {
			$rubrique_parent = sql_fetsel(
				'id_trad, id_rubrique',
				'spip_rubriques',
				'id_rubrique=' . $donnees_objet[$id_parent_table]);

				$id_trad_parent = $rubrique_parent['id_rubrique'];
		}


		$lang_objet = $donnees_objet['lang'];
		$id_trad = $donnees_objet['id_trad'];
		$langues_traduites = [];

		if ($id_trad > 0) {
			$trad_new = FALSE;
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
			$trad_new = TRUE;
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
		$contexte['id_trad_parent'] = $id_trad_parent;
		$contexte['langues_traduites'] = $langues_traduites;


		$barre_langue = recuperer_fond("prive/inclure/barre_traductions_objet", $contexte, array('ajax' => true));
		$flux['data']['texte'] = str_replace('</h1>', '</h1>' . $barre_langue, $flux['data']['texte']);
	}

	// Liste compacte des objets traduits
	if ($exec = _request('exec') AND
		$exec != 'recherche' AND
		strpos($fond, 'prive/objets/liste/') !== false AND
		$segments = explode('/', $fond) AND
		$objets = $segments[3] AND
		$objet = objet_type($objets) AND
		$table_objet_sql = table_objet_sql($objet) AND
		$id_table_objet = id_table_objet($objet) AND
		$tables_spip = lister_tables_spip() AND
		isset($tables_spip[$table_objet_sql]) AND
		$trouver_table = charger_fonction('trouver_table', 'base') AND
		$desc = $trouver_table($table_objet_sql) AND
		isset($desc['field']['lang']) AND
		isset($desc['field']['id_trad']) AND
		isset($desc['field']['langue_choisie']) AND
		$config = explode(',' ,$GLOBALS['meta']['desactiver_liste_compacte']) AND
		!in_array($table_objet_sql, $config)) {

		$contexte['objets'] = $objets;
		$contexte['objet'] = $objet;
		$contexte['table_objet_sql'] = $table_objet_sql;
		$contexte['id_table_objet'] = $id_table_objet;
		$contexte['champs'] = $desc['field'];

		$champ = [$id_table_objet . ' as id'];
		$from = $table_objet_sql;
		$where = [];
		$order = ' order by ' . id_table_objet($objet) . ' desc';
		$left_join = [];
		$join = '';

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
			$champ[] = $champ_date . ' as date';
		}

		/*
		* Des requêtes conditionnelles dépendant du contexte.
		*/

		// Page auteur.
		if (isset($contexte['id_auteur'])) {
			if (isset($desc['field']['id_auteur'])) {
				$where[] = 'id_auteur=' . $contexte['id_auteur'];
			}
			else {
				$left_join[] = 'spip_auteurs_liens';
				$where[] = 'objet LIKE ' . sql_quote($objet) . ' AND id_auteur=' . $contexte['id_auteur'];
			}
		}

		// Page mot clé.
		if (isset($contexte['id_mot'])) {
			$left_join[] = 'spip_mots_liens';
			$where[] = 'spip_mots_liens.objet LIKE ' . sql_quote($objet) . ' AND spip_mots_liens.id_mot=' . $contexte['id_mot'];
		}

		$on = '';
		if (count($left_join) > 0) {
			foreach ($left_join AS $table_jointure) {
				$on = ' ON ' . $table_objet_sql . '.' . $id_table_objet . '=' . $table_jointure . '.id_objet';
				$join .= ' LEFT JOIN ' . $table_jointure . $on;
			}
		}

		// Si on est dans une rubrique on prend les objets de la rubrique
		if (isset($contexte['id_rubrique'])) {
			$where[] = $table_objet_sql . '.id_rubrique=' . $contexte['id_rubrique'];
		}


		// Si pas dans une rubrique ou secteur_langue pas activé,
		// on prend les objets non traduits et ceux de références si traduit.
		if (!isset($contexte['id_rubrique']) OR !test_plugin_actif('secteur_langue')){
			$objets = sql_allfetsel(
				'id_trad,' . $id_table_objet,
				$from . $join,
				$where,
				'',
				$id_table_objet . ' desc');

			$id_objets = [];
			foreach ($objets AS $row) {
				$id_trad = $row['id_trad'];
				$id_objet = $row[$id_table_objet];
				if ($id_trad > 0 AND $id_trad == $id_objet) {
					$id_objets[$id_trad] = $id_objet;
				}
				elseif ($id_trad == 0) {
					$id_objets[$id_objet] = $id_objet;
				}
			}
			if (count($id_objets) == 0) {
				$id_objets = [-1];
			}
			$where[] = $table_objet_sql . '.' .$id_table_objet . ' IN (' . implode(',', $id_objets) . ')';
		}

		// On passe le résultat de la requête dans le contexte.
		$contexte['donnees'] = sql_allfetsel($champ, $from . $join, $where, '', id_table_objet($objet) . ' desc');

		$flux['texte'] = recuperer_fond('prive/objets/liste/objets_compacte', $contexte);
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

	if ($segments[0] == 'editer' AND
		_request('new') == 'oui' AND
		isset($segments[1]) AND
		$table_objet = table_objet($segments[1])) {

		if (!$flux['data']['lang_dest'] = _request('lang_dest') AND $id_parent = _request('id_parent')) {
			$flux['data']['lang_dest'] = sql_getfetsel('lang', 'spip_rubriques', 'id_rubrique=' . $id_parent);
		}
		if (isset($flux['data']['lang_dest'])) {
			$flux['data']['_hidden'] .= '<input type="hidden" name="lang_dest" value="' . $flux['data']['lang_dest'] . '"/>';
		}
	}

	if ($form == 'traduire') {
		// Rendre le changement de langue possible si pas dans rubrique
		// ou si dans rubrique sans que secteur_langue soit activé
		if (!isset($flux['data']['id_rubrique']) OR
			(
				isset($flux['data']['id_rubrique']) AND !test_plugin_actif('secteur_langue')
			)
		) {
			$flux['data']['editable'] = TRUE;
			$flux['data']['_langue'] = $flux['data']['langue'];
		}
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