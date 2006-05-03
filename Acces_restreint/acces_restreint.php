<?php
include_spip('inc/acces_restreint');

//
// <BOUCLE(ARTICLES)>
//
function boucle_ARTICLES($id_boucle, &$boucles) {
	//if ($connect_statut == '0minirezo') return boucle_ARTICLES_dist($id_boucle, $boucles);

	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;

	$t = $boucle->id_table . '.' . $boucle->primary;
	if (!in_array($t, $boucles[$id_boucle]->select))
	  $boucle->select[]= $t; # pour postgres, neuneu ici

	$boucle->hash = '
	// ACCES RESTREINT
	$acces_where = AccesRestreint_articles_accessibles_where("'.$t.'");
	' . $boucle->hash ;

	// et le filtrage d'acces filtre !
	$boucle->where[] = '$acces_where';

	return boucle_ARTICLES_dist($id_boucle, $boucles);
}

//
// <BOUCLE(BREVES)>
//
function boucle_BREVES($id_boucle, &$boucles) {
	//if ($connect_statut == '0minirezo') return boucle_BREVES_dist($id_boucle, $boucles);

	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;

	$t = $boucle->id_table . '.' . $boucle->primary;
	if (!in_array($t, $boucles[$id_boucle]->select))
	  $boucle->select[]= $t; # pour postgres, neuneu ici

	$boucle->hash = '
	// ACCES RESTREINT
	$acces_where = AccesRestreint_breves_accessibles_where("'.$t.'");
	' . $boucle->hash ;

	// et le filtrage d'acces filtre !
	$boucle->where[] = '$acces_where';

	return boucle_BREVES_dist($id_boucle, $boucles);
}

//
// <BOUCLE(FORUMS)>
//
function boucle_FORUMS($id_boucle, &$boucles) {
	//if ($connect_statut == '0minirezo') return boucle_FORUMS_dist($id_boucle, $boucles);

	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$t = $boucle->id_table . '.' . $boucle->primary;
	if (!in_array($t, $boucles[$id_boucle]->select))
	  $boucle->select[]= $t; # pour postgres, neuneu ici

	$boucle->hash = '
	// ACCES RESTREINT
	$acces_where = AccesRestreint_forum_accessibles_where("'.$t.'");
	' . $boucle->hash ;

	// et le filtrage d'acces filtre !
	$boucle->where[] = '$acces_where';

	return boucle_FORUMS_dist($id_boucle, $boucles);
}

//
// <BOUCLE(SIGNATURES)>
//
function boucle_SIGNATURES($id_boucle, &$boucles) {
	//if ($connect_statut == '0minirezo') return boucle_SIGNATURES_dist($id_boucle, $boucles);

	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$t = $boucle->id_table . '.' . $boucle->primary;
	if (!in_array($t, $boucles[$id_boucle]->select))
	  $boucle->select[]= $t; # pour postgres, neuneu ici

	$boucle->hash = '
	// ACCES RESTREINT
	$acces_where = AccesRestreint_signatures_accessibles_where("'.$t.'");
	' . $boucle->hash ;

	// et le filtrage d'acces filtre !
	$boucle->where[] = '$acces_where';

	return boucle_SIGNATURES_dist($id_boucle, $boucles);
}

//
// <BOUCLE(DOCUMENTS)>
//
function boucle_DOCUMENTS($id_boucle, &$boucles) {
	//if ($connect_statut == '0minirezo') return boucle_DOCUMENTS_dist($id_boucle, $boucles);

	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$t = $boucle->id_table . '.' . $boucle->primary;
	if (!in_array($t, $boucles[$id_boucle]->select))
	  $boucle->select[]= $t; # pour postgres, neuneu ici

	$boucle->hash = '
	// ACCES RESTREINT
	$acces_where = AccesRestreint_documents_accessibles_where("'.$t.'");
	' . $boucle->hash ;

	// et le filtrage d'acces filtre !
	$boucle->where[] = '$acces_where';

	return boucle_DOCUMENTS_dist($id_boucle, $boucles);
}

//
// <BOUCLE(RUBRIQUES)>
//
function boucle_RUBRIQUES($id_boucle, &$boucles) {
	//if ($connect_statut == '0minirezo') return boucle_RUBRIQUES_dist($id_boucle, $boucles);

	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;

	$t = $boucle->id_table . '.' . $boucle->primary;
	if (!in_array($t, $boucles[$id_boucle]->select))
	  $boucle->select[]= $t; # pour postgres, neuneu ici
	$boucle->hash = '
	// ACCES RESTREINT
	$acces_where = AccesRestreint_rubriques_accessibles_where("'.$t.'");
	' . $boucle->hash ;

	// et le filtrage d'acces filtre !
	$boucle->where[] = '$acces_where';

	return boucle_RUBRIQUES_dist($id_boucle, $boucles);
}

//
// <BOUCLE(HIERARCHIE)>
//
function boucle_HIERARCHIE($id_boucle, &$boucles) {
	//if ($connect_statut == '0minirezo') return boucle_HIERARCHIE_dist($id_boucle, $boucles);

	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;

	$t = $boucle->id_table . '.' . $boucle->primary;
	if (!in_array($t, $boucles[$id_boucle]->select))
	  $boucle->select[]= $t; # pour postgres, neuneu ici

	$boucle->hash = '
	// ACCES RESTREINT
	$acces_where = AccesRestreint_rubriques_accessibles_where("'.$t.'");
	' . $boucle->hash ;

	// et le filtrage d'acces filtre !
	$boucle->where[] = '$acces_where';

	return boucle_HIERARCHIE_dist($id_boucle, $boucles);
}

//
// <BOUCLE(SYNDICATION)>
//
function boucle_SYNDICATION($id_boucle, &$boucles) {
	//if ($connect_statut == '0minirezo') return boucle_SYNDICATION_dist($id_boucle, $boucles);

	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$t = $boucle->id_table . '.' . $boucle->primary;
	if (!in_array($t, $boucles[$id_boucle]->select))
	  $boucle->select[]= $t; # pour postgres, neuneu ici

	$boucle->hash = '
	// ACCES RESTREINT
	$acces_where = AccesRestreint_syndic_accessibles_where("'.$t.'");
	' . $boucle->hash ;

	// et le filtrage d'acces filtre !
	$boucle->where[] = '$acces_where';

	return boucle_SYNDICATION_dist($id_boucle, $boucles);
}

//
// <BOUCLE(SYNDIC_ARTICLES)>
//
function boucle_SYNDIC_ARTICLES($id_boucle, &$boucles) {
	//if ($connect_statut == '0minirezo') return boucle_SYNDIC_ARTICLES_dist($id_boucle, $boucles);

	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$t = $boucle->id_table . '.' . $boucle->primary;
	if (!in_array($t, $boucles[$id_boucle]->select))
	  $boucle->select[]= $t; # pour postgres, neuneu ici

	$boucle->hash = '
	// ACCES RESTREINT
	$acces_where = AccesRestreint_syndic_articles_accessibles_where("'.$t.'");
	' . $boucle->hash ;

	// et le filtrage d'acces filtre !
	$boucle->where[] = '$acces_where';

	return boucle_SYNDIC_ARTICLES_dist($id_boucle, $boucles);
}

?>