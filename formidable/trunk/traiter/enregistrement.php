<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
include_spip('inc/formidable_fichiers');
function traiter_enregistrement_dist($args, $retours) {
	include_spip('inc/formidable');
	include_spip('base/abstract_sql');
	$retours['fichiers'] = array(); // on va stocker des infos sur les fichiers, pour les prochains traitement
	$options = $args['options'];
	$formulaire = $args['formulaire'];
	$id_formulaire = $args['id_formulaire'];
	$saisies = unserialize($formulaire['saisies']);
	$saisies = saisies_lister_par_nom($saisies);
	$variable_php = '';
	// La personne a-t-elle un compte ?
	$id_auteur = isset($GLOBALS['visiteur_session']) ? (isset($GLOBALS['visiteur_session']['id_auteur']) ?
		$GLOBALS['visiteur_session']['id_auteur'] : 0) : 0;

	// traitement de l'anonymisation de l'auteur lorsque la méthode d'identification se fait par l'identifiant
	if ($options['anonymiser'] == 'on' and $id_auteur) {
		if ($options['identification'] == 'id_auteur' ) {
			$variable_php = formidable_hasher_id_auteur($id_auteur);
		}
		$id_auteur = 0;
	}

	// On cherche le cookie et sinon on le crée
	$nom_cookie = formidable_generer_nom_cookie($id_formulaire);
	if (isset($_COOKIE[$nom_cookie])) {
		$cookie = $_COOKIE[$nom_cookie];
	} else {
		include_spip('inc/acces');
		$cookie = creer_uniqid();
	}

	// On crée un identifiant depuis l'éventuelle variable php d'identification
	if ($options['identification'] == 'variable_php' ) {
		$variable_php = formidable_variable_php_identification($options['variable_php'], $id_formulaire);
	}

	// On regarde si c'est une modif d'une réponse existante
	$id_formulaires_reponse = $args['id_formulaires_reponse'];
	if (!$args['forcer_modif']) {//si on dit lors de l'appel du formulaire qu'on doit modifier dans tous les cas, inutile de chercher si on peut modifier
		$id_formulaires_reponse = formidable_trouver_reponse_a_editer($id_formulaire, $id_formulaires_reponse, $options, true);
	}

	// Si la moderation est a posteriori ou que la personne est un boss, on publie direct
	if ($options['moderation'] == 'posteriori'
		or autoriser(
			'instituer',
			'formulairesreponse',
			$id_formulaires_reponse,
			null,
			array('id_formulaire' => $id_formulaire, 'nouveau_statut' => 'publie')
		)) {
		$statut='publie';
	} else {
		$statut = 'prop';
	}

	// Si ce n'est pas une modif d'une réponse existante, on crée d'abord la réponse
	if (!$id_formulaires_reponse) {
		$id_formulaires_reponse = sql_insertq(
			'spip_formulaires_reponses',
			array(
				'id_formulaire' => $id_formulaire,
				'id_auteur' => $id_auteur,
				'cookie' => $cookie,
				'variable_php' => $variable_php,
				'ip' => $args['options']['ip'] == 'on' ? $GLOBALS['ip'] : '',
				'date' => 'NOW()',
				'statut' => $statut
			)
		);
		// Si on a pas le droit de répondre plusieurs fois ou que les réponses seront modifiables,
		// il faut poser un cookie
		if (!$options['multiple'] or $options['modifiable']) {
			include_spip('inc/cookie');
			// Expiration dans 30 jours
			spip_setcookie($nom_cookie, $_COOKIE[$nom_cookie] = $cookie, time() + 30 * 24 * 3600);
		}
		$retours['modification_reponse'] = false;// signaler aux traitements qu'il s'agit d'une nouvelle réponse
	} else { // si c'est une modif de réponse existante
		// simple mise à jour du champ maj de la table spip_formulaires_reponses
		sql_updateq(
			'spip_formulaires_reponses',
			array('maj' => 'NOW()'),
			"id_formulaires_reponse = $id_formulaires_reponse"
		);
		//effacer les fichiers existant
		formidable_effacer_fichiers_reponse($id_formulaire, $id_formulaires_reponse);
		$retours['modification_reponse'] = true;// signaler aux traitements qui viendraient après qu'il s'agit d'une modif
	}
	// Si l'id n'a pas été créé correctement alors erreur
	if (!($id_formulaires_reponse > 0)) {
		$retours['message_erreur'] .= "\n<br/>"._T('formidable:traiter_enregistrement_erreur_base');
	} else {
		// Sinon on continue à mettre à jour
		$champs = array();
		$insertions = array();
		foreach ($saisies as $nom => $saisie) {
			if ($saisie['saisie'] == 'fichiers') { // traiter à part le cas des saisies fichiers
				$valeur = traiter_enregistrement_fichiers($saisie, $id_formulaire, $id_formulaires_reponse);
				if (($valeur !== null)) {
					$champs[] = $nom;
					$insertions[] = array(
						'id_formulaires_reponse' => $id_formulaires_reponse,
						'nom' => $nom,
						'valeur' => is_array($valeur) ? serialize($valeur) : $valeur
					);
					$retours['fichiers'][$nom] = $valeur;
				}
			}
			elseif (($valeur = _request($nom)) !== null or saisies_saisie_est_tabulaire($saisie)) {
				// Pour le saisies différentes de fichiers,
				// on ne prend que les champs qui ont effectivement été envoyés par le formulaire
				$champs[] = $nom;
				$insertions[] = array(
					'id_formulaires_reponse' => $id_formulaires_reponse,
					'nom' => $nom,
					'valeur' => is_array($valeur) ? serialize($valeur) : $valeur
				);
			}
		}
		// S'il y a bien des choses à modifier
		if ($champs) {
			// On supprime d'abord les champs
			sql_delete(
				'spip_formulaires_reponses_champs',
				array(
					'id_formulaires_reponse = '.$id_formulaires_reponse,
					sql_in('nom', $champs)
				)
			);

			// Puis on insère les nouvelles valeurs
			sql_insertq_multi(
				'spip_formulaires_reponses_champs',
				$insertions
			);
		}
		if (!isset($retours['message_ok'])) {
			$retours['message_ok'] = '';
		}
		$retours['message_ok'] .= "\n"._T('formidable:traiter_enregistrement_message_ok');
		$retours['id_formulaires_reponse'] = $id_formulaires_reponse;
	}

	//Invalider le cache le cas échéant
	if (
		isset($options['invalider'])
		and $options['invalider']
		and $options['moderation']=='posteriori'
	) {
		include_spip('inc/invalideur');
		suivre_invalideur("formulaires_reponse/$id_formulaires_reponse");
	}

	// noter qu'on a deja fait le boulot, pour ne pas risquer double appel
	$retours['traitements']['enregistrement'] = true;
	return $retours;
}

function traiter_enregistrement_update_dist($id_formulaire, $traitement, $saisies_anciennes, $saisies_nouvelles) {
	include_spip('inc/saisies');
	include_spip('base/abstract_sql');
	$comparaison = saisies_comparer($saisies_anciennes, $saisies_nouvelles);

	// Si des champs ont été supprimés, il faut supprimer les réponses à ces champs
	if ($comparaison['supprimees']) {
		// On récupère les réponses du formulaire
		$reponses = sql_allfetsel(
			'id_formulaires_reponse',
			'spip_formulaires_reponses',
			'id_formulaire = '.$id_formulaire
		);
		$reponses = array_map('reset', $reponses);
		// Tous les noms de champs à supprimer
		$noms = array_keys($comparaison['supprimees']);

		// On supprime
		sql_delete(
			'spip_formulaires_reponses_champs',
			array(
				sql_in('id_formulaires_reponse', $reponses),
				sql_in('nom', $noms)
			)
		);
		// On efface les vieux fichiers
		foreach ($noms as $nom) {
			if ($comparaison['supprimees'][$nom]['saisie'] == 'fichiers') {
				formidable_effacer_fichiers_champ($id_formulaire, $reponses, $nom);
			}
		}
	}
}

function traiter_enregistrement_verifier_dist($args, $erreurs) {
	$id_formulaire = $args['id_formulaire'];
	$options = $args['options'];
	$id_formulaires_reponse = $args['id_formulaires_reponse'];
	$etape = $args['etape'];
	if (($unicite = $options['unicite']) != '' and !$erreurs[$unicite]
		and
		(
			$etape === null
			or (
			 array_key_exists($unicite, saisies_lister_par_nom($args['etapes'][$etape]['saisies']))
			)
		)
	) {
		if (!$id_formulaires_reponse) { // si pas de réponse explictement passée au formulaire, on cherche la réponse qui serait édité
			$id_formulaires_reponse = formidable_trouver_reponse_a_editer($id_formulaire, $id_formulaires_reponse, $options);
		}

		if ($id_formulaires_reponse != false) {
			$unicite_exclure_reponse_courante = ' AND R.id_formulaires_reponse != '.$id_formulaires_reponse;
		} else {
			$unicite_exclure_reponse_courante = '';
		}

		$reponses = sql_allfetsel(
			'R.id_formulaire AS id',
			'spip_formulaires_reponses AS R
				LEFT JOIN spip_formulaires AS F
				ON R.id_formulaire=F.id_formulaire
				LEFT JOIN spip_formulaires_reponses_champs AS C
				ON R.id_formulaires_reponse=C.id_formulaires_reponse',
			'R.id_formulaire = ' . $id_formulaire .
				$unicite_exclure_reponse_courante .
				' AND C.nom='.sql_quote($unicite).'
				AND C.valeur='.sql_quote(_request($unicite)).'
				AND R.statut = "publie"'
		);
		if (is_array($reponses) && count($reponses) > 0) {
			$erreurs[$unicite] = $options['message_erreur_unicite'] ?
				_T($options['message_erreur_unicite']) : _T('formidable:erreur_unicite');
		}
	}

	return $erreurs;
}

/**
 * Pour une saisie 'fichiers' particulière,
 * déplace chaque fichier envoyé dans le dossier
 * config/fichiers/formidable/formulaire_$id_formulaire/reponse_$id_formulaires_reponse.
 * @param array $saisie la description de la saisie
 * @param int $id_formulaire le formulaire
 * @param int $id_formulaires_reponse
 * @return array|null un tableau organisé par fichier, contenant 'nom', 'extension','mime','taille'
**/
function traiter_enregistrement_fichiers($saisie, $id_formulaire, $id_formulaires_reponse) {
	return formidable_deplacer_fichiers_produire_vue_saisie($saisie, array('id_formulaire' => $id_formulaire, 'id_formulaires_reponse' => $id_formulaires_reponse));
}
