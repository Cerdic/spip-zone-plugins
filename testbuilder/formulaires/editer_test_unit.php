<?php
/*
 * Plugin TestBuilder
 * (c) 2010 Cedric MORIN Yterium
 * Distribue sous licence GPL
 *
 */

include_spip('inc/tb_lib');
function formulaires_editer_test_unit_charger_dist($filename,$funcname){
	$valeurs = array(
		'essais'=>array(),
		'_args'=>array(),
		'args'=>array(),
		'resultat'=>'',
		'_filename'=>$filename,
		'_funcname'=>$funcname,
		'_filetest'=>'',
	);

	$funcs = tb_liste_fonctions($filename);
	// la liste des arguments de la fonction
	$valeurs['_args'] = reset($funcs[$funcname]);
	// les valeurs saisies pour chaque argument
	$valeurs['args'] = array();
	// recuperer les tests precedents si possible
	if ($filetest=tb_hastest($funcname,true)){
		$valeurs['_essais'] = tb_test_essais($funcname,$filetest);
		$valeurs['_hidden'] = "<input type='hidden' name='ctrl_essais' value='".md5(serialize($valeurs['essais']))."' />";
		$valeurs['_filetest'] = $filetest;
	}
	// regarder si un demande a modifier un jeu d'essai
	$modif = -1;
	if (count($valeurs['_essais'])){
		foreach($valeurs['_essais'] as $k=>$t)
			if (_request("modif_$k")){
				set_request('args'); // effacer la saisie
				set_request('resultat'); // effacer la saisie
				$valeurs['args'] = $t;
				$valeurs['resultat'] = var_export(array_shift($valeurs['args']),'true');
				$valeurs['args'] = array_map('tb_export',$valeurs['args']);
				$modif = $k;
				continue;
			}
	}
	if ($modif>=0 OR $modif=_request('modif_essai')){
		$valeurs['_hidden'] .= "<input type='hidden' name='modif_essai' value='$modif' />";
		$valeurs['_modif_essai'] = $modif;
	}

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
			$erreurs["args_$k"]=_T("tb:erreur_argument_vide");
	set_request('args',$args);
	#var_dump($args);

	if (_request('combi')){
		if (!count($args))
			$erreurs['message_erreur'] = _T("tb:erreur_test_combinatoire_types_requis");
		else {
			#$tb_essais_type = charger_fonction('tb_essais_type','inc');
			#foreach($args as $k=>$type)
			#	if (!count($tb_essais_type($type)))
			#		$erreurs["args_$k"]=_T("tb:erreur_pseudo_type_inconnu");
		}
		if (_request('resultat')){
			$erreurs['resultat'] = _T("tb:erreur_test_combinatoire_resultat_ignore");
		}
	}
	// demande de test sur un jeu d'essai
	if (!count($erreurs) AND _request('tester')){
		$args = eval("return array(".implode(', ',$args).");");
		$test = tb_try_essai($filename,$funcname,$args,$output_test);
		if ($res=_request('resultat') AND $test===eval("return $res;"))
			$erreurs['message_ok'] = $output_test;
		else
			$erreurs['message_erreur'] = $output_test;
		if (!$res)
			set_request('resultat',var_export($test,true));
	}

	return $erreurs;
}

function formulaires_editer_test_unit_traiter_dist($filename,$funcname){
	if (!$filetest=tb_hastest($funcname))
		$filetest = tb_generate_new_blank_test($filename,$funcname);
	$message_ok = "";
	$message_echec = "";

	$essais = tb_test_essais($funcname,$filetest);
	if (_request('enregistrer')){
		$args = _request('args');
		$res = _request('resultat');
		array_unshift($args,$res?$res:"'??TBD??'");
		$essai = eval("return array(".implode(', ',$args).");");

		if ($essai){
			if (!is_null($m=_request('modif_essai'))){
				$essais[$m] = $essai;
				set_request('modif_essai');
			} else
				$essais[] = $essai;
			tb_test_essais($funcname,$filetest,$essais);
			if (!$res)
				tb_refresh_test($filename,$funcname,$filetest);
		}
		set_request('args');
		set_request('resultat');
		$message_ok = _T('tb:ok_test_ajoute');
	}
	elseif(_request('combi')){
		$args = _request('args');
		$argss = tb_essai_combinatoire($args);
		foreach($argss as $args){
			array_unshift($args, "??TBD??");
			$essais[] = $args;
		}
		tb_test_essais($funcname,$filetest,$essais);
		tb_refresh_test($filename,$funcname,$filetest);
		set_request('args',array());
		$message_ok = _T("tb:ok_n_tests_combi_crees",array('nb'=>count($argss)));
	}
	elseif(_request('recalculer_tous')){
		foreach($essais as $k=>$e){
			array_shift($e);
			array_unshift($e, '??TBD??');
			$essais[$k] = $e;
		}
		tb_test_essais($funcname,$filetest,$essais);
		if (tb_refresh_test($filename,$funcname,$filetest)){
			set_request('args');
			set_request('resultat');
			$message_ok = _T('tb:ok_test_recalcules');
		}
	  else
		  $message_echec = _T('tb:echec');
	}
	elseif(_request('supprimer_tous')){
		tb_test_essais($funcname,$filetest,array());
		set_request('args',array());
		$message_ok = _T('tb:ok_tests_supprimes');
	}
	else {
		$save = false;
		foreach($essais as $k=>$t)
			if (_request("del_$k")){
				unset($essais[$k]);
				$save = true;
				set_request('modif_essai','');
			}
		if ($save){
			tb_test_essais($funcname,$filetest,$essais);
			$message_ok = _T("tb:ok_test_supprime");
		}
	}
	return
		$message_echec ?
			array('message_erreur'=>$message_echec,'fichier_test'=>$filetest,'editable'=>true)
			: array('message_ok'=>$message_ok,'fichier_test'=>$filetest,'editable'=>true);
	
}

function tb_affiche_essais($essais,$funcname,$expose=null){
	$output = "";
	if (is_array($essais) AND count($essais)){
		foreach($essais as $k=>$essai){
			$res = array_shift($essai);
			$affiche = "$funcname(".implode(',',array_map('tb_export',$essai)).") = ".tb_export($res);
			$affiche = str_replace(array('&','<','>'),array('&amp;','&lt;','&gt;'),$affiche);
			$on = (!is_null($expose) AND $expose==$k)?' on':'';
			$output .= "<li class='item$on'>"
			  ."<input type='submit' class='submit del' name='del_$k' value='X' />"
			  ." <code>$affiche</code>"
			  ." <input type='submit' class='submit modif' name='modif_$k' value='Modif' />"
			  ."</li>";
		}
		$output = "<h3>"
		  . singulier_ou_pluriel(count($essais), 'tb:un_essai', 'tb:nb_essais')
			. "</h3><ol start='0' class='liste-items essais'>$output</ul>";
	}
	//var_dump($output);
	return $output;
}
?>