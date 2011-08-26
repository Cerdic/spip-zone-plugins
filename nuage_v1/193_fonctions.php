<?php

function nuage($id_mot, $titre = '', $url = '', $poids = -1, $expose = array()){
	return filtre_nuage_dist($id_mot, $titre, $url, $poids, $expose);
}

function filtre_push($array, $val) {
	if($array == '' OR !array_push($array, $val)) return '';
	return $array;	
}

function filtre_find($array, $val) {
	    return (is_array($array) AND in_array($val, $array));
}


function chercher_filtre($fonc, $default=NULL) {
		foreach (
		array('filtre_'.$fonc, 'filtre_'.$fonc.'_dist', $fonc) as $f)
			if (function_exists($f)
			OR (preg_match("/^(\w*)::(\w*)$/", $f, $regs)                            
				AND is_callable(array($regs[1], $regs[2]))
			)) {
				return $f;
			}
		return $default;
}

function balise_FOREACH($p) {
	$_tableau = interprete_argument_balise(1,$p);
	$_tableau = str_replace("'", "", strtoupper($_tableau));
	$_tableau = sinon($_tableau, 'ENV');
	$f = 'balise_'.$_tableau;
	$balise = function_exists($f) ? $f : (function_exists($g = $f.'_dist') ? $g : '');

	if($balise) {
		$_modele = interprete_argument_balise(2,$p);
		$_modele = str_replace("'", "", strtolower($_modele));
		$__modele = 'foreach_'.strtolower($_tableau);
		$_modele = (!$_modele AND find_in_path('modeles/'.$__modele.'.html')) ?
			$__modele : 
			($_modele ? $_modele : 'foreach');

		$p->param = @array_shift(@array_shift($p->param));
		$p = $balise($p);
		$filtre = chercher_filtre('foreach');
		$p->code = $filtre . "(unserialize(" . $p->code . "), '" . $_modele . "')";
	}
	//On a pas trouve la balise correspondant au tableau a traiter
	else {
		erreur_squelette(
			_L(/*zbug*/'erreur #FOREACH: la balise #'.$_tableau.' n\'existe pas'),
			$p->id_boucle
		);
		$p->code = "''";
	}
	return $p;
}

function filtre_foreach($balise_deserializee, $modele = 'foreach') {
	$texte = '';
	if(is_array($balise_deserializee))
		foreach($balise_deserializee as $k => $v)
			$texte .= recuperer_fond(
				'modeles/'.$modele,
				array_merge(array('cle' => $k), (is_array($v) ? $v : array('valeur' => $v)))
			);
	return $texte;
}

?>