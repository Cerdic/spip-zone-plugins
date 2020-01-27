<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Liste les taxes
 *
 * @param string $objet
 *   Le type de l'objet
 * @param int $id_objet
 *   L'identifiant de l'objet
 * @return array
 *   Retourne un tableau détaillant les taxes appliqués à ce contenu. Chaque ligne est une taxe avec
 *   - montant
 *   - label
 *   - taux
 * 		
 */
function inc_taxes_dist($objet, $id_objet, $options=array()) {
	$taxes = array();
	$fonction_prix_ht = charger_fonction('ht', 'inc/prix');
	$prix_ht = $fonction_prix_ht($objet, $id_objet, $options);
	
	// On cherche s'il existe une personnalisation pour les taxes : taxes_<objet>() dans taxes/<objet>.php
	if ($fonction_taxes_objet = charger_fonction($objet, 'taxes/', true)){
		$taxes = $fonction_taxes_objet($id_objet, $prix_ht, $options);
	}
	// Sinon on va d'office gérer le cas simple où il y a un champ "taxe" unique dans l'objet
	elseif (
		include_spip('inc/filtres')
		and include_spip('base/objets')
		and $champs = objet_info($objet, 'field')
		and isset($champs['taxe'])
		and $table_sql = table_objet_sql($objet)
		and $cle_objet = id_table_objet($objet)
		and $id_objet = intval($id_objet)
		and $taxe = floatval(sql_getfetsel('taxe', $table_sql, $cle_objet . '=' . $id_objet))
		and $taxe > 0
	) {
		$taxes[] = array(
			'taux' => $taxe,
			'label' => $taxe*100 . '%',
			'montant' => $prix_ht * $taxe,
		);
	}
	
	// Enfin on passe dans un pipeline pour pouvoir ajouter ou modifier
	$taxes = pipeline(
		'taxes',
		array(
			'args' => array(
				'objet' => $type_objet,
				'id_objet' => $id_objet,
			),
			'data' => $taxes
		)
	);
	
	return $taxes;
}
