ALTER TABLE spip_documents ADD id_tek_rub BIGINT( 21 ) NOT NULL default '0';
ALTER TABLE spip_documents ADD tek_type CHAR( 3 ) NOT NULL ;
ALTER TABLE spip_documents ADD id_owner INT( 11 ) NOT NULL ;
ALTER TABLE spip_documents ADD photo_credit VARCHAR( 255 ) NOT NULL ;

CREATE TABLE spip_xtra_imgrub (
  imgrub_id 				bigint(21) NOT NULL auto_increment,
  imgrub_id_parent 		bigint(21) NOT NULL default '0',
  imgrub_titre 			text NOT NULL,
  imgrub_descriptif 		text NOT NULL,
  imgrub_id_secteur 		bigint(21) NOT NULL default '0',

  PRIMARY KEY  (imgrub_id),
  KEY id_parent (imgrub_id_parent)
) TYPE=MyISAM;


CREATE TABLE spip_xtra_docrub (
  docrub_id 				bigint(21) NOT NULL auto_increment,
  docrub_id_parent 		bigint(21) NOT NULL default '0',
  docrub_titre 			text NOT NULL,
  docrub_descriptif 		text NOT NULL,
  docrub_id_secteur 		bigint(21) NOT NULL default '0',

  PRIMARY KEY  (docrub_id),
  KEY id_parent (docrub_id_parent)
) TYPE=MyISAM;



CREATE TABLE spip_xtra_documents_articles (
  id_document 	bigint(21) NOT NULL default '0',
  id_article 		bigint(21) NOT NULL default '0',
  num_copie 		int(11) NOT NULL default '0',
  alt 			varchar(255) NOT NULL default '',
  longdesc 		varchar(255) NOT NULL default '',
  legende 		varchar(255) NOT NULL default '',
  langue 			varchar(255) NOT NULL default '',
  position 		varchar(255) NOT NULL default '',
  xtra_doc_art_id 	bigint(21) NOT NULL auto_increment,

  PRIMARY KEY  (xtra_doc_art_id)
) TYPE=MyISAM;