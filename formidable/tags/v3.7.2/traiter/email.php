<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
include_spip('inc/utils');
include_spip('inc/formidable_fichiers');

function traiter_email_dist($args, $retours) {
	if (!isset($retours['fichiers'])) {
		$retours['fichiers'] = array();
		$ajouter_fichier = true;
	} else {
		$ajouter_fichier = false;
	}
	$timestamp = time();
	$retours['timestamp'] = $timestamp;
	$formulaire = $args['formulaire'];
	$options = $args['options'];
	$saisies = unserialize($formulaire['saisies']);
	$traitements = unserialize($formulaire['traitements']);
	$champs = saisies_lister_champs($saisies);
	$destinataires = array();
	$taille_fichiers = 0; //taille des fichiers en email
	$fichiers_facteur = array(); // tableau qui stockera les fichiers à envoyer avec facteur
	if (isset($options['exclure_champs_email']) && $options['exclure_champs_email']) {
		$champs_a_exclure = explode(",", $options['exclure_champs_email']);
		$champs = array_diff($champs, $champs_a_exclure);
		foreach ($champs_a_exclure as $champ_a_exclure) {
			$saisies = saisies_supprimer($saisies,$champ_a_exclure);
		}
	}

	// On récupère les destinataires
	if ($options['champ_destinataires']) {
		$destinataires = _request($options['champ_destinataires']);
		if (!is_array($destinataires)) {
			if (intval($destinataires)) {
				$destinataires = array($destinataires);
			} else {
				$destinataires = array();
			}
		}
		if (count($destinataires)) {
			// On récupère les mails des destinataires
			$destinataires = array_map('intval', $destinataires);
			$destinataires = sql_allfetsel(
				'email',
				'spip_auteurs',
				sql_in('id_auteur', $destinataires)
			);
			$destinataires = array_map('reset', $destinataires);
		}
	}

	if ($options['champ_courriel_destinataire_form']) {
		$courriel_champ_form = _request($options['champ_courriel_destinataire_form']);
		$destinataires[] = $courriel_champ_form;
	}


	// On ajoute les destinataires en plus
	if ($options['destinataires_plus']) {
		$destinataires_plus = explode(',', $options['destinataires_plus']);
		$destinataires_plus = array_map('trim', $destinataires_plus);
		$destinataires = array_merge($destinataires, $destinataires_plus);
		$destinataires = array_unique($destinataires);
	}

	// On ajoute les destinataires en fonction des choix de saisie dans le formulaire
	// @selection_1@/choix1 : mail@domain.tld
	// @selection_1@/choix2 : autre@domain.tld, lapin@domain.tld
	if (!empty($options['destinataires_selon_champ'])) {
		if ($destinataires_selon_champ = formidable_traiter_email_destinataire_selon_champ($options['destinataires_selon_champ'])) {
			$destinataires = array_merge($destinataires, $destinataires_selon_champ);
			$destinataires = array_unique($destinataires);
		}
	}

	// On récupère le courriel de l'envoyeur s'il existe
	if ($options['champ_courriel']) {
		$courriel_envoyeur = _request($options['champ_courriel']);
	}
	if (!isset($courriel_envoyeur)) {
		$courriel_envoyeur = '';
	}

	// Si on a bien des destinataires, on peut continuer
	if ($destinataires or ($courriel_envoyeur and $options['activer_accuse'])) {
		include_spip('inc/filtres');
		include_spip('inc/texte');

		$nom_site_spip = supprimer_tags(typo($GLOBALS['meta']['nom_site']));

		// On parcourt les champs pour générer le tableau des valeurs
		$valeurs = array();
		$valeurs_libellees = array();
		$saisies_fichiers = saisies_lister_avec_type($saisies, 'fichiers');
		$saisies_par_nom = saisies_lister_par_nom($saisies);

		// On utilise pas formulaires_formidable_fichiers,
		// car celui-ci retourne les saisies fichiers du formulaire dans la base… or, on sait-jamais,
		// il peut y avoir eu une modification entre le moment où l'utilisateur a vu le formulaire et maintenant
		foreach ($champs as $champ) {
			if (array_key_exists($champ, $saisies_fichiers)) {// si on a affaire à une saisie de type fichiers, on traite à part
				$valeurs[$champ] = traiter_email_fichiers($saisies_fichiers[$champ], $champ, $formulaire['id_formulaire'], $retours, $timestamp);
				if ($ajouter_fichier) {
					$retours['fichiers'][$champ] = $valeurs[$champ];
				}
				$taille_fichiers += formidable_calculer_taille_fichiers_saisie($valeurs[$champ]);
				$fichiers_facteur = array_merge(
					$fichiers_facteur,
					vue_fichier_to_tableau_facteur($valeurs[$champ])
				);
			} else {
				// On récupère la valeur postée
				$valeurs[$champ] = _request($champ);
				
				// Si la saisie est une liste de choix avec des clés et labels humains, on cherche le label humain, sauf si la case champ_sujet_valeurs_brutes est cochée dans la config du traitement
				if (
					isset($saisies_par_nom[$champ]['options']['datas'])
					and $labels_data = saisies_aplatir_tableau(saisies_chaine2tableau($saisies_par_nom[$champ]['options']['datas']))
					and isset($labels_data[$valeurs[$champ]])
					and !$options['champ_sujet_valeurs_brutes']
				) {
					$valeurs_libellees[$champ] = $labels_data[$valeurs[$champ]];
				}
				// Sinon on utilise directement la valeur postée
				else {
					$valeurs_libellees[$champ] = $valeurs[$champ];
				}
			}
		}
		// On récupère le nom de l'envoyeur
		if ($options['champ_nom']) {
			$a_remplacer = array();
			if (preg_match_all('/@[\w]+@/', $options['champ_nom'], $a_remplacer)) {
				$a_remplacer = $a_remplacer[0];
				foreach ($a_remplacer as $cle => $val) {
					$a_remplacer[$cle] = trim($val, '@');
				}
				$a_remplacer = array_flip($a_remplacer);
				$a_remplacer = array_intersect_key($valeurs_libellees, $a_remplacer);
				$a_remplacer = array_merge($a_remplacer, array('nom_site_spip' => $nom_site_spip));
			}
			$nom_envoyeur = trim(_L($options['champ_nom'], $a_remplacer));
		}
		if (!isset($nom_envoyeur) or !$nom_envoyeur) {
			$nom_envoyeur = $nom_site_spip;
		}

		// On récupère le sujet s'il existe sinon on le construit
		if ($options['champ_sujet']) {
			$a_remplacer = array();
			if (preg_match_all('/@[\w]+@/', $options['champ_sujet'], $a_remplacer)) {
				$a_remplacer = $a_remplacer[0];
				foreach ($a_remplacer as $cle => $val) {
					$a_remplacer[$cle] = trim($val, '@');
				}
				$a_remplacer = array_flip($a_remplacer);
				$a_remplacer = array_intersect_key($valeurs_libellees, $a_remplacer);
				$a_remplacer = array_merge($a_remplacer, array('nom_site_spip' => $nom_site_spip));
			}
			$sujet = trim(_L($options['champ_sujet'], $a_remplacer));
		}
		if (!isset($sujet) or !$sujet) {
			$sujet = _T('formidable:traiter_email_sujet', array('nom'=>$nom_envoyeur));
		}
		$sujet = filtrer_entites($sujet);

		// Mais quel va donc être le fond ?
		if (find_in_path('notifications/formulaire_'.$formulaire['identifiant'].'_email.html')) {
			$notification = 'notifications/formulaire_'.$formulaire['identifiant'].'_email';
		} else {
			$notification = 'notifications/formulaire_email';
		}
		// Est-ce qu'on est assez léger pour joindre les pj
		$joindre_pj = false;
		if ($taille_fichiers < 1024 * 1024 * _FORMIDABLE_TAILLE_MAX_FICHIERS_EMAIL
			and
			$traitements['email']['pj'] == 'on'
		) {
			$joindre_pj = true;
			foreach (array_keys($saisies_fichiers) as $nom) {
				$saisies = saisies_supprimer($saisies,$nom);	
			}
		}
		// On génère le mail avec le fond
		$html = recuperer_fond(
			$notification,
			array(
				'id_formulaire' => $args['id_formulaire'],
				'id_formulaires_reponse' => isset($retours['id_formulaires_reponse']) ? $retours['id_formulaires_reponse'] : '',
				'titre' => _T_ou_typo($formulaire['titre']),
				'traitements' => $traitements,
				'saisies' => $saisies,
				'valeurs' => $valeurs,
				'masquer_liens' => $options['masquer_liens'],
				'ip' => $options['activer_ip']?$GLOBALS['ip']:''
			)
		);

		// On génère le texte brut
		include_spip('facteur_fonctions');
		$texte = facteur_mail_html2text($html);

		// On utilise la forme avancée de Facteur
		$corps = array(
			'html' => $html,
			'texte' => $texte,
			'nom_envoyeur' => filtrer_entites($nom_envoyeur),
		);
		// Joindre les pj si léger
		if ($joindre_pj) {
			$corps['pieces_jointes'] = $fichiers_facteur;
		}
	
		// Si l'utilisateur n'a pas indiqué autrement, on met le courriel de l'envoyeur dans
		// Reply-To et on laisse le from par defaut de Facteur car sinon ca bloque sur les
		// SMTP un peu restrictifs.
		$courriel_from = '';
		if ($courriel_envoyeur && $options['activer_vrai_envoyeur']) {
			$courriel_from = $courriel_envoyeur;
		} elseif ($courriel_envoyeur) {
			$corps['repondre_a'] = $courriel_envoyeur;
		}

		// On envoie enfin le message
		$envoyer_mail = charger_fonction('envoyer_mail', 'inc');

		// On envoie aux destinataires
		if ($destinataires) {
			$ok = $envoyer_mail($destinataires, $sujet, $corps, $courriel_from, 'X-Originating-IP: '.$GLOBALS['ip']);
		}

		// Si c'est bon, on envoie l'accusé de réception
		if ($ok and $courriel_envoyeur and $options['activer_accuse']) {
			// On récupère le sujet s'il existe sinon on le construit
			if ($options['sujet_accuse']) {
				$a_remplacer = array();
				if (preg_match_all('/@[\w]+@/', $options['sujet_accuse'], $a_remplacer)) {
					$a_remplacer = $a_remplacer[0];
					foreach ($a_remplacer as $cle => $val) {
						$a_remplacer[$cle] = trim($val, '@');
					}
					$a_remplacer = array_flip($a_remplacer);
					$a_remplacer = array_intersect_key($valeurs_libellees, $a_remplacer);
					$a_remplacer = array_merge($a_remplacer, array('nom_site_spip' => $nom_site_spip));
				}
				$sujet_accuse = trim(_L($options['sujet_accuse'], $a_remplacer));
			}
			if (!isset($sujet_accuse) or !$sujet_accuse) {
				$sujet_accuse = _T('formidable:traiter_email_sujet_accuse');
			}
			$sujet_accuse = filtrer_entites($sujet_accuse);

			// Mais quel va donc être le fond ?
			if (find_in_path('notifications/formulaire_'.$formulaire['identifiant'].'_accuse.html')) {
				$accuse = 'notifications/formulaire_'.$formulaire['identifiant'].'_accuse';
			} else {
				$accuse = 'notifications/formulaire_accuse';
			}

			// On génère l'accusé de réception
			if (_FORMIDABLE_LIENS_FICHIERS_ACCUSE_RECEPTION == false) {
				$valeurs = vues_saisies_supprimer_action_recuperer_fichier_par_email($saisies, $valeurs);
			}
			$html_accuse = recuperer_fond(
				$accuse,
				array(
					'id_formulaire' => $formulaire['id_formulaire'],
					'titre' => _T_ou_typo($formulaire['titre']),
					'message_retour' => $formulaire['message_retour'],
					'traitements' => $traitements,
					'saisies' => $saisies,
					'valeurs' => $valeurs
				)
			);

			// On génère le texte brut
			$texte = facteur_mail_html2text($html_accuse);

			// Si un nom d'expéditeur est précisé pour l'AR, on l'utilise,
			// sinon on utilise le nomde l'envoyeur du courriel principal
			$nom_envoyeur_accuse = trim($options['nom_envoyeur_accuse']);
			if (!$nom_envoyeur_accuse) {
				$nom_envoyeur_accuse = $nom_envoyeur;
			}

			$corps = array(
				'html' => $html_accuse,
				'texte' => $texte,
				'nom_envoyeur' => filtrer_entites($nom_envoyeur_accuse),
			);
			
			//A fortiori, si un courriel d'expéditeur est précisé pour l'AR, on l'utilise
			if ($options['courriel_envoyeur_accuse']) {
				$courriel_envoyeur_accuse = $options['courriel_envoyeur_accuse'];
			} else {
				$courriel_envoyeur_accuse = $courriel_envoyeur;
			}

			//Et on teste si on doit mettre cela en from ou en reply-to
			if ($options['activer_vrai_envoyeur'] and $courriel_envoyeur_accuse) {
				$courriel_from_accuse = $courriel_envoyeur_accuse;
			} elseif ($courriel_envoyeur_accuse) {
				$corps['repondre_a'] = $courriel_envoyeur_accuse;
				$courriel_from_accuse = '';
			}
			
			// Joindre les pj si léger et nécessaire
			if ($joindre_pj and  _FORMIDABLE_LIENS_FICHIERS_ACCUSE_RECEPTION == false) {
				$corps['pieces_jointes'] = $fichiers_facteur;
			}

			$ok = $envoyer_mail($courriel_envoyeur, $sujet_accuse, $corps, $courriel_from_accuse, 'X-Originating-IP: '.$GLOBALS['ip']);
		}

		if ($ok) {
			if (isset($retours['message_ok'])) {
				$retours['message_ok'] .= "\n"._T('formidable:traiter_email_message_ok');
			} else {
				$retours['message_ok'] = _T('formidable:traiter_email_message_ok');
			}
		} else {
			if (isset($retours['message_erreur'])) {
				$retours['message_erreur'] .= "\n"._T('formidable:traiter_email_message_erreur');
			} else {
				$retours['message_erreur'] = _T('formidable:traiter_email_message_erreur');
			}
		}
	}

	// noter qu'on a deja fait le boulot, pour ne pas risquer double appel
	$retours['traitements']['email'] = true;
	return $retours;
}


/**
 * Retourne la liste des destinataires sélectionnés en fonction
 * de l'option 'destinataires_selon_champ' du traitement email.
 *
 * @param string $description
 *     Description saisie dans l'option du traitement du formulaire,
 *     qui respecte le schéma prévu, c'est à dire : 1 description par ligne,
 *     tel que `@champ@/valeur : mail@domain.tld, mail@domain.tld, ...`
 *     {@example : `@selection_2@/choix_1 : toto@domain.tld`}
 * @return array
 *     Liste des destinataires, s'il y en a.
 **/
function formidable_traiter_email_destinataire_selon_champ($description) {
	$destinataires = array();

	// 1 test à rechercher par ligne
	$descriptions = explode("\n", trim($description));
	$descriptions = array_map('trim', $descriptions);
	$descriptions = array_filter($descriptions);

	// pour chaque test, s'il est valide, ajouter les courriels indiqués
	foreach ($descriptions as $test) {
		// Un # est un commentaire
		if ($test[0] == '#') {
			continue;
		}
		// Le premier caractère est toujours un @
		if ($test[0] != '@') {
			continue;
		}


		list($champ, $reste) = explode('/', $test, 2);
		$champ = substr(trim($champ), 1, -1); // enlever les @

		if ($reste) {
			list($valeur, $mails) = explode(':', $reste, 2);
			$valeur = trim($valeur);
			$mails = explode(',', $mails);
			$mails = array_map('trim', $mails);
			$mails = array_filter($mails);
			if ($mails) {
				// obtenir la valeur du champ saisi dans le formulaire.
				// cela peut être un tableau.
				$champ = _request($champ);
				if (!is_null($champ)) {
					$ok = is_array($champ) ? in_array($valeur, $champ) : ($champ == $valeur);

					if ($ok) {
						$destinataires = array_merge($destinataires, $mails);
						$destinataires = array_unique($destinataires);
					}
				}
			}
		}
	}

	return $destinataires;
}

/**
 * Gère une saisie de type fichiers dans le traitement par email.
 * C'est à dire:
 *	- S'il y a eu un enregistement avant, ne déplace pas le fichier
 *	- S'il n'y a pas eu d'enregistrement avant, déplace le fichier
 *		dans un dossier nommé en fonction du timestamp du traitement
 *	- Renvoie un tableau décrivant les fichiers, avec une url d'action sécurisée valable seulement
 *		_FORMIDABLE_EXPIRATION_FICHIERS_EMAIL (sauf si cette constantes est définie à 0)
 * @param array $saisie la description de la saisie
 * @param string $nom le nom de la saisie
 * @param int|string $id_formulaire le formulaire concerné
 * @param array $retours ce qu'a envoyé le précédent traitement
 * @param int $timestamp un timestamp correspondant au début du processus de création du courriel
 * @return array un tableau décrivant la saisie
 **/
function traiter_email_fichiers($saisie, $nom, $id_formulaire, $retours, $timestamp) {
	//Initialisation
	$id_formulaire = strval($id_formulaire);//précaution
	$vue = array();

	if (isset($retours['id_formulaires_reponse']) and $id_formulaires_reponse = $retours['id_formulaires_reponse']) { // cas simple: les réponses ont été enregistrées
		if (isset($retours['fichiers'][$nom])) { // petite précaution
			$options = array(
				'id_formulaire' => $id_formulaire,
				'id_formulaires_reponse' => $retours['id_formulaires_reponse']
			);
			$vue = ajouter_action_recuperer_fichier_par_email($retours['fichiers'][$nom], $nom, $options);
		}
	} else { // si les réponses n'ont pas été enregistrées
		$vue = formidable_deplacer_fichiers_produire_vue_saisie($saisie, array('id_formulaire' => $id_formulaire, 'timestamp' => $timestamp));
			$options = array(
				'id_formulaire' => $id_formulaire,
				'timestamp' => $timestamp
			);
			$vue = ajouter_action_recuperer_fichier_par_email($vue, $nom, $options);
	}

	return $vue;
}


/**
 * Pour une saisie de type 'fichiers'
 * insère dans la description du résultat de cette saisie
 * l'url de l'action pour récuperer la saisie par email
 * Ajoute également une vignette correspondant à l'extension
 * @param array $saisie_a_modifier
 * @param string $nom_saisie
 * @param array $options options qui décrit l'endroit où est stocké le fichier
 * @return array $saisie_a_modifier
 **/
function ajouter_action_recuperer_fichier_par_email($saisie_a_modifier, $nom_saisie, $options) {
	$vignette_par_defaut = charger_fonction('vignette', 'inc/');

	if (_FORMIDABLE_EXPIRATION_FICHIERS_EMAIL > 0) {
		$delai = secondes_en_jour(_FORMIDABLE_EXPIRATION_FICHIERS_EMAIL);
	}
	foreach ($saisie_a_modifier as $i => $valeur) {
		$url = formidable_generer_url_action_recuperer_fichier_email($nom_saisie, $valeur['nom'], $options);
		$saisie_a_modifier[$i]['url'] = $url;
		if (_FORMIDABLE_EXPIRATION_FICHIERS_EMAIL > 0) {
			$saisie_a_modifier[$i]['fichier'] = $valeur['nom'];
			$saisie_a_modifier[$i]['nom'] = '['._T('formidable:lien_expire', array('delai' => $delai)).'] '.$valeur['nom'];
		} else {
			$saisie_a_modifier[$i]['fichier'] = $valeur['nom'];
			$saisie_a_modifier[$i]['nom'] = $valeur['nom'];
		}
		if (isset($valeur['extension'])) {
			$saisie_a_modifier[$i]['vignette'] = $vignette_par_defaut($valeur['extension'], false);
		}
	}
	return $saisie_a_modifier;
}
/**
 * Supprime dans une vue de saisie 'fichiers'
 * l'url de récupération par email
 * et l'information sur le délai d'expiration
 * @param array $vue
 * @return array $vue
**/
function supprimer_action_recuperer_fichier_par_email($vue) {
	foreach ($vue as $f => &$desc) {
		if (isset($desc['url'])) {
			unset($desc['url']);
		}
		$desc['nom'] = $desc['fichier'];
	}
	return $vue;
}

/**
 * Dans l'ensemble de vues des saisies
 * recherche les saisies 'fichiers'
 * et supprime pour chacune d'entre elle les actions de récupération de fichier
 * @param array $saisies
 * @param array $vues
 * @return array $vues
**/
function vues_saisies_supprimer_action_recuperer_fichier_par_email($saisies, $vues) {
	foreach ($saisies as $saisie => $description) {
		if ($description['saisie'] == 'fichiers') { // si de type fichiers
			$nom_saisie = $description['options']['nom'];
			$vues[$nom_saisie] = supprimer_action_recuperer_fichier_par_email($vues[$nom_saisie]);
		}
	}
	return $vues;
}

/**
 * Calcule la taille totale des fichiers
 * d'après une saisie de type fichiers
 * @param array $saisie
 * @return int $taille (en octets)
**/
function formidable_calculer_taille_fichiers_saisie($saisie) {
	$taille = 0;
	foreach ($saisie as $k => $info) {
		$taille += $info['taille'];
	}
	return $taille;
}

/**
 * Converti une description d'une vue fichiers en description passable à facteur
 * @param array $vue
 * @return array $tableau_facteur
**/
function vue_fichier_to_tableau_facteur($vue) {
	$tableau_facteur = array();
	foreach ($vue as $fichier) {
		$arg = unserialize(parametre_url($fichier['url'],'arg'));
		$tableau_facteur[] = array(
			'chemin' => formidable_generer_chemin_fichier($arg),
			'nom' => $fichier['fichier'],
			'encodage' => 'base64',
			'mime' => $fichier['mime']);
	}
	return $tableau_facteur;
}

/**
 * Retourne des secondes sous une jolie forme, du type xx jours, yy heures, zz minutes, aa secondes
 * @param int $seconde
 * @return str
**/
function secondes_en_jour($secondes) {
	//On ne peut pas utiliser date_create, car en PHP 5.2, et SPIP 3.0 est à partir de PHP 5.1…
	$jours = floor($secondes/(24*3600));
	$heures = floor(($secondes-$jours*24*3600)/3600);
	$minutes = floor(($secondes-$jours*24*3600-$heures*3600)/60);
	$secondes = $secondes-$jours*24*3600-$heures*3600-$minutes*60;
	$param = array(
		'j' => $jours,
		'h' => $heures,
		'm' => $minutes,
		's' => $secondes
	);
	if ($jours > 0) {
		return _T('formidable:jours_heures_minutes_secondes', $param);
	} elseif ($heures > 0) {
		return _T('formidable:heures_minutes_secondes', $param);
	} elseif ($minutes > 0) {
		return _T('formidable:minutes_secondes', $param);
	} else {
		return _T('formidable:secondes', $param);
	}
}
