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

	$tables_installees = unserialize(lire_meta('MotsPartout:tables_installees'));

	if(($type != '') && (!$tables_installees[$type])) {
	  spip_query("ALTER TABLE `".$table_pref."_groupes_mots` ADD `mots` CHAR( 3 ) NOT NULL DEFAULT 'non';");
	  spip_query("CREATE TABLE IF NOT EXISTS `".str_replace('spip_',$table_pref.'_mots_',$table_principale)."` (`id_mot` bigint(20) NOT NULL default '0',`$id_chose` bigint(20) NOT NULL default '0', KEY `$id_chose` (`id_mot`),KEY `id_mot` (`id_mot`)) TYPE=MyISAM;;");
	  $tables_installees[$type] = true;
	  ecrire_meta('MotsPartout:tables_installees',serialize($tables_installees));
	  ecrire_metas();
	}
	
	/*Affichage*/

	echo '<br><br><br>';
	
	gros_titre(_T('motspartout:titre_page'));

	barre_onglets("configuration", "config_mots_partout");

	debut_gauche();

	debut_droite();
	

	include_ecrire('inc_config');
	avertissement_config();

	debut_cadre_enfonce();

	$one = false;
	$form = "<form action=\"".generer_url_ecrire('config_mots_partout')."\" method=\"post\">";
	$form .= "<label for=\"nom_chose\">"._T("motspartout:installer").":</label><br><br><select name=\"nom_chose\">";
	foreach($choses_possibles as $chose => $data) {
	  if(!$tables_installees[$chose]) {
		$one = true;
		$form .= "<option value=\"$chose\">$chose</option>";
	  }
	}
	$form .= "</select>";
	$form .= "<input type=\"submit\" value=\""._T('valider')."\"/>";
	$form .= '</form>';
	if($one) {
	  echo $form;
	} else {
	  echo _T("motspartout:toutinstalle");
	}

	fin_cadre_enfonce();

  } 

  fin_page();
  
}

?>
