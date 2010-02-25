<?php
/*
 * Plugin xxx
 * (c) 2009 xxx
 * Distribue sous licence GPL
 *
 */

include_spip('inc/tb_lib');
function formulaires_editer_test_unit_charger_dist($filename,$funcname){
	$valeurs = array(
		'essais'=>array(),
		'_args'=>array(),
		'args'=>array(),
		'_filename'=>$filename,
		'_funcname'=>$funcname
	);

	// recuperer les tests precedents si possible
	if ($filetest=tb_hastest($funcname)){
		$valeurs['_essais'] = tb_test_essais($funcname,$filetest);
		$valeurs['_hidden'] = "<input type='hidden' name='ctrl_essais' value='".md5(serialize($valeurs['essais']))."' />";
	}

	$funcs = tb_liste_fonctions($filename);
	// la liste des arguments de la fonction
	$valeurs['_args'] = reset($funcs[$funcname]);
	// les valeurs saisies pour chaque argument
	$valeurs['args'] = array();

	return $valeurs;
}

function formulaires_editer_test_unit_verifier_dist($filename,$funcname){
	$erreurs = array();
	$args = _request('args');
	// enlever les arguments vide de fin (facultatifs)
	while (count($args) AND !strlen(end($args)))
		array_pop($args);
	// verifier qu'un args n'est pas vide au milieu
	foreach($args as $k=>$v)
		if (!strlen($v))
			$erreurs["args_$k"]=_L("Cet argument ne peut &ecirc;tre vide car il est suivi par d'autres");
	set_request('args',$args);
	#var_dump($args);

	// demande de test sur un jeu d'essai
	if (!count($erreurs) AND _request('tester')){
		tb_try_essai($filename,$funcname,$args,$output_test);
		$erreurs['message_erreur'] = $output_test;
	}

	return $erreurs;
}

function formulaires_editer_test_unit_traiter_dist($filename,$funcname){
	if (!$filetest=tb_hastest($funcname))
		$filetest = tb_generate_new_blank_test($filename,$funcname);
	$message_ok = "";

	$essais = tb_test_essais($funcname,$filetest);
	if (_request('enregistrer')){
		$args = _request('args');
		#var_dump($args);
		$res = tb_try_essai($filename,$funcname,$args,$output_test);

		$essai = eval("return array(".var_export($res,true).", ".implode(', ',$args).");");
		$essais[] = $essai;
		tb_test_essais($funcname,$filetest,$essais);
		set_request('args',array());
		$message_ok = _L("Test ajout&eacute; : ").$output_test;
	}
	else {
		$save = false;
		foreach($essais as $k=>$t)
			if (_request("del_$k")){
				unset($essais[$k]);
				$save = true;
			}
		if ($save){
			tb_test_essais($funcname,$filetest,$essais);
			$message_ok = _L("Test supprim&eacute;");
		}
	}
	return array('message_ok'=>$message_ok,'fichier_test'=>$filetest,'editable'=>true);
	
}

function tb_export($var){
	return var_export($var,true);
}
function tb_affiche_essais($essais,$funcname){
	$output = "";
	if (is_array($essais) AND count($essais)){
		foreach($essais as $k=>$essai){
			$res = array_shift($essai);
			$output .= "<li class='item'><input type='submit' class='submit' name='del_$k' value='X' /> <code>$funcname(".implode(',',array_map('tb_export',$essai)).")=".tb_export($res)."</code></li>";
		}
		$output = "<ul class='liste_items'>$output</ul>";
	}
	//var_dump($output);
	return $output;
}
?>