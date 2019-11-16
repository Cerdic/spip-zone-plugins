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
			$txt .= '<td>'.implode(calculer_balise_LISTER_VALEURS('formulaires_reponses', $champ['options']['nom'],$sql[$champ['options']['nom']]), ', ').'</td>';
		}
	}
	return $txt;
}
