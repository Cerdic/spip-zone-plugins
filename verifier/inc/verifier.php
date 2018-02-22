<?php
/**
 * Fonctions de l'API de vérification
 *
 * @plugin     verifier
 * @copyright  2018
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction de base de l'API de vérification.
 *
 * @param mixed $valeur La valeur a verifier.
 * @param string $type Le type de verification a appliquer.
 * @param array $options Un eventuel tableau d'options suivant le type.
 * @param array $valeur_normalisee
 * 		Si des options de verification modifient la valeur entrante (normalisent),
 * 		alors la valeur modifie sera stockee dans cette variable.
 * @return string Retourne une chaine vide si c'est valide, sinon une chaine expliquant l'erreur.
 */
function inc_verifier_dist($valeur, $type, $options = null, &$valeur_normalisee = null) {

	$erreur = array();
	// On vérifie que les options sont bien un tableau
	if (!is_array($options)) {
		$options = array();
	}

	// Si la valeur est vide, il n'y a rien a verifier donc c'est bon
	if (is_null($valeur) or (is_string($valeur) and $valeur == '')) {
		return '';
	}
	// Si c'est une date avec horaire c'est un tableau
	if (is_array($valeur) and isset($valeur['date']) and $valeur['date'] == '') {
		return '';
	}

	// On cherche si une fonction correspondant au type existe
	if ($verifier = charger_fonction($type, 'verifier', true)) {
		$erreur = $verifier($valeur, $options, $valeur_normalisee);
	}

	// On passe le tout dans le pipeline du meme nom
	$erreur = pipeline(
		'verifier',
		array(
			'args' => array(
				'valeur' => $valeur,
				'type' => $type,
				'options' => $options,
			),
			'data' => $erreur
		)
	);

	return $erreur;
}

/**
 * Liste toutes les vérifications possibles
 *
 * @param string $repertoire
 * 		Dans quel repertoire chercher les yaml.
 *
 * @return array Retourne un tableau listant les vérifications et leurs options
 */
function verifier_lister_disponibles($repertoire = 'verifier') {
	static $verifications = array();

	if (!isset($verifications[$repertoire])) {
		$verifications[$repertoire] = array();
		$liste = find_all_in_path("$repertoire/", '.+[.]yaml$');

		if (count($liste)) {
			foreach ($liste as $fichier => $chemin) {
				$type = preg_replace(',[.]yaml$,i', '', $fichier);
				$dossier = str_replace($fichier, '', $chemin);
				// On ne garde que les vérifications qui ont bien la fonction !
				if (charger_fonction($type, $repertoire, true)
					and (
						is_array($verif = verifier_charger_infos($type, $repertoire))
					)
				) {
					$verifications[$repertoire][$type] = $verif;
				}
			}
		}
	}

	return $verifications[$repertoire];
}

/**
 * Fonction de callback pour uasort()
 * Afin de trier selon le titre
 * @param array $array1 premier élèment
 * @param array $array2 second élèment
 * @return int 1,0,-1
 **/
function verifier_trier_par_titre($array1,$array2) {
	if (isset($array1['titre']) and isset($array2['titre'])) {
		if ($array1['titre'] == $array2['titre']) {
			return 0;
		} elseif ($array1['titre'] > $array2['titre']) {
			return 1;
		} else {
			return -1;
		}
	} else {
		return 0;
	}
}

/**
 * Charger les informations contenues dans le yaml d'une vérification
 *
 * @param string $type_verif
 * 		Le type de la vérification
 *
 * @param string $repertoire
 * 		Dans quel repertoire chercher les yaml.
 *
 * @return array Un tableau contenant le YAML décodé
 */
function verifier_charger_infos($type_verif, $repertoire = 'verifier') {
	$verif = array();	

	if (defined('_DIR_PLUGIN_YAML')) {
		include_spip('inc/yaml');
		$fichier = find_in_path("$repertoire/$type_verif.yaml");

		$verif = yaml_decode_file($fichier);
		if (is_array($verif)) {
			$verif['titre']       = (isset($verif['titre'])       and $verif['titre'])       ? _T_ou_typo($verif['titre']) : $type_verif;
			$verif['description'] = (isset($verif['description']) and $verif['description']) ? _T_ou_typo($verif['description']) : '';
			$verif['icone']       = (isset($verif['icone'])       and $verif['icone'])       ? _T_ou_typo($verif['icone']) : '';
		}
	}
	return $verif;
}
