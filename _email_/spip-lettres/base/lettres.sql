-- 
-- Structure de la table `spip_abonnes`
-- 

CREATE TABLE `spip_abonnes` (
  `id_abonne` bigint(21) NOT NULL auto_increment,
  `email` varchar(255) NOT NULL default '',
  `code` varchar(255) NOT NULL default '',
  `format` enum('html','texte','mixte') NOT NULL default 'mixte',
  `maj` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id_abonne`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Structure de la table `spip_abonnes_archives`
-- 

CREATE TABLE `spip_abonnes_archives` (
  `id_abonne` bigint(21) NOT NULL default '0',
  `id_archive` bigint(21) NOT NULL default '0',
  `statut` enum('a_envoyer','envoye','echec') NOT NULL default 'a_envoyer',
  `format` enum('mixte','html','texte') NOT NULL default 'mixte',
  `maj` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id_abonne`,`id_archive`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Structure de la table `spip_abonnes_lettres`
-- 

CREATE TABLE `spip_abonnes_lettres` (
  `id_abonne` bigint(21) NOT NULL default '0',
  `id_lettre` bigint(21) NOT NULL default '0',
  `date_inscription` datetime NOT NULL default '0000-00-00 00:00:00',
  `statut` enum('a_valider','valide') NOT NULL default 'a_valider',
  PRIMARY KEY  (`id_abonne`,`id_lettre`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Structure de la table `spip_archives`
-- 

CREATE TABLE `spip_archives` (
  `id_archive` bigint(21) NOT NULL auto_increment,
  `id_lettre` bigint(21) NOT NULL default '0',
  `titre` text NOT NULL,
  `message_html` longblob NOT NULL,
  `message_texte` longblob NOT NULL,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `nb_emails_envoyes` bigint(21) NOT NULL default '0',
  `nb_emails_non_envoyes` bigint(21) NOT NULL default '0',
  `nb_emails_echec` bigint(21) NOT NULL default '0',
  `date_debut_envoi` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_fin_envoi` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id_archive`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `spip_archives_statistiques`
-- 

CREATE TABLE `spip_archives_statistiques` (
  `id_archive` bigint(21) NOT NULL auto_increment,
  `url` varchar(255) NOT NULL default '',
  `hits` bigint(21) NOT NULL default '0',
  PRIMARY KEY  (`id_archive`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Structure de la table `spip_lettres`
-- 

CREATE TABLE `spip_lettres` (
  `id_lettre` bigint(21) NOT NULL auto_increment,
  `titre` text NOT NULL,
  `descriptif` text NOT NULL,
  `texte` longblob NOT NULL,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `lang` varchar(10) NOT NULL default '',
  `maj` datetime NOT NULL default '0000-00-00 00:00:00',
  `statut` enum('brouillon','publie','envoi_en_cours') NOT NULL default 'brouillon',
  PRIMARY KEY  (`id_lettre`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Structure de la table `spip_mots_lettres`
-- 

CREATE TABLE `spip_mots_lettres` (
  `id_mot` bigint(21) NOT NULL default '0',
  `id_lettre` bigint(21) NOT NULL default '0',
  PRIMARY KEY  (`id_mot`,`id_lettre`),
  KEY `id_mot` (`id_mot`),
  KEY `id_lettre` (`id_lettre`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Structure de la table `spip_auteurs_lettres`
-- 

CREATE TABLE `spip_auteurs_lettres` (
  `id_auteur` bigint(21) NOT NULL default '0',
  `id_lettre` bigint(21) NOT NULL default '0',
  PRIMARY KEY  (`id_auteur`,`id_lettre`),
  KEY `id_auteur` (`id_auteur`),
  KEY `id_lettre` (`id_lettre`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
