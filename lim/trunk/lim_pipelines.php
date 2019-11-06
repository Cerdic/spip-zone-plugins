<?php
/**
 * Utilisations de pipelines par Lim
 *
 * @plugin     Lim
 * @copyright  2015
 * @author     Pierre Miquel
 * @licence    GNU/GPL
 * @package    SPIP\Lim\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/lim_api');
include_spip('inc/config');

/**
 * gestion forums public et pétitions : supprime ou non le bloc en fonction de la demande
 *
 * @param array $flux
 * @return array $flux
 *     le flux data remanié
**/
function lim_afficher_config_objet($flux) {
	$type = $flux['args']['type'];
	if ($type == 'article' AND !empty($flux['data'])) {

		$tab_data = explode("<div class='ajax'>", $flux['data']);
		$tab_data[1] = "<div class='ajax'>".$tab_data[1];
		$tab_data[2] = "<div class='ajax'>".$tab_data[2];

		if ( strpos($tab_data[1], 'formulaire_activer_forums') AND lire_config('forums_publics') == 'non' AND lire_config('lim/forums_publics') == 'on' ) {
			$tab_data[1] = '';
		}
		if ( strpos($tab_data[2], 'formulaire_activer_petition') AND lire_config('lim/petitions') == 'on') {
			$tab_data[2] = '';
		}
		$flux['data'] = $tab_data[1].$tab_data[2];
	}
	return $flux;
}

/**
 * Gestion des contenus par rubrique
 * Lors de la création d'un objet éditorial, être sûr de renvoyer la bonne valeur de "id_parent".
 *
 * Cas #1 : objet géré par la restriction par rubrique de LIM
 * 	->	#1.1 Si une seule rubrique définie : on récupère la valeur de l'id_rubrique dans la conf.
 * 	->	#1.2 Sinon forcer la valeur vide pour 'id_parent'. Dans ce cas, le premier choix du selecteur de rubrique est vide, et si l'utilisateur ne choisit pas de rubrique il aura un retour en erreur sur un champ obligatoire (sauf dans le cas d'une rubrique qui se retrouvera à la racine).
 *
 * Cas #2 : objet non géré par la restriction par rubrique de LIM.
 * -> Vérifier que l'objet a comme parent une rubrique et si oui, même traitement que pour le cas #1.2
 *
 * @param array $flux
 * @return array $flux
 *     le flux data complété par une valeur de id_parent
**/
function lim_formulaire_charger($flux) {
	if (
		strncmp($flux['args']['form'], 'editer_', 7) == 0 // c'est bien un formulaire d'edition d'objet
		and !is_numeric($flux['args']['args']['0']) // c'est bien une création d'objet (pas une modif ou autre)
	) {
		$objet = substr($flux['args']['form'], 7); // 'editer_article' -> 'article'
		$nom_table	= table_objet_sql($objet); // article -> spip_articles
		$tableau_conf_lim_objet	= lire_config("lim_rubriques/$objet");
		

		if (isset($tableau_conf_lim_objet)) {
			$nbre_rubriques = sql_countsel('spip_rubriques');
			$nbre_rubriques_autorisees = $nbre_rubriques - count($tableau_conf_lim_objet);

			// Cas #0 : voir TODO's
			// if ($nbre_rubriques_autorisees == 0) {
			// 	debug('Cas #0');
			// 	$id_parent = '0';
			// }

			if ($nbre_rubriques_autorisees == 1) { // Cas #1.1
				$tab_rubrique_choisie = lim_publierdansrubriques($objet);
				$id_parent = implode($tab_rubrique_choisie);
			}

			if ($nbre_rubriques_autorisees >= 2) { // Cas #1.2
				$id_parent = '';
			}
		} else { // Cas #2
			// ici dans l'idéal, il faudrait utiliser l'API du plugin  Declarer_parent
			$trouver_table = charger_fonction('trouver_table', 'base');
			$desc = $trouver_table($nom_table);
			if (isset($desc['field']['id_rubrique'])) {
				$id_parent = '';
			}
		}

		if (isset($id_parent)) {
			$flux['data']['id_parent'] = $id_parent;
		}
	}

	return $flux;
}

/**
 * Gestion des contenus par rubrique :
 * Impossible de CREER ou DEPLACER un objet dans une rubrique interdite par la configuration choisie dans exec=configurer_lim_rubriques
 * exception : possibilité de modifier un objet si celui-ci est maintenant dans une rubrique où il est interdit de créer ce type d'objet.
 *
 * @param array $flux
 * @return array $flux
 *     le flux data complété ou non d'un message d'erreur
**/
function lim_formulaire_verifier($flux) {
	// si ce n'est pas un formulaire d'édition d'un objet ou si la restriction par rubrique n'a pas été activée, on sort.
	if (strncmp($flux['args']['form'], 'editer_', 7) !== 0 OR is_null(lire_config('lim_objets'))) {
		return $flux;
	}

	$objet = substr($flux['args']['form'], 7); // 'editer_objet' devient 'objet'
	$nom_table	= table_objet_sql($objet);
	$tableau_tables_lim	= explode(',', lire_config('lim_objets'));

	if (in_array($nom_table, $tableau_tables_lim)) {
		include_spip('inc/autoriser');
		$faire = 'creer'.$objet.'dans';

		$id_objet = $flux['args']['args'][0];
		if (is_numeric($id_objet)) { 	// c'est donc une modification,

			// récupérer l'id_rubrique actuel de l'objet
			// note : dans l'idéal, il faudrait utiliser le plugin déclarer parent ici
			$where = id_table_objet($objet).'='.$id_objet;
			$trouver_table = charger_fonction('trouver_table', 'base');
			$desc = $trouver_table($nom_table);
			if (isset($desc['field']['id_rubrique'])) {
				$id_rub_en_cours = sql_getfetsel('id_rubrique', $nom_table, $where);
			}

			// si c'est un déplacement vers une autre rubrique, on vérifie
			if (isset($id_rub_en_cours) and $id_rub_en_cours !=_request('id_parent')) {
				if (!autoriser($faire, 'rubrique', _request('id_parent'))) {
					$flux['data']['id_parent'] = _T('lim:info_deplacer_dans_rubrique_non_autorise');
				}
			}
		}
		else { //c'est une création
			// en fait, cela ne sert à rien...snif...à cause de /echafaudage qui intercepte les créations avant le CVT (?!).
			// if (!autoriser($faire, 'rubrique', _request('id_parent'))) {
			// 	$flux['data']['id_parent'] = _T('lim:info_creer_dans_rubrique_non_autorise');
		}
	}

	return $flux;
}

/**
 * Gestion de la desactivation de l'affichage de certain champs dans le formulaire Editer Auteur
 * Inserer le JS qui gére l'affichage ou non des champs dans certains formulaires historiques
 * juste le formulaire Auteur
 *
 * @param array $flux
 * @return array
 */
function lim_recuperer_fond($flux) {
	if ($flux['args']['fond'] == "formulaires/editer_auteur") {
		$ajout_script = recuperer_fond('prive/squelettes/inclure/lim');
		$flux['data']['texte'] = str_replace('</form>', '</form>'. $ajout_script, $flux['data']['texte']);
	}
	return $flux;
}
