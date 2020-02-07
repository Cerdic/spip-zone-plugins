<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * La balise qui va avec le prix TTC
 *
 * @param Object $p
 * @return Float
 */
function balise_PRIX_dist($p) {
	$b = $p->nom_boucle ? $p->nom_boucle : $p->descr['id_mere'];
	if (!$_type = interprete_argument_balise(1,$p)){
		$_type = sql_quote($p->boucles[$b]->type_requete);
		$_id = champ_sql($p->boucles[$b]->primary,$p);
	}
	else
		$_id = interprete_argument_balise(2,$p);
	$connect = $p->boucles[$b]->sql_serveur;
	$p->code = "prix_objet(intval(".$_id."),".$_type.','.sql_quote($connect).")";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * La balise qui va avec le prix HT
 *
 * @param Object $p
 * @return Float
 */
function balise_PRIX_HT_dist($p) {
	$b = $p->nom_boucle ? $p->nom_boucle : $p->descr['id_mere'];
	if (!$_type = interprete_argument_balise(1,$p)){
		$_type = sql_quote($p->boucles[$b]->type_requete);
		$_id = champ_sql($p->boucles[$b]->primary,$p);
	}
	else
		$_id = interprete_argument_balise(2,$p);
	$connect = $p->boucles[$b]->sql_serveur;
	$p->code = "prix_ht_objet(intval(".$_id."),".$_type.','.sql_quote($connect).")";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * Obtenir le prix TTC d'un objet
 *
 * @param Integer $id_objet
 * @param String $type_objet
 * @return Float
 */
function prix_objet($id_objet, $objet, $serveur = '') {
	$fonction = charger_fonction('prix', 'inc/');
	return $fonction($objet, $id_objet, 2, $serveur);
}

/**
 * Obtenir le prix HT d'un objet
 *
 * @param Integer $id_objet
 * @param String $type_objet
 * @return Float
 */
function prix_ht_objet($id_objet, $objet) {
	$fonction = charger_fonction('ht', 'inc/prix');
	return $fonction($objet, $id_objet);
}

/**
 * Compatibilité avec la balise #INFO_PRIX
 *
 * @uses prix_objet
 *
 * @param Integer $id_objet
 * @param String $type_objet
 * @param Array $ligne
 * @return Float
 */
function generer_prix_entite($id_objet, $objet, $ligne) {
	return prix_objet($id_objet, $objet);
}

/**
 * Compatibilité avec la balise #INFO_PRIX_HT
 *
 * @uses prix_ht_objet
 *
 * @param Integer $id_objet
 * @param String $type_objet
 * @param Array $ligne
 * @return Float
 */
function generer_prix_ht_entite($id_objet, $objet, $ligne) {
	return prix_ht_objet($id_objet, $objet);
}

/**
 * Formater un nombre pour l'afficher comme un prix avec une devise
 *
 * @param Float $prix
 *     Valeur du prix à formater
 * @return String
 *     Retourne une chaine contenant le prix formaté avec une devise (par défaut l'euro)
 */
 
function prix_formater($prix) { 
	$fonction_formater = charger_fonction('prix_formater', 'filtres/'); 
	return $fonction_formater($prix); 
}

/**
 *  Déport de la fonction pour pouvoir au besoin la surcharger avec
 *  function filtres_prix_formater
 */
function filtres_prix_formater_dist($prix) {

	// Pouvoir débrayer la devise de référence
	if (! defined('PRIX_DEVISE')) {
	  define('PRIX_DEVISE','fr_FR.utf8');
	}
	
	// Pouvoir débrayer l'écriture de la devise par défaut
	if (! defined('DEVISE_DEFAUT')) {
	  define('DEVISE_DEFAUT','&nbsp;&euro;');
	}
	
	setlocale(LC_MONETARY, PRIX_DEVISE); 
	
	if(function_exists('money_format')) {
		$prix = floatval($prix);
		$prix = money_format('%i', $prix); 
		// Afficher la devise € si celle ci n'est pas remontée par la fonction money
		if ((strlen(money_format('%#1.0n', 0)) < 2) || ((money_format('%#1.0n', 0) == 0) AND (strlen(money_format('%#1.0n', 0)) == 3)))
		  $prix .= DEVISE_DEFAUT; 
	} else {
		 $prix .= DEVISE_DEFAUT; 
	}
	
	return $prix;
}
