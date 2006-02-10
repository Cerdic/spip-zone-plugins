<?php

function config_mots_partout() {
  global $connect_statut, $connect_toutes_rubriques;

  include_ecrire ("inc_presentation");
  include_ecrire ("inc_abstract_sql");

  debut_page('&laquo; '._T('motspartout:titre_page').' &raquo;', 'configurations', 'mots_partout');

  if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
	echo _T('avis_non_acces_page');
	exit;
  }

  if ($connect_statut == '0minirezo' AND $connect_toutes_rubriques ) {
	
	$table_pref = 'spip';
	if ($GLOBALS['table_prefix']) $table_pref = $GLOBALS['table_prefix'];
	
	/************************************************************************/
	/*MODIFICATION/CREATION des tables*/
	/************************************************************************/

	$type = addslashes($_POST['nom_chose']);
	
	include("mots_partout_choses.php");
	$id_chose = $choses_possibles[$type]['id_chose'];
	$table_principale = $choses_possibles[$type]['table_principale'];
	
	if($type != '') {
	  spip_query("ALTER TABLE `".$table_pref."_groupes_mots` ADD `mots` CHAR( 3 ) NOT NULL DEFAULT 'non';");
	  spip_query("CREATE TABLE IF NOT EXISTS `".str_replace('spip_',$table_pref.'_mots_',$table_principale)."` (`id_mot` bigint(20) NOT NULL default '0',`$id_chose` bigint(20) NOT NULL default '0', KEY `$id_chose` (`id_mot`),KEY `id_mot` (`id_mot`)) TYPE=MyISAM;;");
	}
	
	/*Affichage*/

	debut_droite();
	echo "<form action=\"".generer_url_ecrire('config_mots_partout')."\" method=\"post\">";
	echo "installer pour avoir des mots sur les: <select name=\"nom_chose\">";
	foreach($choses_possibles as $chose => $data) {
	  echo "<option value=\"$chose\">$chose</option>";
	}
	echo "</select>";
	echo "<input type=\"submit\" value=\""._T('valider')."\"/>";


  } 

  fin_page();
  
}

?>
