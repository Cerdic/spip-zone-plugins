<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Permet d'obtenir le prix HT d'un objet SPIP. C'est le résultat de cette fonction qui est utilisée pour calculer le prix TTC.
 *
 * @param string $type_objet Le type de l'objet
 * @param int $id_objet L'identifiant de l'objet
 * @return float Retourne le prix HT de l'objet sinon 0
 */
function inc_prix_ht_dist($type_objet, $id_objet, $arrondi=2){
	$prix_ht = 0;
	// Cherchons d'abord si l'objet existe bien
	if ($type_objet
		and $id_objet = intval($id_objet)
		and include_spip('base/connect_sql')
		and $type_objet = objet_type($type_objet)
		and $table_sql = table_objet_sql($type_objet)
		and $cle_objet = id_table_objet($type_objet)
		and $ligne = sql_fetsel('*', $table_sql, "$cle_objet = $id_objet")
	){
		// Existe-t-il une fonction précise pour le prix HT de ce type d'objet : prix_ht_<objet>() dans prix/<objet>.php
		if ($fonction_ht = charger_fonction('ht', "prix/$type_objet", true)){
			// On passe la ligne SQL en paramètre pour ne pas refaire la requête
			$prix_ht = $fonction_ht($id_objet, $ligne);
		}
		// S'il n'y a pas de fonction, regardons s'il existe des champs normalisés, ce qui évite d'écrire une fonction pour rien
		elseif ($ligne['prix_ht'])
			$prix_ht = $ligne['prix_ht'];
		elseif ($ligne['prix'])
			$prix_ht = $ligne['prix'];
		
		// Enfin on passe dans un pipeline pour modifier le prix HT
		$prix_ht = pipeline(
			'prix_ht',
			array(
				'args' => array(
					'id_objet' => $id_objet,
					'type_objet' => $type_objet,
					'prix_ht' => $prix_ht
				),
				'data' => $prix_ht
			)
		);
	}
	
	// Si on demande un arrondi, on le fait
	if ($arrondi)
		$prix_ht = round($prix_ht, $arrondi);
	
	return $prix_ht;
}

/*
 * Permet d'obtenir le prix final TTC d'un objet SPIP quel qu'il soit.
 *
 * @param string $type_objet Le type de l'objet
 * @param int $id_objet L'identifiant de l'objet
 * @return float Retourne le prix TTC de l'objet sinon 0
 */
function inc_prix_dist($type_objet, $id_objet, $arrondi=2){
	include_spip('base/connect_sql');
	
	// On va d'abord chercher le prix HT. On délègue le test de présence de l'objet dans cette fonction.
	$fonction_prix_ht = charger_fonction('ht', 'inc/prix');
	$type_objet = objet_type($type_objet);
	$prix = $prix_ht = $fonction_prix_ht($type_objet, $id_objet, $arrondi);
	
	// On cherche maintenant s'il existe une personnalisation pour les taxes : prix_<objet>() dans prix/<objet>.php
	if ($fonction_prix_objet = charger_fonction($type_objet, 'prix/', true)){
		$prix = $fonction_prix_objet($id_objet, $prix_ht);
	}
	
	// Enfin on passe dans un pipeline pour pouvoir ajouter taxes, ristournes ou autres modifications
	$prix = pipeline(
		'prix',
		array(
			'args' => array(
				'id_objet' => $id_objet,
				'type_objet' => $type_objet,
				'prix_ht' => $prix_ht,
				'prix' => $prix
			),
			'data' => $prix
		)
	);
	
	// Si on demande un arrondi, on le fait
	if ($arrondi)
		$prix = round($prix, $arrondi);
	
	// Et c'est fini
	return $prix;
}

?>
