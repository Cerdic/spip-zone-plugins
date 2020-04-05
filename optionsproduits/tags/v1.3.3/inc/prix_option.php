<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/*
 * Permet d'obtenir le prix HT d'un objet SPIP avec une option
 * C'est le résultat de cette fonction qui est utilisée pour calculer le prix TTC.
 *
 * @param string $type_objet Le type de l'objet
 * @param int $id_objet L'identifiant de l'objet
 * @return float Retourne le prix HT de l'objet sinon 0
 */
function inc_prix_option_ht_dist($type_objet, $id_objet, $id_option, $arrondi = 2, $serveur = '') {
	//debug('inc_prix_option_ht_dist');
	$prix_ht = 0;
	// Cherchons d'abord si l'objet existe bien
	if ($type_objet
		and $id_objet = intval($id_objet)
		and include_spip('base/connect_sql')
		and $type_objet = objet_type($type_objet)
		and $table_sql = table_objet_sql($type_objet, $serveur)
		and $cle_objet = id_table_objet($type_objet, $serveur)
		and $ligne = sql_fetsel('*', $table_sql, "$cle_objet = $id_objet", '', '', '', '', $serveur)
	) {

		// Existe-t-il une fonction précise pour le prix HT de ce type d'objet : prix_option_<objet>_ht() dans prix_option/<objet>.php
		if ($fonction_ht = charger_fonction('ht', "prix_option/$type_objet", true)) {
			// On passe la ligne SQL en paramètre pour ne pas refaire la requête
			$prix_ht = $fonction_ht($type_objet, $id_objet, $id_option, $ligne);
		} else {
			// S'il n'y a pas de fonction, regardons s'il existe des champs normalisés, ce qui évite d'écrire une fonction pour rien
			if ($ligne['prix_ht']) {
				$prix_ht = $ligne['prix_ht'];
			} elseif ($ligne['prix']) {
				$prix_ht = $ligne['prix'];
			}
			if ($id_option) {
				$prix_option_ht = sql_getfetsel(
					'prix_option_objet',
					'spip_options_liens',
					array(
						'id_option = ' . $id_option,
						'objet = ' . sql_quote($type_objet),
						'id_objet = ' . $id_objet,
					)
				);
				$prix_ht        += $prix_option_ht;
			}
		}
	}

	// Si on demande un arrondi, on le fait
	if ($arrondi) {
		$prix_ht = round($prix_ht, $arrondi);
	}

	return $prix_ht;
}

/*
 * Permet d'obtenir le prix final TTC d'un objet SPIP avec une option
 *
 * @param string $type_objet Le type de l'objet
 * @param int $id_objet L'identifiant de l'objet
 * @return float Retourne le prix TTC de l'objet sinon 0
 */
function inc_prix_option_dist($type_objet, $id_objet, $id_option, $arrondi = 2, $serveur = '') {
	//debug('inc_prix_option_dist');
	include_spip('base/connect_sql');

	// On va d'abord chercher le prix HT. On délègue le test de présence de l'objet dans cette fonction.
	$fonction_prix_ht = charger_fonction('ht', 'inc/prix_option');
	$type_objet       = objet_type($type_objet);
	$prix             = $prix_ht = $fonction_prix_ht($type_objet, $id_objet, $id_option, 0, $serveur);

	// On cherche maintenant s'il existe une personnalisation pour les taxes : prix_option_<objet>() dans prix_option/<objet>.php
	if ($fonction_prix_objet = charger_fonction($type_objet, 'prix_option/', true)) {
		$prix = $fonction_prix_objet($type_objet, $id_objet, $id_option, $prix_ht);
	}

	// Enfin on passe dans un pipeline pour pouvoir ajouter taxes, ristournes ou autres modifications
	$prix = pipeline(
		'prix_option',
		array(
			'args' => array(
				'id_objet'   => $id_objet,
				'type_objet' => $type_objet,
				'prix_ht'    => $prix_ht,
				'prix'       => $prix,
			),
			'data' => $prix,
		)
	);

	// Si on demande un arrondi, on le fait
	if ($arrondi) {
		$prix = round($prix, $arrondi);
	}

	// Et c'est fini
	return $prix;
}

