<?php
/**
 * Fonctions utiles au plugin Optionsproduits
 *
 * @plugin     Optionsproduits
 * @copyright  2017
 * @author     nicod_
 * @licence    GNU/GPL
 * @package    SPIP\Optionsproduits\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Calcul des prix des options, inspiré du plugin prix

// Un filtre pour obtenir le prix HT d'un objet
function prix_option_ht_objet($id_objet, $type_objet, $options = null) {
	$fonction = charger_fonction('ht', 'inc/prix_option');

	return $fonction($type_objet, $id_objet, $options, 0);
}

// La balise qui va avec le prix HT
function balise_PRIX_OPTION_HT_dist($p) {
	$b = $p->nom_boucle ? $p->nom_boucle : $p->descr['id_mere'];
	if (!$_type = interprete_argument_balise(1, $p)) {
		$_type = sql_quote($p->boucles[$b]->type_requete);
		$_id   = champ_sql($p->boucles[$b]->primary, $p);
	} else {
		$_id        = interprete_argument_balise(2, $p);
		$_options = interprete_argument_balise(3, $p);
	}
	$connect              = $p->boucles[$b]->sql_serveur;
	$p->code              = "prix_option_ht_objet(intval(" . $_id . ")," . $_type . "," . $_options . "," . sql_quote($connect) . ")";
	$p->interdire_scripts = false;

	return $p;
}

// Un filtre pour obtenir le prix TTC d'un objet
function prix_option_objet($id_objet, $type_objet, $options = null, $serveur = '') {
	$fonction = charger_fonction('prix_option', 'inc/');

	return $fonction($type_objet, $id_objet, $options, 0, $serveur);
}

// La balise qui va avec le prix TTC
function balise_PRIX_OPTION_dist($p) {
	$b = $p->nom_boucle ? $p->nom_boucle : $p->descr['id_mere'];
	if (!$_type = interprete_argument_balise(1, $p)) {
		$_type = sql_quote($p->boucles[$b]->type_requete);
		$_id   = champ_sql($p->boucles[$b]->primary, $p);
	} else {
		$_id        = interprete_argument_balise(2, $p);
		$_options = interprete_argument_balise(3, $p);
	}
	$connect              = $p->boucles[$b]->sql_serveur;
	$p->code              = "prix_option_objet(intval(" . $_id . ")," . $_type . "," . $_options . "," . sql_quote($connect) . ")";
	$p->interdire_scripts = false;

	return $p;
}