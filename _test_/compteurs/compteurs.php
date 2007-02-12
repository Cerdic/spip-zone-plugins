<?php

  /* balise pour incrémenter le compteur de "donnee", cle
   * "id", catégorie "categ" de "montant"
   */
function balise_COMPTEUR_PLUS($p) {
	if ($p->param && !$p->param[0][0] && $p->param[0][1]) {
		$param= $p->param[0];

		$donnee=  calculer_liste($param[1],
			$p->descr, $p->boucles, $p->id_boucle);
		$id=  calculer_liste($param[2],
			$p->descr, $p->boucles, $p->id_boucle);
		if($param[3]) {
			$categ=  calculer_liste($param[3],
									$p->descr, $p->boucles, $p->id_boucle);
		} else {
			$categ= "''";
		}
		if($param[4]) {
			$montant= calculer_liste($param[4],
									 $p->descr, $p->boucles, $p->id_boucle);
		} else {
			$montant= "'1'";
		}
	} else {
		erreur_squelette('COMPTEUR_PLUS necessite au moins 3 parametres');
		return $p;
	}

	$p->code = "compteur_plus($donnee, $id, $categ, $montant)";
	////"((\$row=spip_abstract_fetsel('total, nb', 'eureka_compteurs', array(array('=', 'type', '\''.$donnee.'\''), array('=', 'id', '\''.$id.'\''), array('=', 'categ', '\''.$categ.'\''))))!==false?spip_query(\"update eureka_compteurs set total=total+\".".$montant.".\", nb=nb+1 where type='\".".$donnee.".\"' and id='\".".$id.".\"' and categ='\".".$categ.".\"'\"):spip_query(\"insert into eureka_compteurs values ('\".".$donnee.".\"','\".".$id.".\"','\".".$categ.".\"','\".".$montant.".\"',1)\"))";
	$p->interdire_scripts = false;
	return $p;
}

function balise_COMPTEUR($p) {
	if ($p->param && !$p->param[0][0] && $p->param[0][1]) {
		$param= $p->param[0];

		$where=array();

		$donnee=  calculer_liste($param[1],
			$p->descr, $p->boucles, $p->id_boucle);
		$where[]="array('=', 'type', '\''.$donnee.'\'')";

		if($param[2]) {
			$id=  calculer_liste($param[2],
								 $p->descr, $p->boucles, $p->id_boucle);
			$where[]= "array('=', 'id', '\''.$id.'\'')";
		}
		if($param[3]) {
			$categ=  calculer_liste($param[3],
									$p->descr, $p->boucles, $p->id_boucle);
			$where[]= "array('=', 'categ', '\''.$categ.'\'')";
		}

		$p->code= "((\$row= spip_abstract_fetsel('sum(total) as total, sum(nb) as nb', 'eureka_compteurs', array(".join(',', $where).")))?\$row:array(0,0))";
		$p->interdire_scripts = false;
		return $p;
	} else {
		erreur_squelette('COMPTEUR necessite au moins 3 parametres');
		return $p;
	}
}

function compteur_total($row) {
	return $row['total'];
}

function compteur_nb($row) {
	return $row['nb'];
}

function compteur_moyenne($row) {
	$m= ($row['nb']==0)?0:($row['total']/$row['nb']);
	return $m;
}

// ramène $v (connu entre $min et $max) a une valeur entre 0 et $w
// ce qui permet de faire une jauge ou un histogramme
function barre($v, $min, $max, $w, $reste=null) {
	if($reste) {
		return $w - (int)(($v-$min)*$w/($max-$min));
	} else {
		return (int)(($v-$min)*$w/($max-$min));
	}
}
?>
