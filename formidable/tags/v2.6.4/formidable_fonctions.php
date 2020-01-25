<?php

/**
 * Chargement des fonctions pour les squelettes 
 *
 * @package SPIP\Formidable\Fonctions
**/

include_spip('inc/formidable');
include_spip('public/formidable_criteres');

/**
 * #VOIR_REPONSE{checkbox_2} dans une boucle (FORMULAIRES_REPONSES)
 *
 * @param Pile $p
 * @return Pile
 */
function balise_VOIR_REPONSE_dist($p) {
	$nom = interprete_argument_balise(1, $p);
	if (!$type_retour = interprete_argument_balise(2, $p)) { $type_retour = 'null'; }
	if (!$sans_reponse = interprete_argument_balise(3, $p)) { $sans_reponse = 'null'; }
	$id_formulaires_reponse = champ_sql('id_formulaires_reponse', $p);
	$id_formulaire = champ_sql('id_formulaire', $p);
	$p->code = "calculer_voir_reponse($id_formulaires_reponse, $id_formulaire, $nom, $type_retour, $sans_reponse)";
	return $p;
}

function calculer_voir_reponse($id_formulaires_reponse, $id_formulaire, $nom, $type_retour=null, $sans_reponse=null){
	static $formulaires_saisies = array();
	static $reponses_valeurs = array();
	$tenter_unserialize = charger_fonction('tenter_unserialize', 'filtre/');
	
	// Si pas déjà présent, on cherche les saisies de ce formulaire
	if (is_null($formulaires_saisies[$id_formulaire])) {
		$formulaires_saisies[$id_formulaire] = unserialize(sql_getfetsel('saisies', 'spip_formulaires', 'id_formulaire = '.intval($id_formulaire)));
	}
	// Si pas déjà présent, on cherche les valeurs de cette réponse
	if (is_null($reponses_valeurs[$id_formulaires_reponse])) {
		if ($champs = sql_allfetsel('nom,valeur', 'spip_formulaires_reponses_champs', 'id_formulaires_reponse = '.intval($id_formulaires_reponse))) {
			foreach ($champs as $champ) {
				$reponses_valeurs[$id_formulaires_reponse][$champ['nom']] = $tenter_unserialize($champ['valeur']);
			}
		}
	}
	
	// Si on demande la valeur brute, on ne génère rien, on renvoie telle quelle
	if ($type_retour == 'brut') {
		return $reponses_valeurs[$id_formulaires_reponse][$nom];
	}
	
	// Si on trouve bien la saisie demandée
	if ($saisie = saisies_chercher($formulaires_saisies[$id_formulaire], $nom)) {
		// On génère la vue de cette saisie avec la valeur trouvée précédemment
		return recuperer_fond(
			'saisies-vues/_base',
			array_merge(
				array(
					'type_saisie' => $saisie['saisie'],
					'valeur' => $reponses_valeurs[$id_formulaires_reponse][$nom],
					'valeur_uniquement' => ($type_retour == 'valeur_uniquement' ? 'oui' : 'non'),
					'sans_reponse' => $sans_reponse,
				),
				$saisie['options']
			)
		);
	}
}
