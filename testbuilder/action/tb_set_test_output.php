<?php
/*
 * Plugin TestBuilder
 * (c) 2010 Cedric MORIN Yterium
 * Distribue sous licence GPL
 *
 */

/**
 * Lit un fichier de test, rejoue le jeu d'essai
 * et initialisie la sortie attendue pour les essais qui le necessitent
 *
 * Le jeu d'essai est joue entierement dans l'ordre pour permettre des tests
 * reposant sur la memorisation et dont le resultat peut changer entre 2 appels
 * avec arguments identiques
 *
 * @param string $arg
 * 
 */
function action_tb_set_test_output_dist($arg=null){
	if (is_null($arg)){
		$arg = _request('arg');
		$hash = _request('hash');
		include_spip('inc/securiser_action');
		if (!verifier_cle_action($arg, $hash))
			die("nothing to do");
	}
	include_spip('inc/tb_lib');
	$arg = explode('|',$arg);
	$filename = array_shift($arg);
	$funcname = array_shift($arg);
	$filetest = array_shift($arg);

	// recuperer le jeu d'essai deja prepare par le formulaire
	$essais = tb_test_essais($funcname,$filetest);
	foreach($essais as $k=>$e){
		$args = $e;
		array_shift($args);
		$res = tb_try_essai($filename,$funcname,$args,$output_test);
		if (reset($e)==="??TBD??"){
			array_unshift($args, $res);
			$essais[$k] = $args;
		}
	}

	tb_filter_essais($essais);
	tb_test_essais($funcname,$filetest,$essais);
	var_export($essais);
}

?>