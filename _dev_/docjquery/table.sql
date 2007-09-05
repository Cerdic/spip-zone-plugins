CREATE TABLE docjquery (
  id int(11) NOT NULL auto_increment,
  reference int(11) NOT NULL default 0,
  nom varchar(100) default NULL,
  params varchar(255) default NULL,
  nbparams int(11) default NULL,
  lang varchar(10) default NULL,
  etat varchar(3) default NULL,
  modif datetime default NULL,
  xml text,
  PRIMARY KEY(id),
  KEY nom(nom,params,lang)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
