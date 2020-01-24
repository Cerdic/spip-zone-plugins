<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

include_spip('base/abstract_sql');
include_spip('action/editer_article');

// Récupère le tableau des $t[k]['nom_champ']
// Il y a probablement plus simple
// et plus idiomatique que ce "truc"...

function dim2to1($t, $nom_champ){
  $u = array();
  foreach ($t as $x) {
    $u[] = $x[$nom_champ];
  }
  return $u;
}


function action_balayer_dist(){
  $securiser_action = charger_fonction('securiser_action', 'inc');
  $arg = $securiser_action();

	// Détermine les rubriques directement protégées
	$rub_protegees = sql_select('id_objet', 'spip_balai', "objet = 'rubrique'");
  $tableau_rub_protegees = dim2to1(sql_fetch_all($rub_protegees), 'id_objet');

	// Puis leurs filles
  $rub_parent = $tableau_rub_protegees;
  $c = count($rub_parent);
  while ($c >0){
    $rub_parent = sql_select('id_rubrique', 'spip_rubriques', sql_in('id_parent', $rub_parent));
    $rub_parent = dim2to1(sql_fetch_all($rub_parent), 'id_rubrique');
    $tableau_rub_protegees = array_merge($tableau_rub_protegees, $rub_parent);
    $c = count($rub_parent);
  };

	// On déprotège les articles qui ont été mis à la poubelle afin qu'ils n'interfèrent pas dans le calcul
	$art_poubelle = sql_select('id_article', 'spip_articles', 'statut = "poubelle"');
	$tableau_art_poubelle = sql_fetch_all($art_poubelle);
	$tableau_art_poubelle = dim2to1($tableau_art_poubelle, 'id_article');
	sql_delete('spip_balai', array('objet="article"', sql_in('id_objet', $tableau_art_poubelle)));

	// Les articles directement protégés
  $art_proteges = sql_select('id_objet', 'spip_balai', "objet='article'");
  $tableau_art_proteges = sql_fetch_all($art_proteges);
  $tableau_art_proteges = dim2to1($tableau_art_proteges, 'id_objet');

	// Les articles protégés par héritage
  $art_proteges_par_heritage = sql_select('id_article', 'spip_articles', sql_in('id_rubrique', $tableau_rub_protegees));
  $tableau_art_proteges_par_heritage = sql_fetch_all($art_proteges_par_heritage);
  $tableau_art_proteges_par_heritage = dim2to1($tableau_art_proteges_par_heritage, 'id_article');

	// Les articles protégés (directement ou par héritable)
  $tableau_art_proteges = array_merge($tableau_art_proteges, $tableau_art_proteges_par_heritage);

	// D'où les articles à supprimer
	$tableau_art_a_supprimer = dim2to1(sql_fetch_all(sql_select('id_article', 'spip_articles', sql_in('id_article', $tableau_art_proteges, 'NOT'))), 'id_article');

	// Rubriques contenant un aticle protégé
	$tableau_rub_contient_art_protege = dim2to1(sql_fetch_all(sql_select('id_rubrique', 'spip_articles', sql_in('id_article', $tableau_art_proteges))), 'id_rubrique');

	// D'où des rubriques à ne pas supprimer
	$tableau_rub_ne_pas_supprimer = array_unique(array_merge($tableau_rub_contient_art_protege, $tableau_rub_protegees));

	// Ajoutons les rubriques contenant une rubrique protégée
	$rub_fille = $tableau_rub_ne_pas_supprimer;
	$c = count($rub_fille);
	while($c>0){
		$rub_fille = sql_select('id_parent', 'spip_rubriques', sql_in('id_rubrique', $rub_fille). ' AND id_parent <>0');
		$rub_fille = dim2to1(sql_fetch_all($rub_fille), 'id_parent');
		$tableau_rub_ne_pas_supprimer = array_merge($tableau_rub_ne_pas_supprimer, $rub_fille);
		$c = count($rub_fille);
	}
	$tableau_rub_ne_pas_supprimer = array_unique($tableau_rub_ne_pas_supprimer);
	$tableau_rub_a_supprimer = dim2to1(sql_fetch_all(sql_select('id_rubrique', 'spip_rubriques', sql_in('id_rubrique', $tableau_rub_ne_pas_supprimer, 'NOT'))), 'id_rubrique');

	//Ouf. Passons à l'action !
	foreach ($tableau_art_a_supprimer as $a){
		article_instituer($a, array('statut'=>"poubelle"), true);
	}

	$supprimer_rubrique = charger_fonction('supprimer_rubrique', 'action');
  foreach ($tableau_rub_a_supprimer as $r) {
  	$supprimer_rubrique($r);
  }

  return;
};
?>
