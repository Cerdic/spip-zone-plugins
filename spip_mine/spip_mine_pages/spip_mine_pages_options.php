<?php

// chemin pour les fichiers squelettes
// $GLOBALS['dossier_squelettes'] = find_in_path($fond."squelettes");
_chemin(_DIR_PLUGIN_SPIP_MINE_PAGES.'squelettes/');


include_spip('base/spip_mine_pages_fonctions.php');

// Pour faire prendre en compte la fonction "propre" pour tous les champs de type "texte"
// pour le passage par le filtre "propre" sur certains champs texte identifiés
global $table_des_traitements;

define('_TRAITEMENT_TYPO', 'typo(%s, "TYPO", $connect)'); // champs courts
define('_TRAITEMENT_RACCOURCIS', 'propre(%s, $connect)'); // champs longs

$table_des_traitements['OBJECTIFS'][]	= _TRAITEMENT_RACCOURCIS;

global $table_date;
$table_date['spip_mine_pages']='date_creation';



global $tables_jointures;
$tables_jointures['spip_mine_pages'][]= 'spip_mine_blocs_relations';
$tables_jointures['spip_mine_blocs'][]= 'spip_mine_blocs_relations';


global $exceptions_des_jointures;
$exceptions_des_jointures['id_page'] = array('spip_mine_blocs', 'id_parent')
// Attention ! le id_parent peut être un id_bloc si type parent = bloc

/*
$tables_auxiliaires['spip_mine_blocs_relations'] = array(
	'field' => &$spip_mine_blocs_relations,
	'key' => &$spip_mine_blocs_relations);


$tables_jointures['spip_mine_blocs']['id_bloc']= 'spip_mine_blocs_relations';
$tables_jointures['spip_mine_blocs']['id_parent']= 'spip_mine_blocs_relations';
$tables_jointures['spip_mine_pages']['id_parent']= 'spip_mine_blocs_relations';
$tables_jointures['spip_mine_pages']['id_section']= 'spip_mine_sections';
*/

// echo "<script language=\"javascript\">alert('Pouet !')</script>";

?>