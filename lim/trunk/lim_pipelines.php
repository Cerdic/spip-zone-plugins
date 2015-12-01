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


/**
 * supprime ou non le bloc en fonction de la demande 
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
 * vérifier si on à le droit de publier l'objet dans cette rubrique
 * en fonction des paramètres spécifiés dans la page exec=configurer_lim_rubriques
 *
 * @param array $flux
 * @return array $flux
 *     le flux data complété ou non d'un message d'erreur
**/
function lim_formulaire_verifier($flux){
	$form				= $flux['args']['form'];
	$nom_objet			= substr($form, 7); // 'editer_objet' devient 'objet'
	$nom_table			= table_objet_sql($nom_objet);
	$tableau_tables_lim	= explode(',', lire_config('lim_objets'));

	if (in_array($nom_table, $tableau_tables_lim)) {
		$id_objet	= $flux['args']['args'][0];
		$cle_table	= id_table_objet($nom_objet);
		$quelles_rubriques = lire_config("lim_rubriques/$nom_objet");
		$id_rub = sql_getfetsel('id_rubrique', $nom_table, "$cle_table=".intval($id_objet));

		if (is_array($quelles_rubriques) AND in_array($id_rub,$quelles_rubriques)) {
			// echo 'lalala<br>';
			// echo $nom_table.'<br>';
			// echo bel_env($quelles_rubriques);

			// echo $id_rub;
			// exit();
			$flux['data']['id_parent'] = "Vous ne pouvez pas publier un $nom_objet à l'intérieur de cette rubrique";
		}
			
		//echo bel_env($flux);
		// echo $id_objet.'<br>';
		// echo $nom_objet.'<br>';
		// echo $nom_table.'<br>';
		// echo $cle_table.'<br>';
		// echo bel_env($quelles_rubriques);
		// echo $id_rub;

		// is_null($quelles_rubriques) ? $lim_rub = true : $lim_rub = !in_array($id,$quelles_rubriques);
		// exit();
	}
	return $flux;
	
}
?>