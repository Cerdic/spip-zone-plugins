<?php

function config_chercher_squelettes_mots() {
  global $connect_statut, $connect_toutes_rubriques;

  include_ecrire ("inc_presentation");
  include_ecrire ("inc_abstract_sql");

  debut_page('&laquo; '._T('motspartout:titre_page').' &raquo;', 'configurations', 'mots_partout');

  if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
	echo _T('avis_non_acces_page');
	exit;
  }

  if ($connect_statut == '0minirezo' AND $connect_toutes_rubriques ) {
	
	/*Affichage*/

	debut_droite();
	echo 'Salut!';
  } 

  $fonds = array('article' => array(6,'articles','id_article'));
  ecrire_meta('SquelettesMots:fond_pour_groupe',serialize($fonds));
  ecrire_metas();

  fin_page();
  
}

?>
