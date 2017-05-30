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
include_spip('inc/config');

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
	$form	= $flux['args']['form'];
	$valid 	= strpos($form, 'editer');
	// si ce n'est pas un formulaire d'édition 
	//ou si la restriction par rubrique n'a pas été activée, on sort
	if ($valid === false OR is_null(lire_config('lim_objets'))) return $flux;


	$type				= substr($form, 7); // 'editer_objet' devient 'objet'
	$nom_table			= table_objet_sql($type);
	$tableau_tables_lim	= explode(',', lire_config('lim_objets'));
	
	if (in_array($nom_table, $tableau_tables_lim)) {
		$tab_rubriques_choisies = lim_publierdansrubriques($type);
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
 * gestion des contenus par rubrique : vérifier si on à le droit de publier l'objet dans cette rubrique
 * en fonction des rubriques décochées dans la page exec=configurer_lim_rubriques
 *
 * @param array $flux
 * @return array $flux
 *     le flux data complété ou non d'un message d'erreur
**/
function lim_formulaire_verifier($flux){
	$form	= $flux['args']['form'];
	$valid	= strpos($form, 'editer');

	// si ce n'est pas un formulaire d'édition 
	//ou si la restriction par rubrique n'a pas été activée, on sort
	if ($valid === false OR is_null(lire_config('lim_objets'))) return $flux;
	
	$type	= substr($form, 7); // 'editer_objet' devient 'objet'
	$nom_table			= table_objet_sql($type);
	$tableau_tables_lim	= explode(',', lire_config('lim_objets'));

	if (in_array($nom_table, $tableau_tables_lim)) {
		include_spip('inc/autoriser');

		// Si modification : le rédacteur doit pouvoir modifier le contenu d'un objet existant, 
		// même ci celui-ci est maintenant dans une rubrique où il est interdit de publier cet objet.
		$id_objet = $flux['args']['args'][0];
		if (is_numeric($id_objet)) { 	// c'est donc une modification, 

			// récupérer l'id_rubrique actuel (en BdD) de l'objet 
			$faire = 'publierdans';
			$where = id_table_objet($type).'='.$id_objet;
			if ($type == 'rubrique')
				$id_rub_en_cours = sql_getfetsel('id_parent', $nom_table, $where);
			else
				$id_rub_en_cours = sql_getfetsel('id_rubrique', $nom_table, $where);
			$opt = array('lim_except_rub' => $id_rub_en_cours, 'type' => $type);
			$msg_error = _T('lim:info_deplacer_dans_rubrique_non_autorise');
		}
		else { //c'est une création
			// en fait, cela ne sert à rien...snif...à cause de /echafaudage qui intercepte les créations avant le CVT (?!).
			$faire = 'creer'.$type.'dans';
			$opt = null;
			$msg_error = _T('lim:info_creer_dans_rubrique_non_autorise');
		}

		// mise en berne car il faudrait pourvoir gérer les cas suivants :
		// 1- cas de la création : voir #122 (juste au dessus)
		// 2- en l'état avec SPIP, impossible de surcharger deux fois une autorisation. Du coup devient compliqué de gérer aussi le cas des rédacteurs
		// voir à ce propos : https://www.spip.net/fr_article3517.html et https://core.spip.net/projects/spip/repository/entry/spip/ecrire/inc/autoriser.php#L555
		/*
		if (!autoriser($faire, 'rubrique', _request('id_parent'),'', $opt)) {
			$flux['data']['id_parent'] = $msg_error;
		}
		*/
	}
	return $flux;
}

/**
 * Inserer le JS qui gére l'affichage ou non des champs dans certains formulaires historiques
 * juste le formulaire Auteur (pour l'instant ?)
 *
 * @param array $flux
 * @return array
 */
function lim_recuperer_fond($flux){
	if ($flux['args']['fond'] == "formulaires/editer_auteur") {
		$ajout_script = recuperer_fond('prive/squelettes/inclure/lim');
		$flux['data']['texte'] = str_replace('</form>', '</form>'. $ajout_script, $flux['data']['texte']);
	}
	return $flux;
}

?>