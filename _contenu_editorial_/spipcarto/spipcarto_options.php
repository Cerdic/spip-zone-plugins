<?php
$GLOBALS['sq_cartes']=array('map','logo','lien','svg','svgx','geosvgwms');
//////////////////////////////////////////////////
//////////////////////////////////////////////////
// PARAMETRAGE
//////////////////////////////////////////////////
//////////////////////////////////////////////////

//////////////////////////////////////////////////
// CARTO_CARTES
//////////////////////////////////////////////////
$spip_carto_cartes = array(
	"id_carto_carte" => "bigint(21) NOT NULL",
	"titre" => "VARCHAR(255) BINARY NOT NULL",
	"texte" => "TEXT BINARY NOT NULL",
	"url_carte" => "TEXT BINARY NOT NULL",
	"callage" => "TEXT BINARY NOT NULL",
	"id_srs" => "bigint(21) NOT NULL");

$spip_carto_cartes_key = array(
	"PRIMARY KEY" => "id_carto_carte",
	"KEY id_carto_carte" => "id_carto_carte");


//////////////////////////////////////////////////
// CARTO_OBJETS
//////////////////////////////////////////////////

$spip_carto_objets = array(
	"id_carto_objet" => "bigint(21) NOT NULL",
	"id_carto_carte" => "bigint(21) NOT NULL",
	"titre" => "VARCHAR(255) BINARY NOT NULL",
	"texte" => "TEXT BINARY NOT NULL",
	"url_objet" => "TEXT BINARY NOT NULL",
	"url_logo" => "TEXT BINARY NOT NULL",
	"geometrie" => "TEXT BINARY NOT NULL",
	"statut"	=> "VARCHAR(8) NOT NULL default 'publie'"
	);
	
$spip_carto_objets_key = array(
	"PRIMARY KEY" => "id_carto_objet",
	"KEY id_carto_carte" => "id_carto_carte",
	"KEY titre" => "titre",
	"KEY statut" => "statut"
	);

//////////////////////////////////////////////////
// CARTO_CARTES_ARTICLES
//////////////////////////////////////////////////

$spip_carto_cartes_articles = array(
	"id_carto_carte" 	=> "BIGINT (21) DEFAULT '0' NOT NULL",
	"id_article" 	=> "BIGINT (21) DEFAULT '0' NOT NULL");

$spip_carto_cartes_articles_key = array(
	"KEY id_carto_carte" 	=> "id_carto_carte",
	"KEY id_article" => "id_article");


//////////////////////////////////////////////////
// CARTO_CARTES_ARTICLES
//////////////////////////////////////////////////

$spip_carto_cartes_articles = array(
	"id_carto_carte" 	=> "BIGINT (21) DEFAULT '0' NOT NULL",
	"id_article" 	=> "BIGINT (21) DEFAULT '0' NOT NULL");

$spip_carto_cartes_articles_key = array(
	"KEY id_carto_carte" 	=> "id_carto_carte",
	"KEY id_article" => "id_article");


//////////////////////////////////////////////////
// MOTS_CARTO_OBJETS
//////////////////////////////////////////////////

$spip_mots_carto_objets= array(
	"id_carto_objet" 	=> "BIGINT (21) DEFAULT '0' NOT NULL",
	"id_mot" 	=> "BIGINT (21) DEFAULT '0' NOT NULL");

$spip_mots_carto_objets_key = array(
	"KEY id_carto_objet" 	=> "id_carto_objet",
	"KEY id_mot" => "id_mot");

//////////////////////////////////////////////////
// DOCUMENTS_CARTO_OBJETS
//////////////////////////////////////////////////

$spip_documents_carto_cartes= array(
	"id_carto_carte" 	=> "BIGINT (21) DEFAULT '0' NOT NULL",
	"id_document" 	=> "BIGINT (21) DEFAULT '0' NOT NULL");

$spip_documents_carto_cartes_key = array(
	"KEY id_carto_carte" 	=> "id_carto_carte",
	"KEY id_document" => "id_document");


//////////////////////////////////////////////////
//////////////////////////////////////////////////
// DECLARATION
//////////////////////////////////////////////////
//////////////////////////////////////////////////

//global $tables_principales,$table_primary,$tables_auxiliaires,$tables_relations;


$GLOBALS['tables_principales']['spip_carto_cartes'] =
	array('field' => &$spip_carto_cartes, 'key' => &$spip_carto_cartes_key);

$GLOBALS['tables_principales']['spip_carto_objets'] =
	array('field' => &$spip_carto_objets, 'key' => &$spip_carto_objets_key);

//Relation avec les articles
$GLOBALS['tables_auxiliaires']['spip_carto_cartes_articles'] = array(
	'field' => &$spip_carto_cartes_articles,
	'key' => &$spip_carto_cartes_articles_key);

$GLOBALS['tables_auxiliaires']['spip_mots_carto_objets'] = array(
	'field' => &$spip_mots_carto_objets,
	'key' => &$spip_mots_carto_objets_key);
	
$GLOBALS['tables_auxiliaires']['spip_documents_carto_cartes'] = array(
	'field' => &$spip_documents_carto_cartes,
	'key' => &$spip_documents_carto_cartes_key);
	

$GLOBALS['table_primary']['carto_objets']="id_carto_objet";
$GLOBALS['table_primary']['carto_cartes']="id_carto_carte";

$GLOBALS['table_des_tables']['carto_objets']="carto_objets";
$GLOBALS['table_des_tables']['carto_cartes']="carto_cartes";

$GLOBALS['tables_jointures']['spip_mots'][]= 'mots_carto_objets';
$GLOBALS['tables_jointures']['spip_carto_objets'][]='mots_carto_objets';
$GLOBALS['tables_jointures']['spip_documents'][]='documents_carto_cartes';
$GLOBALS['tables_jointures']['spip_carto_cartes'][]='documents_carto_cartes';
$GLOBALS['tables_jointures']['spip_articles'][]='carto_cartes_articles';
$GLOBALS['tables_jointures']['spip_carto_cartes'][]='carto_cartes_articles';

$GLOBALS['choses_possibles']['carto_objets'] = array(
									  'titre_chose' => 'Objets',
									  'id_chose' => 'id_carto_objet',
									  'table_principale' => 'spip_carto_objets',
								  	  'url_base' => 'cartes_edit&id_carte=',
									  
									  'table_carte' => 'spip_carto_cartes',
									  'tables_limite' => array(
															   'carto_objets' => array(
																				   'table' => 'spip_carto_objets',
																				   'nom_id' => 'id_carto_objet'),
															   'carto_cartes' => array(
																					'table' => 'spip_carto_objets',
																					'nom_id' =>  'id_carto_carte'),
															   )
									  );

function spipcarto_header_prive($flux) {
	return $flux;
}
////////////////////////////////////////////////////////////////////////
function afficher_liste_carto_objets($choses,$nb_aff=20) {
  echo "<div style='height: 12px;'></div>";
  echo "<div class='liste'>";
  bandeau_titre_boite2("Objets", "../"._DIR_PLUGIN_SPIPCARTO."img/carte-24.gif");
  
  echo afficher_liste_debut_tableau();
  
  $from = array('spip_carto_objets as carto_objets');
  $select= array();
  $select[] = 'id_carto_objet';
  $select[] = 'titre';
  $select[] = 'url_objet';
  $select[] ='id_carto_carte';
//  $select[] = 'statut';
  $where = array('carto_objets.id_carto_objet IN ('.calcul_in($choses).')');
  
  $result = spip_abstract_select($select,$from,$where);
  $i = 0;
  while ($row = spip_abstract_fetch($result)) {
	$i++;
	$vals = '';
	
	$id_carto_objet = $row['id_carto_objet'];
	$tous_id[] = $id_carto_objet;
	$titre = $row['titre'];
	$id_carto_carte = $row['id_carto_carte'];
	$url_objet = $row['url_objet'];
	
	$vals[] = "<input type='checkbox' name='id_choses[]' value='$id_carto_objet' id='id_chose$i'/>";
	
	// Le titre (et la langue)
	$s = "<div>";
	
	$s .= "<a href=\"carte_edit.php3?id_carte=$id_carto_carte#objet$id_carto_objet\" style=\"display:block;\">";
	
	$s .= typo($titre);
	$s .= "</a>";
	$s .= "</div>";
	
	$vals[] = $s;
	
	// L'url
	$s = "<a href=\"$url_objet\" style=\"display:block;\">lien</a>";
	$vals[] = $s;
	
	// Le numero (moche)
	if ($options == "avancees") {
	  $vals[] = "<b>"._T('info_numero_abbreviation')."$id_carto_objet</b>";
	}
	
	
	$table[] = $vals;
  }
  spip_free_result($result);
  
  if ($options == "avancees") { // Afficher le numero (JMB)
	if ($afficher_auteurs) {
	  $largeurs = array(11, '', 80, 100, 35);
	  $styles = array('', 'arial2', 'arial1', 'arial1', 'arial1');
	} else {
	  $largeurs = array(11, '', 100, 35);
	  $styles = array('', 'arial2', 'arial1', 'arial1');
	}
  } else {
	if ($afficher_auteurs) {
	  $largeurs = array(11, '', 100, 100);
	  $styles = array('', 'arial2', 'arial1', 'arial1');
	} else {
	  $largeurs = array(11, '', 100);
	  $styles = array('', 'arial2', 'arial1');
	}
  }
  afficher_liste($largeurs, $table, $styles);
  
  echo afficher_liste_fin_tableau();
}
?>