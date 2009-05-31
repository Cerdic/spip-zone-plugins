<?php
function balise_PAGINATION_FLICKR_dist($p, $liste='true') {
	$b = $p->nom_boucle ? $p->nom_boucle : $p->descr['id_mere'];

	// s'il n'y a pas de nom de boucle, on ne peut pas paginer
	if ($b === '') {
		erreur_squelette(
			_T('zbug_champ_hors_boucle',
				array('champ' => '#PAGINATION_FLICKR')
			), $p->id_boucle);
		$p->code = "''";
		return $p;
	}

	// s'il n'y a pas de total_parties, c'est qu'on se trouve
	// dans un boucle recurive ou qu'on a oublie le critere {pagination}
	if (!$p->boucles[$b]->total_parties) {
		erreur_squelette(
			_L('zbug_xx: #PAGINATION_FLICKR sans critere {pagination}
				ou employe dans une boucle recursive',
				array('champ' => '#PAGINATION_FLICKR')
			), $p->id_boucle);
		$p->code = "''";
		return $p;
	}
	$__modele = "";
	if ($p->param && !$p->param[0][0]) {
		$__modele = ",". calculer_liste($p->param[0][1],
					$p->descr,
					$p->boucles,
					$p->id_boucle);
	}
	

	$p->boucles[$b]->numrows = true;

	$p->code = "calcul_pagination(
	(isset(\$Numrows['$b']['fpipr_grand_total']) ?
		\$Numrows['$b']['fpipr_grand_total'] : \$Numrows['$b']['total']
	), '$b', "
	. $p->boucles[$b]->total_parties
	. ", $liste $__modele)";

	$p->interdire_scripts = false;
	return $p;
}
?>
