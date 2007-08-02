<?
//déclaration des tables du plugin jeux //
global $table_des_tables;
global $tables_principales;
include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees
global $tables_jointures;

$table_des_tables['jeux'] = 'jeux';
$table_des_tables['jeux_resultats'] = 'jeux_resultats';
$jeux = array(
	'id_jeu'	=>'bigint(21) NOT NULL',
	'date'		=>"timestamp",
	'contenu'	=>'text NOT NULL'
	);

$jeux_key = array(
	'PRIMARY KEY'	=>'id_jeu');
$jeux_resultats = array(
	'id_jeu'		=> 'bigint(21) NOT NULL',
	'id_auteur'		=> 'bigint(21) NOT NULL',
	'date'			=>	"timestamp",
	'score_court'	=>	'int(12)',
	'score_long'	=>	'text NOT NULL');
$jeux_resultats_key=array('KEY id_jeu'	=>'id_jeu',
	'KEY id_auteur'	=>'id_auteur');

$tables_principales['spip_jeux']=
	array('field' => &$jeux, 'key' => &$jeux_key);
$tables_principales['spip_jeux_resultats']=
	array('field' => &$jeux_resultats, 'key' => &$jeux_resultats_key);


global $table_des_traitements;
$table_des_traitements['CONTENU'][]= 'propre(%s)';

?>