<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/*
 * Fonction de base de l'API de vérification.
 * @param mixed $valeur La valeur a verifier.
 * @param string $type Le type de verification a appliquer.
 * @param array $options Un eventuel tableau d'options suivant le type.
 * @return string Retourne une chaine vide c'est valide, sinon une chaine expliquant l'erreur.
 */
function verifier($valeur, $type, $options=array()){

	// Si la valeur est vide, il n'y a rien a verifier donc c'est bon
	if (!$valeur) return true;
	
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

?>
