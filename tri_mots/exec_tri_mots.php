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

  /***********************************************************************/
  /* affichage*/
  /***********************************************************************/

  debut_page('&laquo; '._T('motspartout:titre_page').' &raquo;', 'documents', 'mots', '', _DIR_PLUGIN_MOTS_PARTOUT."/mots_partout.css");

		<script type="text/javascript" src="'. _DIR_PLUGIN_MOTS_PARTOUT.'/javascript/MultiStateRadio.js"></script>
	<script type="text/javascript">

		MultiStateRadio.apply(\'.liste ul\');

  </script>';


  //Colonne de gauche
  debut_gauche();


  //Milieu

  debut_droite();

  $result_articles = "SELECT article.titre, article.id_article, lien.rang FROM spip_mots_articles AS lien, spip_articles AS article
 	    WHERE article.id_article=lien.id_article AND article.statut='publie' AND lien.id_mot=$id_mot ORDER BY lien.rang";
 
echo 'TEST';
	    $tranches = afficher_tranches_requete($result_articles, 2);

  if($tranches) {
    echo "<div style='height: 12px;'></div>";
    echo "<div class='liste'>";
bandeau_titre_boite2('ARTICLES', "article-24.gif");
echo afficher_liste_debut_tableau();
echo $tranches;
    $result = spip_query($result_articles);
 	        while ($row = spip_fetch_array($result)) {
$id_artilce=$row['id_article'];
$titre=$row['titre'];
$rang=$row['rang'];

$vals = '';
$vals[] = $rang;
$vals[] = "<a href='" . generer_url_ecrire("articles","id_article=$id_article") . "'>$titre</a>";

$table[] = $vals;

}
 $largeurs = array(11, 100);
 $styles = array('arial2', 'arial1');

afficher_liste($largeurs, $table, $styles);

echo afficher_liste_fin_tableau();

}  

  fin_page();
  
}
?>

