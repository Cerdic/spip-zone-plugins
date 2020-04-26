<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Ajoute le liens vers le tableau sur les pages des formulaires et des listes de réponses
 * @param array $flux
 * @return array flux_modifie
 **/
function formidable_ts_affiche_gauche($flux) {
	$args = $flux['args'];
	if (in_array($args['exec'], array( 'formulaire', 'formulaires_analyse', 'formulaires_reponses','formulaires_reponse'))) {
		include_spip('inc/presentation');
		if (isset($args['id_formulaire'])) {
			$id_formulaire = $args['id_formulaire'];
		} else {
			$id_formulaire = sql_getfetsel('id_formulaire','spip_formulaires_reponses','id_formulaires_reponse='.$args['id_formulaires_reponse']);
		}
		$boite_fermer = boite_fermer();
		$url = parametre_url(generer_url_ecrire('formidable_tablesorter'),'id_formulaire',$id_formulaire);
		$url = parametre_url($url, 'statut', 'publie');
		$lien = icone_horizontale(_T('formidable_ts:tableau_reponses'), $url, 'formulaire-reponses-24');
		$flux['data'] = str_replace($boite_fermer,"$lien\n\r$boite_fermer", $flux['data']);
	}
	return $flux;
}

/**
 * Pipeline permettant de régler data-sort-value
 * @array $flux 'args' => array(
 *		'type'=> 'extra'/'champ',
 *		'valeur' => ,
 *		'saisie' => ),
 *	'data' => ce qu'on veut retourner dans l'attribut
 * @return $flux
 * Pour l'heure
 *	- les évènements => date brute
 *  - les cxextras date date => $date brut
 *  - les cextras int/float => $valeur brut
**/
function formidable_ts_formidable_ts_data_sort_value($flux) {
	$saisie = $flux['args']['saisie'];
	if ($saisie['saisie'] === 'evenements') {
		$flux['data'] = sql_getfetsel('date_debut', 'spip_evenements', 'id_evenement='.$flux['args']['valeur']);
	}
	if ($flux['args']['type'] === 'extra') {
		if (strpos($saisie['options']['sql'], 'INT') !== false
			or
			strpos($saisie['options']['sql'], 'FLOAT') !== false
			or
			strpos($saisie['options']['sql'], 'DATE') !== false
		)  {
			$flux['data'] = $flux['args']['valeur'];
		}
	}

	return $flux;
}
