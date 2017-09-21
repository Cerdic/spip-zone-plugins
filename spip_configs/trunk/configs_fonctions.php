<?php
/**
 * Fonctions utiles au plugin SPIP Variables
 *
 * @plugin     SPIP Variables
 * @copyright  2017
 * @author     tofulm
 * @licence    GNU/GPL
 * @package    SPIP\Configs\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


function balise_MA_CONFIG_dist($p){
	if (!$arg = interprete_argument_balise(1, $p)) {
		$arg = "''";
	}
	$_sinon = interprete_argument_balise(2, $p);
	$p->code = 'planning_ma_config(' . $arg . ',' .
		($_sinon && $_sinon != "''" ? $_sinon : 'null').')';
	return $p;
}


function planning_ma_config($nom, $js = ''){

	if (!$nom) {
		return false;
	}

	$where = array();

	// cas 1 : $nom ne contient pas de '/'
	if (strpos($nom, '/') === false) {
		$where = array(
			'nom_valeur='.sql_quote($nom)
		);
	} else {
	// cas 2 : si $nom de la forme prefixe/nom_valeur #MA_CONFIG{prefixe/nom_valeur}
	list($prefixe, $nom_valeur) = explode('/', $nom);
		$where = array(
			'nom_valeur='.sql_quote($nom_valeur),
			'prefixe='.sql_quote($prefixe)
		);
	}
	$res = sql_fetsel('valeur,defaut','spip_configs', $where);

	$rep =  ($res['valeur']) ? $res['valeur'] : $res['defaut'];

	// Si dans l'appelle #MA_CONFIG{ma_valeur,js}, il y a la présence d'un deuxième argument,
	// on renvoie une forme utilisable en variable js :
	// var ma_var = [(#MA_CONFIG{ma_valeur,js})]
	if ( !$js ) {
		return $rep;
	} else {
		if ( in_array($rep, array('oui' , 'true'))) {
			return 'true';
		} else if ( in_array($rep, array('non' , 'false'))) {
			return 'false';
		} else {
			return '"'.$rep.'"';
		}
	}
}
