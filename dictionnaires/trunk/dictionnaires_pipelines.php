<?php

/**
 * Usage de pipelines
 * 
 * @package SPIP\Dictionnaires\Pipelines
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Crée des liaisons entre les objets et les définitions.
 *
 * Pour chaque objet édité, regarde si les contenus possèdent des définitions
 * que l'on connaît et dans ce cas crée une liaison entre l'objet et la définition.
 * 
 * Cela permet de connaître, pour une définition donnée, la liste des
 * objets sur lesquels est rattaché une définition. À l'inverse, cela
 * permet, pour un objet de connaître les définitions qu'il possède.
 *
 * @todo
 *     Tout cela est à faire !!!
 * 
 * @pipeline post_edition 
 *
 * @param array $flux   Données du pipeline
 * @return array        Données du pipeline
**/
function dictionnaires_post_edition($flux){
	// TOUT CELA EST A FAIRE
	return $flux;

	// Seulement si c'est une modif d'objet
	if ($flux['args']['action'] == 'modifier' and $id_objet = $flux['args']['id_objet']){
		$trouver_table = charger_fonction('trouver_table', 'base/');
		$desc = $trouver_table($flux['args']['table_objet'], $flux['args']['serveur']);
		$id_table_objet = id_table_objet($flux['args']['type']);
		
		// On cherche les champs textuels
		$champs_texte = array();
		foreach ($desc['field'] as $champ=>$sql){
			if (preg_match('/(text|blob|varchar)/', $sql)){
				$champs_texte[] = $champ;
			}
		}
		
		// On récupère ces champs
		$textes = sql_fetsel($champs_texte, $flux['args']['spip_table_objet'], "$id_table_objet = $id_objet");
		// On récupère les définitions
		include_spip('inc/dictionnaires');
		$definitions = dictionnaires_lister_definitions();
		
		// On les scanne
		foreach ($textes as $texte){
			
		}
	}
}

/**
 * Ajoute pour les textes passés à propre les définitions sur les
 * termes à définir.
 *
 * Les définitions sont calculées ici uniquement si le dictionnaire
 * n'est pas en mode manuel. Ce mode est activable par la constante
 * DICTIONNAIRES_DETECTION_MANUELLE mise à TRUE.
 * 
 * @pipeline post_edition 
 *
 * @param string $texte  Texte 
 * @return string        Texte
**/
function dictionnaires_post_propre($texte) {
	static $filtre_definitions = false;

	// lorsqu'il n'est pas demandé explicitement un usage
	// manuel, appliquer automatiquement la recherche de terme
	if (!defined('DICTIONNAIRES_DETECTION_MANUELLE')
		OR !DICTIONNAIRES_DETECTION_MANUELLE)
	{
		if (!$filtre_definitions) {
			$filtre_definitions = charger_filtre('definitions');
		}
		$texte = $filtre_definitions($texte);
	}

	return $texte;
}


?>
