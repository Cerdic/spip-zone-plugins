<?php
function balise_FLICKR_GRAND_TOTAL_dist($p, $liste='true') {
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
  $p->code= "(isset(\$Numrows['$b']['fpipr_grand_total']) ?\$Numrows['$b']['fpipr_grand_total'] : \$Numrows['$b']['total'])";
  $p->interdire_scripts = false;
  return $p;
}
?>
