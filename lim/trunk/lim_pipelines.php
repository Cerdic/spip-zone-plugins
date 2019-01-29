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
 * Gestion des contenus par rubrique : si pour un objet A la conf dans LIM ne laisse qu'un seule rubrique dans laquelle cet objet peut être éditer, rediriger l'enregistrement vers cette rubrique en renvoyant l'id_parent
 * ce traitement est rendu nécessaire par 
 * -> l'action de la fonction inc/lim_api.php -> inc_chercher_rubrique qui supprime l'affichage du sélecteur de rubrique si une seule rubrique
 * -> le cas 2 (voir ci-dessous)
 *
 * cas 1 : le rédacteur créer une nouvelle instance de l'objet depuis la bonne rubrique : on a l'id_parent depuis le flux
 * cas 2 : le rédacteur créer une instance depuis la barre d'outils rapides, ou via la page exec=objets. On n'a pas l'id_parent. 
 * Il faut le calculer pour enregistrer l'instance dans une rubrique gérée par LIM
 *  a/ via LIM l'objet ne peut être associé qu'à une seule rubrique. On renvoi l'id de cette rubrique
 *  b/ via LIM l'objet peut être associé à plusieurs rubriques : pas de traitement. Le sélecteur de rubrique est affiché.
 *
 * @param array $flux
 * @return array $flux
 *     le flux data complété par un input hidden 'id_parent' avec la bonne valeur
**/
function lim_formulaire_charger($flux) {
	// si ce n'est pas un formulaire d'édition d'un objet ou si la restriction par rubrique n'a pas été activée, on sort.
	if (strncmp($flux['args']['form'], 'editer_', 7) !== 0 OR is_null(lire_config('lim_objets'))) {
		return $flux;
	}

	$objet = substr($flux['args']['form'], 7); // 'editer_objet' devient 'objet'
	$nom_table	= table_objet_sql($objet);
	$tableau_tables_lim	= explode(',', lire_config('lim_objets'));
	
	if (in_array($nom_table, $tableau_tables_lim)) {
		$tab_rubriques_choisies = lim_publierdansrubriques($objet);
		if (count($tab_rubriques_choisies) == 1) {
			$id_parent = $flux['data']['id_parent'];
			if (empty($id_parent)) {
				$id_parent = implode($tab_rubriques_choisies);
			}
			$flux['data']['_hidden'] = "<input type='hidden' name='id_parent' value='$id_parent'>";
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
			switch ($objet) {
				case 'rubrique':
					$id_rub_en_cours = sql_getfetsel('id_parent', $nom_table, $where);
					break;
				case 'document':
					// rien à faire ici
					break;
				default:
					$id_rub_en_cours = sql_getfetsel('id_rubrique', $nom_table, $where);
					break;
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
