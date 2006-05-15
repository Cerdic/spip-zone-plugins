<?php

global $table_des_tables;
global $tables_principales;
global $tables_auxiliaires;
global $tables_jointures;

$table_des_tables['lettres']='lettres';

$spip_lettres = array(
				"id_lettre"		=> "bigint(21) NOT NULL",
				"titre"			=> "text NOT NULL",
				"descriptif"	=> "text NOT NULL",
				"texte"			=> "longblob NOT NULL",
				"date"			=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
				"lang"			=> "varchar(10) NOT NULL",
				"maj"			=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL"
	);
$spip_lettres_key = array("PRIMARY KEY" => "id_lettre");
$tables_principales['spip_lettres'] =
	array('field' => &$spip_lettres, 'key' => &$spip_lettres_key);

$spip_mots_lettres = array(
		"id_mot"		=> "bigint(21) NOT NULL",
		"id_lettre"		=> "bigint(21) NOT NULL");
$spip_mots_lettres_key = array(
		"PRIMARY KEY" => "id_mot, id_lettre",
		"KEY id_mot"	=> "id_mot",
		"KEY id_lettre"	=> "id_lettre");
$tables_auxiliaires['spip_mots_lettres'] = array(
	'field' => &$spip_mots_lettres,
	'key' => &$spip_mots_lettres_key);

$spip_auteurs_lettres = array(
		"id_auteur"		=> "bigint(21) NOT NULL",
		"id_lettre"		=> "bigint(21) NOT NULL");
$spip_auteurs_lettres_key = array(
		"PRIMARY KEY" => "id_auteur, id_lettre",
		"KEY id_auteur"	=> "id_auteur",
		"KEY id_lettre"	=> "id_lettre");
$tables_auxiliaires['spip_auteurs_lettres'] = array(
	'field' => &$spip_auteurs_lettres,
	'key' => &$spip_auteurs_lettres_key);

$tables_jointures['spip_lettres'][]= 'mots_lettres';
$tables_jointures['spip_lettres'][]= 'auteurs_lettres';
$tables_jointures['spip_lettres'][]= 'mots';
$tables_jointures['spip_auteurs'][]= 'auteurs_lettres';



//
// <BOUCLE(LETTRES)>
//
function boucle_LETTRES_dist($id_boucle, &$boucles) {
        $boucle = &$boucles[$id_boucle];
        $id_table = $boucle->id_table;
        $boucle->from[$id_table] =  "spip_lettres";  

		if (!$GLOBALS['var_preview']) {
			// Restreindre aux elements publies
			if (!$boucle->statut) {
				$boucle->where[] = "$id_table.statut IN ('publie','envoi_en_cours')";
			}
		}
        return calculer_boucle($id_boucle, $boucles); 
}


?>