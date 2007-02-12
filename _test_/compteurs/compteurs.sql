CREATE TABLE `compteurs` (
  `type` char(50) NOT NULL default '',
  `id` char(200) NOT NULL default '',
  `categ` char(50) NOT NULL default '',
  `total` int(10) NOT NULL default '0',
  `nb` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`type`,`id`,`categ`)
);
