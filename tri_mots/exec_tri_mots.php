<?php 


//	  exec_tri_mots.php
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


define('_DIR_PLUGIN_TRI_MOTS',(_DIR_PLUGINS . basename(dirname(__FILE__))));

/***********************************************************************/
/* function*/
/***********************************************************************/


function verifier_admin() {
  global $connect_statut, $connect_toutes_rubriques;
  return (($connect_statut == '0minirezo') AND $connect_toutes_rubriques);
}

function verifier_admin_restreint($id_rubrique) {
  global $connect_id_auteur;
  global $connect_statut, $connect_toutes_rubriques;

}

function verifier_auteur($table, $id_objet, $id) {
  global $connect_id_auteur;
  $select = array('id_auteur');
  
  $from =  array($table);
  
  $where = array("id_auteur = $connect_id_auteur", "$id_objet = $id");
  
  $result = spip_abstract_select($select,$from,$where);
  
  if (spip_abstract_count($result) > 0) {
	spip_abstract_free($result);
	return true;
  }
  spip_abstract_free($result);
  return false;
}

//------------------------la fonction qui fait tout-----------------------------------

function tri_mots() {
  global $connect_id_auteur, $connect_statut, $connect_toutes_rubriques;

  include_ecrire ("inc_presentation");
  include_ecrire ("inc_abstract_sql");

  /***********************************************************************/
  /* PREFIXE*/
  /***********************************************************************/
  $table_pref = 'spip';
  if ($GLOBALS['table_prefix']) $table_pref = $GLOBALS['table_prefix'];

  if(addslashes($_GET['installation'])) {
	spip_query("ALTER TABLE `".$table_pref."_mots_articles` ADD `rang` BIGINT NOT NULL DEFAULT 0;");
  }

  $id_mot = intval($_REQUEST['id_mot']);

  /************************************************************************/
  /* insertion */
  /************************************************************************/
  //o[]=118&o=120&o[]=128
  if($_POST['order']) {
	$order = split('&',$_POST['order']);
	for($i=0;$i<count($order);$i++) {
	  spip_query("UPDATE ".$table_pref."_mots_articles SET rang = $i WHERE id_mot=$id_mot AND id_article=".intval(substr($order[$i],4)));
	}
  }

  /***********************************************************************/
  /* affichage*/
  /***********************************************************************/

  debut_page('&laquo; '._T('motspartout:titre_page').' &raquo;', 'documents', 'mots', '', _DIR_PLUGIN_TRI_MOTS."/tri_mots.css");
  echo '		<script type="text/javascript" src="'._DIR_PLUGIN_TRI_MOTS.'/javascript/prototype.js"></script>';
  echo '		<script type="text/javascript" src="'._DIR_PLUGIN_TRI_MOTS.'/javascript/scriptaculous.js"></script>';
  echo '	<script type="text/javascript">';
  echo 'function initialiseSort() {
	Sortable.create(\'liste_tri_articles\');
	$(\'submit_form\').onsubmit = function() {
	  $(\'order\').value=Sortable.serialize(\'liste_tri_articles\',{name:\'o\'});
	};
  }';
  echo "Event.observe(window, 'load', initialiseSort, false);";
  echo ' </script>';


  //Colonne de gauche
  debut_gauche();


  //Milieu

  debut_droite();

	echo  "<h1>Ordonner les articles du mot $id_mot</h1>";

  $result_articles = "SELECT article.titre, article.id_article, lien.rang FROM spip_mots_articles AS lien, spip_articles AS article
 	    WHERE article.id_article=lien.id_article AND article.statut='publie' AND lien.id_mot=$id_mot ORDER BY lien.rang";
  
  $tranches = afficher_tranches_requete($result_articles, 2);

  if($tranches) {
    echo "<div style='height: 12px;'></div>";
    echo "<div class='liste'>";
	bandeau_titre_boite2('ARTICLES', "article-24.gif");

	echo "<table width='100%' cellpadding='2' cellspacing='0' border='0'>";

	echo $tranches;
	echo "<ul id='liste_tri_articles'>";
    $result = spip_query($result_articles);
	while ($row = spip_fetch_array($result)) {
	  $id_article=$row['id_article'];
	  $titre=$row['titre'];
	  $rang=$row['rang'];

	  echo "<li id='article_$id_article'><span>$rang</span><span><a href='" . generer_url_ecrire("articles","id_article=$id_article") . "'>$titre</a></span></li>";

	}
	echo '</ul>';

	echo '</table>';


	echo '<form id="submit_form" action="'.generer_url_ecrire('tri_mots',"id_mot=$id_mot").'" method="post"><input type="hidden" name="order" id="order"/><input type="hidden" name="id_mot" value="'.$id_mot.'"/><input type="submit" id="submit_button"></form>';

  }  

  fin_page();
  
}
?>

