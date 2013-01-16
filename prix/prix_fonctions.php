<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// Un filtre pour obtenir le prix HT d'un objet
function prix_ht_objet($id_objet, $type_objet){
	$fonction = charger_fonction('ht', 'inc/prix');
	return $fonction($type_objet, $id_objet);
}

// La balise qui va avec le prix HT
function balise_PRIX_HT_dist($p) {
	if (!$_type = interprete_argument_balise(1,$p)){
		$_type = sql_quote($p->type_requete);
		$_id = champ_sql($p->boucles[$p->id_boucle]->primary,$p);
	}
	else
		$_id = interprete_argument_balise(2,$p);
	$connect = $p->boucles[$p->id_boucle]->sql_serveur;
	$p->code = "prix_ht_objet(intval(".$_id."),".$_type.','.sql_quote($connect).")";
	$p->interdire_scripts = false;
	return $p;
}

// Un filtre pour obtenir le prix TTC d'un objet
function prix_objet($id_objet, $type_objet){
	$fonction = charger_fonction('prix', 'inc/');
	return $fonction($type_objet, $id_objet);
}

// La balise qui va avec le prix TTC
function balise_PRIX_dist($p) {
	if (!$_type = interprete_argument_balise(1,$p)){
		$_type = _q($p->type_requete);
		$_id = champ_sql($p->boucles[$p->id_boucle]->primary,$p);
	}
	else
		$_id = interprete_argument_balise(2,$p);
	$connect = $p->boucles[$p->id_boucle]->sql_serveur;
	$p->code = "prix_objet(intval(".$_id."),".$_type.','.sql_quote($connect).")";
	$p->interdire_scripts = false;
	return $p;
}

/*
 * Formater un nombre pour l'afficher comme un prix avec une devise
 *
 * @param float $prix Valeur du prix à formater
 * @return string Retourne une chaine contenant le prix formaté avec une devise (par défaut l'euro)
 */
 
function prix_formater($prix){ 
	$fonction_formater = charger_fonction('prix_formater', 'filtres/'); 
	return $fonction_formater($prix); 
}

/*
 *  Déport de la fonction pour pouvoir au besoin la surcharger avec
 *  function filtres_prix_formater
 *
 */
function filtres_prix_formater_dist($prix){

	// Pouvoir débrayer la devise de référence
	if (! defined('PRIX_DEVISE')) {
	  define('PRIX_DEVISE','fr_FR.utf8');
	}
	
	setlocale(LC_MONETARY, PRIX_DEVISE); 
	
	if(function_exists(money_format)) {
		$prix = floatval($prix);
		$prix = money_format('%i', $prix); 
		// Afficher la devise € si celle ci n'est pas remontée par la fonction money
		if ((strlen(money_format('%#1.0n', 0)) < 2) || ((money_format('%#1.0n', 0) == 0) AND (strlen(money_format('%#1.0n', 0)) == 3)))
		  $prix .= '&nbsp;&euro;'; 
	} else {
		 $prix .= '&nbsp;&euro;'; 
	}
	
	
	return $prix;
}

?>
