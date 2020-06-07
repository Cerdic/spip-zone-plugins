<?php

/**
 * Retourner le code HTML de la vue d'un champ d'une réponse formidable pour Crayons
 *
 * @param string $type
 *     Type d'objet
 * @param string $modele
 *     Nom du modèle donné par le contrôleur
 * @param int $id
 *     Identifiant de l'objet
 * @param array $content
 *     Couples champs / valeurs postés.
 * @param $wid
 *     Identifiant du formulaire
 */
function vues_formulaires_reponses_champ_dist($type, $modele, $id, $content, $wid) {
	$data = sql_fetsel('spip_formulaires.id_formulaire, spip_formulaires_reponses.id_formulaires_reponse, nom', 'spip_formulaires_reponses_champs JOIN spip_formulaires_reponses JOIN spip_formulaires', "id_formulaires_reponses_champ=$id AND spip_formulaires_reponses.id_formulaires_reponse = spip_formulaires_reponses_champs.id_formulaires_reponse AND spip_formulaires.id_formulaire = spip_formulaires_reponses.id_formulaire");
	return calculer_voir_reponse($data['id_formulaires_reponse'],  $data['id_formulaire'],  $data['nom']);
}
