<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Permet d'obtenir le prix HT d'un objet SPIP. C'est le résultat de cette fonction qui est utilisée pour calculer le prix TTC.
 *
 * @param string $objet
 *   Le type de l'objet
 * @param int $id_objet
 *   L'identifiant de l'objet
 * @param array $options
 *   Tableau d'options
 *   - arrondi
 *   - serveur
 *   ou un entier pour l'arrondi pour compat avec l'ancienne signature
 * @param string $serveur
 *   Déprécié. Autre base distante.
 * @return float Retourne le prix HT de l'objet sinon 0
 */
function inc_prix_ht_dist($objet, $id_objet, $options = array(), $serveur = ''){
	include_spip('base/objets');
	$prix_ht = 0;
	
	// Compatibilité avec l'ancienne signature
	if (is_int($options)) {
		$options = array(
			'arrondi' => $options,
			'serveur' => $serveur,
		);
	}
	// Options par défaut
	$options_defaut = array(
		'arrondi' => 2,
		'serveur' => '',
	);
	// On fusionne avec les défauts
	$options = array_merge($options_defaut, $options);
	
	// Cherchons d'abord si l'objet existe bien
	if (
		$objet
		and $id_objet = intval($id_objet)
		and $objet = objet_type($objet)
		and $table_sql = table_objet_sql($objet, $options['serveur'])
		and $cle_objet = id_table_objet($objet, $options['serveur'])
		and $ligne = sql_fetsel('*', $table_sql, "$cle_objet = $id_objet", '', '', '', '', $options['serveur'])
	){
		// Existe-t-il une fonction précise pour le prix HT de ce type d'objet : prix_<objet>_ht() dans prix/<objet>.php
		if ($fonction_ht = charger_fonction('ht', "prix/$objet", true)){
			// On passe la ligne SQL en paramètre pour ne pas refaire la requête
			$prix_ht = $fonction_ht($id_objet, $ligne, $options);
		}
		// S'il n'y a pas de fonction, regardons s'il existe des champs normalisés, ce qui évite d'écrire une fonction pour rien
		elseif (!empty($ligne['prix_ht'])) {
			$prix_ht = $ligne['prix_ht'];
		}
		elseif ($ligne['prix']) {
			$prix_ht = $ligne['prix'];
		}
		
		// Enfin on passe dans un pipeline pour modifier le prix HT
		$prix_ht = pipeline(
			'prix_ht',
			array(
				'args' => array(
					'objet' => $objet,
					'id_objet' => $id_objet,
					'type_objet' => $objet, // déprécié, utiliser plutôt "objet"
					'prix_ht' => $prix_ht,
					'options' => $options,
				),
				'data' => $prix_ht
			)
		);
	}
	
	// Si on demande un arrondi, on le fait
	if ($options['arrondi']) {
		$prix_ht = round($prix_ht, $options['arrondi']);
	}
	
	return $prix_ht;
}

/*
 * Permet d'obtenir le prix final TTC d'un objet SPIP quel qu'il soit.
 *
 * @param string $objet
 *   Le type de l'objet
 * @param int $id_objet
 *   L'identifiant de l'objet
 * @param array $options
 *   Tableau d'options
 *   - arrondi
 *   - serveur
 *   ou un entier pour l'arrondi pour compat avec l'ancienne signature
 * @param string $serveur
 *   Déprécié. Autre base distante.
 * @return float Retourne le prix TTC de l'objet sinon 0
 */
function inc_prix_dist($objet, $id_objet, $options = array(), $serveur = '') {
	include_spip('base/objets');
	
	// Compatibilité avec l'ancienne signature
	if (is_int($options)) {
		$options = array(
			'arrondi' => $options,
			'serveur' => $serveur,
		);
	}
	// Options par défaut
	$options_defaut = array(
		'arrondi' => 2,
		'serveur' => '',
	);
	// On fusionne avec les défauts
	$options = array_merge($options_defaut, $options);
	
	// On va d'abord chercher le prix HT. On délègue le test de présence de l'objet dans cette fonction.
	$fonction_prix_ht = charger_fonction('ht', 'inc/prix');
	$objet = objet_type($objet);
	$options_ht = array_merge($options, array('arrondi'=>0));
	$prix = $prix_ht = $fonction_prix_ht($objet, $id_objet, $options_ht);
	$taxes = array();
	
	// On cherche maintenant s'il existe une personnalisation pour le prix total TTC : prix_<objet>() dans prix/<objet>.php
	if ($fonction_prix_objet = charger_fonction($objet, 'prix/', true)){
		$prix = $fonction_prix_objet($id_objet, $prix_ht, $options);
	}
	// Sinon on appelle une fonction générique pour trouver les taxes d'un objet, et on ajoute au HT
	elseif ($fonction_taxes = charger_fonction('taxes', 'inc/', true)) {
		$taxes = $fonction_taxes($objet, $id_objet, $options);
		$taxes_total = array_sum(array_column($taxes, 'montant'));
		$prix = $prix_ht + $taxes_total;
	}
	
	// Enfin on passe dans un pipeline pour pouvoir ajouter taxes, ristournes ou autres modifications
	$prix = pipeline(
		'prix',
		array(
			'args' => array(
				'objet' => $objet,
				'id_objet' => $id_objet,
				'type_objet' => $objet, // déprécié, utiliser plutôt "objet"
				'prix_ht' => $prix_ht,
				'options' => $options,
				'taxes' => $taxes,
			),
			'data' => $prix
		)
	);
	
	// Si on demande un arrondi, on le fait
	if ($options['arrondi']) {
		$prix = round($prix, $options['arrondi']);
	}
	
	// Et c'est fini
	return $prix;
}

