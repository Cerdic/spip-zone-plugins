<?php

//    Fichier créé pour SPIP avec un bout de code emprunté à celui ci.
//    Distribué sans garantie sous licence GPL./
//    Copyright (C) 2006  Pierre ANDREWS
//
//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


define('_DIR_PLUGIN_CHERCHER_SQUELETTES',(_DIR_PLUGINS . basename(dirname(__FILE__))));

function config_chercher_squelettes_mots() {
  global $connect_statut, $connect_toutes_rubriques;

  include_ecrire ("inc_presentation");
  include_ecrire ("inc_abstract_sql");

  debut_page('&laquo; '._T('squelettesmots:titre_page').' &raquo;', 'configurations', 'mots_partout','',_DIR_PLUGIN_CHERCHER_SQUELETTES.'/chercher_squelettes_mots.css');

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
	if (!is_array($fonds))
		$fonds = array();

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
	  echo '<fieldset class="regle">';
	  echo '<legend>'._T('squelettesmots:reglei',array('id'=>$index)).'</legend>';
	  echo '<div class="champs">';
	  echo "<input type=\"checkbox\" class=\"actif\" name=\"actif[$index]\" checked=\"true\"/>";
	  echo "<label for=\"fond_$index\" class=\"fond\">"._T('squelettesmots:fond')."</label>";
	  echo "<input type=\"text\" name=\"fonds[$index]\" class=\"fond\" value=\"$fond\" id=\"fond_$index\"/>";
	  echo "<label for=\"id_groupe_$index\" class=\"id_groupe\">"._T('squelettesmots:groupe')."</label>";
	  echo "<select name=\"tid_groupe[$index]\" class=\"id_groupe\" id=\"id_groupe_$index\">";
	  foreach($groupes_mots as $id => $titre) {
		echo "<option value=\"$id\"".(($id_groupe == $id)?' selected="true"':'').">$titre</option>";
	  }
	  echo '</select>';
	  echo "<label for=\"type_$index\" class=\"type\">"._T('squelettesmots:type')."</label>";
	  echo "<select name=\"type[$index]\" class=\"type\" id=\"type_$index\">";
	  foreach($id_tables as $t => $x) {
		echo "<option value=\"$t\"".(($type == $t)?' selected="true"':'').">$t</option>";
	  }
	  echo '</select>';
	  echo '</div>';
	  $select1 = array('titre');
	  $from1 = array('spip_mots AS mots');
	  $where1 = array("id_groupe=$id_groupe");
	  $rez =spip_abstract_select($select1,$from1,$where1);
	  $liste_squel = '<ul>';
	  $ext = $GLOBALS['extension_squelette'];
	  $cnt = 0;
	  while ($r = spip_abstract_fetch($rez)) {
		include_ecrire("inc_charsets");
		$n = translitteration(ereg_replace('[0-9 ]', '', $r['titre']));
		if ($squel = find_in_path("$fond==$n.$ext")) {
		  $cnt++;
		  $liste_squel .= "<li><a href=\"$squel\">$fond==$n.$ext</a></li>";
		}
		if ($squel = find_in_path("$fond-$n.$ext")) {
		  $cnt++;
		  $liste_squel .= "<li><a href=\"$squel\">$fond-$n.$ext</a></li>";
		}
	  }
	  spip_abstract_free($rez);
	  $liste_squel .= '</ul>';

	  
	  echo '<div class="possible">';
	  if($cnt > 0) echo bouton_block_invisible("regle$index");
	  echo _T('squelettesmots:possibilites',array('total' => $cnt));
	  if ($cnt > 0) {
		echo debut_block_invisible("regle$index");
		echo $liste_squel;
		echo fin_block();
	  }
	  echo '</div>';

	  echo '</fieldset>';
	}
	
	$index++;
	
	echo '<hr/>';
	echo '<fieldset class="nouvelle_regle">';
	echo '<legend>'._T('squelettesmots:nouvelle_regle').'</legend>';
	echo "<input type=\"checkbox\" class=\"actif\" name=\"actif[$index]\"/>";
	echo "<label for=\"fond_$index\" class=\"fond\">"._T('squelettesmots:fond')."</label>";
	echo "<input type=\"text\" name=\"fonds[$index]\" class=\"fond\" value=\"article\"/>";
	echo "<label for=\"id_groupe_$index\" class=\"id_groupe\">"._T('squelettesmots:groupe')."</label>";
	echo "<select name=\"tid_groupe[$index]\" class=\"id_groupe\" id=\"id_groupe_$index\">";
	foreach($groupes_mots as $id => $titre) {
	  echo "<option value=\"$id\">$titre</option>";
	}
	echo '</select>';
	echo "<label for=\"type_$index\" class=\"type\">"._T('squelettesmots:type')."</label>";
	echo "<select name=\"type[$index]\" class=\"type\" id=\"type_$index\">";
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
