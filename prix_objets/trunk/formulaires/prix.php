<?php
if (!defined("_ECRIRE_INC_VERSION"))
	return;

include_spip('inc/saisies');

function formulaires_prix_charger_dist($id_objet, $objet = 'article') {
	include_spip('inc/config');
	include_spip('inc/prix_objets');

	$devises_dispos = lire_config('prix_objets/devises');
	$taxes_inclus = lire_config('prix_objets/taxes_inclus');
	$taxes = lire_config('prix_objets/taxes');
	$table = 'spip_prix_objets';

	// Devise par défaut si rien configuré
	if (!$devises_dispos) {
		$devises_dispos = array(
			'0' => 'EUR'
		);
	}

	$devises_choisis = array();
	$prix_choisis = array();
	if (is_array($id_objet)) {
		$id_objet_produit = implode(',', $id_objet);
	}

	if ($id_objet) {
		$d = sql_select('*', 'spip_prix_objets', 'id_objet IN(' . $id_objet . ') AND objet =' . sql_quote($objet));
	}

	// établit les devises diponible moins ceux déjà utilisés
	while ($row = sql_fetch($d)) {
		$prix_choisis[] = $row;
	}

	$devises = array_diff($devises_dispos, $devises_choisis);

	$valeurs = array(
		'prix_choisis' => $prix_choisis,
		'taxes_inclus' => $taxes_inclus,
		'devises' => $devises,
		'code_devise' => '',
		'objet' => $objet,
		'id_objet' => $id_objet,
		'prix_ht' => $taxes_inclus,
		'objet_titre' => '',
		'taxes' => $taxes,
		'taxe' => ''
	);

	$valeurs['_hidden'] = '<input type="hidden" name="objet" value="' . $objet . '">';
	$valeurs['_hidden'] .= '<input type="hidden" name="id_objet" value="' . $id_objet . '">';

	// Inclure les extensions.
	$valeurs['_saisies_extras'] = prix_objets_extensions_declaration($valeurs);
	$trouver_table = charger_fonction('trouver_table', 'base');
	$decription_table = $trouver_table($table);
	$extensions = array();
	$saisies = array();

	foreach ($valeurs['_saisies_extras'] as $s) {
		$saisies = array_merge($saisies, $s);
		foreach (saisies_lister_par_nom($s) as $nom => $definition) {
			$valeurs[$nom] = _request($nom);
			$objet = $definition['objet'];
			$extensions[] = $objet;

			// Assurer que un champ d'identifiant de l'extension existe, sinon l'ajouter.
			if ($identifiant_extension = id_table_objet($objet) and
					!isset($decription_table['field'][$identifiant_extension])
					) {
				sql_alter("TABLE $table ADD $identifiant_extension bigint(21) NOT NULL");

				// Vide le chache des déscriptions des tables.
				$trouver_table('');
			}
		}
	}

	// Déclarer les extensions
	if (count($extensions) > 0) {
		$saisie = array(
				array(
				'saisie' => 'hidden',
				'options' => array(
					'nom' => 'extensions',
					'defaut' => implode(',', $extensions),
				)
			)
		);
		$valeurs['extensions'] = _request('extensions');

		$valeurs['_saisies_extras'] = array_merge($saisies, $saisie);
	}



	return $valeurs;
}
function formulaires_prix_verifier_dist($id_objet, $objet = 'article') {


	if (!_request('code_devise')) {
		$erreurs['code_devise'] = _T('info_obligatoire');
	}

	if (!is_numeric(_request('prix'))) {
		$erreurs['prix'] = _T('info_obligatoire');
	}



	return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera resoumis
}
function formulaires_prix_traiter_dist($id_objet, $objet = 'article') {
	$prix = _request('prix');
	$id_declinaison = _request('id_declinaison');
	$extensions =  _request('extensions') ? explode(',', _request('extensions')) : array();

	// Génération du titre
	$titre = extraire_multi(supprimer_numero(generer_info_entite($id_objet, $objet, 'titre', '*')));

	// Le titre secondaire composé des extensions.
	if (!is_array($extensions)) {
		$extensions = array(0 => $extensions);
	}

	$titre_secondaire = array();
	$valeurs_extensions = array();
	foreach($extensions as $extension) {
		if ($id_extension = _request('id_prix_extension_' . $extension)) {
			if (!is_array($id_extension)) {
				$titre = extraire_multi(
					supprimer_numero(
						generer_info_entite(
							$id_extension,
							$extension,
							'titre', '*'
							)
						)
					);
				$titre_secondaire[] = $titre;
				$valeurs_extensions[] = array(
					'titre' => $titre,
					'extension' => $extension,
					'id_extension' => $id_extension
				);
			}
			else {
				foreach ($id_extension as $id) {
					$titre = extraire_multi(
						supprimer_numero(
							generer_info_entite(
								$id,
								$extension,
								'titre', '*'
								)
							)
						);
					$titre_secondaire[] = $titre;
					$valeurs_extensions[] = array(
						'titre' => $titre,
						'extension' => $extension,
						'id_extension' => $id
					);
				}
			}
		}
	}

		$titre_secondaire = implode(' / ', $titre_secondaire);

		if ($titre_secondaire) {
			$titre = $titre . ' - ' . $titre_secondaire;
		}

	// On inscrit dans la bd
	$valeurs =  array(
			'id_objet' => $id_objet,
			'objet' => $objet,
			'code_devise' => _request('code_devise'),
			'titre' => $titre,
			'taxe' => _request('taxe'),
			'prix' => 0,
			'prix_ht' => 0
		);

	if ($ttc = _request('taxes_inclus'))
		$valeurs['prix'] = $prix;
	else
		$valeurs['prix_ht'] = $prix;

	$result = sql_insertq('spip_prix_objets', $valeurs);

	// Ivalider le cache
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_prix_objet/$id_prix_objet'");

	return $valeur['message_ok'] = true;
}

?>