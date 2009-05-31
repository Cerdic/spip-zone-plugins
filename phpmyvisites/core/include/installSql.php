<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: installSql.php,v 1.14 2006/01/13 01:24:49 matthieu_ Exp $


$create[] = 
"CREATE TABLE ".T_A_CATEGORY." (
  id int(10) unsigned NOT NULL auto_increment,
  name varchar(100) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM
";

$create[] = 
"CREATE TABLE ".T_A_CONFIG." (
  id int(10) unsigned NOT NULL auto_increment,
  name varchar(100) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM
";


$create[] = 
"CREATE TABLE ".T_A_KEYWORD." (
  id int(10) unsigned NOT NULL auto_increment,
  name text,
  PRIMARY KEY  (id)
) TYPE=MyISAM
";


$create[] = 
"CREATE TABLE ".T_A_NEWSLETTER." (
  id int(11) NOT NULL auto_increment,
  name varchar(60) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM
";

$create[] = 
"CREATE TABLE ".T_A_PAGE." (
  id int(10) unsigned NOT NULL auto_increment,
  name text,
  PRIMARY KEY  (id)
) TYPE=MyISAM
";

$create[] = 
"CREATE TABLE ".T_A_FILE." (
  id int(10) unsigned NOT NULL auto_increment,
  name text,
  PRIMARY KEY (id)
) TYPE=MyISAM
";


$create[] = 
"CREATE TABLE ".T_A_PARTNER_NAME." (
  id int(10) unsigned NOT NULL auto_increment,
  name varchar(100) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM
";


$create[] = 
"CREATE TABLE ".T_A_PARTNER_URL." (
  id int(11) NOT NULL auto_increment,
  name text NOT NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM
";


$create[] = 
"CREATE TABLE ".T_A_PROVIDER." (
  id int(10) unsigned NOT NULL auto_increment,
  name varchar(100) default NULL,
  PRIMARY KEY  (id),
  KEY name (name)
) TYPE=MyISAM
";


$create[] = 
"CREATE TABLE ".T_A_RESOLUTION." (
  id int(11) NOT NULL auto_increment,
  name varchar(20) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM
";


$create[] = 
"CREATE TABLE ".T_A_SEARCH_ENGINE." (
  id int(10) unsigned NOT NULL auto_increment,
  name varchar(100) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM
";


$create[] = 
"CREATE TABLE ".T_A_SITE." (
  id int(10) unsigned NOT NULL auto_increment,
  name text,
  PRIMARY KEY  (id)
) TYPE=MyISAM
";


$create[] = 
"CREATE TABLE ".T_A_VARS_NAME." (
  id int(11) NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM
";


$create[] = 
"CREATE TABLE ".T_A_VARS_VALUE." (
  id int(11) NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM
";

$create[] = 
"CREATE TABLE ".T_ARCHIVES." (
  idarchives int(10) unsigned NOT NULL auto_increment,
  idsite int(10) unsigned NOT NULL default '0',
  done tinyint(4) NOT NULL default '0',
  period tinyint(1) NOT NULL default '0',
  `simple` tinyint(4) NOT NULL default '0',
  date1 varchar(10) NOT NULL default '00:00:00',
  date2 varchar(10) NOT NULL default '00:00:00',
  nb_uniq_vis mediumint(8) unsigned NOT NULL default '0',
  nb_vis mediumint(8) unsigned NOT NULL default '0',
  nb_vis_returning int(8) NOT NULL default '0',
  nb_uniq_vis_returning int(11) NOT NULL default '0',
  nb_pag mediumint(8) unsigned NOT NULL default '0',
  nb_pag_returning int(8) NOT NULL default '0',
  nb_uniq_pag smallint(5) unsigned NOT NULL default '0',
  nb_max_pag smallint(5) unsigned NOT NULL default '0',
  nb_vis_1pag mediumint(8) unsigned NOT NULL default '0',
  nb_vis_1pag_returning mediumint(8) NOT NULL default '0',
  sum_vis_lth int(10) unsigned NOT NULL default '0',
  sum_vis_lth_returning int(10) NOT NULL default '0',
  nb_direct mediumint(8) unsigned NOT NULL default '0',
  nb_search_engine mediumint(8) unsigned NOT NULL default '0',
  nb_site mediumint(8) unsigned NOT NULL default '0',
  nb_newsletter mediumint(8) unsigned NOT NULL default '0',
  nb_partner mediumint(8) unsigned NOT NULL default '0',
  vis_period longtext,
  vis_nb_vis text,
  vis_st text,
  vis_lt text,
  pag_st text,
  pag_lt text,
  vis_lth text,
  vis_nb_pag text,
  vis_pag_grp longtext,
  vis_country text,
  vis_continent text,
  vis_provider longtext,
  vis_config longtext,
  vis_os text,
  vis_browser text,
  vis_browser_type text,
  vis_resolution longtext,
  vis_plugin text,
  vis_depth text,
  vis_search_engine longtext,
  vis_keyword longtext,
  vis_site longtext,
  vis_partner longtext,
  vis_newsletter longtext,
  int_lt text,
  int_st text,
  int_referer_type longtext,
  int_search_engine longtext,
  int_keyword longtext,
  int_site longtext,
  int_partner longtext,
  int_newsletter longtext,
  int_country longtext,
  int_continent longtext,
  int_provider longtext,
  int_config longtext,
  int_depth longtext,
  int_os longtext,
  int_browser longtext,
  int_resolution longtext,
  `compressed` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (idarchives),
  KEY pmvindex1 (idsite),
  KEY pmvindex2 (done)
) TYPE=MyISAM
";


$create[] = 
"CREATE TABLE ".T_CATEGORY." (
  idcategory int(10) unsigned NOT NULL auto_increment,
  complete_name varchar(255) NOT NULL default '',
  name varchar(20) default NULL,
  level smallint(5) unsigned NOT NULL default '0',
  idparent smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (idcategory)
) TYPE=MyISAM
";


$create[] = 
"CREATE TABLE ".T_IP_IGNORE." (
  idip_ignore int(10) unsigned NOT NULL auto_increment,
  idsite int(10) unsigned NOT NULL default '0',
  ip_min int(11) default NULL,
  ip_max int(11) default NULL,
  PRIMARY KEY  (idip_ignore),
  KEY pmvindex (idsite)
) TYPE=MyISAM
";


$create[] = 
"CREATE TABLE ".T_LINK_VP." (
  idlink_vp int(11) NOT NULL auto_increment,
  idvisit int(10) unsigned NOT NULL default '0',
  idpage int(10) unsigned NOT NULL default '0',
  idpage_ref int(11) unsigned NOT NULL default '0',
  total_time_page_ref int(10) unsigned default NULL,
  PRIMARY KEY  (idlink_vp),
  KEY pmvindex (idvisit,idpage)
) TYPE=MyISAM
";


$create[] = 
"CREATE TABLE ".T_LINK_VPV." (
  idlink_vp int(11) NOT NULL default '0',
  idvars int(11) NOT NULL default '0',
  PRIMARY KEY  (idlink_vp,idvars)
) TYPE=MyISAM
";


$create[] = 
"CREATE TABLE ".T_NEWSLETTER." (
  idnewsletter int(10) unsigned NOT NULL auto_increment,
  idsite int(10) unsigned NOT NULL default '0',
  name varchar(90) default NULL,
  PRIMARY KEY  (idnewsletter),
  KEY pmvindex (idsite)
) TYPE=MyISAM
";


$create[] = 
"
CREATE TABLE ".T_PAGE." (
  idpage int(10) unsigned NOT NULL auto_increment,
  idcategory int(10) unsigned NOT NULL default '0',
  name varchar(255) default NULL,
  PRIMARY KEY  (idpage),
  KEY pmvindex (idcategory)
) TYPE=MyISAM
";

$create[] = 
"CREATE TABLE ".T_PAGE_MD5URL." (
  idpage_md5url int(10) unsigned NOT NULL auto_increment,
  idpage int(10) unsigned NOT NULL default '0',
  md5url char(32) default NULL,
  idpage_url int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (idpage_md5url),
  KEY idpage (idpage),
  KEY url (md5url)
) TYPE=MyISAM 
";

$create[] = 
"
CREATE TABLE ".T_PAGE_URL." (
  idpage_url int(10) unsigned NOT NULL auto_increment,
  url text,
  PRIMARY KEY  (idpage_url)
) TYPE=MyISAM
";


$create[] = 
"CREATE TABLE ".T_PATH." (
  idpath int(11) NOT NULL auto_increment,
  idvisit int(11) NOT NULL default '0',
  sequence varchar(255) NOT NULL default '',
  PRIMARY KEY  (idpath)
) TYPE=MyISAM
";


$create[] = 
"CREATE TABLE ".T_QUERY_LOG." (
  idquery_log int(11) NOT NULL auto_increment,
  idsite int(11) NOT NULL default '0',
  query smallint(6) NOT NULL default '0',
  time float NOT NULL default '0',
  date date NOT NULL default '0000-00-00',
  daytime time NOT NULL default '00:00:00',
  PRIMARY KEY  (idquery_log)
) TYPE=MyISAM
";


$create[] = 
"CREATE TABLE ".T_SITE." (
  idsite int(10) unsigned NOT NULL auto_increment,
  name varchar(90) default NULL,
  logo varchar(15) default NULL,
  params_choice varchar(6) NOT NULL default 'all',
  params_names varchar(255) NOT NULL default '',
  PRIMARY KEY  (idsite)
) TYPE=MyISAM
";


$create[] = 
"CREATE TABLE ".T_SITE_PARTNER." (
  idsite_partner int(10) unsigned NOT NULL auto_increment,
  idsite int(10) unsigned NOT NULL default '0',
  name varchar(90) default NULL,
  PRIMARY KEY  (idsite_partner),
  KEY pmvindex (idsite)
) TYPE=MyISAM
";


$create[] = 
"CREATE TABLE ".T_SITE_PARTNER_URL." (
  idsite_partner_url int(10) unsigned NOT NULL auto_increment,
  idsite_partner int(10) unsigned NOT NULL default '0',
  url varchar(200) default NULL,
  PRIMARY KEY  (idsite_partner_url)
) TYPE=MyISAM
";


$create[] = 
"CREATE TABLE ".T_SITE_URL." (
  idsite_url int(10) unsigned NOT NULL auto_increment,
  idsite int(10) unsigned NOT NULL default '0',
  url varchar(255) default NULL,
  PRIMARY KEY  (idsite_url),
  KEY pmvindex (idsite)
) TYPE=MyISAM
";


$create[] = 
"CREATE TABLE ".T_VARS." (
  idvars int(10) unsigned NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  int_value int(10) default NULL,
  varchar_value varchar(255) default NULL,
  PRIMARY KEY  (idvars),
  KEY pmvindex (name)
) TYPE=MyISAM
";


$create[] = 
"CREATE TABLE ".T_VERSION." (
  `version` varchar(255) NOT NULL default ''
) TYPE=MyISAM
";


$create[] = 
"CREATE TABLE ".T_VISIT." (
  idvisit int(10) unsigned NOT NULL auto_increment,
  idsite int(10) unsigned NOT NULL default '0',
  idcookie varchar(32) default NULL,
  returning tinyint(1) NOT NULL default '0',
  last_visit_time time NOT NULL default '00:00:00',
  server_date date default NULL,
  server_time time NOT NULL default '00:00:00',
  referer text,
  os char(3) default NULL,
  browser_name varchar(10) NOT NULL default '',
  browser_version varchar(20) NOT NULL default '',
  resolution varchar(9) default NULL,
  color_depth tinyint(2) unsigned default NULL,
  pdf tinyint(1) NOT NULL default '0',
  flash tinyint(1) NOT NULL default '0',
  java tinyint(1) NOT NULL default '0',
  director tinyint(1) NOT NULL default '0',
  quicktime tinyint(1) NOT NULL default '0',
  realplayer tinyint(1) NOT NULL default '0',
  windowsmedia tinyint(1) NOT NULL default '0',
  local_time time NOT NULL default '00:00:00',
  ip int(10) default NULL,
  hostname_ext varchar(100) default NULL,
  browser_lang varchar(60) default NULL,
  total_pages smallint(5) unsigned default NULL,
  total_time smallint(5) unsigned default NULL,
  country char(3) default NULL,
  continent char(3) default NULL,
  exit_idpage int(11) NOT NULL default '0',
  entry_idpage int(11) NOT NULL default '0',
  entry_idpageurl int(11) NOT NULL default '0',
  md5config varchar(32) NOT NULL default '',
  PRIMARY KEY  (idvisit),
  KEY idsite (idsite),
  KEY server_date (server_date),
  KEY md5config (md5config)
) TYPE=MyISAM 
";


$create[] = 
"CREATE TABLE ".T_GROUPS." (
  `idgroups` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(40) default NULL,
  `view` tinyint(1) unsigned default '0',
  `admin` tinyint(3) unsigned default '0',
  PRIMARY KEY  (`idgroups`)
) TYPE=MyISAM
";


$create[] = 
"CREATE TABLE ".T_USERS." (
  `login` varchar(20) NOT NULL default '',
  `password` varchar(255) default NULL,
  `alias` varchar(45) default NULL,
  `email` varchar(100) NOT NULL default '',
  `send_mail` int( 10 ) default NULL,
  `rss_hash` varchar(100) NOT NULL default '',
  `date_registered` int(11) default NULL,
  PRIMARY KEY  (`login`)
) TYPE=MyISAM
";



$create[] = 
"CREATE TABLE ".T_USERS_LINK_GROUPS." (
  `idsite` int(10) unsigned NOT NULL default '0',
  `idgroups` int(10) unsigned NOT NULL default '0',
  `login` varchar(20) NOT NULL default '0',
  PRIMARY KEY  (`idsite`,`idgroups`,`login`)
) TYPE=MyISAM
";


$create[] = 
	"INSERT INTO ".T_GROUPS." VALUES (1, 'admin', 1, 1)"
;

$create[] = 
	"INSERT INTO ".T_GROUPS." VALUES (2, 'view', 1, 0)"
;

$create[] = 
	"INSERT INTO ".T_USERS." VALUES ('anonymous', NULL, 'Anonymous user', '', 0, 'ffffffffffffff493e8d55a4a75de3f90a1', NULL)"
	;
	
$create[] = 
	"INSERT INTO ".T_VERSION." VALUES ( '".PHPMV_VERSION."')"
	;


?>