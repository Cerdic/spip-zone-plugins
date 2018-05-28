<?php
/**
 * Utilisations de pipelines par Rang
 *
 * @plugin     Rang
 * @copyright  2016
 * @author     Peetdu
 * @licence    GNU/GPL
 * @package    SPIP\Rang\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/rang_api');
include_spip('inc/config');

/**
 * Declaration du champ Rang sur les objets sélectionnés
 *
 * @param array $tables
 * @return array
 */
function rang_declarer_tables_objets_sql($tables) {
	$tables_objets_selectionnes = lire_config('rang/objets');
	
	// Tant qu'on n'a rien rajouté, on commence par lister les tables qui ont DEJA un champ rang !
	$tables_deja_rang = rang_lister_tables_deja_rang($tables);
	
	// On déclare le champ "rang" sur les tables demandées
	if (is_array($tables_objets_selectionnes)) {
		foreach ($tables_objets_selectionnes as $table) {
			// Mais on ne déclare le champ que s'il n'existait pas déjà !
			if (!isset($tables[$table]['field']['rang'])) {
				$tables[$table]['field']['rang'] = "SMALLINT NOT NULL";
			}
		}
	}
	
	return $tables;
}

/**
 * Calculer et Inserer le JS qui gére le tri par Drag&Drop dans le bon contexte (la page ?exec=xxxxx)
 *
 * @param    array $flux Données du pipeline
 * @return    array        Données du pipeline
 */
function rang_recuperer_fond($flux) {
	$tables_objets_selectionnes = lire_config('rang/objets');
	
	if (
		// S'il y a bien des objets qu'on veut trier
		isset($tables_objets_selectionnes)
		and !empty($tables_objets_selectionnes)
		// On cherche un objet en rapport avec le squelette
		and $objet_info = rang_trouver_objet_liste($flux['args']['fond'])
		// Cet objet fait partie de ceux qu'on veut pouvoir trier
		and in_array($objet_info['table_objet_sql'], $tables_objets_selectionnes)
		// On cherche l'objet correspondant à la page en cours
		// Si la page sur laquelle on est fait partie des contextes qui peut avoir des rangs à trier
		//and in_array(_request('exec'), rang_get_contextes())
	) {
		//var_dump(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
		
		// On teste rapide pour les choses à ajouter
		$ajouter_objet = (strpos($flux['data']['texte'], 'data-objet=') === false);
		$ajouter_ids = (strpos($flux['data']['texte'], 'data-id_objet') === false);
		$ajouter_rangs = !preg_match('%<th[^>]+?class=("|\')[\w ]*?rang%is', $flux['data']['texte']);
		
		// On continue et on fait de l'analyse de DOM seulement si au moins un des trois
		if ($ajouter_objet or $ajouter_ids or $ajouter_rangs) {
			// On charge le DOM de la liste
			libxml_use_internal_errors(true);
			$dom = new DOMDocument;
			$dom->loadHTML('<?xml encoding="utf-8" ?>' . $flux['data']['texte']);
			$finder = new DomXPath($dom);
			
			// Si pas déjà présent, on ajoute l'info de l'objet sur le tableau
			if ($ajouter_objet and $table = $dom->getElementsByTagName('table')->item(0)) {
				$table->setAttribute('data-objet', $objet_info['objet']);
			}
			
			// On voit si on doit ajouter un th de rang
			if (
				$ajouter_rangs
				and $thead_tr = $finder->query('//thead/tr')->item(0)
				and $th_premier = $dom->getElementsByTagName('th')->item(0)
			) {
				$url_trier_rang = parametre_url(self(), 'par', 'rang', '&');
				$lien_tri = $dom->createElement('a', 'Rang');
				$lien_tri->setAttribute('href', $url_trier_rang);
				$lien_tri->setAttribute('class', 'ajax');
				$th = $dom->createElement('th');
				$th->appendChild($lien_tri);
				$thead_tr->insertBefore($th, $th_premier);
			}
			
			// On parcourt toutes les lignes de contenu
			$tbody_trs = $finder->query('//tbody/tr');
			foreach ($tbody_trs as $tr) {
				// Il faut toujours avoir l'id sous la main
				if ($td_id = $finder->query('.//td[contains(@class, "id")]', $tr)->item(0)) {
					$id_objet = intval($td_id->textContent);
					
					// Si on doit ajouter les ids
					if ($ajouter_ids) {
						$tr->setAttribute('data-id_objet', $id_objet);
					}
					
					// Si on doit ajouter les rangs
					if (
						$ajouter_rangs
						and $td_premier = $tr->getElementsByTagName('td')->item(0)
					) {
						$rang = sql_getfetsel('rang', $objet_info['table_objet_sql'], $objet_info['cle_objet'].'='.$id_objet);
						$tr->insertBefore($dom->createElement('td', $rang), $td_premier);
					}
				}
			}
			
			// S'il a un tfoot on rajoute aussi
			if (
				$ajouter_rangs
				and $tfoot_trs = $finder->query('//tfoot/tr')
			) {
				foreach ($tfoot_trs as $tr) {
					$td_premier = $tr->getElementsByTagName('td')->item(0);
					$tr->insertBefore($dom->createElement('td'), $td_premier);
				}
			}
			
			// On retransforme en HTML à la fin
			$flux['data']['texte'] = $dom->saveHTML();
		}
		
		$objet = $objet_info['objet'];
		
		// récupérer le type de parent…
		include_spip('base/objets_parents');
		$parent       = type_objet_info_parent($objet);
		$parent_champ = $parent['0']['champ'];
		$id_parent    = $flux['args']['contexte'][$parent_champ];

		// suffixe de la pagination : particularité des objets historiques
		switch ($objet) {
			case 'article':
				$suffixe_pagination = 'art';
				break;
			case 'site':
				$suffixe_pagination = 'sites';
				break;
			case 'breve':
				$suffixe_pagination = 'bre';
				break;
			default:
				$suffixe_pagination = $objet;
				break;
		}

		// Calcul du JS à insérer avec les paramètres
		$ajout_script = recuperer_fond(
			'prive/squelettes/inclure/rang',
			array(
				'suffixe_pagination' => $suffixe_pagination,
				'objet'              => table_objet($objet),
				'id_parent'          => $id_parent,
			)
		);

		// et hop, on insère le JS calculé
		$flux['data']['texte'] = str_replace('</table>', '</table>' . $ajout_script, $flux['data']['texte']);
	}
	
	return $flux;
}

/**
 * Insertion dans le pipeline pre_edition pour le classer l'objet quand on le publie
 *
 * @param    array $flux Données du pipeline
 * @return    array        Données du pipeline
 */
function rang_pre_edition($flux) {
	$rang_max = lire_config('rang/rang_max');

	if (isset($rang_max) && !empty($rang_max) && $flux['args']['action'] == 'instituer') {
		$liste_objets  = lire_config('rang/objets');
		$table         = $flux['args']['table'];

		if (in_array($table, $liste_objets)) {
			$id_objet = $flux['args']['id_objet'];

			// cas des objets avec statut
			if (isset($flux['data']['statut']) && $flux['data']['statut'] == 'publie') {
				$flux['data']['rang'] = rang_classer_dernier($table, $id_objet);
			}
			// cas des mots clés
			if ($table == 'spip_mots') {
				$flux['data']['rang'] = rang_classer_dernier($table, $id_objet);
			}
		}
	}
	
	return $flux;
}
