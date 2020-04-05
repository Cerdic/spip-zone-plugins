<?php

/***********/
/* Filtres */
/***********/

function enumerer($max) {

	$resultat = array();
	for ($i=0; $i<=$max; $i++) {
		$resultat[] = $i;
	}
	return $resultat;
}

function joindre($tableau, $liant) {

	return implode($liant, $tableau);
}

/**
 * Appliquer récursivement une fonction à une liste de saisies
 *
 * On traite aussi les sous-saisies quand il y en a.
 *
 * @param callable $f : La fonction qu'on applique aux saisies. N'est pas sensée
 *     toucher aux sous-saisies…
 * @param array $saisies : Le tableau de saisies.
 *
 * @return array : La liste après application de la fonction
 */
function saisies_map_recursif($f, $saisies) {

	foreach ($saisies as $i => $saisie) {
		$saisies[$i] = $f($saisie);

		if (isset($saisie['saisies']) and
				is_array($saisie['saisies'])) {
			$saisies[$i]['saisies'] =
				saisies_map_recursif($f, $saisie['saisies']);
		}
	}

	return $saisies;
}

/**
 * preparer_tableau_saisies - convertit un tableau définissant des saisies
 *
 * Convertit un tableau définissant une saisie au format :
 *
 *   array(
 *         'saisie'             => 'type_saisie',
 *         'nom'                => 'nom_saisie',
 *         'un_autre_paramètre' => 'blabla',
 *         'saisies'            => array( … ),
 *        )
 *
 * vers le format exigé par #GENERER_SAISIES.
 *
 * @param array $tableau_saisie
 *     Un tableau au format ci-dessus
 * @return array
 *     Un tableau équivalent au format de #GENERER_SAISIES
 */
function preparer_tableau_saisie($tableau_saisie) {

	if ((! isset($tableau_saisie['saisie'], $tableau_saisie) )
			or ((isset($tableau_saisie['saisies']))
					and (! is_array($tableau_saisie['saisies']) ))
			or ((isset($tableau_saisie['options']))
						and ( ! is_array($tableau_saisie['options']) ))) {
		erreur_squelette(
			_T(
				'erreur_saisie_invalide',
				array(
					'tableau' => var_export(
						$tableau_saisie,
						true
					)
				)
			)
		);
		return;
	}

	$resultat = array(
		'saisie'  => $tableau_saisie['saisie'],
	);
	if (isset($tableau_saisie['saisies'])) {
		$resultat['saisies'] = $tableau_saisie['saisies'];
	}
	if (isset($tableau_saisie['options'])) {
		$resultat['options'] = $tableau_saisie['options'];
	}

	unset($tableau_saisie['saisie']);
	unset($tableau_saisie['saisies']);
	unset($tableau_saisie['options']);

	foreach ($tableau_saisie as $option => $valeur) {
		$resultat['options'][$option] = $valeur;
	}
	return $resultat;
}

/**
 * charger_valeurs - charge des valeurs par défaut dans un tableau de saisies
 *
 * @param array $tableau_saisie
 *     Un tableau de saisies au format de #GENERER_SAISIES représentant
 *     un objet de la saisie liste.
 * @param array $valeurs
 *     Les valeurs pour la saisie liste en entier.
 * @param array $index_objet
 *     L'index de l'objet dont on veut charger les valeurs.
 * @return array
 *     Un tableau de saisies au format de #GENERER_SAISIES représentant
 *     un objet de la saisie liste, dans lequel l'objet $index_objet
 *     a comme valeurs par défaut les valeurs de la $index_objet-ième
 *     ligne du tableau $valeurs.
 */
function charger_valeurs($tableau_saisie, $valeurs, $index_objet) {

	if (! isset($valeurs[$index_objet])) {
		return $tableau_saisie;
	}

	if ($valeurs[ $index_objet ][ $tableau_saisie['options']['nom'] ]) {
		$tableau_saisie['options']['defaut'] = $valeurs[ $index_objet ][ $tableau_saisie['options']['nom'] ];
	} elseif (isset($tableau_saisie['options']['defaut'])) {
		$tableau_saisie['options']['defaut'] = $tableau_saisie['options']['defaut'];
	}

	return $tableau_saisie;
}

/**
 * Fusionner une saisie avec des options passées en argument.
 *
 * Les options sont un tableau de saisies, dont on peut omettre tous les
 * paramètres sauf le nom. Les paramètres des options prennent le pas sur les
 * options définies dans la saisie de base.
 *
 * @param array $tableau_saisie
 *     Un tableau de saisies au format de #GENERER_SAISIES représentant
 *     un objet de la saisie liste.
 * @param array $options_saisies
 *     Des options à remplacer.
 * @return array
 *     Un tableau de saisies correspondant au $tableau_saisie, dans lequel les
 *     options définies dans $options_saisies ont remplacé les valeurs de
 *     départ.
 *
 * @example

var_dump(fusionner_options_saisies(
	array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'test1',
				'label' => 'test1',
			),
		),
		array(
			'saisie' => 'select',
			'options' => array(
				'nom' => 'test2',
				'label' => 'test222',
			),
		),
		array(
			'saisie' => 'select',
			'options' => array(
				'nom' => 'test3',
				'label' => 'test3',
			),
		),
	),
	array(
		array(
			'options' => array(
				'nom' => 'test2',
				'label' => 'test2'
			),
		),
		array(
			'saisie' => 'textarea',
			'options' => array(
				'nom' => 'test3',
			),
		),
	)
));

/** ==>

array(
	array(
		'saisie' => 'input',
		'options' => array(
			'nom' => 'test1',
			'label' => 'test1',
		),
	),
	array(
		'saisie' => 'select',
		'options' => array(
			'nom' => 'test2',
			'label' => 'test2',
		),
	),
	array(
		'saisie' => 'textarea',
		'options' => array(
			'nom' => 'test3',
			'label' => 'test3',
		),
	),
)
**/
function fusionner_options_saisies($tableau_saisie, $options_saisies) {

	if (is_array($options_saisies)) {
		foreach ($options_saisies as $options) {
			$nom_option = $options['options']['nom'];
			foreach ($tableau_saisie as $i => $saisie) {
				if ($saisie['options']['nom'] === $nom_option) {
					if (isset($options['saisie'])) {
						$tableau_saisie[$i]['saisie'] = $options['saisie'];
					}
					foreach ($options['options'] as $cle => $val) {
						$tableau_saisie[$i]['options'][$cle] = $val;
					}
					if (isset($options['saisies'])) {
						$tableau_saisie[$i]['saisies'] = fusionner_options_saisies(
							$tableau_saisie[$i]['saisies'],
							$options['saisies']
						);
					}
				}
			}
		}
	}

	return $tableau_saisie;
}


/**
 * renommer_saisies - renomme les saisies d'un objet d'une saisie liste_objet
 * pour en faire des sous-saisies.
 *
 * Parcours les noms de l'objet, et change "nom" en
 * "nom-saisie-liste-objet[$index_objet][nom]"
 *
 * @param array $tableau_saisie
 *     Un tableau de saisies au format de #GENERER_SAISIES représentant
 *     un objet de la saisie liste.
 * @param array $index_objet
 *     L'index de l'objet en cours de traitement
 * @param array $nom_saisie_liste
 *     Le nom de la saisie liste
 * @return array
 *     Le tableau $tableau_saisie dans lequels on a renommé les saisies.
 */
function renommer_saisies($tableau_saisie, $index_objet, $nom_objet) {

	$renommer = function ($saisie) use ($index_objet, $nom_objet) {
		$nom = $saisie['options']['nom'];

		// Si le nom contient des [], il faut les sortir des crochets qu'on met
		// autour du nom :
		//   blabla					=> [blabla]
		//   bla[bli][blou] => [bla][bli][blou]
		$pos = strpos($nom, '[');
		if ($pos === false) {
			$suffixe_nom = "[$nom]";
		} else {
			$suffixe_nom = sprintf('[%s]%s', substr($nom, 0, $pos), substr($nom, $pos));
		}

		$saisie['options']['nom'] =
			sprintf('%s[%s]%s', $nom_objet, $index_objet, $suffixe_nom);
		return $saisie;
	};

	$tableau_saisie = $renommer($tableau_saisie);

	if (isset($tableau_saisie['saisies']) and
			is_array($tableau_saisie['saisies'])) {
		$tableau_saisie['saisies'] = saisies_map_recursif(
			$renommer,
			$tableau_saisie['saisies']
		);
	}

	return $tableau_saisie;
}

/****************************/
/* Traitements du formulaire */
/****************************/

/**
 * Déterminer si une valeur n'est pas « vide »
 *
 * On considère qu'un string vide, ou un tableau dont les valeurs sont toutes
 * des strings vides sont vides. Tout le reste n'est pas vide.
 *
 * @param mixed $valeur : Une valeur dont on teste la non-vacuité
 * @return bool
 */
function est_non_vide($valeur) {
	$resultat = false;

	if (is_array($valeur)) {
		foreach ($valeur as $v) {
			if (est_non_vide($v)) {
				$resultat = true;
				break;
			}
		}
	} elseif (is_string($valeur)) {
		if ($valeur !== '') {
			$resultat = true;
		}
	} else {
		$resultat = true;
	}

	return $resultat;
}

/**
 * filtrer_valeurs - filtre un tableau de valeurs pour retirer les infos
 *     qui n'importent que pour le fonctionnement interne de la saisie
 *     liste. Retire aussi les valeurs vides.
 *
 * @param array $valeurs
 *     Les valeurs retournées par _request('nom-saisie-liste')
 * @return array
 *     Les valeurs prêtes à être utilisées dans les fonctions verifier et traiter.
 */
function filtrer_valeurs($valeurs) {

	unset($valeurs['action']);
	unset($valeurs['permutations']);

	return array_filter($valeurs, 'est_non_vide');
}

/**
 * permuter - Permute les index d'un tableau selon un permutation donnée.
 *
 * @param array $tableau
 *     un tableau indexé par des nombres entiers.
 * @param array permutations
 *     un tableau de même taille représentant une permutation.
 *     P.ex ce tableau de permutation :
 *         array(
 *               0 => 2,
 *               1 => 1,
 *               2 => 0,
 *              )
 *     permet d'échanger les valeurs de la première et la dernière ligne
 *     d'un tableau a 3 éléments.
 * @return array
 *     Le tableau après permutation.
 */
function permuter($tableau, $permutations) {

	$resultat = array();
	for ($i=0; $i<count($permutations); $i++) {
		$resultat[$i] = $tableau[$permutations[$i]];
	}
	return $resultat;
}

/**
 * executer_actions_liste_objet - execute les actions demandées par la
 *     valeur associée à la clé 'action' d'un tableau de valeurs retourné
 *     par une saisie liste
 *
 * @param array $valeurs
 *     un tableau de valeurs retourné par une saisie liste
 * @return array
 *     Le tableau après execution des actions.
 */
function executer_actions_liste_objet($valeurs) {

	if (isset($valeurs['permutations'])) {
		$permutations = explode(',', $valeurs['permutations']);
	} else {
		$permutations = range(0, count($tableau));
	}

	if (array_key_exists('action', $valeurs)) {
		foreach ($valeurs['action'] as $details_action => $valeur_submit) {
			$details_action = explode('-', $details_action);
			$action      = $details_action[0];
			$index_objet = isset($details_action[1]) ? $details_action[1] : null;
			switch ($action) {
				case 'supprimer':
					unset($valeurs[intval($index_objet)]);
					break;
				case 'ajouter':
					// on n'as rien à faire pour ajouter un objet, il suffit de
					// recharger le formulaire
					break;
				case 'monter':
					// il faut opérer sur la liste des permutations, parce ce qu'elle
					// correspond à l'ordre des objets affichés quand l'utilisateur
					// a submit.
					$index_objet     = array_search($index_objet, $permutations);
					$objet_au_dessus = $permutations[$index_objet-1];
					$permutations[$index_objet-1] = $permutations[$index_objet];
					$permutations[$index_objet]   = $objet_au_dessus;
					break;
				case 'descendre':
					$index_objet      = array_search($index_objet, $permutations);
					$objet_en_dessous = $permutations[$index_objet+1];
					$permutations[$index_objet+1] = $permutations[$index_objet];
					$permutations[$index_objet]   = $objet_en_dessous;
					break;
			}
		}
	}
	return filtrer_valeurs(permuter($valeurs, $permutations));
}

/**
 * traitements_liste - execute les traitements nécessaire pour
 *     le bon fonctionnement d'une saisie liste_objet.
 *
 * @param string $nom_saisie
 *     le nom d'une saisie liste
 * @param string $appelant
 *     le contexte dans lequel la fonction est appelée. Deux valeurs
 *     sont possibles : 'verifier' ou 'traiter'
 * @return bool
 *     TRUE si l'on souhaite interrompre les traitements définis par les
 *     fonctions verifier et traiter du formulaire. FALSE, sinon.
 */
function traitements_liste($nom_saisie, $appelant) {

	static $interrompre_traitements_formulaire = array();

	/* cette fonction est appellée dans vérifier, puis dans traiter.
	   La première fois on calcule la valeur de $interrompre_traitements_formulaire,
	   et la deuxième fois on ne fais que la retourner. */
	if ($appelant === 'verifier') {
		$interrompre_traitements_formulaire[$nom_saisie] = false;
	} elseif ($appelant === 'traiter') {
		return $interrompre_traitements_formulaire[$nom_saisie];
	}

	$valeurs = _request($nom_saisie) ? _request($nom_saisie) : array();

	if (array_key_exists('action', $valeurs)) {
		$interrompre_traitements_formulaire[$nom_saisie] = true;
		if (isset($valeurs['action']['ajouter'])) {
			set_request("saisie_liste-$nom_saisie-ajouter", 'oui');
		}
	}

	$valeurs = executer_actions_liste_objet($valeurs);
	set_request($nom_saisie, $valeurs);

	return $interrompre_traitements_formulaire[$nom_saisie];
}

function traitements_listes($saisies, $appelant) {

	if (! is_array($saisies)) {
		$saisies = array($saisies);
	}

	$resultat = false;

	foreach ($saisies as $nom_saisie) {
		if (traitements_liste($nom_saisie, $appelant)) {
			$resultat = $nom_saisie;
		}
	}
	return $resultat;
}

/**
 * verifier et préparer les valeurs de saisies liste
 *
 * @param mixed $saisies  Le nom d'une saisie liste ou une liste de nom de
 *                        saisies liste.
 * @return mixed   Retourne FALSE si le submit cliqué n'est pas un submit
 *                 de saisie liste. On peut alors continuer les
 *                 vérifications.
 *                 Si le submit est un submit d'une saisie liste, on
 *                 retourne le nom de la saisie en question.
 */
function saisies_liste_verifier($saisies) {

	return traitements_listes($saisies, 'verifier');
}

/**
 * traiter les valeurs de saisies liste
 *
 * @param mixed $saisies  Le nom d'une saisie liste ou une liste de nom de
 *                        saisies liste.
 * @return mixed   Retourne FALSE si le submit cliqué n'est pas un submit
 *                 de saisie liste. On peut alors continuer les
 *                 vérifications.
 *                 Si le submit est un submit d'une saisie liste, on
 *                 retourne le nom de la saisie en question.
 */
function saisies_liste_traiter($saisies) {

	return traitements_listes($saisies, 'traiter');
}
