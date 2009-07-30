<?php

// DEFINITION DES TABLES

/* table des pages */
$spip_mine_pages = array(
	"id_page" 			=> "int(11) NOT NULL auto_increment",
	"id_section" 		=> "int(11) NOT NULL",
	"nom_page" 			=> "text NOT NULL",
	"url_page" 			=> "varchar(255) NOT NULL COMMENT 'L''url qui permet d''accéder à la page'",
	"objectifs" 		=> "text NOT NULL COMMENT 'Indiquer la raison d''être de cette page'",
	"contenus" 			=> "text NOT NULL COMMENT 'Lister les contenus principaux de la page'",
	"style" 			=> "text NOT NULL COMMENT 'Indiquer le style de la page'",
	"particularites" 	=> "text NOT NULL COMMENT 'Indiquer si la page a des particularités'",
	"url_maquette" 		=> "varchar(255) NOT NULL default ''",
	"status" 			=> "enum('prepa','propose','validee','refuse','poubelle') NOT NULL default 'prepa' COMMENT 'Statut de la validation de la page'",
	"date_creation" 	=> "datetime NOT NULL default '0000-00-00 00:00:00'",
	"date_validation" 	=> "datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Date où la page a été validée'",
	"date_maj" 			=> "timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP"
);
$spip_mine_pages_key = array(
	"PRIMARY KEY"		=>	"id_page",
	"KEY id_section"	=>	"id_section"
);

/* table des blocs */
$spip_mine_blocs = array(
	"id_bloc" 			=> "int(11) NOT NULL auto_increment",
	"nom_bloc" 			=> "text NOT NULL",
	"nom_squelette" 	=> "text NOT NULL COMMENT 'Indiquer le nom du squelette correspondant au bloc'",
	"objectifs" 		=> "text NOT NULL COMMENT 'A quoi sert ce bloc ?'",
	"contenus" 			=> "text NOT NULL COMMENT 'Lister les contenus principaux du bloc'",
	"taille" 			=> "text NOT NULL COMMENT 'Définir la taille ou les dimensions du bloc'",
	"position" 			=> "text NOT NULL COMMENT 'Définir la position du bloc'",
	"forme" 			=> "text NOT NULL COMMENT 'Définir la forme du bloc'",
	"style" 			=> "text NOT NULL COMMENT 'Indiquer le style du bloc'",
	"particularites" 	=> "text NOT NULL COMMENT 'Indiquer si le bloc a des particularités'",
	"status" 			=> "enum('prepa','propose','publie','refuse','poubelle') NOT NULL default 'prepa' COMMENT 'Statut du bloc'",
	"date_creation" 	=> "datetime NOT NULL default '0000-00-00 00:00:00'",
	"date_validation" 	=> "datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Date où le bloc a été publié'",
	"date_maj" 			=> "timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP"
	PRIMARY KEY  (`id_bloc`)
);
$spip_mine_blocs_key = array(
	"PRIMARY KEY"		=>	"id_bloc"
);


/* table des relations entre les blocs et les pages, et entre blocs eux-mêmes */
$spip_mine_blocs_relations = array(
	"id_bloc" 			=> "bigint(21) NOT NULL default '0'",
	"id_parent" 		=> "bigint(21) NOT NULL default '0' COMMENT 'L''ID de l''objet parent'",
	"type_parent" 		=> "varchar(25) NOT NULL COMMENT 'Le type de l''objet parent'",
	"vu" 				=> "enum('non'",'oui') NOT NULL default 'non'
);
$spip_mine_blocs_relations_key = array(
	"PRIMARY KEY"		=> "id_bloc,id_parent,type_parent"
);


/* table des sections (une section est un conteneur de pages) */
$spip_mine_sections = array(
	"id_section" 		=> "int(11) NOT NULL auto_increment",
	"id_parent"			=> "int(11) NOT NULL auto_increment COMMENT 'L''ID de la section parent'",
	"id_projet" 		=> "int(11) NOT NULL COMMENT 'Le nom ou l ID du projet (ou du site) d où est issue la section'",
	"nom_section" 		=> "text NOT NULL COMMENT 'Le nom donné à cette section du site'",
	"date_maj" 			=> "timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP",
	"descriptif" 		=> "text NOT NULL COMMENT 'Une courte description de la section'"
);
$spip_mine_sections_key = array(
	"PRIMARY KEY"		=> "id_section"
);


/* table des liens */
$spip_mine_liens = array(
	"id_lien" 			=> "int(11) NOT NULL auto_increment",
	"url_lien" 			=> "text NOT NULL COMMENT 'Adresse vers laquelle pointe le lien'",
	"titre_lien" 		=> "varchar(25) NOT NULL"
);
$spip_mine_liens_key = array(
	"PRIMARY KEY"		=> "id_lien"
);


/* table des relations entre les liens et les blocs */
$spip_mine_liens_relations = array(
	"id_lien" 			=> "bigint(21) NOT NULL default '0'",
	"id_parent" 		=> "bigint(21) NOT NULL default '0' COMMENT 'L''ID de l''objet parent'",
	"type_parent" 		=> "varchar(25) NOT NULL COMMENT 'Le type de l''objet parent'",
	"vu" 				=> "enum('non','oui') NOT NULL default 'non'"
);
$spip_mine_liens_relations_key = array(
	"PRIMARY KEY"		=> "id_bloc,id_parent,type_parent"
)

?>