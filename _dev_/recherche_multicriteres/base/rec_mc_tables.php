<?php
######################################################################
# RECHERCHE MULTI-CRITERES                                           #
# Auteur: Dominique (Dom) Lepaisant - Octobre 2007                   #
# Adaptation de la contrib de Paul Sanchez - Netdeveloppeur          #
# http://www.netdeveloppeur.com                                      #
# Ce programme est un logiciel libre distribue sous licence GNU/GPL. #
# Pour plus de details voir le fichier COPYING.txt                   #
######################################################################
global $table_des_tables;
global $tables_principales;
global $tables_auxiliaires;
global $tables_jointures;

	$table_des_tables['rmc_rubs_groupes'] = 'rmc_rubs_groupes';

$spip_rmc_rubs_groupes = array(
			"id_groupe" => "int(11) NOT NULL default'0'",
			"id_rubrique" => "int(11) NOT NULL default'0'"
			);
$spip_rmc_rubs_groupes_key = array(
			"PRIMARY KEY" => "id_groupe,id_rubrique"
			);

$spip_rmc_mots_exclus = array(
			"id_mot_exclu" => "int(11) NOT NULL default'0'",
			"id_rubrique" => "int(11) default NULL"
			);
$spip_rmc_mots_exclus_key = array(
			"PRIMARY KEY" => "id_mot_exclu,id_rubrique"
			);
			
$tables_auxiliaires['spip_rmc_rubs_groupes'] = array(
			'field' => &$spip_rmc_rubs_groupes, 
			'key' => &$spip_rmc_rubs_groupes_key
			);

			$tables_principales['spip_rmc_mots_exclus'] = array(
			'field' => &$spip_rmc_mots_exclus, 
			'key' => &$spip_rmc_mots_exclus_key
			);
			
$tables_jointures['spip_rubrique'][]= 'rmc_rub_groupes';
$tables_jointures['spip_groupes_mots'][]= 'rmc_rubs_groupes';
$tables_jointures['spip_rmc_rubs_groupes'][]= 'groupes_mots';
$tables_jointures['spip_rmc_rubs_groupes'][]= 'rubrique';
$tables_jointures['spip_mots'][]= 'spip_rmc_mots_exclus';
$tables_jointures['spip_groupes_mots'][]= 'spip_rmc_mots_exclus';

?>

