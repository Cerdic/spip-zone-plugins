<?php 

// #CALENDRIER
// http://www.spip.net/fr_articleXXXX.html
// http://doc.spip.org/@balise_CALENDRIER_dist
function balise_CALENDRIER_dist($p, $bloc_cal='true') {
	$b = $p->nom_boucle ? $p->nom_boucle : $p->descr['id_mere'];
	$boucle = $p->boucles[$b];
	$_type = $boucle->type_requete;

	// s'il n'y a pas de nom de boucle, on ne peut pas paginer
	if ($b === '') {
		erreur_squelette(
			_T('zbug_champ_hors_boucle',
				array('champ' => '#CALENDRIER')
			), $p->id_boucle);
		$p->code = "''";
		return $p;
	}

	// s'il n'y a pas de plage, c'est qu'on se trouve
	// dans un boucle recurive ou qu'on a oublie le critere {calendrier}
	if (!$p->boucles[$b]->modificateur['plage']) {
		erreur_squelette(
			_L('zbug_xx: #CALENDRIER sans critere {calendrier}
				ou employe dans une boucle recursive',
				array('champ' => '#CALENDRIER')
			), $p->id_boucle);
		$p->code = "''";
		return $p;
	}
	$_modele = interprete_argument_balise(1,$p);
	if(!$_modele) $_modele = "'$_type'";
	$_periode = interprete_argument_balise(2,$p);
	if(!$_periode) $_periode = "'mois'";

	$p->code = "calcul_calendrier(".$p->boucles[$b]->modificateur['plage'].", '$b',
	$bloc_cal,
	$_modele.'_'.$_periode)";

	return $p;
}

// N'afficher que l'ancre du calendrier (au-dessus, par exemple, alors
// qu'on mettra le calendrier en-dessous de la liste paginee)
// http://doc.spip.org/@balise_ANCRE_CALENDRIER_dist
function balise_ANCRE_CALENDRIER_dist($p) {
	$p = balise_CALENDRIER_dist($p, $bloc_cal='false');
	return $p;
}

?>