<?

include_spip('inc/modele_pagination');
function balise_PAGINATION($p, $liste='true') {
	#nouvel_balise
	$modele=$p->param[0][1][0]->texte;
	
	
	if (gettype($modele)=="NULL"){
		$modele = "dist";
		;}
	
	
	
	
	
	
	#dist#
	$b = $p->nom_boucle ? $p->nom_boucle : $p->descr['id_mere'];
	// s'il n'y a pas de nom de boucle, on ne peut pas paginer
	if ($b === '') {
		erreur_squelette(
			_T('zbug_champ_hors_boucle',
				array('champ' => '#PAGINATION')
			), $p->id_boucle);
		$p->code = "''";
		return $p;
	}

	// s'il n'y a pas de total_parties, c'est qu'on se trouve
	// dans un boucle recurive ou qu'on a oublie le critere {pagination}
	if (!$p->boucles[$b]->total_parties) {
		erreur_squelette(
			_L('zbug_xx: #PAGINATION sans critere {pagination}
				ou employe dans une boucle recursive',
				array('champ' => '#PAGINATION')
			), $p->id_boucle);
		$p->code = "''";
		return $p;
	}

	$p->boucles[$b]->numrows = true;
	
	$p->code = "pagination((isset(\$Numrows['$b']['grand_total']) ?\$Numrows['$b']['grand_total'] : \$Numrows['$b']['total']), '$b', ". $p->boucles[$b]->total_parties. ", $liste,'$modele')";

	
	$p->interdire_scripts = false;
	return $p;
}


function pagination($total, $nom, $pas, $liste = true,$modele="dist") {
	
	return call_user_func("pagination_".$modele,$total, $nom, $pas, $liste);
}



?>