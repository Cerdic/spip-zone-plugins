<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// On pourrait aussi passer par les pipelines "prix" et "prix_ht"

/*
 * Prix HT d'un produitdemo. 
 * On calcule le prix HT en fonction du prix TTC, qui correspond au champ "prix"
 * 
 * taxe = 0.2
 * coef = 100 / (100 + (taxe * 100))
 * HT  = TTC * coef;
 *
 * @param int $id_produitdemo
 *     L'identifiant du produitdemo
 * @param array $ligne
 *     Les champs du produitdemo
 * @return float
 *     Retourne le prix HT du produitdemo sinon 0
 */
function prix_produitdemo_ht_dist($id_produitdemo, $ligne){

	// on récupère le prix TTC
	if (isset($ligne['prix']))
		$prix_ttc = $ligne['prix'];
	elseif (intval($id_produitdemo)>0)
		$prix_ttc = sql_getfetsel('prix', table_objet_sql('produitdemo'), "id_produitdemo=".intval($id_produitdemo));
	else
		$prix_tcc = 0;

	// TVA = 20%
	$taxe = 0.2;

	// Calcul
	$coef = 100 / (100 + ($taxe*100));
	$prix_ht = $prix_ttc * $coef;

	return $prix_ht;
}

/*
 * Prix TTC d'un produitdemo. 
 * C'est le prix enregistré en base dans le champ "prix"
 *
 * @param int $id_produitdemo
 *     L'identifiant du produitdemo
 * @param array $ligne
 *     Les champs du produitdemo
 * @return float
 *     Retourne le prix TTC du produitdemo sinon 0
 */
function prix_produitdemo_dist($id_produitdemo, $ligne){

	if (isset($ligne['prix']))
		$prix_ttc = $ligne['prix'];
	elseif (intval($id_produitdemo)>0)
		$prix_ttc = sql_getfetsel('prix', table_objet_sql('produitdemo'), "id_produitdemo=".intval($id_produitdemo));
	else
		$prix_tcc = 0;

	return $prix_ttc;
}

?>
