<?php 


  //	  exec_tri_mots.php
  //    Fichier créé pour SPIP avec un bout de code emprunt‚ à celui ci.
  //    Distribu‚ sans garantie sous licence GPL./
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


$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
define('_DIR_PLUGIN_TRI_MOTS',(_DIR_PLUGINS.end($p)));

/***********************************************************************/
/* function*/
/***********************************************************************/


function verifier_admin() {
  global $connect_statut, $connect_toutes_rubriques;
  return (($connect_statut == '0minirezo') AND $connect_toutes_rubriques);
}

//------------------------la fonction qui fait tout-----------------------------------

function exec_tri_mots() {
  global $connect_id_auteur;

  
  $table = addslashes(_request('objet'));
  if(!$table) $table = 'articles';
  $id_table = addslashes(_request('ident_objet'));
  if(!$id_table) $id_table = 'id_article';

  include_spip("inc/presentation");
  include_spip("base/abstract_sql");

  debut_page('&laquo; '._T('trimots:titre_page',array('objets'=>_T("public:$table"))).' &raquo;', 'documents', 'mots');
   
  if(!verifier_admin()) {
	echo "<strong>"._T('avis_acces_interdit')."</strong>";
	fin_page();
	exit;
  }

  /***********************************************************************/
  /* PREFIXE*/
  /***********************************************************************/
  $table_pref = 'spip';
  if ($GLOBALS['table_prefix']) $table_pref = $GLOBALS['table_prefix'];

  $id_mot = intval(_request('id_mot'));

  $select = array("titre,type");

  $from = array("spip_mots");
  $where = array("id_mot=$id_mot");
  $res = spip_abstract_select($select,$from,$where);
  if($row = spip_abstract_fetch($res)) {
	$titre = $row['titre'];
	$type = $row['type'];
  } else {
	$titre = '';
	$type ='';
  }
  spip_abstract_free($res);

  //Installation
  $installe = unserialize(lire_meta('TriMots:installe'));
  if(!isset($installe)) { 
	$installe = array(); 
  }		
  if(!isset($installe[$table])) {
	$res = spip_query("SHOW COLUMNS FROM `".$table_pref."_mots_$table` LIKE 'rang'");
	if(!spip_fetch_array($res)) {
	  spip_query("ALTER TABLE `".$table_pref."_mots_$table` ADD `rang` BIGINT NOT NULL DEFAULT 0;");
	  $from = array("spip_$table");	
	  if($table == 'auteurs') {
		$select = array($id_table,'nom');
	  } else 
		$select = array($id_table,'titre');
	  if($table == 'auteurs') {
		$where = array("nom REGEXP '^[0-9]+\\. '");
	  } else
		$where = array("titre REGEXP '^[0-9]+\\. '");
	  $results = spip_abstract_select($select,$from,$where);
	  while($row = spip_abstract_fetch($results)) {
		$rang = substr($row['titre'],0,strpos($row['titre'],'.'));
		if($rang > 0) {
		  spip_query("UPDATE ".$table_pref."_mots_$table SET rang = $rang WHERE $id_table=".intval($row[$id_table]));
		}
	  }
	  spip_abstract_free($results);
	  $installe[$table] = true;
	  ecrire_meta('TriMots:installe',serialize($installe)); //histoire de pas faire une recherche dans la base à chaque coup
	  ecrire_metas();
	}
	spip_free_result($res);
  }

  /***********************************************************************/
  /* affichage*/
  /***********************************************************************/
  echo '		<script type="text/javascript" src="'._DIR_PLUGIN_TRI_MOTS.'/javascript/prototype.js"></script>';
  echo '		<script type="text/javascript" src="'._DIR_PLUGIN_TRI_MOTS.'/javascript/scriptaculous.js"></script>';
  echo '	<script type="text/javascript">';
  echo "function initialiseSort() {
  Sortable.create('liste_tri');
  $('submit_form').onsubmit = function() {
  $('order').value=Sortable.serialize('liste_tri',{name:'o'});
  };
  }";
  echo "Event.observe(window, 'load', initialiseSort, false);";
  echo ' </script>';

  gros_titre(_T('trimots:titre_tri_mots',array('titre_mot'=>$titre,'type_mot'=>$type,'objets'=>_T("public:$table"))));

  //Colonne de gauche
  debut_gauche();

  debut_cadre_enfonce();

  echo _T('trimots:tri_mots_help',array('titre_mot'=>$titre, 'type_mot'=>$type,'objets'=>_T("public:$table")));

  fin_cadre_enfonce();

  debut_cadre_enfonce();
  $redirect = generer_url_ecrire('tri_mots',"objet=$table&ident_objet=$id_table&id_mot=$id_mot");
  echo '<form id="submit_form" action="'.generer_url_action('tri_mots',"table=$table&id_table=$id_table&id_mot=$id_mot").'" method="post">
  <input type="hidden" name="redirect" value="'.$redirect.'"/>
  <input type="hidden" name="hash" value="'.calculer_action_auteur("tri_mots $table $id_table $id_mot").'"/>
  <input type="hidden" name="id_auteur" value="'.$connect_id_auteur.'" />
  <input type="hidden" name="order" id="order"/><label for="submit_button">'._T('trimots:envoyer').'</label><input type="submit" id="submit_button" value="'._T('bouton_valider').'"/></form>';
  fin_cadre_enfonce();

  if(_request('retour')) icone(_T('icone_retour'), addslashes(_request('retour')), "mot-cle-24.gif", "rien.gif");

  //Milieu

  debut_droite();

  if($table == 'auteurs') 
	$select = array("$table.nom", "$table.$id_table", 'lien.rang');
  else
	$select = array("$table.titre", "$table.$id_table", 'lien.rang');
  $from = array("spip_mots_$table AS lien", "spip_$table AS $table");
  $where = array("$table.$id_table=lien.$id_table" , "lien.id_mot=$id_mot");
  if($table != 'auteurs') $where[] = "$table.statut IN ('prop', 'publie')";
  $order = array('lien.rang');

  global $spip_lang_left;
  echo "<div style='height: 12px;'></div>";
  echo "<div class='liste'>";  
  echo "<div style='position: relative;'>";
  echo "<div style='position: absolute; top: -12px; $spip_lang_left: 3px;'>
	  <img src='"._DIR_PLUGIN_TRI_MOTS."/img/updown.png'/></div>";
  echo "<div style='background-color: white; color: black; padding: 3px; padding-$spip_lang_left: 30px; border-bottom: 1px solid #444444;' class='verdana2'><b>"._T("public:$table")."</b></div>";
  echo "</div>";

  echo "<ul id='liste_tri'>";
  $result = spip_abstract_select($select,$from,$where,'',$order);
  while ($row = spip_abstract_fetch($result)) {
	$id=$row[$id_table];
	if($table == 'auteurs')
	  $titre=$row['nom'];
	else $titre=$row['titre'];
	$rang=$row['rang'];

	echo "<li id='".$table."_$id'><span class=\"titre\">$titre</span><span class=\"lien\"><a href='" . generer_url_ecrire("$table","$id_table=$id") . "'>"._T('trimots:voir')."</a></span><span class=\"rang\">$rang</span></li>";

  }

  spip_abstract_free($result);
  echo '</ul>';
  echo '</div>';

  fin_page();

}
?>
