<?php

function config_chercher_squelettes_mots() {
  global $connect_statut, $connect_toutes_rubriques;

  include_ecrire ("inc_presentation");
  include_ecrire ("inc_abstract_sql");

  debut_page('&laquo; '._T('squelettesmots:titre_page').' &raquo;', 'configurations', 'mots_partout');

  if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
	echo _T('avis_non_acces_page');
	exit;
  }

  if ($connect_statut == '0minirezo' AND $connect_toutes_rubriques ) {

	gros_titre(_T('squelettesmots:gros_titre'));

	/*Affichage*/
	debut_gauche();	
	
	debut_boite_info();
	echo propre(_T('squelettesmots:help'));
	fin_boite_info();

	debut_droite();
	
	echo '<form action="'.generer_url_ecrire('config_chercher_squelettes_mots').'" method="post">';

	$groupes_mots = '';
	$select = array('id_groupe','titre');
	$from = array('spip_groupes_mots');

	$rez = spip_abstract_select($select,$from);
	while($row = spip_abstract_fetch($rez)) {
	  $groupes_mots[$row['id_groupe']] = $row['titre'];
	}
	spip_abstract_free($rez);

	//TODO: trouver automatiquement ces informations pour toutes les tables avec un jonction sur les mots
	$id_tables = array('articles' => 'id_article',
					   'rubriques' => 'id_rubrique',
					   'breves' => 'id_breve',
					   'sites' => 'id_site');
	

	$fonds = unserialize(lire_meta('SquelettesMots:fond_pour_groupe'));

	$field_fonds = $_REQUEST['fonds'];
	$id_groupes = $_REQUEST['tid_groupe'];
	$types = $_REQUEST['type'];
	$actif = $_REQUEST['actif'];
	
	/*On transforme les _POST en jolie tableau*/
	if($field_fonds) {
	  foreach($field_fonds as $index => $fond) {		
		$index = intval($index);
		$fond = addslashes($fond);
		if($actif[$index]) {
		  $id_groupe = intval($id_groupes[$index]);
		  $type = addslashes($types[$index]);
		  $fonds[$fond] = array($id_groupe,$type,$id_tables[$type]);
		} else {
		  unset($fonds[$fond]);
		}
	  }
	}
	
	$index = 0;
	
	foreach($fonds as $fond => $a) {
	  list($id_groupe,$type,$id_table) = $a;
	  $index++;
	  echo '<fieldset>';
	  echo '<legend>'._T('squelettesmots:reglei',array('id'=>$index)).'</legend>';
	  echo "<input type=\"checkbox\" name=\"actif[$index]\" checked=\"true\"/>";
	  echo "<label for=\"fond_$index\">"._T('squelettesmots:fond')."</label>";
	  echo "<input type=\"text\" name=\"fonds[$index]\" value=\"$fond\" id=\"fond_$index\"/>";
	  echo "<label for=\"id_groupe_$index\">"._T('squelettesmots:groupe')."</label>";
	  echo "<select name=\"tid_groupe[$index]\" id=\"id_groupe_$index\">";
	  foreach($groupes_mots as $id => $titre) {
		echo "<option value=\"$id\"".(($id_groupe == $id)?' selected="true"':'').">$titre</option>";
	  }
	  echo '</select>';
	  echo "<label for=\"type_$index\">"._T('squelettesmots:type')."</label>";
	  echo "<select name=\"type[$index]\" id=\"type_$index\">";
	  foreach($id_tables as $t => $x) {
		echo "<option value=\"$t\"".(($type == $t)?' selected="true"':'').">$t</option>";
	  }
	  echo '</select>';
	  echo '</fieldset>';
	}
	
	$index++;
	
	echo '<hr/>';
	echo '<fieldset>';
	echo '<legend>'._T('squelettesmots:nouvelle_regle').'</legend>';
	echo "<input type=\"checkbox\" name=\"actif[$index]\"/>";
	echo "<label for=\"fond_$index\">"._T('squelettesmots:fond')."</label>";
	echo "<input type=\"text\" name=\"fonds[$index]\" value=\"article\"/>";
	echo "<label for=\"id_groupe_$index\">"._T('squelettesmots:groupe')."</label>";
	echo "<select name=\"tid_groupe[$index]\" id=\"id_groupe_$index\">";
	foreach($groupes_mots as $id => $titre) {
	  echo "<option value=\"$id\">$titre</option>";
	}
	echo '</select>';
	echo "<label for=\"type_$index\">"._T('squelettesmots:type')."</label>";
	echo "<select name=\"type[$index]\" id=\"type_$index\">";
	foreach($id_tables as $t => $x) {
	  echo "<option value=\"$t\">$t</option>";
	}
	echo '</select>';
	echo '</fieldset>';
	
	echo '<input type="submit" value="'._T('valider').'"/>';
	echo '</form>';
  } 
  
  ecrire_meta('SquelettesMots:fond_pour_groupe',serialize($fonds));
  ecrire_metas();
  
  fin_page();
  
}

?>
