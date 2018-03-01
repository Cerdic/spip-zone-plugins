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
		and $id_document = intval($flux['args']['id_document'])
		and autoriser('modifier', 'document', $id_document)
		and $objet
		and $id_objet
	) {
		// bloc à recharger
		$ajaxreload = !empty($flux['args']['ajaxreload']) ? $flux['args']['ajaxreload'] : '#documents';
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
 * Chercher le logo d'un ojet
 *
 * S'il n'y a pas de logo, on prend la 1ère image avec le rôle "logo"
 *
 * @pipeline quete_logo_objet
 *
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function roles_documents_quete_logo_objet($flux) {
	// Si personne n'a trouvé de logo avant
	if (empty($flux['data'])) {
		// On cherche la première image avec un rôle "logo"
		include_spip('base/abstract_sql');

		// Quel rôle va-t-on chercher ?
		if ($flux['args']['mode'] === 'on') {
			$role = 'logo';
		} elseif ($flux['args']['mode'] === 'off') {
			$role = 'logo_survol';
		} else {
			$role = $flux['args']['mode'];
		}

		// Hack : le logo du site à un id négatif
		if ($flux['args']['objet'] == 'site_spip'
			and intval($flux['args']['id_objet']) === 0
		) {
			$flux['args']['id_objet'] = -1;
		}

		if ($image = sql_fetsel(
			'fichier, extension',
			'spip_documents as d inner join spip_documents_liens as l on d.id_document = l.id_document',
			array(
				'l.objet = '.sql_quote($flux['args']['objet']),
				'l.id_objet = '.intval($flux['args']['id_objet']),
				sql_in('extension', array('png', 'jpg', 'gif')),
				'l.role='.sql_quote($role),
			),
			'', //group
			'0+titre, titre'
		)) {
			// Si c'est un URL on retourne le chemin directement
			if (filter_var($image['fichier'], FILTER_VALIDATE_URL)) {
				$chemin_complet = $image['fichier'];
			}
			// Sinon on va le chercher dans IMG
			else {
				$chemin_complet = _DIR_IMG . $image['fichier'];
			}

			$flux['data'] = array(
				'chemin' => $chemin_complet,
				'timestamp' => @filemtime($chemin_complet),
			);
		}
	}
	return $flux;
}


/**
 * Empêcher les logos de sortir dans les boucles DOCUMENTS standards.
 *
 * C'est nécessaire pour la rétro-compatibilité avec les squelettes existants.
 * Pour voir les logos dans les boucles DOCUMENTS, il faut utiliser
 * explicitement le critère {tout} ou {role=logo}
 *
 * @pipeline pre_boucle
 * @param  array $boucle Données du pipeline
 * @return array       Données du pipeline
 */
function roles_documents_pre_boucle($boucle) {

	// Boucle DOCUMENTS
	if ($boucle->type_requete === 'documents') {

		// Vérifier la présence du critère {role}
		// [FIXME] vérifier sa valeur (=logo)
		$utilise_critere_logo = false;
		foreach ($boucle->criteres as $critere) {
			if ($critere->type === 'critere') {
				if (($critere->param[0][0]->texte === 'role') or
					($critere->op === 'role')) {
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
		if (!$utilise_critere_logo
			and $boucle->modificateur['tout'] == false
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
		$flux['data']['role'] = '';
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
	if ($flux['args']['form'] == 'joindre_document'
		and $role = _request('role')
		and $role != 'document'
		and $objet = $flux['args']['args'][2]
		and $id_objet = $flux['args']['args'][1]
		and !empty($flux['data']['ids'])
	) {
		foreach ($flux['data']['ids'] as $id_document) {
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

	return $flux;
}



/**
 * Modifier le résultat du calcul d’un squelette donné.
 *
 * Ajout du sélecteur de rôle sur un inclure du formulaire d'ajout de document.
 *
 * @pipeline recuperer_fond
 *
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function roles_documents_recuperer_fond($flux) {

	// Ajout de document
	if ($flux['args']['fond'] == 'formulaires/inc-upload_document'
		and !empty($flux['args']['contexte']['objet'])
		and !empty($flux['args']['contexte']['id_objet'])
	) {
	
		// Est-ce qu'il s'agit d'un ajout de logo ?
		$editer_logo = !empty($flux['args']['contexte']['editer_logo']);
		$principaux = $editer_logo ? true : false;

		// Retrouver les rôles restant à associer
		$objet = $flux['args']['contexte']['objet'];
		$id_objet = $flux['args']['contexte']['id_objet'];
		$roles = roles_documents_presents_sur_objet($objet, $id_objet, 0, $principaux);
		$contexte = array(
			'role' => $flux['args']['contexte']['role'],
			'roles' => $editer_logo ? $roles['non_attribues'] : $roles['possibles'],
		);

		$selecteur_roles = recuperer_fond('formulaires/inc-selecteur_role', $contexte);
		$flux['data']['texte'] = $selecteur_roles . $flux['data']['texte'];
	}

	return $flux;
}