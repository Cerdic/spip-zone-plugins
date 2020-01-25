<?php

/**
 * Gestion de listes des saisies.
 *
 * @return SPIP\Saisies\Listes
 **/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Prend la description complète du contenu d'un formulaire et retourne
 * les saisies "à plat" classées par identifiant unique.
 *
 * @param array $contenu        Le contenu d'un formulaire
 * @param bool  $avec_conteneur Indique si on renvoie aussi les saisies ayant des enfants, comme les fieldsets
 *
 * @return array Un tableau avec uniquement les saisies
 */
function saisies_lister_par_identifiant($contenu, $avec_conteneur = true) {
	$saisies = array();

	if (is_array($contenu)) {
		foreach ($contenu as $ligne) {
			if (is_array($ligne)) {
				$enfants_presents = (isset($ligne['saisies']) and is_array($ligne['saisies']));
				if (array_key_exists('saisie', $ligne) and (!$enfants_presents or $avec_conteneur)) {
					$saisies[$ligne['identifiant']] = $ligne;
				}
				if ($enfants_presents) {
					$saisies = array_merge($saisies, saisies_lister_par_identifiant($ligne['saisies']));
				}
			}
		}
	}

	return $saisies;
}

/**
 * Prend la description complète du contenu d'un formulaire et retourne
 * les saisies "à plat" classées par nom.
 *
 * @param array $contenu        Le contenu d'un formulaire
 * @param bool  $avec_conteneur Indique si on renvoie aussi les saisies ayant des enfants, comme les fieldset
 *
 * @return array Un tableau avec uniquement les saisies
 */
function saisies_lister_par_nom($contenu, $avec_conteneur = true) {
	$saisies = array();
	if (is_array($contenu)) {
		foreach ($contenu as $ligne) {
			if (is_array($ligne)) {
				if (
					array_key_exists('saisie', $ligne)
					and (!isset($ligne['saisies']) or !is_array($ligne['saisies']) or $avec_conteneur)
					and isset($ligne['options'])
				) {
					$saisies[$ligne['options']['nom']] = $ligne;
				}
				if (isset($ligne['saisies']) and is_array($ligne['saisies'])) {
					$saisies = array_merge($saisies, saisies_lister_par_nom($ligne['saisies']));
				}
			}
		}
	}

	return $saisies;
}

/**
 * Liste les saisies en parcourant tous les niveau de la hiérarchie, et en excluant les saisies ayant des sous-saisies
 *
 *
 * @param array  $saisies Liste de saisies
 *
 * @return liste de ces saisies triées selon l'ordre de déclaration initiale
 */
function saisies_lister_finales($saisies) {
	$saisies_retour = array();
	foreach ($saisies as $identifiant => $saisie) {
		if (isset($saisie['saisies'])) {
			$saisies_retour = array_merge($saisies_retour, saisies_lister_finales($saisie['saisies']));
		} elseif (isset($saisie['saisie'])) {// pour ne pas avoir les options gloables des saisies
			$saisies_retour[] = $saisie;
		}
	}
	return $saisies_retour;
}
/**
 * Liste les saisies ayant une option X
 * # saisies_lister_avec_option('sql', $saisies);.
 *
 *
 * @param string $option  Nom de l'option cherchée
 * @param array  $saisies Liste de saisies
 * @param string $tri     tri par défaut des résultats (s'ils ne sont pas deja triés) ('nom', 'identifiant')
 *
 * @return liste de ces saisies triees par nom ayant une option X définie
 */
function saisies_lister_avec_option($option, $saisies, $tri = 'nom') {
	$saisies_option = array();

	// tri par nom si ce n'est pas le cas
	$s = array_keys($saisies);
	if (is_int(array_shift($s))) {
		$trier = 'saisies_lister_par_'.$tri;
		$saisies = $trier($saisies);
	}

	foreach ($saisies as $nom_ou_id => $saisie) {
		if (isset($saisie['options'][$option]) and $saisie['options'][$option]) {
			$saisies_option[$nom_ou_id] = $saisie;
		}
	}

	return $saisies_option;
}

/**
 * Liste les saisies ayant une definition SQL.
 *
 * @param array  $saisies liste de saisies
 * @param string $tri     tri par défaut des résultats (s'ils ne sont pas deja triés) ('nom', 'identifiant')
 *
 * @return liste de ces saisies triees par nom ayant une option sql définie
 */
function saisies_lister_avec_sql($saisies, $tri = 'nom') {
	return saisies_lister_avec_option('sql', $saisies, $tri);
}

/**
 * Liste les saisies d'un certain type.
 *
 * @example `$saisies_date = saisies_lister_avec_type($saisies, 'date')`
 *
 * @param array  $saisies liste de saisies
 * @param string|array $type    Type de la saisie, ou tableau de types
 * @param string $tri     tri par défaut des résultats (s'ils ne sont pas deja triés) ('nom')
 * @param bool avec_conteneur faut-il conserver l'arbo?
 *
 * @return liste de ces saisies triees par nom
 */
function saisies_lister_avec_type($saisies, $type, $tri = 'nom', $avec_conteneur = false) {
	if (!is_array($type)) {
		$type = array($type);
	}
	$saisies_type = array();

	// tri par nom si ce n'est pas le cas
	$s = array_keys($saisies);
	if (is_int(array_shift($s)) and $tri and !$avec_conteneur) {
		$trier = 'saisies_lister_par_'.$tri;
		$saisies = $trier($saisies);
	}

	foreach ($saisies as $nom_ou_id => $saisie) {
		if (in_array($saisie['saisie'], $type)) {
			if ($avec_conteneur and isset($saisie['saisies'])) {
				$saisie['saisies'] = saisies_lister_avec_type($saisie['saisies'], $type, $tri, $avec_conteneur);
			}
			$saisies_type[$nom_ou_id] = $saisie;
		}
	}
	return $saisies_type;
}

/**
 * Prend la description complète du contenu d'un formulaire et retourne
 * les saisies "à plat" classées par type de saisie.
 * $saisie['input']['input_1'] = $saisie.
 *
 * @param array $contenu Le contenu d'un formulaire
 *
 * @return array Un tableau avec uniquement les saisies
 */
function saisies_lister_par_type($contenu) {
	$saisies = array();

	if (is_array($contenu)) {
		foreach ($contenu as $ligne) {
			if (is_array($ligne)) {
				if (array_key_exists('saisie', $ligne) and  (!isset($ligne['saisies']))) {
					$saisies[ $ligne['saisie'] ][ $ligne['options']['nom'] ] = $ligne;
				}
				if (isset($ligne['saisies']) and is_array($ligne['saisies'])) {
					$saisies = array_merge_recursive($saisies, saisies_lister_par_type($ligne['saisies']));
				}
			}
		}
	}

	return $saisies;
}

/**
 * Liste les saisies par étapes s'il y en a
 *
 * @param array $saisies
 * 		Liste des saisies
 * @return array|bool
 * 		Retourne un tableau associatif par numéro d'étape avec pour chacune leurs saisies, false si pas d'étapes
 */
function saisies_lister_par_etapes($saisies) {
	$saisies_etapes = false;
	$etapes = 0;

	if (isset($saisies['options']['etapes_activer']) and $saisies['options']['etapes_activer']) {
		// Un premier parcourt pour compter les étapes
		foreach ($saisies as $cle => $saisie) {
			if (is_array($saisies) and $saisie['saisie'] == 'fieldset') {
				$etapes++;
			}
		}

		// Seulement s'il y a au moins deux étapes
		if ($etapes > 1) {
			$saisies_etapes = array();
			$compteur_etape = 0;

			// On reparcourt pour lister les saisies
			foreach ($saisies as $cle => $saisie) {
				// Si c'est un groupe, on ajoute son contenu à l'étape
				if (isset($saisie['saisie']) and $saisie['saisie'] == 'fieldset') {
					$compteur_etape++;
					// S'il y a eu des champs hors groupe avant, on fusionne
					if (isset($saisies_etapes[$compteur_etape]['saisies'])) {
						$saisies_precedentes = $saisies_etapes[$compteur_etape]['saisies'];
						$saisies_etapes[$compteur_etape] = $saisie;
						$saisies_etapes[$compteur_etape]['saisies'] = array_merge($saisies_precedentes, $saisie['saisies']);
					}
					else {
						$saisies_etapes[$compteur_etape] = $saisie;
					}
				}
				// Sinon si champ externe à un groupe, on l'ajoute à toutes les étapes
				elseif (isset($saisie['saisie'])) {
					for ($e=1;$e<=$etapes;$e++) {
						if (!isset($saisies_etapes[$e]['saisies'])) {
							$saisies_etapes[$e] = array('saisies'=>array());
						}
						array_push($saisies_etapes[$e]['saisies'], $saisie);
					}
				}
			}
		}
	}

	return $saisies_etapes;
}

/**
 * Prend la description complète du contenu d'un formulaire et retourne
 * une liste des noms des champs du formulaire.
 *
 * @param array $contenu        Le contenu d'un formulaire
 * @param bool  $avec_conteneur Indique si on renvoie aussi les saisies ayant des enfants, comme les fieldset
 *
 * @return array Un tableau listant les noms des champs
 */
function saisies_lister_champs($contenu, $avec_conteneur = true) {
	$saisies = saisies_lister_par_nom($contenu, $avec_conteneur);

	return array_keys($saisies);
}

/**
 * Prend la description complète du contenu d'un formulaire et retourne
 * une liste des labels humains des vrais champs du formulaire (par nom)
 *
 * @param array $contenu        Le contenu d'un formulaire
 * @param bool  $avec_conteneur Indique si on renvoie aussi les saisies ayant des enfants, comme les fieldset
 *
 * @return array Un tableau listant les labels humains des champs
 */
function saisies_lister_labels($contenu, $avec_conteneur = false) {
	$saisies = saisies_lister_par_nom($contenu, $avec_conteneur);

	$labels = array();
	foreach ($saisies as $nom => $saisie) {
		if (isset($saisie['options']['label'])) {
			$labels[$nom] = $saisie['options']['label'];
		}
	}

	return $labels;
}

/**
 * A utiliser dans une fonction charger d'un formulaire CVT,
 * cette fonction renvoie le tableau de contexte correspondant
 * de la forme $contexte['nom_champ'] = ''.
 *
 * @param array $contenu Le contenu d'un formulaire (un tableau de saisies)
 *
 * @return array Un tableau de contexte
 */
function saisies_charger_champs($contenu) {
	if (function_exists('array_fill_keys')) { // php 5.2
		return array_fill_keys(saisies_lister_champs($contenu, false), null);
	}
	$champs = array();
	foreach (saisies_lister_champs($contenu, false) as $champ) {
		$champs[$champ] = null;
	}

	return $champs;
}

/**
 * Prend la description complète du contenu d'un formulaire et retourne
 * une liste des valeurs par défaut des champs du formulaire.
 *
 * @param array $contenu Le contenu d'un formulaire
 *
 * @return array Un tableau renvoyant la valeur par défaut de chaque champs
 */
function saisies_lister_valeurs_defaut($contenu) {
	$contenu = saisies_lister_par_nom($contenu, false);
	$defauts = array();

	foreach ($contenu as $nom => $saisie) {
		// Si le nom du champ est un tableau indexé, il faut parser !
		if (preg_match('/([\w]+)((\[[\w]+\])+)/', $nom, $separe)) {
			$nom = $separe[1];
			// Dans ce cas on ne récupère que le nom,
			// la valeur par défaut du tableau devra être renseigné autre part
			$defauts[$nom] = array();
		}
		else {
			$defauts[$nom] = isset($saisie['options']['defaut']) ? $saisie['options']['defaut'] : '';
		}
	}

	return $defauts;
}

/**
 * Compare deux tableaux de saisies pour connaitre les différences.
 *
 * @param array  $saisies_anciennes Un tableau décrivant des saisies
 * @param array  $saisies_nouvelles Un autre tableau décrivant des saisies
 * @param bool   $avec_conteneur    Indique si on veut prendre en compte dans la comparaison les conteneurs comme les fieldsets
 * @param string $tri               Comparer selon quel tri ? 'nom' / 'identifiant'
 *
 * @return array Retourne le tableau des saisies supprimées, ajoutées et modifiées
 */
function saisies_comparer($saisies_anciennes, $saisies_nouvelles, $avec_conteneur = true, $tri = 'nom') {
	$trier = "saisies_lister_par_$tri";
	$saisies_anciennes = $trier($saisies_anciennes, $avec_conteneur);
	$saisies_nouvelles = $trier($saisies_nouvelles, $avec_conteneur);

	// Les saisies supprimées sont celles qui restent dans les anciennes quand on a enlevé toutes les nouvelles
	$saisies_supprimees = array_diff_key($saisies_anciennes, $saisies_nouvelles);
	// Les saisies ajoutées, c'est le contraire
	$saisies_ajoutees = array_diff_key($saisies_nouvelles, $saisies_anciennes);
	// Il reste alors les saisies qui ont le même nom
	$saisies_restantes = array_intersect_key($saisies_anciennes, $saisies_nouvelles);
	// Dans celles-ci, celles qui sont modifiées sont celles dont la valeurs est différentes
	$saisies_modifiees = array_udiff(array_diff_key($saisies_nouvelles, $saisies_ajoutees), $saisies_restantes, 'saisies_comparer_rappel');
	#$saisies_modifiees = array_udiff($saisies_nouvelles, $saisies_restantes, 'saisies_comparer_rappel');
	// Et enfin les saisies qui ont le même nom et la même valeur
	$saisies_identiques = array_diff_key($saisies_restantes, $saisies_modifiees);

	return array(
		'supprimees' => $saisies_supprimees,
		'ajoutees' => $saisies_ajoutees,
		'modifiees' => $saisies_modifiees,
		'identiques' => $saisies_identiques,
	);
}

/**
 * Compare deux saisies et indique si elles sont égales ou pas.
 *
 * @param array $a Une description de saisie
 * @param array $b Une autre description de saisie
 *
 * @return int Retourne 0 si les saisies sont identiques, 1 sinon.
 */
function saisies_comparer_rappel($a, $b) {
	if ($a === $b) {
		return 0;
	} else {
		return 1;
	}
}

/**
 * Compare deux tableaux de saisies pour connaitre les différences
 * en s'appuyant sur les identifiants de saisies.
 *
 * @see saisies_comparer()
 *
 * @param array $saisies_anciennes Un tableau décrivant des saisies
 * @param array $saisies_nouvelles Un autre tableau décrivant des saisies
 * @param bool  $avec_conteneur    Indique si on veut prendre en compte dans la comparaison
 *                                 les conteneurs comme les fieldsets
 *
 * @return array Retourne le tableau des saisies supprimées, ajoutées et modifiées
 */
function saisies_comparer_par_identifiant($saisies_anciennes, $saisies_nouvelles, $avec_conteneur = true) {
	return saisies_comparer($saisies_anciennes, $saisies_nouvelles, $avec_conteneur, 'identifiant');
}

/**
 * Liste toutes les saisies configurables (ayant une description).
 * @param string $saisies_repertoire le répertoire où trouver les saisies
 * @return array Un tableau listant des saisies et leurs options
 */
function saisies_lister_disponibles($saisies_repertoire = 'saisies') {
	static $saisies = null;

	if (is_null($saisies)) {
		$saisies = array();
		$liste = find_all_in_path("$saisies_repertoire/", '.+[.]yaml$');

		if (count($liste)) {
			foreach ($liste as $fichier => $chemin) {
				$type_saisie = preg_replace(',[.]yaml$,i', '', $fichier);
				$dossier = str_replace($fichier, '', $chemin);

				// On ne garde que les saisies qui ont bien le HTML avec !
				if (
					file_exists("$dossier$type_saisie.html")
					and (
						is_array($saisie = saisies_charger_infos($type_saisie))
					)
				) {
					$saisies[$type_saisie] = $saisie;
				}
			}
		}
	}

	return $saisies;
}

/**
 * Liste tous les groupes de saisies configurables (ayant une description).
 *
 * @return array Un tableau listant des saisies et leurs options
 */
function saisies_groupes_lister_disponibles($saisies_repertoire = 'saisies') {
	static $saisies = null;

	if (is_null($saisies)) {
		$saisies = array();
		$liste = find_all_in_path("$saisies_repertoire/", '.+[.]yaml$');

		if (count($liste)) {
			foreach ($liste as $fichier => $chemin) {
				$type_saisie = preg_replace(',[.]yaml$,i', '', $fichier);
				$dossier = str_replace($fichier, '', $chemin);

				if (is_array($saisie = saisies_charger_infos($type_saisie, $saisies_repertoire))) {
					$saisies[$type_saisie] = $saisie;
				}
			}
		}
	}

	return $saisies;
}

/**
 * Lister les saisies existantes ayant une définition SQL.
 *
 * @return array Un tableau listant des saisies et leurs options
 */
function saisies_lister_disponibles_sql($saisies_repertoire = 'saisies') {
	$saisies = array();
	$saisies_disponibles = saisies_lister_disponibles($saisies_repertoire);
	foreach ($saisies_disponibles as $type => $saisie) {
		if (isset($saisie['defaut']['options']['sql']) and $saisie['defaut']['options']['sql']) {
			$saisies[$type] = $saisie;
		}
	}

	return $saisies;
}

/**
 * Charger les informations contenues dans le YAML d'une saisie.
 *
 * @param string $type_saisie Le type de la saisie
 *
 * @return array Un tableau contenant le YAML décodé
 */
function saisies_charger_infos($type_saisie, $saisies_repertoire = 'saisies') {
	if (defined('_DIR_PLUGIN_YAML')) {
		include_spip('inc/yaml');
		$fichier = find_in_path("$saisies_repertoire/$type_saisie.yaml");
		$saisie = yaml_decode_file($fichier);

		if (is_array($saisie)) {
			$saisie['titre'] = (isset($saisie['titre']) and $saisie['titre'])
				? _T_ou_typo($saisie['titre']) : $type_saisie;
			$saisie['description'] = (isset($saisie['description']) and $saisie['description'])
				? _T_ou_typo($saisie['description']) : '';
			$saisie['icone'] = (isset($saisie['icone']) and $saisie['icone'])
				? find_in_path($saisie['icone']) : '';
		}
	}
	else {
		$saisie = array();
	}

	return $saisie;
}

/**
 * Quelles sont les saisies qui se débrouillent toutes seules, sans le _base commun.
 *
 * @return array Retourne un tableau contenant les types de saisies qui ne doivent pas utiliser le _base.html commun
 */
function saisies_autonomes() {
	$saisies_autonomes = pipeline(
		'saisies_autonomes',
		array(
			'fieldset',
			'hidden',
			'destinataires',
			'explication',
		)
	);

	return $saisies_autonomes;
}

/**
 * La saisie renvoie t-elle un tableau?
 * note: on teste saisie par saisie, et non pas type de saisie par type de saisie, car certaine type (Evenements par ex) peut, en fonction des options, être tabulaire ou pas.
 * @param $saisie
 * @return return bool true si la saisie est tabulaire, false sinon
**/
function saisies_saisie_est_tabulaire($saisie) {
	if (in_array($saisie['saisie'], array('checkbox', 'selection_multiple'))) {
		$est_tabulaire = true;
	} else {
		$est_tabulaire =  false;
	}
	return pipeline('saisie_est_tabulaire',
		array('args' => $saisie, 'data' => $est_tabulaire)
	);
}

/**
 * Indique si une saisie à sa valeur gelée
 * - soit par option disabled avec envoi cachée
 * - soit par option readonly
 * @param array $description description de la saisie
 * @return bool true si gélée, false sinon)
**/
function saisies_verifier_gel_saisie($description) {
	$options = $description['options'];
	//As t-on bloqué d'une manière ou d'une autre la valeur postée?
	if ((
		isset($options['readonly'])
		and $options['readonly']
	)
	or (
		isset($options['disable'])
		and isset($options['disable_avec_post'])
		and $options['disable']
		and $options['disable_avec_post']
	)
	) {
		return true;
	} else {
		return false;
	}
}
