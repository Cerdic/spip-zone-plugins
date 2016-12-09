<?php

/**
 * Utilisations de pipelines
 *
 * @package SPIP\Cextras\Pipelines
**/

// sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Retourne la liste des saisies de champs extras concernant un objet donné
 *
 * @pipeline_appel declarer_champs_extras
 * @param string $table
 *     Nom d'une table SQL éditoriale
 * @return array
 *     Liste des saisies de champs extras de l'objet
**/
function champs_extras_objet($table) {
	static $saisies_tables = array();
	if (!$saisies_tables) {
		$saisies_tables = pipeline('declarer_champs_extras', array());
	}
	return isset($saisies_tables[$table]) ? $saisies_tables[$table] : array();
}

/**
 * Filtrer par autorisation les saisies transmises 
 *
 * Chacune des saisies est parcourue et si le visiteur n'a pas l'autorisation
 * de la voir, elle est enlevée de la liste.
 * La fonction ne retourne donc que la liste des saisies que peut voir
 * la personne.
 * 
 * @param string $faire
 *     Type d'autorisation testée : 'voir', 'modifier'
 * @param string $quoi
 *     Type d'objet tel que 'article'
 * @param array $saisies
 *     Liste des saisies à filtrer
 * @param array $args
 *     Arguments pouvant être utiles à l'autorisation
 * @return array
 *     Liste des saisies filtrées
**/
function champs_extras_autorisation($faire, $quoi='', $saisies=array(), $args=array()) {
	if (!$saisies) return array();
	include_spip('inc/autoriser');

	foreach ($saisies as $cle => $saisie) {
		$id = isset($args['id']) ? $args['id'] : $args['id_objet'];
		if (!autoriser($faire . 'extra', $quoi, $id, '', array(
			'type' => $quoi,
			'id_objet' => $id,
			'contexte' => isset($args['contexte']) ? $args['contexte'] : array(),
			'table' => table_objet_sql($quoi),
			'saisie' => $saisie,
			'champ' => $saisie['options']['nom'],
		))) {
			// on n'est pas autorise
			unset($saisies[$cle]);
		}
		else
		{
			// on est autorise
			// on teste les sous-elements
			if (!empty($saisie['saisies'])) {
				$saisies[$cle]['saisies'] = champs_extras_autorisation($faire, $quoi, $saisie['saisies'], $args);
			}
		}
	}
	return $saisies;
}

/**
 * Ajoute pour chaque saisie de type SQL un drapeau (input hidden)
 * permettant de retrouver les saisies editées.
 * 
 * Particulièrement utile pour les checkbox qui ne renvoient
 * rien si on les décoche.
 *
 * @param array $saisies
 *     Liste de saisies 
 * @return array $saisies
 *     Saisies complétées des drapeaux d'édition
**/
function champs_extras_ajouter_drapeau_edition($saisies) {
	$saisies_sql = champs_extras_saisies_lister_avec_sql($saisies);
	foreach ($saisies_sql as $saisie) {
		$nom = $saisie['options']['nom'];
		$saisies[] = array(
			'saisie' => 'hidden',
			'options' => array(
				'nom' => "cextra_$nom",
				'defaut' => 1 
			)
		);
	}
	return $saisies;
}

// ---------- pipelines -----------


/**
 * Ajouter les champs extras sur les formulaires CVT editer_xx
 *
 * Liste les champs extras de l'objet, et s'il y en a les ajoute
 * sur le formulaire d'édition en ayant filtré uniquement les saisies
 * que peut voir le visiteur et en ayant ajouté des champs hidden
 * servant à champs extras.
 * 
 * @pipeline editer_contenu_objet
 * @param array $flux Données du pipeline
 * @return array      Données du pipeline
**/ 
function cextras_editer_contenu_objet($flux){
	// recuperer les saisies de l'objet en cours
	$objet = $flux['args']['type'];
	include_spip('inc/cextras');
	if ($saisies = champs_extras_objet( table_objet_sql($objet) )) {
		// filtrer simplement les saisies que la personne en cours peut voir
		$saisies = champs_extras_autorisation('modifier', $objet, $saisies, $flux['args']);
		// pour chaque saisie presente, de type champs extras (hors fieldset et autres)
		// ajouter un flag d'edition
		$saisies = champs_extras_ajouter_drapeau_edition($saisies);
		// ajouter au formulaire
		$ajout = recuperer_fond('inclure/generer_saisies', array_merge($flux['args']['contexte'], array('saisies'=>$saisies)));

		// div par défaut en 3.1+, mais avant ul / li
		$balise = saisie_balise_structure_formulaire('ul');
		$flux['data'] = preg_replace(
			'%(<!--extra-->)%is',
			"<$balise class='editer-groupe champs_extras'>$ajout</$balise>\n" . '$1',
			$flux['data']
		);
	}

	return $flux;
}


/**
 * Ajouter les champs extras soumis par les formulaire CVT editer_xx
 *
 * Pour chaque champs extras envoyé par le formulaire d'édition,
 * ajoute les valeurs dans l'enregistrement à effectuer.
 * 
 * @pipeline pre_edition
 * @param array $flux Données du pipeline
 * @return array      Données du pipeline
**/ 
function cextras_pre_edition($flux){

	include_spip('inc/cextras');
	include_spip('inc/saisies_lister');
	$table = $flux['args']['table'];
	if ($saisies = champs_extras_objet( $table )) {
		// Restreindre les champs postés en fonction des autorisations de les modifier
		// au cas où un malin voudrait en envoyer plus que le formulaire ne demande
		$saisies = champs_extras_autorisation('modifier', objet_type($table), $saisies, $flux['args']);
		$saisies = champs_extras_saisies_lister_avec_sql($saisies);
		foreach ($saisies as $saisie) {
			$nom = $saisie['options']['nom'];
			if (_request('cextra_' .  $nom)) {
				$extra = _request($nom);
				if (is_array($extra)) {
					$extra = join(',' , $extra);
				}
				$flux['data'][$nom] = corriger_caracteres($extra);
			}
		}
	}

	return $flux;
}


/**
 * Ajouter les champs extras sur la visualisation de l'objet
 *
 * S'il y a des champs extras sur l'objet, la fonction les ajoute
 * à la vue de l'objet, en enlevant les saisies que la personne n'a
 * pas l'autorisation de voir.
 * 
 * @pipeline afficher_contenu_objet
 * @param array $flux Données du pipeline
 * @return array      Données du pipeline
**/ 
function cextras_afficher_contenu_objet($flux){
	// recuperer les saisies de l'objet en cours
	$objet = $flux['args']['type'];
	include_spip('cextras_fonctions');
	$contexte = isset($flux['args']['contexte']) ? $flux['args']['contexte'] : array();
	if ($html = champs_extras_voir_saisies($objet, $flux['args']['id_objet'], $contexte)) {
		$flux['data'] .= $html;
	}
	return $flux;
}

/**
 * Pour les saisies transmises, appliquer les traitements sur leurs contenus
 *
 * On applique les eventuels traitements definis pour chaque saisie,
 * dans l'option 'traitements'. Si la valeur de l'option correspond
 * à une constante connue (tel que `_TRAITEMENTS_RACCOURCIS`), ce
 * sera la valeur de la constante qui sera utilisée, sinon
 * directement le texte écrit. Il faut absolument `%s` pour que cela fonctionne.
 *
 * @note
 *     La `saisies-vues/_base` applique le filtre `|propre` par défaut si
 *     elle ne trouve pas de saisie. Dans ce cas, certains traitements peuvent
 *     être effectués 2 fois !
 *
 * @param array $saisies
 *      Liste décrivant les saisies
 * @param array $valeurs
 *      Couples (champ => valeur)
 * @return array
 *      Couples (champ => valeur traitée, le cas échéant)
 */
function cextras_appliquer_traitements_saisies($saisies, $valeurs) {
	$saisies = saisies_lister_avec_traitements($saisies);

	// Fournir $connect et $Pile[0] au traitement si besoin (l'evil eval)
	$connect = '';
	$Pile = array(0 => (isset($flux['args']['contexte']) ? $flux['args']['contexte'] : array()));

	foreach ($saisies as $saisie) {
		$traitement = $saisie['options']['traitements'];
		$traitement = defined($traitement) ? constant($traitement) : $traitement;
		$nom = $saisie['options']['nom'];
		list($avant, $apres) = explode('%s', $traitement);
		eval('$val = ' . $avant . ' $valeurs[$nom] ' . $apres . ';');
		$valeurs[$nom] = $val;
	}

	return $valeurs;
}

/**
 * Vérification de la validité des champs extras
 *
 * Lorsqu'un formulaire 'editer_xx' se présente, la fonction effectue,
 * pour chaque champs extra les vérifications prévues dans la
 * définition de la saisie, et retourne les éventuelles erreurs rencontrées.
 * 
 * @pipeline formulaire_verifier
 * @param array $flux Données du pipeline
 * @return array      Données du pipeline
**/ 
function cextras_formulaire_verifier($flux){
	$form = $flux['args']['form'];
	
	if (strncmp($form, 'editer_', 7) !== 0) {
		return $flux;
	}
	
	$objet = substr($form, 7);
	if ($saisies = champs_extras_objet( $table = table_objet_sql($objet) )) {
		include_spip('inc/autoriser');
		include_spip('inc/saisies');

		// restreindre les saisies selon les autorisations
		$id_objet = $flux['args']['args'][0]; // ? vraiment toujours ?
		$saisies = champs_extras_autorisation('modifier', $objet, $saisies, array_merge($flux['args'], array(
			'id' => $id_objet,
			'contexte' => array()))); // nous ne connaissons pas le contexte dans ce pipeline

		// restreindre les vérifications aux saisies enregistrables
		$saisies = champs_extras_saisies_lister_avec_sql($saisies);

		foreach ($saisies as $saisie) {
			$nom = $saisie['options']['nom'];
			$normaliser = null;
			$erreur = cextras_verifier_saisie($saisie, _request($nom), $normaliser);
			if ($erreur) {
				$flux['data'][$nom] = $erreur;
			} elseif (!is_null($normaliser)) {
				set_request($nom, $normaliser);
			}
		}
	}
	return $flux;
}

/**
 * Vérifie qu'une valeur est acceptée par la déclaration de saisie transmise.
 *
 * Peut aussi normaliser (la formater d'une certaine manière pour le format attendu
 * dans la base de donnée). Dans ce cas la valeur normalisée est placée dans `$normaliser`
 *
 * @param array $saisie
 * @param mixed $valeur
 * @param mixed $normaliser Valeur normalisée, le cas échéant
 * @param bool $depuis_crayons true si en provenance d'une vérification par Crayons.
 * @return false|string Message d'erreur, le cas echéant.
 */
function cextras_verifier_saisie($saisie, $valeur, &$normaliser = null, $depuis_crayons = false) {
	static $verifier = null;

	// Si le plugin verifier est présent.
	if (is_null($verifier)) {
		$verifier = charger_fonction('verifier', 'inc', true);
	}

	// verifier obligatoire
	if (
		isset($saisie['options']['obligatoire'])
		and $saisie['options']['obligatoire']
		and !$valeur
	) {
		return _T('info_obligatoire');
	}

	// verifier (api) + normalisation
	if (
		$verifier
		AND isset($saisie['verifier']['type'])
		AND $verif = $saisie['verifier']['type']
	) {
		$options = isset($saisie['verifier']['options']) ? $saisie['verifier']['options'] : array();
		$erreur = $verifier($valeur, $verif, $options, $normaliser);
		if ($erreur) {
			return $erreur;
		}

		// Si une valeur normalisée a ete transmise, c'est tout bon.
		if (!is_null($normaliser)) {
			return false;
		}

		// [FIXME] exceptions connues de vérifications (pour les dates entre autres)
		// en attendant une meilleure solution !
		//
		// Lorsque le champ n'est pas rempli dans le formulaire
		// alors qu'une normalisation est demandée,
		// verifier() sort sans indiquer d'erreur (c'est normal).
		//
		// Sauf que la donnée alors soumise à SQL sera une chaine vide,
		// ce qui ne correspond pas toujours à ce qui est attendu.
		if ((is_string($valeur) and !strlen($valeur) or (is_array($valeur) and $saisie['saisie']=='date'))
			and isset($options['normaliser'])
			and $norme = $options['normaliser']) {
			// Charger la fonction de normalisation théoriquement dans verifier/date
			// et si on en trouve une, obtenir la valeur normalisée
			// qui est théoriquement la valeur par défaut, puisque $valeur est vide
			include_spip("verifier/$verif");
			if ($f_normaliser = charger_fonction("${verif}_${norme}", "normaliser", true)) {
				$erreur = null;
				$normaliser = $f_normaliser($valeur, $options, $erreur);
				if (!is_null($erreur)) {
					return $erreur;
				}
				// On suppose la normalisation OK.
				return false;
			} else {
				include_spip('inc/cextras');
				extras_log("Fonction de normalisation pour ${verif}_${norme} introuvable");
			}
		}
	}
	return false;
}


/**
 * Insertion dans le pipeline revisions_chercher_label (Plugin révisions)
 * Trouver le bon label à afficher sur les champs dans les listes de révisions
 *
 * Si un champ est un champ extra, son label correspond au label défini du champs extra
 *
 * @pipeline revisions_chercher_label
 * @param array $flux Données du pipeline
 * @return array      Données du pipeline
**/
function cextras_revisions_chercher_label($flux) {
	$table = table_objet_sql($flux['args']['objet']);
	$saisies_tables = champs_extras_objet($table);
	foreach($saisies_tables as $champ){
		if($champ['options']['nom'] == $flux['args']['champ']){
			$flux['data'] = $champ['options']['label'];
			break;
		}
	}
	return $flux;
}


/**
 * Ajouter les saisies de champs extras sur des formulaires spécifiques
 * 
 * Les champs extras s'ajoutent déjà automatiquement sur les formulaires d'édition
 * des objets éditoriaux. Pour d'autres formulaires plus spécifiques, tel
 * que des formulaires d'inscriptions, il est possible d'envoyer,
 * dans la partie 'charger' du formulaire en question la clé
 * `_champs_extras_saisies`, listant les saisies à afficher dedans.
 * 
 * Elles seront ajoutées automatiquement à l'endroit où le code
 * html `<!--extra-->` est présent dans le formulaire.
 *
 * @see cextras_obtenir_saisies_champs_extras() qui aide à récupérer les saisies.
 *
 * @pipeline formulaire_fond
 * @param array $flux
 * @return array
**/
function cextras_formulaire_fond($flux) {
	if (!empty($flux['args']['contexte']['_champs_extras_saisies'])) {
		$saisies = $flux['args']['contexte']['_champs_extras_saisies'];

		// ajouter au formulaire
		$ajout = recuperer_fond('inclure/generer_saisies', array_merge($flux['args']['contexte'], array('saisies' => $saisies)));

		// div par défaut en 3.1+, mais avant ul / li
		$balise = saisie_balise_structure_formulaire('ul');
		$flux['data'] = preg_replace(
			'%(<!--extra-->)%is',
			"<$balise class='editer-groupe champs_extras'>$ajout</$balise>\n" . '$1',
			$flux['data']
		);
	}
	return $flux;
}


/**
 * Définir une fonction de contrôleur pour Crayons si on tente d'éditer un champs extras.
 *
 * Si on édite un champs extras avec Crayons, sans avoir créé manuellement de contrôleur spécifique
 * pour le champ en question, Crayons propose soit un textarea, soit un input.
 *
 * Vu que l'on connaît les déclarations de nos champs extras, on va les utiliser pour créer
 * un formulaire d'édition plus adapté à notre champ.
 *
 * @pipeline crayons_controleur
 * @param array $flux
 * @return array
 */
function cextras_crayons_controleur($flux) {
	// si Crayons a déjà trouvé de contrôleur PHP, on ne fait rien
	if ($flux['data'] != 'controleur_dist') {
		return $flux;
	}

	$type = $flux['args']['type'];
	$champ = $flux['args']['champ'];

	// controleur_dist teste aussi la présence des controleurs html…
	if (find_in_path(($controleur = 'controleurs/' . $type . '_' . $champ) . '.html')) {
		return $flux;
	}
	if (find_in_path(($controleur = 'controleurs/' . $champ) .'.html')) {
		return $flux;
	}

	$id = $flux['args']['id'];
	$table = table_objet_sql($type);

	// Il n'existe pas de controleur spécifique pour ce champ. Voyons si ce champ est un champs extra
	$saisies = champs_extras_objet($table);
	// Restreindre aux vrais champs en bdd
	$saisies_sql = champs_extras_saisies_lister_avec_sql($saisies);
	if (!empty($saisies_sql[$champ])) {
		$flux['data'] = charger_fonction('champs_extras', 'controleurs');
	} else {
		// Ou un fieldset de champs extras
		$saisies_fieldset = saisies_lister_avec_type($saisies, 'fieldset');
		if (!empty($saisies_fieldset[$champ])) {
			$flux['data'] = charger_fonction('champs_extras_fieldset', 'controleurs');
		}
	}

	return $flux;
}

/**
 * Vérifier une saisie envoyée depuis un formulaire de Crayons.
 *
 * @pipeline crayons_verifier
 * @param array $flux
 * @return array
 */
function cextras_crayons_verifier($flux) {
	// Le nom du modèle envoyé par le controleur/champs_extras.
	if ($flux['args']['modele'] != 'champs_extras') {
		return $flux;
	}

	$type = $flux['args']['type'];
	$table = table_objet_sql($type);
	$valeurs = $flux['args']['content'];

	// Récupérer les saisies SQL de la table
	$saisies = champs_extras_objet($table);
	$saisies = champs_extras_saisies_lister_avec_sql($saisies);

	foreach ($valeurs as $champ => $valeur) {
		if ($saisie = saisies_chercher($saisies, $champ)) {
			$nom = $saisie['options']['nom'];

			// Crayons applatit les name envoyés sous forme de tableau (lorsque name="truc[]").
			// On récupère les véritables valeurs dans ce cas. Autrement la saisie 'date'
			// n'arrive pas à retrouver ses petits
			$key = 'content_' . $flux['args']['wid'] . '_' . $champ;
			$valeur = _request($key);

			$normaliser = null;
			$erreur = cextras_verifier_saisie($saisie, $valeur, $normaliser, true);
			if ($erreur) {
				$flux['data']['erreurs'][$nom] = $erreur;
			} elseif (!is_null($normaliser)) {
				$flux['data']['normaliser'][$nom] = $normaliser;
			}
		}
	}

	return $flux;
}
