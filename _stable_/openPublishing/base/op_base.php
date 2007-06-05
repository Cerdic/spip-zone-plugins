<?php
// -------------------------------------------------------------------------------
// Declaration des tables | spip_op_config | spip_op_rubriques | spip_op_auteurs |

/* Les tables du plugin openPublishing

spip-op-rubriques : contient les numéros des rubriques openPublishing
- op_rubrique

spip-op-config : contient les options de configuration
- agenda : flag OUI / NON
- documents : flag OUI / NON
- anti-spam : flag OUI / NON
- titre-minus : flag OUI / NON (majuscule interdite dans le titre ?)
- rubrique-agenda : numéro de la rubrique agenda
- lien-retour : adresse de la page de retour
- lien-retour-abandon : adresse de la page de retour en cas d'abandon
- id-auteur-op : numéro id de l'auteur openPublishing
- message-retour : message affiché lors du retour
- message-retour-abandon : message affiché lors de l'abandon
- version : numéro de version du plugin
- tagmachine : flag OUI / NON
- motclefs : flag OUI / NON
- statut : prepa, prop, publie

spip_op_auteurs : contient les informations sur les auteurs op
- id_auteur : id de l'auteur op
- id_article : id de son article
- id_real_auteur : id de l'auteur anonymous
- nom : nom
- email : mail
- group_name : nom de son groupe
- phone : num de téléphone

*/

include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees

global $tables_principales;
global $tables_jointures;


//-- Table OP_CONFIG ------------------------------------------
$op_config = array(
		"id_config"			=> "bigint(21) NOT NULL auto_increment",
		"agenda"			=> "ENUM('oui','non') DEFAULT 'oui' NOT NULL",
		"documents"			=> "ENUM('oui','non') DEFAULT 'oui' NOT NULL",
		"anti_spam"			=> "ENUM('oui','non') DEFAULT 'oui' NOT NULL",
		"titre_minus"			=> "ENUM('oui','non') DEFAULT 'oui' NOT NULL",
		"rubrique_agenda"		=> "bigint(21) NOT NULL",
		"lien_retour"			=> "text NOT NULL",
		"lien_retour_abandon" 		=> "text NOT NULL",
		"id_auteur_op"			=> "bigint(21) NOT NULL",
		"message_retour"		=> "text NOT NULL",
		"message_retour_abandon"	=> "text NOT NULL",
		"version"			=> "text NOT NULL",
		"tagmachine"			=> "ENUM('oui','non') DEFAULT 'non' NOT NULL",
		"motclefs"			=> "ENUM('oui','non') DEFAULT 'non' NOT NULL",
		"statut"			=> "ENUM('prepa','prop','publie') DEFAULT 'prop' NOT NULL"
		);

$op_config_key = array(
		"PRIMARY KEY"	=> "id_config"
		);

$tables_principales['spip_op_config'] =
	array('field' => &$op_config, 'key' => &$op_config_key);



//-- Table OP_AUTEURS ------------------------------------------
$op_auteurs = array(
		"id_auteur"		=> "bigint(21) NOT NULL auto_increment",
		"id_article" 		=> "bigint(21) DEFAULT '0' NOT NULL",
		"id_real_auteur" 	=> "bigint(21) DEFAULT '0' NOT NULL",
		"nom"			=> "text NOT NULL",
		"email"			=> "text NOT NULL",
		"group_name"		=> "text NOT NULL",
		"phone" 		=> "text NOT NULL"
		);

$op_auteurs_key = array(
		"PRIMARY KEY"	=> "id_auteur",
		"KEY"		=> "id_article"
		);

$tables_principales['spip_op_auteurs'] =
	array('field' => &$op_auteurs, 'key' => &$op_auteurs_key);



//-- Table OP_RUBRIQUES  ------------------------------------------

$op_rubriques = array(
		"id_rubrique"		=> "bigint(21) NOT NULL auto_increment",
		"op_rubrique"		=> "bigint(21) DEFAULT '0' NOT NULL"
		);

$op_rubriques_key = array(
		"PRIMARY KEY"	=> "id_rubrique"
		);

$tables_principales['spip_op_rubriques'] =
	array('field' => &$op_rubriques, 'key' => &$op_rubriques_key);

//-- Jointures ---------------------------------------------------------

$tables_jointures['spip_rubriques'][]= 'op_rubriques';
$tables_jointures['spip_op_rubriques'][] = 'rubriques';


//-- table des table et table primary ------------------------------------

global $table_des_tables, $table_primary;


$table_primary['op_rubriques']="id_rubrique";
$table_primary['op_auteurs']="id_auteur";
$table_primary['op_config']="id_config";

$table_des_tables['op_rubriques']='op_rubriques';
$table_des_tables['op_auteurs']='op_auteurs';
$table_des_tables['op_config']='op_config';


?>