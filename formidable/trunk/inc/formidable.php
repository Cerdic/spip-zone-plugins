<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/*
 * Liste tous les traitements configurables (ayant une description)
 *
 * @return array Un tableau listant des saisies et leurs options
 */
function traitements_lister_disponibles() {
	static $traitements = null;

	if (is_null($traitements)) {
		$traitements = array();
		$liste = find_all_in_path('traiter/', '.+[.]yaml$');
		ksort($liste);

		if (count($liste)) {
			foreach ($liste as $fichier => $chemin) {
				$type_traitement = preg_replace(',[.]yaml$,i', '', $fichier);
				// On ne garde que les traitements qui ont bien la fonction
				if (charger_fonction($type_traitement, 'traiter', true)
					and (
						is_array($traitement = traitements_charger_infos($type_traitement))
					)
				) {
					$traitements[$type_traitement] = $traitement;
				}
			}
		}
	}

	$traitements = pipeline(
		'formidable_traitements',
		array('data' => $traitements, 'args' => array())
	);
	return $traitements;
}

/**
 * Charger les informations contenues dans le yaml d'un traitement
 *
 * @param string $type_saisie Le type de la saisie
 * @return array Un tableau contenant le YAML décodé
 */
function traitements_charger_infos($type_traitement) {
	include_spip('inc/yaml');
	$fichier = find_in_path("traiter/$type_traitement.yaml");
	$traitement = yaml_decode_file($fichier);

	if (is_array($traitement)) {
		$traitement += array('titre' => '', 'description' => '', 'icone' => '');
		$traitement['titre'] = $traitement['titre'] ? _T_ou_typo($traitement['titre']) : $type_traitement;
		$traitement['description'] = $traitement['description'] ? _T_ou_typo($traitement['description']) : '';
		$traitement['icone'] = $traitement['icone'] ? find_in_path($traitement['icone']) : '';
	}
	return $traitement;
}

/*
 * Liste tous les types d'échanges (export et import) existant pour les formulaires
 *
 * @return array Retourne un tableau listant les types d'échanges
 */
function echanges_formulaire_lister_disponibles() {
	// On va chercher toutes les fonctions existantes
	$liste = find_all_in_path('echanger/formulaire/', '.+[.]php$');
	$types_echange = array('exporter' => array(), 'importer' => array());
	if (count($liste)) {
		foreach ($liste as $fichier => $chemin) {
			$type_echange = preg_replace(',[.]php$,i', '', $fichier);

			// On ne garde que les échanges qui ont bien la fonction
			if ($f = charger_fonction('exporter', "echanger/formulaire/$type_echange", true)) {
				$types_echange['exporter'][$type_echange] = $f;
			}
			if ($f = charger_fonction('importer', "echanger/formulaire/$type_echange", true)) {
				$types_echange['importer'][$type_echange] = $f;
			}
		}
	}
	return $types_echange;
}

/*
 * Génère le nom du cookie qui sera utilisé par le plugin lors d'une réponse
 * par un visiteur non-identifié.
 *
 * @param int $id_formulaire L'identifiant du formulaire
 * @return string Retourne le nom du cookie
 */
function formidable_generer_nom_cookie($id_formulaire) {
	return $GLOBALS['cookie_prefix'].'cookie_formidable_'.$id_formulaire;
}


/*
 * Trouver la réponse à éditer pour un formulaire donné,
 * dans un contexte donné
 * en fonction de la configuration du formulaire.
 * @param int $id_formulaire L'identifiant du formulaire
 * @param int $id_formulaires_reponse L'identifant de réponse passé au moment de l'appel du formulaire
 * @param array $options Les options d'enregistrement du formulaire
 * @param boolean $verifier_est_auteur si égal à true, on vérifie si $id_formulaires_reponse est passé que l'auteur connecté est bien l'auteur de la réponse passée en argument
 * @return int $id_formulaires_reponse L'identifiant de la réponse à modifier effectivement.
 *
 */
function formidable_trouver_reponse_a_editer($id_formulaire, $id_formulaires_reponse, $options, $verifier_est_auteur = false) {
	// Si on passe un identifiant de reponse, on edite cette reponse si elle existe
	if ($id_formulaires_reponse = intval($id_formulaires_reponse) and ($verifier_est_auteur == false or $options['identification'] == 'id_reponse')) {
		return $id_formulaires_reponse;
	} else {
		// calcul des paramètres d'anonymisation

		$reponses = formidable_verifier_reponse_formulaire(
			$id_formulaire,
			$options['identification'],
			isset($options['variable_php']) ? $options['variable_php'] : ''
		);

		//A-t-on demandé de vérifier que l'auteur soit bien celui de la réponse?
		if ($id_formulaires_reponse = intval($id_formulaires_reponse)
			and $verifier_est_auteur == true) {
			if (!is_array($reponses) or in_array($id_formulaires_reponse, $reponses) == false) {
				$id_formulaires_reponse = false;
			}
			return $id_formulaires_reponse;
		}

		// Si multiple = non mais que c'est modifiable, alors on va chercher
		// la dernière réponse si elle existe
		if ($options
			and !$options['multiple']
			and $options['modifiable']
			and is_array($reponses)
			) {
				$id_formulaires_reponse = array_pop($reponses);
		}
	}
	return $id_formulaires_reponse;
}

/*
 * Vérifie si le visiteur a déjà répondu à un formulaire
 *
 * @param int $id_formulaire L'identifiant du formulaire
 * @param string $choix_identification Comment verifier une reponse. Priorite sur 'cookie' ou sur 'id_auteur'
 * @param string $variable_php_identification : la variable php servant à identifier une réponse
 * @param string $anonymiser : si 'on', le formulaire doit-être anonymisé
 * @return unknown_type Retourne un tableau contenant les id des réponses si elles existent, sinon false
 */
function formidable_verifier_reponse_formulaire($id_formulaire, $choix_identification = 'cookie', $variable_php_identification = '', $anonymiser='') {
	global $auteur_session;
	$id_auteur = $auteur_session ? intval($auteur_session['id_auteur']) : 0;
	$nom_cookie = formidable_generer_nom_cookie($id_formulaire);
	$cookie = isset($_COOKIE[$nom_cookie]) ? $_COOKIE[$nom_cookie] : false;
	$variable_php_identification = formidable_variable_php_identification($variable_php_identification, $id_formulaire);

	// ni cookie ni id, ni variable_php,  on ne peut rien faire
	if (!$cookie and !$id_auteur and !$variable_php_identification) {
		return false;
	}

	// Determiner les différentes clauses $WHERE possible en fonction de ce qu'on a
	$where_id_auteur = '';
	$where_cookie = '';
	$where_variable_php = '';
	if ($id_auteur) {
		$where_id_auteur = 'id_auteur='.$id_auteur;
	}
	if ($cookie) {
		$where_cookie = 'cookie='.sql_quote($cookie);
	}
	if ($variable_php_identification) {
		$where_variable_php = 'variable_php='.$variable_php_identification;
	}

	// Comment identifie-t-on? Attention, le choix d'identification indique une PRIORITE, donc cela veut dire que les autres méthodes peuvent venir après, sauf dans le cas d'identification explicitement par id_reponse
	if ($choix_identification == 'cookie' or !$choix_identification) {
		if ($cookie) {
			$where = array($where_cookie);
		} else {
			$where = array($where_id_auteur, $where_variable_php);
		}
	} elseif ($choix_identification == 'id_auteur') {
		if ($id_auteur) {
			if ($anonymiser == 'on') {
				$id_auteur = formidable_crypter_id_auteur($id_auteur);
				$where_id_auteur = 'cookie="'.$id_auteur.'"';
			}
			$where = array($where_id_auteur);
		} else {
			$where = array($where_cookie, $where_variable_php);
		}
	} elseif ($choix_identification == 'variable_php') {
		if ($variable_php_identification) {
			$where = array($where_variable_php);
		} else {
			$where = array($where_cookie, $where_id_auteur);
		}
	} elseif ($choix_identification == 'id_reponse') {//Si le filtrage se fait par réponse, on prend tout (mais normalement on devrait pas aboutir ici si tel est le cas)
		$where = array("1=1");
	}
	$where = array_filter($where);//Supprimer les wheres null
	$where = implode($where, ' OR ');

	$reponses = sql_allfetsel(
		'id_formulaires_reponse',
		'spip_formulaires_reponses',
		array(
			array('=', 'id_formulaire', intval($id_formulaire)),
			array('=', 'statut', sql_quote('publie')),
			$where
		),
		'',
		'date'
	);

	if (is_array($reponses)) {
		return array_map('reset', $reponses);
	} else {
		return false;
	}
}

/*
 * Génère la vue d'analyse de toutes les réponses à une saisie
 *
 * @param array $saisie Un tableau décrivant une saisie
 * @param array $env L'environnement, contenant normalement la réponse à la saisie
 * @return string Retour le HTML des vues
 */
function formidable_analyser_saisie($saisie, $valeurs = array(), $reponses_total = 0, $format_brut = false) {
	// Si le paramètre n'est pas bon ou que c'est un conteneur, on génère du vide
	if (!is_array($saisie) or (isset($saisie['saisies']) and $saisie['saisies'])) {
		return '';
	}

	$contexte = array('reponses_total'=>$reponses_total);

	// On sélectionne le type de saisie
	$contexte['type_saisie'] = $saisie['saisie'];

	// Peut-être des transformations à faire sur les options textuelles
	$options = $saisie['options'];
	foreach ($options as $option => $valeur) {
		$options[$option] = _T_ou_typo($valeur, 'multi');
	}

	// On ajoute les options propres à la saisie
	$contexte = array_merge($contexte, $options);

	// On récupère toutes les valeurs du champ
	if (isset($valeurs[$contexte['nom']])
		and $valeurs[$contexte['nom']]
		and is_array($valeurs[$contexte['nom']])) {
		$contexte['valeurs'] = $valeurs[$contexte['nom']];
	} else {
		$contexte['valeurs'] = array();
	}

	// On génère la saisie
	if ($format_brut) {
		return analyser_saisie($contexte);
	} else {
		return recuperer_fond(
			'saisies-analyses/_base',
			$contexte
		);
	}
}

/*
 * Renvoie une ligne de réponse sous la forme d'un tableau
 *
 * @param array $saisie Un tableau décrivant une saisie
 * @return array Tableau contenant une ligne
 */
function analyser_saisie($saisie) {
	if (!isset($saisie['type_saisie']) or $saisie['type_saisie'] == '') {
		return '';
	}

	$ligne = array();

	switch ($saisie['type_saisie']) {
		case 'selecteur_rubrique':
		case 'selecteur_rubrique_article':
		case 'selecteur_article':
			$ligne['plein'] = count(array_filter($saisie['valeurs']));
			$ligne['vide'] = count(array_diff_key($saisie['valeurs'], array_filter($saisie['valeurs'])));
			break;
		case 'radio':
		case 'selection':
		case 'selection_multiple':
		case 'choix_couleur':
		case 'checkbox':
			$stats = array();
			foreach ($saisie['valeurs'] as $valeur) {
				if (is_array($valeur)) {
					foreach ($valeur as $choix) {
						if (isset($stats["choix-$choix"])) {
							$stats["choix-$choix"]++;
						} else {
							$stats["choix-$choix"] = 1;
						}
					}
				} else {
					if (isset($stats["choix-$valeur"])) {
						$stats["choix-$valeur"]++;
					} else {
						$stats["choix-$valeur"] = 1;
					}
				}
			}
			$datas = is_string($saisie['datas'])
				? saisies_chaine2tableau(saisies_aplatir_chaine($saisie['datas']))
				: $saisie['datas'];
			foreach ($datas as $key => $val) {
				$nb = (isset($stats["choix-$key"]))
					? $stats["choix-$key"]
					: 0;
				$ligne[$val] = $nb;
			}
			break;
		case 'destinataires':
			$stats = array();
			foreach ($saisie['valeurs'] as $valeur) {
				foreach ($valeur as $choix) {
					if (isset($stats["choix-$choix"])) {
						$stats["choix-$choix"]++;
					} else {
						$stats["choix-$choix"] = 1;
					}
				}
			}
			foreach ($stats as $key => $val) {
				$key = str_replace('choix-', '', $key);
				if ($key == '') {
					$key = '<valeur vide>';
				}
				$auteur = sql_getfetsel('nom', 'spip_auteurs', "id_auteur=$key");
				$ligne[$auteur] = $val;
			}
			break;
	}

	$vide = 0;
	foreach ($saisie['valeurs'] as $valeur) {
		if ($valeur == '') {
			$vide++;
		}
		switch ($saisie['type_saisie']) {
			case 'case':
			case 'oui_non':
				if (isset($ligne['oui']) == false) {
					$ligne['oui'] = 0;
				}
				if (isset($ligne['non']) == false) {
					$ligne['non'] = 0;
				}
				if ($valeur) {
					$ligne['oui']++;
				} else {
					$ligne['non']++;
				}
				break;
			case 'input':
			case 'hidden':
			case 'explication':
				break;
		}
	}
	$ligne['sans_reponse'] = $vide;
	$ligne['header'] = $saisie['label'] != ''
		? $saisie['label']
		: $saisie['type_saisie'];

	return $ligne;
}


/**
 * Tente de déserialiser un texte
 *
 * Si le paramètre est un tableau, retourne le tableau,
 * Si c'est une chaîne, tente de la désérialiser, sinon
 * retourne la chaîne.
 *
 * @filtre
 *
 * @param string|array $texte
 *	 Le texte (possiblement sérializé) ou un tableau
 * @return array|string
 *	 Tableau, texte désérializé ou texte
**/
function filtre_tenter_unserialize_dist($texte) {
	if (is_array($texte)) {
		return $texte;
	}
	if ($tmp = @unserialize($texte)) {
		return $tmp;
	}
	return $texte;
}


/**
 * Retourne un texte du nombre de réponses
 *
 * @param int $nb
 *	 Nombre de réponses
 * @return string
 *	 Texte indiquant le nombre de réponses
**/
function titre_nb_reponses($nb) {
	if (!$nb) {
		return _T('formidable:reponse_aucune');
	}
	if ($nb == 1) {
		return _T('formidable:reponse_une');
	}
	return _T('formidable:reponses_nb', array('nb' => $nb));
}

/**
 * Transforme le hash MD5 en une valeur numérique unique
 *
 * trouvé ici : http://stackoverflow.com/questions/1422725/represent-md5-hash-as-an-integer
 * @param string $hex_str La valeur alphanumérique à transformer
 * @return string Valeur numérique
*/
function md5_hex_to_dec($hex_str) {
	$arr = str_split($hex_str, 4);
	$dec = array();
	foreach ($arr as $grp) {
		$dec[] = str_pad(hexdec($grp), 5, '0', STR_PAD_LEFT);
	}

	/* on s'assure que $result ne commence pas par un zero */
	$result = implode('', $dec);
	for ($cpt = 0; $cpt < strlen($result); $cpt++) {
		if ($result[$cpt] != '0') {
			break;
		}
	}
	$result = substr($result, $cpt);
	return $result;
}

/**
 * Transforme un login en une valeur numérique de 19 caractères
 *
 * NOTE: il devient impossible de retrouver la valeur d'origine car le HASH
 * est coupé à 19cars et est donc incomplet. L'unicité n'est pas garantie mais
 * les chances pour que deux logins tombent sur le même HASH sont de 1 sur
 * 10 milliards de milliards
 * A la fin, on recherche et supprime les éventuels zéros de début
 * @param string $login Login à transformer
 * @param string $id_form ID du formulaire concerné
 * @return string Un nombre de 19 chiffres
*/
function formidable_scramble($login, $id_form) {
	if (isset($GLOBALS['formulaires']['passwd']['interne']) == false) {
		$passwd = $GLOBALS['formulaires']['passwd']['interne'];
	} else {
		$passwd = 'palabresecreta';
	}
	$login_md5 = md5("$login$passwd$id_form");
	$login_num = md5_hex_to_dec($login_md5);
	$login_num = substr($login_num, 0, 19);

	return $login_num;
}

/**
 * Dans une chaîne, remplace les @raccourci@
 * par la valeur saisie.
 * @param string $chaine la chaîne à transformer
 * @param array $saisies la liste des saisies du formulaire
 * @param bool|string $brut=false, pour indiquer si on veut utiliser les valeurs brutes;
 * @param string|bool $sans_reponse chaine à afficher si pas de réponse. Si true, prend la chaîne par défaut
 * @param string $source 'request' pour prendre dans _request(); 'base' pour prendre dans une réponse enregistrée en base
 * @param int|string $id_formulaires_reponse le cas échéant le numéro de réponse en base
 * @param int|string $id_formulaire le cas échéant le numéro du formulaire en base
 * @param array &$valeurs un tableau clé/valeur listant les valeurs que prenne les @. Passage par référence
 * @param array &$valeurs_libelles un tableau clé/valeur listant les valeurs libéllées que prenne les @. Passage par référence
 * @return string la chaîne transformée
 */
function formidable_raccourcis_arobases_2_valeurs_champs($chaine, $saisies, $brut = false, $sans_reponse = true, $source = 'request', $id_formulaires_reponse = 0, $id_formulaire = 0, &$valeurs = array(), &$valeurs_libellees = array()) {
	if ($source == 'request') {
		list($valeurs, $valeurs_libellees) = formidable_tableau_valeurs_saisies($saisies, $sans_reponse);
	}
	elseif ($source == 'base' and $id_formulaires_reponse and $id_formulaire) {
		$saisies = saisies_lister_par_nom($saisies);
		foreach ($saisies as $nom => $saisie) {
			$valeurs[$nom] = formidable_nettoyer_saisie_vue(saisies_tableau2chaine(calculer_voir_reponse($id_formulaires_reponse, $id_formulaire, $nom, '', 'brut', $sans_reponse)));
			$valeurs_libellees[$nom] =  formidable_nettoyer_saisie_vue(calculer_voir_reponse($id_formulaires_reponse, $id_formulaire, $nom, '', 'valeur_uniquement', $sans_reponse));
		}
	}
	$a_remplacer = array();
	if (preg_match_all('/@[\w]+@/', $chaine, $a_remplacer)) {
		$a_remplacer = $a_remplacer[0];
		foreach ($a_remplacer as $cle => $val) {
			$a_remplacer[$cle] = trim($val, '@');
		}
		$a_remplacer = array_flip($a_remplacer);
		if ($brut) {
			$a_remplacer = array_intersect_key($valeurs, $a_remplacer);
		}
		else {
			$a_remplacer = array_intersect_key($valeurs_libellees, $a_remplacer);
		}
		$a_remplacer = array_merge($a_remplacer, array('nom_site_spip' => lire_config("nom_site")));
	}
	return trim(_L($chaine, $a_remplacer));
}
/**
 * Récupère l'ensemble des valeurs postée dans un formulaires
 * Les retourne sous deux formes : brutes et libellés (par ex. pour les @select@
 * @param array $saisies les saisies du formulaires
 * @param string|bool $sans_reponse chaine à afficher si pas de réponse. Si true, prend la chaîne par défaut
 * @return array (brutes, libellées)
 * On met les résultats en statiques pour gagner un peu de temps
 */
function formidable_tableau_valeurs_saisies($saisies, $sans_reponse = true) {
	if (isset($valeurs)) {
		return array($valeurs,$valeurs_libellees);
	}
	// On parcourt les champs pour générer le tableau des valeurs
	static $valeurs = array();
	static $valeurs_libellees = array();
	if ($sans_reponse === true) {
		$sans_reponse =  _T('saisies:sans_reponse');
	}
	include_spip('inc/saisies_afficher_si_php');
	$saisies_apres_afficher_si = saisies_verifier_afficher_si($saisies);
	$saisies_fichiers = saisies_lister_avec_type($saisies, 'fichiers');
	$saisies_par_nom = saisies_lister_par_nom($saisies);
	$saisies_par_nom_apres_afficher_si = saisies_lister_par_nom($saisies_apres_afficher_si);
	$champs = saisies_lister_champs($saisies);


	// On n'utilise pas formulaires_formidable_fichiers,
	// car celui-ci retourne les saisies fichiers du formulaire dans la base… or, on sait-jamais,
	// il peut y avoir eu une modification entre le moment où l'utilisateur a vu le formulaire et maintenant
	foreach ($champs as $champ) {
		if (array_key_exists($champ, $saisies_fichiers)) {// si on a affaire à une saisie de type fichiers, on considère qu'il n'y pas vraiment de valeur brute
		} elseif ($saisies_par_nom[$champ]['saisie'] == 'explication') {
			$valeurs[$champ] = $saisies_par_nom_apres_afficher_si[$champ]['options']['texte'];
			$valeurs_libellees[$champ] =  $valeurs[$champ];
		}	else {
			// On récupère la valeur postée
			$valeurs[$champ] = _request($champ);
			$valeurs_libellees[$champ] = formidable_nettoyer_saisie_vue(recuperer_fond(
				'saisies-vues/_base',
				array_merge(
					array(
						'type_saisie' => $saisies_par_nom[$champ]['saisie'],
						'valeur' => $valeurs[$champ],
						'valeur_uniquement' => 'oui',
						'sans_reponse' => $sans_reponse
					),
					$saisies_par_nom[$champ]['options']
				)
			));
		}
	}
	return array($valeurs, $valeurs_libellees);
}

/**
 * Retourne la valeur "scrambelisée" de la variable PHP d'identification.
 * pour les deux variables proposés par formidable, recherche directement dans $_SERVER
 * sinon utilise un eval() si une autre variable a été défini en global.
 * Mais peu probable que le cas se présente, car pas d'interface dans le .yaml pour proposer d'autres variables que celle définies par formidable
 * @param string $nom_variable le nom de la variable
 * @param string $id_formulaire le formulaire concerné
 * @return string
 */
function formidable_variable_php_identification($nom_variable, $id_formulaire) {
	//Pour compat ascendante
	if (isset($GLOBALS['formulaires']['variables_anonymisation'])) {
		$nom_variable = $GLOBALS['formulaires']['variables_anonymisation'][$nom_variable];
		$valeur_variable = eval ("return $nom_variable;");
	}

	if (in_array($nom_variable, array("remote_user", "php_auth_user"))) {
		$nom_variable = strtoupper($nom_variable);
		$valeur_variable = isset($_SERVER[$nom_variable]) ? $_SERVER[$nom_variable] : 0;
	}  else {
		$valeur_variable = 0;
	}

	if ($valeur_variable) {
		$valeur_variable = formidable_scramble($valeur_variable, $id_formulaire);
	}
	return $valeur_variable;
}

/**
 * Retourne une valeur crypté de l'id_auteur.
 * @param string $id_auteur
 * @return string
 */
function formidable_crypter_id_auteur($id_auteur) {
	include_spip('inc/securiser_action');
	$pass = secret_du_site();
	return md5($pass.$id_auteur);
}
