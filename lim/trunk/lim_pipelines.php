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

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/lim_api');

/**
 * gestion forums public et pétitions : supprime ou non le bloc en fonction de la demande 
 *
 * @param array $flux
 * @return array $flux
 *     le flux data remanié
**/
function lim_afficher_config_objet($flux){
	$type = $flux['args']['type'];
	if ($type == 'article' AND !empty($flux['data'])){

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
 * gestion des contenus par rubrique : rediriger la creation d'un objet vers la bonne rubrique si celle-ci est pris en compte par LIM
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
function lim_formulaire_charger($flux){
	$form				= $flux['args']['form'];
	$type				= substr($form, 7); // 'editer_objet' devient 'objet'
	$nom_table			= table_objet_sql($type);
	$tableau_tables_lim	= explode(',', lire_config('lim_objets'));

	if (in_array($nom_table, $tableau_tables_lim)) {
		//echo bel_env($flux);
		
		$tab_rubriques_choisies = lim_publierdansrubriques($type);
		if (count($tab_rubriques_choisies) == 1) {
			$id_parent = $flux['data']['id_parent'];
			if (empty($id_parent)) {
				$id_parent = implode($tab_rubriques_choisies);
			}
			$flux['data']['_hidden'] .= "<input type='hidden' name='id_parent' value='$id_parent'>";
		}
	}
	return $flux;
}

/**
 * gestion des contenus par rubrique : vérifier si on à le droit de publier l'objet dans cette rubrique
 * en fonction des rubriques décochées dans la page exec=configurer_lim_rubriques
 *
 * @param array $flux
 * @return array $flux
 *     le flux data complété ou non d'un message d'erreur
**/
function lim_formulaire_verifier($flux){
	$form				= $flux['args']['form'];
	$type				= substr($form, 7); // 'editer_objet' devient 'objet'
	// $nom_table			= table_objet_sql($type);
	// $tableau_tables_lim	= explode(',', lire_config('lim_objets'));
	$id = 

	$faire = 'creer'.$type.'dans';
	if (!autoriser($faire, 'rubrique', _request('id_parent'))) {
		$flux['data']['id_parent'] .= _T('info_creerdansrubrique_non_autorise');
	}
		

	// if (in_array($nom_table, $tableau_tables_lim)) {
		
	// 	$id_rubrique	= $flux['args']['args'][1];
	// 	$id_rubrique = _request('id_parent');
	// 	$tab_rubriques_choisies = lim_publierdansrubriques($type);

	// 	if (!in_array($id_rubrique, $tab_rubriques_choisies)) {
	// 		$flux['data']['id_parent'] .= "Vous ne pouvez pas publier un $type à l'intérieur de cette rubrique";
	// 	}
	// }
	return $flux;
}


// function lim_editer_contenu_objet($flux){
// 	$type				= $flux['args']['type'];
// 	$nom_table			= table_objet_sql($type);
// 	$tableau_tables_lim = explode(',', lire_config("lim_objets"));

// 	if (in_array($nom_table, $tableau_tables_lim)) {
// 		$id_parent = $flux['args']['contexte']['id_parent'];
// 		if (empty($id_parent)) {
// 			$tab_rubriques_choisies = lim_publierdansrubriques($type);
// 			if (count($tab_rubriques_choisies) == 1) {
// 				$id_parent = implode($tab_rubriques_choisies);
// 			} 
// 			// le cas où plusieurs rubriques possibles : c'est le sélecteur qui gère.
// 			else return $flux;
// 		}
// 		$id_parent_hidden= "<input type='hidden' name='id_parent' value='$id_parent'>";
// 		$flux['data'] = preg_replace('%(<input name="exec(.*?)/>)%is', '$1'."\n".$id_parent_hidden, $flux['data']);
// 	}
// 	return $flux;
// }
?>