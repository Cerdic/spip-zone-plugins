<?php
/**
 * Plugin Inscription3 pour SPIP
 * © cmtmt, BoOz, kent1
 * Licence GPL v3
 *
 * Utilisations de pipelines
 *
 * @package SPIP\Inscription3\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 *
 * Insertion dans le pipeline i3_exceptions_chargement_champs_auteurs_elargis (Inscription3)
 * qui empêche le chargement et la recherche de champs lors de l'affichage de formulaires (editer_auteur / inscription)
 *
 * @return array Un tableau des champs correspondant au "name" de son input de configuration dans le CFG
 * @param array $array Prend un tableau en argument qui doit être complété en fonction des besoins
 */

function inscription3_i3_exceptions_chargement_champs_auteurs_elargis($array = array()) {
	if (!is_array($array)) {
		$array = array();
	}
	// liste des champs pour lesquels on ne doit pas charger la valeur
	$array[] = 'creation';
	return $array;
}

/**
 *
 * Insertion dans le pipeline i3_exceptions_des_champs_auteurs_elargis
 * qui empêche la création de certains champs dans la table
 * après les avoir configuré
 *
 * @return array Un tableau des champs correspondant au "name" de son input de configuration dans le CFG
 * @param array $array Prend un tableau en argument qui doit être complété en fonction des besoins
 */

function inscription3_i3_exceptions_des_champs_auteurs_elargis($array = array()) {
	// liste des champs pour lesquels on ne doit pas créer de champs dans la table spip_auteurs
	if (!is_array($array)) {
		$array = array();
	}
	// Principalement les champs déjà présents dans spip_auteurs
	$array[] = 'id_auteur';
	$array[] = 'bio';
	$array[] = 'nom';
	$array[] = 'pass';
	$array[] = 'login';
	$array[] = 'email';
	$array[] = 'statut';
	$array[] = 'pgp';
	$array[] = 'url_site';
	$array[] = 'nom_site';

	// Des choses spécifiques à inscription3
	$array[] = 'logo';
	$array[] = 'username';
	$array[] = 'statut_nouveau';
	$array[] = 'statut_int';
	$array[] = 'statut_interne';
	$array[] = 'accesrestreint';
	$array[] = 'password';
	$array[] = 'pass';
	$array[] = 'affordance_form';
	$array[] = 'reglement';
	$array[] = 'reglement_article';
	$array[] = 'password_complexite';
	$array[] = 'validation_numero_international';
	$array[] = 'pays_defaut';
	$array[] = 'valider_comptes';
	$array[] = 'message_form_inscription';
	$array[] = 'auto_login';

	return $array;
}

/**
 *
 * Insertion dans le pipeline i3_verifications_specifiques du plugin inscription3
 * Utilisation de l'API verifier du plugin éponyme
 *
 * Pour chaque champs on fourni un array associatif contenant :
 * - type => la fonction de l'api de vérification à utiliser
 * - options => un array des options à passer à cette fonction
 *
 * @return array Tableau contenant plusieurs tableaux en fonction du type de champs
 * @param object $array Doit recevoir un tableau du même type
 */

function inscription3_i3_verifications_specifiques($array = array()) {
	if (!is_array($array)) {
		$array = array();
	}
	// Les emails : fonction verifier/email
	$array['email'] = array('type' => 'email','options' => array('disponible'=>'disponible'));
	$array['mail_inscription'] = array('type' => 'email','options' => array('disponible'=>'disponible'));

	// Les noms (signature)
	$array['nom'] = array('type' => 'signature');
	$array['nom_inscription'] = array('type' => 'signature');

	// Les logins : fonction verifier/login
	$array['login'] = array('type' => 'login');

	// Les statuts : fonction verifier/statut
	$array['statut'] = array('type' => 'statut');

	// Les codes postaux : fonction verifier/code_postal
	$array['code_postal'] = array('type' => 'code_postal');

	// Les numéros de téléphone : fonction verifier/telephone
	$array['telephone'] = array('type' => 'telephone');
	$array['fax'] = array('type' => 'telephone');
	$array['mobile'] = array('type' => 'telephone');

	// Les dates
	$array['naissance'] = array('type' => 'date','options' => array('format' => 'amj'));

	return $array;
}

/**
 * Insertion dans le pipeline affiche_droite (SPIP)
 *
 * Dans certaines pages définies, afficher le lien d'accès à la page des comptes utilisateurs
 *
 * @return array Le même tableau qu'il reçoit en argument
 * @param array $flux Un tableau donnant des informations sur le contenu passé au pipeline
 */

function inscription3_affiche_droite($flux) {
	if (is_array($flux) and isset($flux['args']['exec'])){
		if (((preg_match('/^inscription3/', $flux['args']['exec']))
			or (preg_match('/^auteur/', $flux['args']['exec']))
			or (preg_match('/^i3_/', $flux['args']['exec']))
			or ($flux['args']['exec'] == 'configurer_inscription3')
			or (($flux['args']['exec'] == 'cfg')
				and ((_request('cfg') == 'inscription3') or preg_match('/^i3_/', _request('cfg'))))
			)
			and ($flux['args']['exec'] != 'inscription3_adherents')) {
			$flux['data'] .= recuperer_fond('prive/inscription3_affiche_droite');
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline i3_definition_champs
 *
 * Définition spécifique des champs qui ne sont pas de type text
 * Par défaut inscription3 définit les champs comme étant de type texte, cela peut être
 * différent pour d'autres ...
 */
function inscription3_i3_definition_champs($flux) {
	include_spip('inc/config');
	/**
	 * Récupération de la configuration d'inscription3
	 * pour éviter d'avoir à utiliser la fonction lire_config beaucoup de fois
	 */
	$config_i3 = lire_config('inscription3', array());

	$flux['pays'] = array(
		'saisie' => 'pays', // type de saisie
		'options' => array(
			'sql' => 'int NOT NULL', // declaration sql
			'option_intro'=>_T('inscription3:aucun'),
			'class' => 'pays',
			'defaut' => ((array_key_exists('pays_defaut', $config_i3)
				and isset($config_i3['pays_defaut'])) ? $config_i3['pays_defaut'] : ''),
			'obligatoire' => (array_key_exists('pays_obligatoire', $config_i3)
				and $config_i3['pays_obligatoire'] == 'on') ? true : false,
			'rechercher' => false
		)
	);
	$flux['validite'] = array(
		'saisie' => 'date_jour_mois_annee', // type de saisie
		'options'=> array(
			'sql' => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL", // declaration sql
			'obligatoire' => (array_key_exists('validite_obligatoire', $config_i3)
				and $config_i3['validite_obligatoire'] == 'on') ? true : false,
			'rechercher' => false
		),
		'verifier' => array(
			'type' => 'date',
			'options' => array(
				'format' => 'amj'
			)
		)
	);
	$flux['creation'] = array(
		'saisie' => 'date_jour_mois_annee', // type de saisie
		'options' => array(
			'sql' => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL", // declaration sql
			'rechercher' => false
		),
		'restrictions' => array(
			'voir' => array('auteur' => ''),
			'modifier' => array('auteur' => 'webmestre')
		)
	);
	$flux['naissance'] = array(
		'saisie' => 'date_jour_mois_annee', // type de saisie
		'options' => array(
			'sql' => "DATE DEFAULT '0000-00-00' NOT NULL", // declaration sql
			'obligatoire' => (array_key_exists('naissance_obligatoire', $config_i3)
				and $config_i3['naissance_obligatoire'] == 'on') ? true : false,
			'class'=>'nomulti',
			'datetime'=>'non',
			'rechercher' => false
		),
		'verifier' => array(
			'type' => 'date',
			'options' => array(
				'format' => 'amj'
			)
		)
	);
	$flux['sexe'] = array(
		'saisie' => 'radio', // type de saisie
		'options' => array(
			'label'=> _T('inscription3:label_civilite'),
			'datas'=> array(
				'F' => _T('inscription3:choix_feminin'),
				'M' => _T('inscription3:choix_masculin')
			),
			'sql' => "varchar(2) NOT NULL default ''", // declaration sql
			'obligatoire' => (array_key_exists('sexe_obligatoire', $config_i3)
				and $config_i3['sexe_obligatoire'] == 'on') ? true : false,
			'rechercher' => false
		)
	);

	$flux['commentaire']['saisie'] = 'textarea';
	$flux['commentaire']['options'] = array_merge(((isset($flux['commentaire']['options'])
		and is_array($flux['commentaire']['options'])) ?
			$flux['commentaire']['options'] : array()), array('rows' => 5,'class' => 'adresse'));

	$flux['adresse']['saisie'] = 'textarea';
	$flux['adresse']['options'] = array_merge(((isset($flux['adresse']['options'])
		and is_array($flux['adresse']['options'])) ?
			$flux['adresse']['options'] : array()), array('rows' => 5, 'class' => 'adresse'));

	$flux['telephone']['verifier']['type'] = 'telephone';
	$flux['telephone']['options'] = array('class'=>'nomulti');

	$flux['fax']['verifier'] = 'telephone';
	$flux['fax']['options'] = array('class'=>'nomulti');

	$flux['mobile']['verifier']['type'] = 'telephone';
	$flux['mobile']['options'] = array('class'=>'nomulti');

	$flux['code_postal']['verifier']['type'] = 'code_postal';
	$flux['code_postal']['options']['class'] = 'nomulti';

	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_charger (SPIP)
 *
 * Charge des valeurs spécifiques dans le formulaire d'inscription
 *
 * @param array $flux Le contexte d'environnement du pipeline
 * @return array $flux Le contexte d'environnement modifié
 */
function inscription3_formulaire_charger($flux) {
	if (is_array($flux)
		and isset($flux['args']['form'])
		and $flux['args']['form'] =='inscription') {
		$valeurs = array();
		$chercher_champs = charger_fonction('inscription3_champs_formulaire', 'inc');
		$champs = $chercher_champs(null, 'inscription');
		foreach ($champs as $valeur) {
			$valeurs[$valeur] = _request($valeur);
			if (is_array($valeurs[$valeur])) {
				$valeurs[$valeur] = implode(',', $valeurs[$valeur]);
			}
			$valeurs[$valeur] = trim($valeurs[$valeur]);
			if ($valeur == 'naissance') {
				$date_naissance = array();
				if (_request('naissance')
					and preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2})/', _request('naissance'), $date_naissance)) {
					$valeurs['naissance_annee'] = $date_naissance[1];
					$valeurs['naissance_mois'] = $date_naissance[2];
					$valeurs['naissance_jour'] = $date_naissance[3];
				} else {
					$valeurs['naissance_annee'] = _request('naissance_annee') ? _request('naissance_annee') : '';
					$valeurs['naissance_mois'] = _request('naissance_mois') ? _request('naissance_mois') : '';
					$valeurs['naissance_jour'] = _request('naissance_jour') ? _request('naissance_jour') : '';
				}
			}
		}
		include_spip('cextras_pipelines');
		$saisies = champs_extras_objet('spip_auteurs');
		foreach ($champs as $valeur) {
			if (!$valeurs[$valeur]) {
				if (array_key_exists($valeur, $saisies)) {
					$saisie_nom = $saisies[$valeur]['options']['nom'];
					if (_request($saisie_nom)) {
						$valeurs[$saisie_nom] = trim(_request($saisie_nom));
					}
				}
			}
		}
		$valeurs = pipeline(
			'i3_charger_formulaire',
			array(
				'args' => $flux['args'],
				'data' => $valeurs
			),
			array()
		);

		if (is_array($flux['data'])) {
			$flux['data'] = array_merge($flux['data'], $valeurs);
		} else {
			$flux['data'] = $valeurs;
		}
	}

	if (in_array($flux['args']['form'], array('editer_auteur', 'inscription'))
		and (isset($flux['data']['naissance']) and $flux['data']['naissance'] == '0000-00-00')) {
		unset($flux['data']['naissance']);
		unset($flux['data']['naissance_annee']);
		unset($flux['data']['naissance_mois']);
		unset($flux['data']['naissance_jour']);
	}

	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_verifier (SPIP)
 *
 * Vérifie des valeurs spécifiques dans le formulaire d'inscription et d'édition d'auteur (pour le logo)
 *
 * @pipeline formulaire_verifier
 * @param array $flux
 * 		Le contexte d'environnement du pipeline
 * @return array $flux
 * 		Le contexte d'environnement modifié
 */
function inscription3_formulaire_verifier($flux) {
	include_spip('inc/config');
	if ($flux['args']['form'] == 'configurer_inscription3') {
		/**
		 * On supprime l'ancienne configuration pour avoir la nouvelle dans l'ordre
		 */
		include_spip('inc/meta');
		effacer_meta('inscription3');
	}
	if ($flux['args']['form']=='oubli') {
		$erreurs = $flux['args']['erreurs'];
		if (!$erreurs or (count($erreurs) == 0)) {
			$email = _request('oubli');
			if (!empty($email)) {
				$statut = sql_getfetsel('statut', 'spip_auteurs', 'email='.sql_quote($email));
				if ($statut == '8aconfirmer') {
					$flux['data']['oubli'] = _T('inscription3:erreur_compte_attente_mail');
					$flux['data']['message_erreur'] = _T('inscription3:erreur_compte_attente');
				}
			} else {
				$erreurs['email'] = 'Champ email vide';
			}
		}
	}

	if (in_array($flux['args']['form'], array('editer_auteur', 'inscription'))) {
		/**
		 * On inclue inscription3_fonctions pour prendre en compte la surcharge de
		 * formulaires_inscription_traiter en ajax
		 */
		$erreurs = $flux['data'];

		include_spip('inscription3_fonctions');
		include_spip('inc/editer');
		$config_i3 = lire_config('inscription3', array());
		if ($erreurs['message_erreur'] == null) {
			unset($erreurs['message_erreur']);
		}

		/**
		 * Vérifier le logo
		 */
		if (isset($_FILES['logo']) and ($_FILES['logo']['error'] == 0)) {
			$f =_DIR_LOGOS . rand() . '.tmp';
			include_spip('inc/documents');
			if ($erreur = check_upload_error($_FILES['logo']['error'], '', $f)) {
				$erreurs['logo'] = $erreur;
			} else {
				$source = deplacer_fichier_upload($_FILES['logo']['tmp_name'], $f);
			}

			if ($source and $f) {
				global $formats_logos;
				$size = @getimagesize($f);
				$type = !$size ? '': ($size[2] > 3 ? '' : $formats_logos[$size[2]-1]);
				if ($type) {
					$poids = filesize($f);

					if (_LOGO_MAX_SIZE > 0
						and $poids > _LOGO_MAX_SIZE*1024) {
						spip_unlink($f);
						$erreur = _T(
							'info_logo_max_poids',
							array(
								'maxi' => taille_en_octets(_LOGO_MAX_SIZE*1024),
								'actuel' => taille_en_octets($poids)
							)
						);
					} elseif (_LOGO_MAX_WIDTH * _LOGO_MAX_HEIGHT
						and ($size[0] > _LOGO_MAX_WIDTH
						or $size[1] > _LOGO_MAX_HEIGHT)) {
						spip_unlink($f);
						$erreur = _T(
							'info_logo_max_poids',
							array(
								'maxi' => _T(
									'info_largeur_vignette',
									array(
										'largeur_vignette' => _LOGO_MAX_WIDTH,
										'hauteur_vignette' => _LOGO_MAX_HEIGHT
									)
								),
								'actuel' => _T(
									'info_largeur_vignette',
									array(
										'largeur_vignette' => $size[0],
										'hauteur_vignette' => $size[1]
									)
								)
							)
						);
					}
					spip_unlink($f);
				} else {
					spip_unlink($f);
					$erreur = _T(
						'info_logo_format_interdit',
						array('formats' => join(', ', $formats_logos))
					);
				}
			}

			if ($erreur) {
				$erreurs['logo'] = $erreur;
			}
		}

		/**
		 * Vérification des champs obligatoires
		 * En fonction de ceux présents dans le formulaire
		 */
		$champs_obligatoires = charger_fonction('inscription3_champs_obligatoires', 'inc');
		$obligatoires = $champs_obligatoires(null, $flux['args']['form']);
		unset($obligatoires['email']);
		unset($obligatoires['nom']);
		$erreurs = array_merge($erreurs, formulaires_editer_objet_verifier('auteur', null, $obligatoires));

		if ($flux['args']['form'] == 'inscription') {
			if (lire_config('inscription3/pass_obligatoire') == 'on'
				and lire_config('inscription3/pass') == 'on'
				and (!_request('pass') or !_request('password1'))) {
				$erreurs['pass'] = _T('info_obligatoire');
			} elseif (lire_config('inscription3/pass') == 'on') {
				if (_request('pass') != _request('password1')) {
					$erreurs['pass'] = _T('info_passes_identiques');
				} elseif (strlen(_request('pass')) > 0) {
					$pass_min = !defined('_PASS_MIN') ? 6 : _PASS_MIN;
					if (strlen(_request('pass')) < $pass_min) {
						$erreurs['pass'] = _T('info_passe_trop_court');
					}
				}
			}

			if ($erreurs['reglement']) {
				$erreurs['reglement'] = _T('inscription3:erreur_reglement_obligatoire');
			}
		}

		if (count($erreurs)) {
			$erreurs_obligatoires = true;
		}

		$valeurs = array();

		$verifier = charger_fonction('verifier', 'inc', true);

		if ($verifier) {
			/**
			 * Vérification des champs de champs extras
			 */
			$options = array();
			if ($flux['args']['form'] == 'editer_auteur' and intval(_request('id_auteur'))) {
				$infos_auteurs = sql_fetsel('*', 'spip_auteurs', 'id_auteur='.intval(_request('id_auteur')));
				if (intval($infos_auteurs['id_auteur']) > 0) {
					$options['id_auteur'] = $infos_auteurs['id_auteur'];
				}
			}

			$champs_a_verifier = pipeline('i3_verifications_specifiques', array());
			//gere la correspondance champs -> _request(champs)
			foreach ($champs_a_verifier as $clef => $type) {
				/*
				 * Si le champs n'est pas déjà en erreur suite aux champs obligatoires
				 * On s'assure qu'il est bien présent dans le formulaire également
				 */
				if ($flux['args']['form'] == 'editer_auteur'
					and intval(_request('id_auteur')) > 0
					and in_array($type['type'], array('email', 'signature'))) {
					if ($type['type'] == 'email' and isset($type['options']['disponible'])
						and $infos_auteurs[$clef] == _request($clef)) {
						unset($type['options']['disponible']);
					} elseif (($type['type'] == 'signature') and ($infos_auteurs[$clef] == _request($clef))) {
						continue;
					}
				}
				if (!isset($erreurs[$clef]) and _request($clef)) {
					$valeurs[$clef] = trim(_request($clef));
					$type['options'] = array_merge(array_merge(is_array($type['options']) ?
						$type['options'] : array(), $_GET), $options);
					$erreurs[$clef] = $verifier($valeurs[$clef], $type['type'], $type['options']);
					if ($erreurs[$clef] == null) {
						unset($erreurs[$clef]);
					}
				}
			}
			/**
			 * Vérification des champs de cextras
			 * Uniquement sur le formulaire d'inscription
			 *
			 * On ne vérifie pas les obligatoires qui doivent être faits plus haut
			 */
			if (($flux['args']['form'] == 'inscription')
				and $saisies = champs_extras_objet('spip_auteurs')) {
				include_spip('inc/autoriser');
				include_spip('inc/saisies');

				$saisies = saisies_lister_avec_sql($saisies);

				// restreindre la vue selon les autorisations
				$id_objet = $flux['args']['args'][0]; // ? vraiment toujours ?
				$saisies = champs_extras_autorisation(
					'modifier',
					'auteur',
					$saisies,
					array_merge(
						$flux['args'],
						array(
							'id' => $id_objet,
							'contexte' => array()
						)
					)
				); // nous ne connaissons pas le contexte dans ce pipeline

				foreach ($saisies as $saisie) {
					$nom = $saisie['options']['nom'];
					// verifier (api) + normalisation
					if ($verifier
						and isset($saisie['verifier']['type'])
						and $verif = $saisie['verifier']['type']) {
						$options = isset($saisie['verifier']['options']) ? $saisie['verifier']['options'] : array();
						$normaliser = null;
						if ($erreur = $verifier(_request($nom), $verif, $options, $normaliser)) {
							$erreurs[$nom] = $erreur;
						} elseif (!is_null($normaliser)) {
							// si une valeur de normalisation a ete transmis, la prendre.
							set_request($nom, $normaliser);
						}
					}
				}
			}
		}
		/**
		 * Naisance est un champs spécifique coupé en trois on le vérifie séparément
		 * s'il est obligatoire
		 */
		if ($erreurs['naissance']) {
			$annee = trim(_request('naissance_annee'));
			$mois = trim(_request('naissance_mois'));
			$jour = trim(_request('naissance_jour'));
			if ((!$annee or !$mois or !$jour)
				and $config_i3['naissance_obligatoire'] != 'on'
				and (trim(_request('naissance')) == '0000-00-00')) {
				unset($erreurs['naissance']);
			}
		}
		if (!$erreurs['naissance'] and _request('naissance') and (_request('naissance') != '0000-00-00')) {
			if (_request('naissance_annee') > (date('Y'))) {
				$erreurs['naissance'] = _T('inscription3:erreur_naissance_futur');
			} elseif (_request('naissance_annee') > (date('Y')-10)) {
				$erreurs['naissance'] = _T('inscription3:erreur_naissance_moins_cinq');
			} elseif ((date('Y') - _request('naissance_annee')) > 110) {
				$erreurs['naissance'] = _T('inscription3:erreur_naissance_plus_110');
			}
		}

		$args = array_merge($flux['args'], array('champs' => $valeurs));

		/**
		 * Offrir aux autres plugins la possibilite de verifier les donnees
		 */
		$erreurs = pipeline(
			'i3_verifier_formulaire',
			array(
				'args' => $args,
				'data' => $erreurs
			)
		);

		/**
		 * Message d'erreur generalise
		 */
		if (count($erreurs) and !isset($erreurs['message_erreur'])) {
			if (isset($erreurs_obligatoires)) {
				$erreurs['message_erreur'] .= _T('inscription3:formulaire_remplir_obligatoires');
			} else {
				$erreurs['message_erreur'] .= _T('inscription3:formulaire_remplir_validation');
			}
		}
		$flux['data'] = $erreurs;
	}

	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_traiter (SPIP)
 *
 * Traitement des valeurs spécifiques dans le formulaire d'inscription
 *
 * @param array $flux Le contexte d'environnement du pipeline
 * @return array $flux Le contexte d'environnement modifié
 */
function inscription3_formulaire_traiter($flux) {
	if ($flux['args']['form']=='configurer_inscription3') {
		/**
		 * Crée les champs dans la table spip_auteurs dès la validation du CFG
		 */
		$verifier_tables = charger_fonction('inscription3_verifier_tables', 'inc');
		$verifier_tables();
	}
	if ($flux['args']['form']=='mot_de_passe') {
		$row = sql_fetsel(
			'id_auteur,email,login,source',
			'spip_auteurs',
			array("statut<>'5poubelle'","pass<>''"),
			'',
			'maj DESC',
			'1'
		);
		$affordance = lire_config('inscription3/affordance_form', 'login');
		switch ($affordance) {
			case 'email':
				$flux['data']['message_ok'] = _T('pass_nouveau_enregistre').
					'<p>' . _T('inscription3:pass_rappel_email', array('email' => $row['email']));
				break;
			case 'login_et_email':
				$flux['data']['message_ok'] = _T('pass_nouveau_enregistre').
					'<p>' .
					_T(
						'inscription3:pass_rappel_login_email',
						array('email' => $row['email'],'login'=>$row['login'])
					);
				break;
		}
	}
	/**
	 * Prise en charge du logo dans le formulaire d'édition d'auteur
	 */
	if ($flux['args']['form'] == 'editer_auteur') {
		if (isset($_FILES['logo']) and ($_FILES['logo']['error'] == 0)) {
			$id_auteur = $flux['data']['id_auteur'];
			if (intval($id_auteur) > 0) {
				$chercher_logo = charger_fonction('chercher_logo', 'inc');

				if ($on = $chercher_logo($id_auteur, 'id_auteur', 'on')) {
					spip_unlink($on[0]);
				}
				include_spip('formulaires/editer_logo');
				include_spip('action/editer_logo');
				$sources = formulaire_editer_logo_get_sources();
				foreach ($sources as $etat => $file) {
					if ($file and $file['error'] == 0) {
						logo_modifier('auteur', $id_auteur, $etat, $file);
						set_request('logo_up', ' ');
					}
				}
				// indiquer qu'on doit recalculer les images
				$GLOBALS['var_images'] = true;
			}
		} elseif (_request('supprimer_logo') and _request('supprimer_logo') == 'on') {
			$id_auteur = $flux['data']['id_auteur'];
			if (intval($id_auteur) > 0) {
				$chercher_logo = charger_fonction('chercher_logo', 'inc');

				// supprimer l'ancien logo s'il existe
				if ($on = $chercher_logo($id_auteur, 'id_auteur', 'on')) {
					spip_unlink($on[0]);
				}
			}
		}
	}
	if ($flux['args']['form']=='inscription') {
		if (!function_exists('lire_config')) {
			include_spip('inc/config');
		}
		$config_i3 = lire_config('inscription3', array());
		include_spip('inscription3_fonctions');

		$valeurs = array();
		/**
		 * Les valeurs "normales" du formulaire d'inscription
		 * qui nous permettront de retrouver l'id_auteur
		 */
		$nom = _request('nom_inscription');
		$mail = _request('mail_inscription');

		/**
		 * A ce moment là SPIP a déjà créé l'auteur et lui a déjà donné un login et pass
		 */
		$user = sql_fetsel('*', 'spip_auteurs', 'email='.sql_quote($mail));

		/**
		 * Si l'on demande le passe dans le formulaire
		 * On a un mode avec pass fourni
		 * Sinon un mode simple
		 */
		if (($config_i3['pass'] == 'on') and (strlen(_request('pass')))) {
			$mode = 'inscription_pass';
		} else {
			$mode = 'inscription';
		}

		/**
		 * Generer la liste des champs a traiter
		 * champ => valeur formulaire
		 */
		$chercher_champs = charger_fonction('inscription3_champs_formulaire', 'inc');
		$champs = $chercher_champs(null, 'inscription');
		foreach ($champs as $valeur) {
			$valeurs[$valeur] = _request($valeur);
			if (is_array($valeurs[$valeur])) {
				$valeurs[$valeur] = implode(',', $valeurs[$valeur]);
			}

			$valeurs[$valeur] = trim($valeurs[$valeur]);
			if ($valeur == 'naissance') {
				$annee = trim(_request('naissance_annee'));
				$mois = _request('naissance_mois');
				$jour = _request('naissance_jour');
				$valeurs[$valeur] = sql_format_date($annee, $mois, $jour);
				if ($valeurs[$valeur] == '0000-00-00') {
					unset($valeurs[$valeur]);
					set_request($valeur, '');
				}
			}
		}
		// Definir le login s'il a besoin de l'etre
		// NOM et LOGIN sont des champs obligatoires donc a la creation il ne doivent pas etre vide
		// Apres on s'en fiche s'il n'est pas dans le formulaire
		if (!$valeurs['login'] and !$nom) {
			if ($valeurs['nom_famille'] or $valeurs['prenom']) {
				$valeurs['nom'] = trim($valeurs['prenom'].' '.$valeurs['nom_famille']);
			} else {
				$valeurs['nom'] = strtolower(translitteration(preg_replace('/@.*/', '', $mail)));
			}
		} else {
			$valeurs['nom'] = $nom;
		}

		$valeurs['email'] = $mail;
		if (!$valeurs['login']) {
			if ($user['login']) {
				$valeurs['login'] = $user['login'];
			}
		}

		$trouver_table = charger_fonction('trouver_table', 'base');

		//genere le tableau des valeurs a mettre a jour pour spip_auteurs
		//toutes les clefs qu'inscription3 peut mettre a jour
		$clefs = $trouver_table('auteurs');
		$clefs = $clefs['field'];

		//extrait uniquement les donnees qui ont ete proposees a la modification
		$val = array_intersect_key($valeurs, $clefs);

		/**
		 * Si on demande le pass dans le formulaire
		 * Le compte est automatiquement activé
		 */
		if ($mode == 'inscription_pass') {
			if (strlen(_request('password')) != 0) {
				$new_pass = _request('password');
			} elseif ($mode == 'inscription_pass') {
				$new_pass = _request('pass');
			}

			if (strlen($new_pass)>0) {
				include_spip('inc/acces');
				include_spip('auth/sha256.inc');
				$val['htpass'] = generer_htpass($new_pass);
				$val['alea_actuel']  = creer_uniqid();
				$val['alea_futur'] = creer_uniqid();
				$val['pass'] = _nano_sha256($val['alea_actuel'].$new_pass);
				$val['low_sec'] = '';
			}
			$val['statut'] = (strlen($flux['args']['args'][0]) > 1) ?
				$flux['args']['args'][0] : ($config_i3['statut_nouveau'] ?
					$config_i3['statut_nouveau'] : '6forum');
		}

		/**
		 * On met le compte en "à confirmer" si on a configuré les choses comme cela
		 * Dans ce cas on met la bio à '' si elle n'est pas dans le form afin d'enlever le statut temporaire
		 * qui y est stocké par SPIP
		 * Sinon si on a la bio dans le formulaire et qu'on la reçoit, on met directement un statut à
		 * l'auteur, sinon on laisse l'ancien (nouveau normalement)
		 */
		if ($config_i3['valider_comptes'] == 'on') {
			$mode = 'aconfirmer';
			if (!$val['bio']) {
				$val['bio'] = '';
			}
			$val['statut'] = '8aconfirmer';
		} elseif (_request('bio')) {
			/**
			 * Si on a le champ bio dans le formulaire on force le statut
			 */
			$val['statut'] = (strlen($flux['args']['args'][0]) > 1) ?
				$flux['args']['args'][0] : ($config_i3['statut_nouveau'] ?
					$config_i3['statut_nouveau'] : '6forum');
		}

		if (strlen($val['pass']) == 0) {
			unset($val['pass']);
		}

		if (function_exists('test_inscription')) {
			$f = 'test_inscription';
		} else {
			$f = 'test_inscription_dist';
		}

		$desc = $f($user['bio'], $mail, $valeurs['nom'], $user['id_auteur']);

		if (is_array($desc) and $mail = $desc['email']) {
			/**
			 * On recrée le pass pour être sûr d'avoir le bon
			 */
			$desc['pass'] = creer_pass_pour_auteur($user['id_auteur']);
			$desc['login'] = $val['login'];

			/**
			 * Mise à jour des infos
			 */
			sql_updateq(
				'spip_auteurs',
				$val,
				'id_auteur = '.$user['id_auteur']
			);

			$args = array_merge(
				$flux['args'],
				array(
					'id_auteur' => $user['id_auteur'],
					'champs' => $valeurs
				)
			);

			/**
			 * Prise en charge du logo
			 */
			if (isset($_FILES['logo']) and ($_FILES['logo']['error'] == 0)) {
				$chercher_logo = charger_fonction('chercher_logo', 'inc');

				// supprimer l'ancien logo
				if ($on = $chercher_logo($id_auteur, 'id_auteur', 'on')) {
					spip_unlink($on[0]);
				}

				include_spip('formulaires/editer_logo');
				include_spip('action/editer_logo');
				$sources = formulaire_editer_logo_get_sources();
				foreach ($sources as $etat => $file) {
					if ($file and $file['error'] == 0) {
						logo_modifier('auteur', $user['id_auteur'], $etat, $file);
						set_request('logo_up', ' ');
					}
				}
				// indiquer qu'on doit recalculer les images
				$GLOBALS['var_images'] = true;
			}
			/**
			 * On appelle le pipeline traiter de inscription3
			 * On connait dorénavant l'id_auteur
			 * Ce pipeline doit retourner un array avec les valeurs possibles suivantes :
			 * - ne_pas_confirmer_par_mail boolean (permet de squeezer la notification)
			 * - message_ok string (permet de modifier le message de retour du formulaire)
			 * - editable boolean (permet de modifier le comportement d'affichage au retour)
			 */
			$traiter_plugin = pipeline(
				'i3_traiter_formulaire',
				array(
					'args' => $args,
					'data' => $flux['data']
				)
			);
			if (!$traiter_plugin['ne_pas_confirmer_par_mail']) {
				if ($mode == 'aconfirmer') {
					$traiter_plugin['message_ok'] = _T('inscription3:form_retour_aconfirmer');
					if ($notifications = charger_fonction('notifications', 'inc')) {
						$notifications('i3_inscriptionauteur', $user['id_auteur'],
							array('statut' => '8aconfirmer')
						);
					}
				} elseif ($mode == 'inscription_pass') {
					$traiter_plugin['message_ok'] = _T('inscription3:form_retour_inscription_pass');
					if ($notifications = charger_fonction('notifications', 'inc')) {
						$notifications('i3_inscriptionauteur', $user['id_auteur'],
							array('statut' => $val['statut'],'pass' => 'ok')
						);
					}
					if ($config_i3['auto_login'] == 'on') {
						$auteur = sql_fetsel('*', 'spip_auteurs', 'id_auteur='.intval($user['id_auteur']));
						include_spip('inc/auth');
						auth_loger($auteur);
						$traiter_plugin['message_ok'] = _T('inscription3:form_retour_inscription_pass_logue');
					}
				} else {
					$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
					if (function_exists('envoyer_inscription3')) {
						$mode = $config_i3['statut_nouveau'];
						$f = 'envoyer_inscription3';
						list($sujet,$msg,$from,$head) = $f($desc, $nom, $mode);
					}
					if ($desc) {
						if (!$envoyer_mail($mail, $sujet, $msg, $from, $head)) {
							$flux['data']['message_erreur'] = _T('form_forum_probleme_mail');
						} else {
							$traiter_plugin['message_ok'] = _T('form_forum_identifiant_mail');
						}
					} else {
						$traiter_plugin['message_ok'] = _T('form_forum_identifiant_mail');
					}
				}
			}
			$flux['data']['editable'] = $traiter_plugin['editable'];
			$flux['data']['message_ok'] = $traiter_plugin['message_ok'];
			$flux['data']['redirect'] = $traiter_plugin['redirect'];
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline recuperer_fond (SPIP)
 * Insère des champs dans le formulaire d'inscription
 * Ajoute un vérificateur de complexité de mot de passe sur les formulaires de mot de passe
 * et d'édition d'auteur si besoin
 *
 * @param array $flux
 * @return array
 */
function inscription3_recuperer_fond($flux) {
	$forms = array(
		'formulaires/inscription',
		'formulaires/login',
		'formulaires/mot_de_passe',
		'formulaires/editer_auteur'
	);
	if (in_array($flux['args']['fond'], $forms)) {
		if (!function_exists('lire_config')) {
			include_spip('inc/config');
		}
		$config = lire_config('inscription3', array());
		if ($flux['args']['fond']=='formulaires/inscription') {
			$insc = recuperer_fond('formulaires/inc-inscription-inscription3', $flux['data']['contexte']);
			$flux['data']['texte'] = preg_replace(
				"/(<(li|div) [^>]*class=[\"']editer saisie_mail_inscription.*<\/(li|div)>)/Uims",
				"\\1". $insc,
				$flux['data']['texte'],
				1
			);
			if (($texte_inscription = $config['inscription_texte']) and ($texte_inscription != 'origine')) {
				switch ($texte_inscription) {
					case 'aucun':
						$flux['data']['texte'] = preg_replace(
							"/<p [^>]*class=[\"']explication.*<\/p>/Uims",
							'',
							$flux['data']['texte'],
							1
						);
						break;
					case 'libre':
						$texte = PtoBR(propre($config['inscription_texte_libre']));
						$flux['data']['texte'] = preg_replace(
							"/(<p class=[\"']explication mode[\"']>)(.*)(<\/p>)/Uims",
							"\\1".$texte."\\3",
							$flux['data']['texte'],
							1
						);
						break;
				}
			}
		}
		if ($flux['args']['fond']=='formulaires/login') {
			if (isset($config['affordance_form'])
				and ($type_affordance = $config['affordance_form'])
				and ($type_affordance != 'login')) {
				switch ($type_affordance) {
					case 'email':
						$label = _T('inscription3:votre_mail');
						break;
					case 'login_et_email':
						$label = _T('inscription3:votre_login_mail');
						break;
					case 'libre':
						$label = $config['inscription3/affordance_form_libre'] ?
							$config['inscription3/affordance_form_libre'] : _T('login_login2');
						break;
				}
				if ($label) {
					$flux['data']['texte'] = preg_replace(
						'/(<label.*for="var_login">)(.*)(<\/label>)/Uims',
						"\\1".$label."\\3",
						$flux['data']['texte'],
						1
					);
				}
			}
		}
		/**
		 * On ajoute un vérificateur de complexité de mot de passe
		 */
		if (
			isset($config['inscription3/password_complexite'])
			and $config['inscription3/password_complexite'] == 'on'
			and in_array($flux['args']['fond'], array('formulaires/mot_de_passe','formulaires/editer_auteur'))) {
			$js = recuperer_fond('formulaires/inc-js_pass_verification', $flux['data']['contexte']);
			$flux['data']['texte'] = preg_replace('/(<\/form>)(.*)/Uims', "\\1".$js."\\2", $flux['data']['texte'], 1);
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline editer_contenu_objet (SPIP)
 * Enlève les champs dans le formulaire d'édition d'auteur pour le profil utilisateur
 * comme il est configuré dans inscription3
 *
 * @param array $flux
 * @return array
 */
function inscription3_editer_contenu_objet($flux) {
	$args = $flux['args'];
	$type = $args['type'];
	if ($type == 'auteur' and intval($args['id']) > 0 and !test_espace_prive()) {
		if (!function_exists('lire_config')) {
			include_spip('inc/config');
		}
		$config = lire_config('inscription3', array());
		$champs_spip = array('nom','email','bio','pgp','url_site','nom_site','login','pass');
		$champs_vires = array();
		$inserer_saisie = '';
		foreach ($champs_spip as $champ) {
			if (isset($config[$champ.'_fiche_mod']) and $config[$champ.'_fiche_mod'] != 'on') {
				if ($champ == 'login') {
					$flux['data'] = preg_replace(
						"/(<(li|div) [^>]*class=[\"']editer editer_new_($champ).*<\/(li|div)>)/Uims",
						'',
						$flux['data'],
						1
					);
				} elseif ($champ == 'pass') {
					$flux['data'] = preg_replace(
						"/(<(li|div) [^>]*class=[\"']editer editer_new_($champ).*<\/(li|div)>)/Uims",
						'',
						$flux['data'],
						1
					);
					$flux['data'] = preg_replace(
						"/(<(li|div) [^>]*class=[\"']editer editer_new_($champ)2.*<\/(li|div)>)/Uims",
						'',
						$flux['data'],
						1
					);
				} else {
					$flux['data'] = preg_replace(
						"/(<(li|div) [^>]*class=[\"']editer editer_($champ).*<\/(li|div)>)/Uims",
						'',
						$flux['data'],
						1
					);
				}
				$champs_vires[] = $champ;
				if (in_array($champ, array('nom','email'))) {
					$inserer_saisie .= "<input type='hidden' name='$champ' value='".$flux['args']['contexte'][$champ]."' />\n";
				}
			}
		}

		/**
		 * On vire le champs création du formulaire (ne doit pas être modifié manuellement)
		 * Si on n'a pas ce champs rempli, on utilise la date actuelle pour le remplir
		 * Logiquement ce champs est rempli automatiquement via pre_insertion pour tous les auteurs
		 */
		if (isset($config['creation']) and $config['creation'] == 'on') {
			$flux['data'] = preg_replace(
				inscription3_regexp_capturer_balise('class', 'editer editer_creation', true),
				'',
				$flux['data'],
				1
			);
			spip_log(inscription3_regexp_capturer_balise('class', 'editer editer_cextra_creation', true), 'test.'._LOG_ERREUR);
			$flux['data'] = preg_replace(
				inscription3_regexp_capturer_balise('class', 'editer editer_cextra_creation', true),
				'',
				$flux['data'],
				1
			);

			if ($flux['args']['contexte']['creation'] == '0000-00-00 00:00:00') {
				$flux['args']['contexte']['creation'] = date('Y-m-d H:i:s');
			}
			$inserer_saisie .= "<input type='hidden' name='creation' value='".$flux['args']['contexte']['creation']."' />\n";
		}

		if (!test_espace_prive() and isset($config['logo_fiche_mod']) and $config['logo_fiche_mod'] == 'on') {
			$flux['data'] = preg_replace(
				'/<(form.*[^>])>/Uims',
				'<\\1 enctype=\'multipart/form-data\'>',
				$flux['data'],
				1
			);
			$saisie_logo = recuperer_fond('formulaires/inscription_logo', $flux['args']['contexte']);
			$flux['data'] = preg_replace(
				'/(<!--extra-->)/is',
				'<ul class="champs_extras inscription_logo">'.$saisie_logo.'</ul>'."\n".'$1',
				$flux['data']
			);
		}
		if (in_array('url_site', $champs_vires) and in_array('nom_site', $champs_vires)) {
			$flux['data'] = preg_replace(
				"/(<(li|div) [^>]*class=[\"']editer_liens.*<\/(li|div)>)/Uims",
				'',
				$flux['data'],
				1
			);
		}
		if (in_array('pass', $champs_vires) and in_array('login', $champs_vires)) {
			$flux['data'] = preg_replace(
				"/(<(li|div) [^>]*class=[\"']editer_identification.*<\/(li|div)>)/Uims",
				'',
				$flux['data'],
				1
			);
		}
		if (strlen($inserer_saisie)) {
			$flux['data'] = preg_replace('/(<!-- controles md5 -->)/is', $inserer_saisie."\n".'$1', $flux['data']);
		}
	}
	return $flux;
}


/**
 * Retourne une expression régulière complexe de capture d'une balise spécifique
 * ayant un attribut à une certaine valeur.
 *
 * Cette balise (une div par défaut) au milieu d'un code HTML peut contenir
 * d'autres balises à l'intérieur.
 *
 * @example `inscription3_regexp_capturer_balise('id', 'content')
 * @example `inscription3_regexp_capturer_balise('class', 'editer editer_truc', true)
 *
 * @link http://mac-blog.org.ua/regex-for-div-and-it-content/ pour l'inspiration
 * @link http://php.net/manual/fr/regexp.reference.recursive.php#95568
 *
 * @note
 *     Les expressions régulières ne sont pas adaptées pour traiter du html…
 *     Mais on tente un miracle.
 * @note
 *     Mélange adapté entre 3 expressions :
 *     ```
 *     ",(<div [^>]*class=[\"']editer editer_creation.*<\/div>),Uims"
 *     '#<div\s+class="s"[^>]*>((?:(?:(?!<div[^>]*>|</div>).)+|<div[^>]*>[\s\S]*?</div>)*)</div>#six'
 *     "/<([\w]+)([^>]*?)(([\s]*\/>)|" . "(>((([^<]*?|<\!\-\-.*?\-\->)|(?R))*)<\/\\1[\s]*>))/sm";
 *     ```
 *
 * @param string $attr
 *     Attribut à chercher (exemple: class)
 * @param string $valeur
 *     Valeur à chercher (exemple: editer editer_truc)
 * @param bool $flou
 *     True si l'attribut peut avoir d'autres éléments avant ou après la valeur cherchée
 * @param string $balise
 *     La balise qu'on recherche
 * @param string $modificateurs
 *     Modificateurs de l'expression (pas de modificateur U qui provoquerait un echec).
 * @return string Expression régulière
**/
function inscription3_regexp_capturer_balise($attr, $valeur, $flou = false, $balise = 'div', $modificateurs = 'ims') {
	$regexp =
		# une balise indiquée avec un attribut ouvrant avec soit " ou ' (info capturée en \1)
		'#<' . $balise . ' [^>]*' . $attr . '=(["\'])'
		# la valeur cherchée, avec ou sans flou autour
		. ($flou ? '(?:(?!\1).)*' . $valeur . '(?:(?!\1).)*': $valeur)
		# la fin de guillement de l'attribut trouvé
		. '\1'
		# juste à la fin de la div ouvrante trouvée
		. '[^>]*>'
		# autant de couples de balises (dont autofermantes) et commentaires qu'on veut
			// des balises imbriquées ou non
			. '(?<balises>('
				// pas de début de balise
				. '([^<]*?)('
					// une balise qui s'ouvre
					. '<(?<tag>[\w]+)([^>]*?)'
					// et soit
					. '('
						// 'une balise autofermante'
						. '([\s]*\/>)'
						// 'une fermeture de balise et soit
						. '|(>(('
							. '('
								// pas de balise ouvrante
								. '[^<]*?'
								// ou commentaire html
								. '|<\!\-\-.*?\-\->'
								// ou script html
								. '|<script[^>]*>.*?<\/script>'
							.')'
							// ou recursion sur balise
							. '|(?&balises)'
						// 0 ou plusieurs fois
						. ')*)'
						// et la fin de la balise
						. '<\/(?&tag)[\s]*>)'
					. ')'
				. ')'
			. ')*)'
			// pas de début de balise
			. '([^<]*?)'
		# la fin de notre balise
		. '</' . $balise . '>'
		# les modificateurs
		. '#' . $modificateurs;

	return $regexp;
}

/**
 * Insertion dans le pipeline "notifications_destinataires" (SPIP)
 *
 * En fonction du type de notification, rempli un tableau d'adresses emails
 *
 * @param array $flux Le contexte du pipeline
 * @return array
 */
function inscription3_notifications_destinataires($flux) {
	$quoi = $flux['args']['quoi'];
	$options = $flux['args']['options'];

	/**
	 * Cas de la validation ou invalidation d'un compte d'un utilisateur
	 * Cas également de l'inscription d'un auteur
	 * Envoi à l'utilisateur ($options['type'] == 'user')
	 */
	if (($quoi=='instituerauteur' and $options['statut_ancien'] == '8aconfirmer' and $options['type'] == 'user')
		or ($quoi=='i3_inscriptionauteur' and $options['type'] == 'user')) {
		$id_auteur = $flux['args']['id'];
		include_spip('base/abstract_sql');
		$mail = sql_getfetsel('email', 'spip_auteurs', 'id_auteur='.intval($id_auteur));
		$flux['data'][] = $mail;
	} elseif (($quoi=='instituerauteur' and $options['statut_ancien'] == '8aconfirmer'
		and $options['type'] == 'admin')
		or ($quoi=='i3_inscriptionauteur' and $options['type'] == 'admin')) {
			/**
			 * Cas de la validation ou invalidation d'un compte d'un utilisateur
			 * Envoi aux administrateurs ($options['type'] == 'admin')
			 */
			/**
			 * Aller chercher dans la conf les admins à notifier si configuré
			 */
			if (is_array(lire_config('inscription3/admin_notifications'))) {
				$id_admins = lire_config('inscription3/admin_notifications');
				$admins = sql_allfetsel(
					'email',
					'spip_auteurs',
					'statut="0minirezo" and ' . sql_in('id_auteur', $id_admins)
				);
			} else {
				$admins = sql_allfetsel('email', 'spip_auteurs', 'statut="0minirezo" and webmestre="oui"');
			}

		foreach ($admins as $qui) {
			$flux['data'][] = $qui['email'];
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline taches_generales_cron (SPIP)
 *
 * Vérifie la présence à intervalle régulier d'utilisateurs à valider ou invalider et notifie les admins
 *
 * @param array $taches_generales Un array des tâches du cron de SPIP
 * @return L'array des taches complété
 */
function inscription3_taches_generales_cron($taches_generales) {
	$taches_generales['inscription3_taches_generales'] = 24*60*60;
	return $taches_generales;
}

/**
 * Insertion dans le pipeline pre_insertion (SPIP)
 *
 * Insérer la date d'inscription à la création de l'auteur
 * - la date d'inscription ne se met qu'à ce moment là
 *
 * @param array $flux Le contexte du pipeline
 * @return array
 */
function inscription3_pre_insertion($flux) {
	include_spip('inc/config');
	if (lire_config('inscription3/creation') == 'on' and $flux['args']['table'] == 'spip_auteurs') {
		$flux['data']['creation'] = date('Y-m-d H:i:s');
	}
	return $flux;
}

/**
 * Insertion dans le pipeline openid_recuperer_identite (OpenID)
 * On décrypte les informations fournies par OpenID pour les insérer dans notre formulaire
 *
 * @param $flux array
 * 	Le contexte du pipeline
 * 	Les informations fournies par le compte openid de la personne souhaitant s'inscrire sont dans $flux['args']
 * @return $flux
 * 	Le contexte du pipeline décrypté, on place dans $flux['data'] les informations qui nous intéresse
 */
function inscription3_openid_recuperer_identite($flux) {
	if (isset($flux['args']['dob'])) {
		$flux['data']['naissance'] = $flux['args']['dob'];
	}

	if (isset($flux['args']['country'])) {
		$id_pays = sql_getfetsel('id_pays', 'spip_geo_pays', 'code_iso='.sql_quote($flux['args']['country']));
		$flux['data']['pays'] = $id_pays;
	}
	if (isset($flux['args']['postcode'])) {
		$flux['data']['code_postal'] = $flux['args']['postcode'];
	}

	if (isset($flux['args']['gender'])) {
		$flux['data']['sexe'] = $flux['args']['gender'];
	}

	if (isset($flux['args']['fullname'])) {
		$noms = explode(' ', $flux['args']['fullname']);
		$flux['data']['prenom'] = $noms[0];
		array_shift($noms);
		$flux['data']['nom_famille'] = implode(' ', $noms);
	}
	return $flux;
}

/**
 * Insertion dans le pipeline openid_inscrire_redirect (OpenID)
 */
function inscription3_openid_inscrire_redirect($flux) {

	$auteur = $flux['args']['infos_auteur'];

	$url = $flux['args']['url'];

	$url = parametre_url($url, 'code_postal', $auteur['code_postal']);
	$url = parametre_url($url, 'pays', $auteur['pays']);
	$url = parametre_url($url, 'naissance', $auteur['naissance']);
	$url = parametre_url($url, 'sexe', $auteur['sexe']);
	$url = parametre_url($url, 'login', $auteur['login']);
	$url = parametre_url($url, 'nom_famille', $auteur['nom_famille']);
	$url = parametre_url($url, 'prenom', $auteur['prenom']);
	$flux['data'] = $url;
	return $flux;
}

/**
 * Insertion dans le pipeline post_edition (SPIP)
 *
 * Notifier les admins et les users quand ils passent de 8aconfirmer à un statut valide
 *
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié
 */
function inscription3_post_edition($flux) {
	if (isset($flux['args']['action'])
		and $flux['args']['action'] == 'instituer'
		and $flux['args']['table'] == 'spip_auteurs'
		and $flux['args']['statut_ancien'] == '8aconfirmer') {
		if ($notifications = charger_fonction('notifications', 'inc')) {
			$notifications('i3_inscriptionauteur', $flux['args']['id_objet'],
				array('statut_ancien' => '8aconfirmer', 'statut_nouveau' => $flux['data']['statut'])
			);
		}
	}
	return $flux;
}
