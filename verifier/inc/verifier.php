<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/*
 * Fonction de base de l'API de vérification.
 * @param mixed $valeur La valeur a verifier.
 * @param string $type Le type de verification a appliquer.
 * @param array $options Un eventuel tableau d'options suivant le type.
 * @return string Retourne une chaine vide si c'est valide, sinon une chaine expliquant l'erreur.
 */
function verifier($valeur, $type, $options=null){

	// On vérifie que les options sont bien un tableau
	if (!is_array($options))
		$options = array();
	
	// Si la valeur est vide, il n'y a rien a verifier donc c'est bon
	if (!$valeur) return '';
	
	// On cherche si une fonction correspondant au type existe
	if ($verifier = charger_fonction($type, 'verifier/')){
		$erreur = $verifier($valeur, $options);
	}
	
	// On passe le tout dans le pipeline du meme nom
	$erreur = pipeline(
		'verifier',
		array(
			'args' => array(
				'valeur' => $valeur,
				'type' => $type,
				'options' => $options
			),
			'data' => $erreur
		)
	);
	
	return $erreur;
}

/*
 * Vérifier tout un formulaire tel que décrit avec les Saisies
 * @param array $formulaire Le contenu d'un formulaire décrit dans un tableau de Saisies
 * @return array Retourne un tableau d'erreurs
 */
function verifier_saisies($formulaire){
	$erreurs = array();
	
	$saisies = saisies_recuperer_saisies($formulaire);
	foreach ($saisies as $saisie){
		$obligatoire = $saisie['options']['obligatoire'];
		$champ = $saisie['options']['nom'];
		$verifier = $saisie['verifier'];
		
		// On regarde d'abord si le champ est obligatoire
		if ($obligatoire and $obligatoire != 'non' and ($valeur=_request($champ)) == '')
			$erreurs[$champ] = _T('info_obligatoire');
		
		// On continue seulement si ya pas d'erreur d'obligation et qu'il y a une demande de verif
		if (!$erreurs[$champ] and is_array($verifier)){
			// Si le champ n'est pas valide par rapport au test demandé, on ajoute l'erreur
			if ($erreur_eventuelle = verifier($valeur, $verifier['type'], $verifier['options']))
				$erreurs[$champ] = $erreur_eventuelle;
		}
	}
	
	return $erreurs;
}

?>
