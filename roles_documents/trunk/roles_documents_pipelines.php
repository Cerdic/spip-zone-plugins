<?php
/**
 * Plugin Rôles de documents
 * (c) 2015
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Ajout de contenu dans le bloc «actions» des documents
 *
 * - Formulaire pour définir les rôles des documents
 *
 * @pipeline document_desc_actions
 *
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function roles_documents_document_desc_actions($flux) {
	include_spip('inc/autoriser');

	$texte               = '';
	$exec                = trouver_objet_exec(_request('exec'));
	$objet_exec          = $exec['type'];
	$id_table_objet_exec = $exec['id_table_objet'];
	$id_objet_exec       = intval(_request($id_table_objet_exec));
	// soit objet et id_objet sont passés en paramètre, soit on prend l'objet édité sur la page
	$objet               = !empty($flux['args']['objet']) ? $flux['args']['objet'] : $objet_exec;
	$id_objet            = !empty($flux['args']['id_objet']) ? $flux['args']['id_objet'] : $id_objet_exec;

	if (
		$exec !== false // page d'un objet éditorial
		and $exec['edition'] === false // pas en mode édition
		and $flux['args']['variante'] != 'editer_logo'
		and $id_document = intval($flux['args']['id_document'])
		and autoriser('modifier', 'document', $id_document)
		and $objet
		and $id_objet
	) {
		// bloc à recharger
		$ajaxreload = !empty($flux['args']['ajaxreload']) ? $flux['args']['ajaxreload'] : '.liste_items.documents';
		// mini-formulaire
		$form = recuperer_fond(
			'prive/squelettes/inclure/editer_roles_document',
			array(
				'id_document' => $id_document,
				'objet'       => $objet,
				'id_objet'    => $id_objet,
				'options'     => array(
					'ajaxReload' => $ajaxreload,
				),
			)
		);
		$texte = $form;
	}

	if ($texte) {
		$flux['data'] .= $texte;
	}

	return $flux;
}


/**
 * Synchroniser les champs "vu" et "rang_lien" lors de la création d'un nouveau lien de document portant un rôle.
 *
 * @pipeline post_edition_liens
 *
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function roles_documents_post_edition_lien($flux) {
	if (
		$flux['args']['action'] == 'insert'             // il s'agit d'une création de lien
		and $flux['args']['objet_source'] == 'document' // on a affaire à un document
		and isset($flux['args']['role'])
		and $role = $flux['args']['role']
		and strlen($role)                               // et il y a un role donné
		and isset($flux['args']['colonne_role'])
		and $colonne_role = $flux['args']['colonne_role']
		and $id_document = intval($flux['args']['id_objet_source'])
		and $objet = $flux['args']['objet']
		and $id_objet = intval($flux['args']['id_objet'])
	) {
		// le champ 'rang_lien' n'est présent qu'à partir de SPIP 3.2 (ou avec le plugin ordoc)
		$champs_synchronises = array('vu', 'rang_lien');

		$trouver_table = charger_fonction('trouver_table', 'base');
		$desc = $trouver_table('spip_documents_liens');
		$champs_presents = array_flip(array_intersect_key(array_flip($champs_synchronises), $desc['field']));

		$qualifier = sql_fetsel(
			$champs_presents,
			'spip_documents_liens',
			array(
				'id_document=' . $id_document,
				'objet=' . sql_quote($objet),
				'id_objet=' . $id_objet,
				$colonne_role . '=' . sql_quote('document')
			)
		);
		if ($qualifier) {
			include_spip('action/editer_liens');
			objet_qualifier_liens(
				array('document' => $id_document),
				array($objet => $id_objet),
				array($colonne_role => $role) + $qualifier
			);
		}
	}

	return $flux;
}


/**
 * Après la modif d'un objet, synchroniser le vu de tous les document liés ayant un rôle
 * avec celui du lien de base (ayant le rôle par défaut)
 *
 * @pipeline post_edition
 *
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function roles_documents_post_edition($flux) {
	if (
		isset($flux['args']['action'])
		and $flux['args']['action'] == 'modifier'       // on modifie un objet
		and $flux['args']['table'] !== 'spip_documents'  // mais pas un document
		and $objet = $flux['args']['type']
		and $id_objet = intval($flux['args']['id_objet'])
	) {
		include_spip('action/editer_liens');

		// on regarde s'il y a des documents liés à l'objet modifié
		if (count($liens = objet_trouver_liens(array('document'=>'*'), array($objet=>$id_objet)))) {
			foreach ($liens as $l) {
				// on récupère le champ "vu" du lien avec le rôle par défaut
				$vu = sql_getfetsel(
					'vu',
					'spip_documents_liens',
					'id_document=' .$l['id_document'] .' AND objet='.sql_quote($objet) .'
						AND id_objet='.$id_objet .' AND role='.sql_quote('document')
				);
				// on met à jour tous les autres liens avec rôle
				sql_updateq(
					'spip_documents_liens',
					array('vu'=>$vu),
					'id_document=' .$l['id_document'] .' AND objet='.sql_quote($objet) .'
						AND id_objet='.$id_objet .' AND role!='.sql_quote('document')
				);
			}
		}
	}

	return $flux;
}


/**
 * Empêcher les logos de sortir dans les boucles DOCUMENTS lorsqu'il y a une jointure sur la table de liens (et donc des rôles actifs).
 *
 * C'est nécessaire pour la rétro-compatibilité avec les squelettes existants.
 * Pour voir les logos dans les boucles DOCUMENTS, il faut utiliser
 * explicitement le critère {tout} ou {role=logo} ou {role?}
 *
 * @pipeline pre_boucle
 * @param  array $boucle Données du pipeline
 * @return array       Données du pipeline
 */
function roles_documents_pre_boucle($boucle) {

	// Boucle DOCUMENTS
	if ($boucle->type_requete === 'documents') {

		// Vérifier s'il y a une jointure sur la table de liens
		$jointure_documents = false;
		if ($boucle->join) {
			foreach($boucle->join as $join) {
				if (array_search(sql_quote('documents'), $join) !== false) {
					$jointure_documents = true;
					break;
				}
			}
		}

		// Vérifier la présence du critère {role}
		// [FIXME] vérifier sa valeur (=logo)
		$utilise_critere_logo = false;
		foreach ($boucle->criteres as $critere) {
			if ($critere->type === 'critere') {
				if (
					(isset($critere->param[0][0]->texte) and $critere->param[0][0]->texte === 'role')
					or ($critere->op === 'role')
				) {
					$utilise_critere_logo = true;
				}
			}
		}

		// Gros hack : on évite le traitement pour certains squelettes,
		// afin  d'éviter de les surcharger
		$bypass = false;
		$squelettes_bypass = array(
			array(
				'sourcefile' => 'document_desc.html',
				'id_boucle'  => '_docslies',
			)
		);
		foreach($squelettes_bypass as $squelette) {
			if (substr($boucle->descr['sourcefile'], -strlen($squelette['sourcefile'])) == $squelette['sourcefile']
				and $boucle->id_boucle == $squelette['id_boucle']
			) {
				$bypass = true;
			}
		}

		// Go go go
		if (
			$jointure_documents
			and !$utilise_critere_logo
			and (empty($boucle->modificateur['tout']) or $boucle->modificateur['tout'] === false)
			and !$bypass
		) {
			$table_liens = 'spip_documents_liens';
			$abbrev_table_lien = array_search($table_liens, $boucle->from);

			if ($abbrev_table_lien) {
				$boucle->where[] = array(
					"'!='",
					"'SUBSTR($abbrev_table_lien.role, 1, 4)'",
					"'\'logo\''"
				);
			}
		}
	}

	return $boucle;
}


/**
 * Modifier le tableau retourné par la fonction charger d'un formulaire
 *
 * Ajout du champ 'role' sur le formulaire d'ajout de document
 *
 * @pipeline formulaire_charger
 *
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function roles_documents_formulaire_charger($flux) {

	// Formulaire d'ajout de document
	if ($flux['args']['form'] == 'joindre_document') {
		$flux['data']['roles'] = '';
	}

	return $flux;
}


/**
 * Complèter le tableau de réponse ou faire des traitements supplémentaires pour un formulaire
 *
 * Qualifier le lien crée avec le rôle choisi
 *
 * @pipeline formulaire_traiter
 *
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function roles_documents_formulaire_traiter($flux) {

	// Formulaire d'ajout de document
	// En présence d'un role sélectionne, on requalifie le lien créé
	// sauf si c'est le rôle par défaut
	if ($flux['args']['form'] == 'joindre_document'
		and $roles = _request('roles')
		and $objet = $flux['args']['args'][2]
		and $id_objet = $flux['args']['args'][1]
		and !empty($flux['data']['ids'])
	) {
		foreach ($flux['data']['ids'] as $id_document) {
			if (!is_array($roles)) {
				$roles = array($roles);
			}
			foreach ($roles as $role) {
				if ($role != 'document') {
					$update = sql_updateq(
						'spip_documents_liens',
						array('role' => $role),
						array(
							'id_document=' . intval($id_document),
							'objet='       . sql_quote($objet),
							'id_objet='    . intval($id_objet),
							'role='        . sql_quote('document'),
						)
					);
				}
			}
		}
	}

	return $flux;
}


/**
 * Modifier le résultat du calcul d’un squelette donné.
 *
 * - Formulaire d'ajout de document : ajout du sélecteur de rôle, et rendre les identifiants uniques pour éviter un pb de JS quand le form est présent plusieurs fois sur la page.
 * - Mediathèque : ajout du filtrage par rôle
 * 
 * @pipeline recuperer_fond
 *
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function roles_documents_recuperer_fond($flux) {

	// Ajout de document
	if ($flux['args']['fond'] == 'formulaires/inc-upload_document'
		and isset($flux['args']['contexte']['objet'])
		and isset($flux['args']['contexte']['id_objet'])
	) {

		// 1) Ajout du sélecteur de rôle

		// Est-ce qu'il s'agit d'un ajout de logo ?
		$editer_logo = !empty($flux['args']['contexte']['editer_logo']);
		$principaux = $editer_logo ? true : false;

		// Retrouver les rôles restant à associer
		$objet = $flux['args']['contexte']['objet'];
		$id_objet = $flux['args']['contexte']['id_objet'];
		$roles = roles_documents_presents_sur_objet($objet, $id_objet, 0, $principaux);
		$roles_attribuables = isset($flux['args']['contexte']['roles_attribuables']) ?
			$flux['args']['contexte']['roles_attribuables'] :
			($editer_logo ?
				$roles['attribuables'] :
				$roles['possibles']
			);
		$multiple = $editer_logo ? null : (count($roles_attribuables) > 1 ? 'oui' : null);
		$contexte = array(
			'roles'              => $flux['args']['contexte']['roles'],
			'roles_attribuables' => $roles_attribuables,
			'multiple'           => $multiple,
		);

		// On place le sélecteur au début (compliqué de le placer juste avant les boutons, déplacés en JS, et des blocs cachés)
		$selecteur_roles = recuperer_fond('formulaires/inc-selecteur_role', $contexte);
		$flux['data']['texte'] = $selecteur_roles . $flux['data']['texte'];

		// 2) Rendre les identifiants vraiment uniques pour le JS

		if (preg_match('/id=["\']defaultsubmit([^"\']+)/i', $flux['data']['texte'], $res)) {
			$domid = $res[1]; // L'identifiant pas si unique présent par défaut
			$uniqid = $domid . '_' . uniqid(); // Identifiant vraiement unique
			$flux['data']['texte'] = str_replace($domid, $uniqid, $flux['data']['texte']);
		}

	}

	// Médiathèque
	if ($flux['args']['fond'] == 'prive/squelettes/inclure/mediatheque-navigation') {

		$fond_roles = recuperer_fond('prive/squelettes/inclure/mediatheque-navigation-roles', $flux['args']['contexte']);
		// On s'insère après le dernier <ul> de la barre d'onglets secondaires
		// Sans parseur, c'est la galère
		$cherche = "#<ul\s+class=[\"']sanstitre[\"']>\s*(?:<li[^>]*>(?!.*<li>).*?</li>\s*)+\s*</ul>#i";
		$remplace = "$0$fond_roles";
		$flux['data']['texte'] = preg_replace($cherche, $remplace, $flux['data']['texte']);
	}

	return $flux;
}


/**
 * Modifier le résultat du calcul d’un squelette de formulaire.
 *
 * - Formulaire d'édition de logo : on a besoin de bénéficier des éventuelles modifications effectuées auu formulaire joindre_document.
 *   Pour se faire on appelle le même pipeline à nouveau en se faisant passer pour ce dernier.
 *
 * @param array $flux
 * @return array
 **/
function roles_documents_formulaire_fond($flux) {

	if ($flux['args']['form'] == 'editer_logo'
		//and !empty($flux['args']['contexte']['_bigup_rechercher_fichiers'])
	) {
		$flux_joindre_document = $flux;
		$flux_joindre_document['args']['form'] = 'joindre_document';
		$data = pipeline('formulaire_fond', $flux_joindre_document);
		$flux['data'] = $data;
	}

	return $flux;
}