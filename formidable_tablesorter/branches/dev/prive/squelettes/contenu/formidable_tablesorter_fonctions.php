<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/saisies');
include_spip('inc/saisies_lister');

/**
 * Affiche les valeurs des champs extras sous forme de <td>, en excluant les explications
 * @param array $cextras le tableau des cextras
 * @param int $id_formulaire_reponse
 * @return string > la listes des valeur, séparés par des td
**/
function cextras2td($cextras, $id_formulaires_reponse) {
	$txt = '';
	$sql = sql_fetsel('*', 'spip_formulaires_reponses', "id_formulaires_reponse=$id_formulaires_reponse");
	foreach ($cextras as $champ) {
		if ($champ['saisie'] != 'explication') {

			// Crayonnage
			if (test_plugin_actif('crayons')) {
				$attributs = ' class="'.classe_boucle_crayon('formulaires_reponse', $champ['options']['nom'], $id_formulaires_reponse).'"';
			} else {
				$atrributs = '';
			}

			// Data-sort
			if ($sort = formidable_ts_data_sort_value($sql[$champ['options']['nom']], $champ, 'extra')) {
				$attributs .= " data-sort-value='$sort'";
			}

			// Comment afficher le champ ? avec traitement ou de manière brut/liste valeur ?
			if (isset($champ['options']['traitements'])) {
				$valeur = appliquer_traitement_champ($sql[$champ['options']['nom']], $champ['options']['nom'], 'formulaires_reponse');
			} else {
				$valeur = implode(calculer_balise_LISTER_VALEURS('formulaires_reponses', $champ['options']['nom'],$sql[$champ['options']['nom']]), ', ');
			}

			$txt .= "<td$attributs>$valeur</td>";
		}
	}
	return $txt;
}

/**
 * Appelle le pipeline formidable_ts_data_sort_value
 * Pour rempli l'attribut data-sort-value sur les td du tableau
 * @param str|int $valeur valeur brute du champ de formulaire
 * @param array $saisie decrit la saisie
 * @param string $type='champ' ou bien 'extra'
 * @return string valeur du data-sort-attribut
 **/
function formidable_ts_data_sort_value($valeur, $saisie, $type = 'champ') {
	return pipeline ('formidable_ts_data_sort_value', array(
		'args' => array(
			'valeur' => $valeur,
			'saisie' => $saisie,
			'type' => $type
		),
		'data' => ''
	));
}
