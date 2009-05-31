-- phpMyAdmin SQL Dump
-- version 2.10.3deb1ubuntu0.2
-- http://www.phpmyadmin.net
-- 
-- Serveur: localhost
-- Généré le : Lun 17 Mars 2008 à 23:29
-- Version du serveur: 5.0.45
-- Version de PHP: 5.2.3-1ubuntu6.3
-- 
-- Creation des tables siou
-- 

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Base de données: `officebac`
-- 

-- --------------------------------------------------------

-- 
-- Structure de la table `odb_candidats`
-- 

CREATE TABLE IF NOT EXISTS `odb_candidats` (
  `id_saisie` int(11) NOT NULL default '0',
  `id_table` varchar(31) NOT NULL default '',
  `id_table_old` varchar(31) NOT NULL,
  `annee` year(4) NOT NULL default '0000',
  `serie` int(3) NOT NULL default '0',
  `prefixe` int(3) default NULL,
  `nom` varchar(63) NOT NULL default '',
  `prenoms` varchar(63) NOT NULL default '',
  `ne_le` date NOT NULL default '0000-00-00',
  `ne_en` year(4) default NULL,
  `ne_vers` year(4) default NULL,
  `ldn` varchar(63) NOT NULL default '',
  `sexe` int(3) NOT NULL default '0',
  `nationalite` smallint(6) NOT NULL,
  `pdn` int(11) NOT NULL,
  `lv1` int(3) NOT NULL default '0',
  `lv2` int(3) default NULL,
  `eps` int(3) NOT NULL default '0',
  `ef1` int(3) default NULL,
  `ef2` int(3) default NULL,
  `ville` smallint(6) NOT NULL,
  `departement` int(3) NOT NULL default '0',
  `etablissement` smallint(6) NOT NULL default '0',
  `ajourne` tinyint(1) NOT NULL default '0',
  `non_inscrit` tinyint(1) NOT NULL default '0',
  `maj` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `login` varchar(31) default NULL,
  PRIMARY KEY  (`id_saisie`,`annee`),
  KEY `id_table` (`id_table`),
  KEY `serie` (`serie`),
  KEY `departement` (`departement`),
  KEY `etablissement` (`etablissement`),
  KEY `ville` (`ville`),
  KEY `nom` (`nom`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Import candidats';

-- --------------------------------------------------------

-- 
-- Structure de la table `odb_decisions`
-- 

CREATE TABLE IF NOT EXISTS `odb_decisions` (
  `id_table` varchar(31) NOT NULL,
  `id_anonyme` mediumint(9) NOT NULL,
  `annee` year(4) NOT NULL,
  `moyenne` decimal(5,3) NOT NULL,
  `coeff` smallint(6) default NULL,
  `delib1` enum('Ajourne','Absent','Refuse','Admissible','Reserve','Passable','Abien','Bien','TBien') default NULL,
  `delib2` enum('','Oral','Reserve','Passable','Abien','Bien','TBien') NOT NULL,
  `delib3` enum('-','Passable','Reserve','Refuse') NOT NULL default '-',
  PRIMARY KEY  (`id_table`,`annee`),
  UNIQUE KEY `id_anonyme` (`id_anonyme`,`annee`),
  KEY `id_decision` (`delib1`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Decisions sur les candidats';

-- --------------------------------------------------------

-- 
-- Structure de la table `odb_histo_candidats`
-- 

CREATE TABLE IF NOT EXISTS `odb_histo_candidats` (
  `id_saisie` int(11) NOT NULL default '0',
  `id_table` varchar(31) NOT NULL default '',
  `id_table_old` varchar(31) NOT NULL,
  `annee` year(4) NOT NULL default '0000',
  `serie` int(3) NOT NULL default '0',
  `prefixe` int(3) default NULL,
  `nom` varchar(63) NOT NULL default '',
  `prenoms` varchar(63) NOT NULL default '',
  `ne_le` date NOT NULL default '0000-00-00',
  `ne_en` year(4) default NULL,
  `ne_vers` year(4) default NULL,
  `ldn` varchar(63) NOT NULL default '',
  `sexe` int(3) NOT NULL default '0',
  `nationalite` smallint(6) NOT NULL default '0',
  `pdn` int(11) NOT NULL,
  `lv1` int(3) NOT NULL default '0',
  `lv2` int(3) default NULL,
  `eps` int(3) NOT NULL default '0',
  `ef1` int(3) default NULL,
  `ef2` int(3) default NULL,
  `ville` smallint(6) NOT NULL default '0',
  `departement` int(3) NOT NULL default '0',
  `etablissement` smallint(6) NOT NULL default '0',
  `ajourne` tinyint(1) NOT NULL default '0',
  `non_inscrit` tinyint(1) NOT NULL default '0',
  `maj` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `login` varchar(31) NOT NULL default '',
  PRIMARY KEY  (`maj`),
  KEY `id_table` (`id_table`),
  KEY `serie` (`serie`),
  KEY `departement` (`departement`),
  KEY `etablissement` (`etablissement`),
  KEY `ville` (`ville`),
  KEY `nom` (`nom`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Import candidats';

-- --------------------------------------------------------

-- 
-- Structure de la table `odb_histo_notes`
-- 

CREATE TABLE IF NOT EXISTS `odb_histo_notes` (
  `id` int(11) NOT NULL auto_increment,
  `id_table` varchar(31) NOT NULL default '',
  `id_anonyme` tinytext NOT NULL,
  `annee` year(4) NOT NULL default '0000',
  `id_matiere` smallint(6) NOT NULL default '0',
  `note` tinyint(4) default NULL,
  `type` enum('','Oral','Ecrit','Pratique') NOT NULL,
  `coeff` tinyint(4) NOT NULL,
  `operateur` varchar(12) NOT NULL,
  `maj` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `id_etablissement` (`id_matiere`),
  KEY `id_salle` (`note`),
  KEY `maj` (`maj`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Notes des candidats (historique)' AUTO_INCREMENT=6670 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `odb_notes`
-- 

CREATE TABLE IF NOT EXISTS `odb_notes` (
  `id_table` char(15) NOT NULL,
  `id_anonyme` mediumint(6) NOT NULL,
  `annee` year(4) NOT NULL default '0000',
  `id_serie` smallint(6) NOT NULL,
  `jury` smallint(6) NOT NULL,
  `id_matiere` smallint(6) NOT NULL default '0',
  `note` tinyint(4) default NULL,
  `type` enum('','Oral','Ecrit','Pratique','Divers') NOT NULL,
  `coeff` tinyint(4) NOT NULL,
  `operateur` varchar(12) NOT NULL,
  `maj` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id_table`,`annee`,`id_matiere`,`type`),
  UNIQUE KEY `id_anonyme_2` (`id_anonyme`,`annee`,`id_matiere`,`type`),
  KEY `id_etablissement` (`id_matiere`),
  KEY `id_salle` (`note`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Notes des candidats';

-- --------------------------------------------------------

-- 
-- Structure de la table `odb_param`
-- 

CREATE TABLE IF NOT EXISTS `odb_param` (
  `param` varchar(31) NOT NULL default '',
  `valeur` longtext NOT NULL,
  PRIMARY KEY  (`param`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Parametres OBD';

-- --------------------------------------------------------

-- 
-- Structure de la table `odb_ref_deliberation`
-- 

CREATE TABLE IF NOT EXISTS `odb_ref_deliberation` (
  `id` int(11) NOT NULL auto_increment,
  `deliberation` varchar(63) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `ville` (`deliberation`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Centres de deliberation' AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `odb_ref_departement`
-- 

CREATE TABLE IF NOT EXISTS `odb_ref_departement` (
  `id` int(11) NOT NULL auto_increment,
  `departement` varchar(63) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `departement` (`departement`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Departements' AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `odb_ref_ecole`
-- 

CREATE TABLE IF NOT EXISTS `odb_ref_ecole` (
  `id` int(11) NOT NULL auto_increment,
  `ecole` varchar(63) NOT NULL,
  `commentaire` text NOT NULL,
  `id_serie` tinyint(4) NOT NULL,
  `id_matiere1` tinyint(4) NOT NULL,
  `id_matiere2` tinyint(4) NOT NULL,
  `id_matiere3` tinyint(4) NOT NULL,
  `id_matiere4` tinyint(4) NOT NULL,
  `coeff1` tinyint(4) NOT NULL,
  `coeff2` tinyint(4) NOT NULL,
  `coeff3` tinyint(4) NOT NULL,
  `coeff4` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Ecoles (concours)' AUTO_INCREMENT=22 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `odb_ref_ef`
-- 

CREATE TABLE IF NOT EXISTS `odb_ref_ef` (
  `id` int(11) NOT NULL auto_increment,
  `ef` varchar(63) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `valeur` (`ef`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Epreuves facultatives' AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `odb_ref_eps`
-- 

CREATE TABLE IF NOT EXISTS `odb_ref_eps` (
  `id` int(11) NOT NULL auto_increment,
  `eps` varchar(63) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `valeur` (`eps`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Epreuves physiques et sportives' AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `odb_ref_etablissement`
-- 

CREATE TABLE IF NOT EXISTS `odb_ref_etablissement` (
  `id` int(11) NOT NULL auto_increment,
  `etablissement` varchar(63) NOT NULL default '',
  `id_ville` smallint(6) NOT NULL default '0',
  `id_centre` smallint(6) NOT NULL default '0',
  `id_departement` smallint(6) NOT NULL default '0',
  `annee_centre` year(4) NOT NULL default '0000',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Etablissements' AUTO_INCREMENT=493 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `odb_ref_examen`
-- 

CREATE TABLE IF NOT EXISTS `odb_ref_examen` (
  `id` int(11) NOT NULL auto_increment,
  `annee` year(4) NOT NULL,
  `id_serie` int(11) NOT NULL default '0',
  `id_matiere` int(11) NOT NULL default '0',
  `examen` datetime NOT NULL default '0000-00-00 00:00:00',
  `type` enum('','Pratique','Ecrit','Oral','Divers') NOT NULL,
  `duree` float unsigned NOT NULL default '0',
  `coeff` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`,`annee`),
  KEY `id_serie` (`id_serie`,`id_matiere`,`annee`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Date examens' AUTO_INCREMENT=142 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `odb_ref_lv`
-- 

CREATE TABLE IF NOT EXISTS `odb_ref_lv` (
  `id` int(11) NOT NULL auto_increment,
  `lv` varchar(63) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `valeur` (`lv`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Langues vivantes' AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `odb_ref_matiere`
-- 

CREATE TABLE IF NOT EXISTS `odb_ref_matiere` (
  `id` int(11) NOT NULL auto_increment,
  `matiere` varchar(63) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Liste des matieres' AUTO_INCREMENT=101 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `odb_ref_operateur`
-- 

CREATE TABLE IF NOT EXISTS `odb_ref_operateur` (
  `id` int(11) NOT NULL auto_increment,
  `annee` year(4) NOT NULL,
  `operateur` varchar(12) NOT NULL,
  `mot_passe` varchar(4) NOT NULL,
  `id_deliberation` smallint(6) NOT NULL default '0',
  `jury1` smallint(6) default NULL,
  `jury2` smallint(6) default NULL,
  `jury3` smallint(6) default NULL,
  `jury4` smallint(6) default NULL,
  PRIMARY KEY  (`id`,`annee`),
  UNIQUE KEY `ville` (`operateur`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Operateurs de saisie' AUTO_INCREMENT=66 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `odb_ref_pays`
-- 

CREATE TABLE IF NOT EXISTS `odb_ref_pays` (
  `id` int(11) NOT NULL auto_increment,
  `pays` varchar(63) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `pays` (`pays`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Departements' AUTO_INCREMENT=194 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `odb_ref_prefixe`
-- 

CREATE TABLE IF NOT EXISTS `odb_ref_prefixe` (
  `id` int(11) NOT NULL auto_increment,
  `prefixe` char(3) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `valeur` (`prefixe`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Prefixe (particule du nom)' AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `odb_ref_salle`
-- 

CREATE TABLE IF NOT EXISTS `odb_ref_salle` (
  `id` int(11) NOT NULL auto_increment,
  `annee` year(4) NOT NULL,
  `salle` varchar(15) NOT NULL default '',
  `id_etablissement` smallint(6) NOT NULL default '0',
  `nb_salles` tinyint(4) NOT NULL default '0',
  `capacite` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`,`annee`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Capacite de chaque salle pour chaque etablissement' AUTO_INCREMENT=106 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `odb_ref_serie`
-- 

CREATE TABLE IF NOT EXISTS `odb_ref_serie` (
  `id` int(11) NOT NULL auto_increment,
  `serie` char(3) NOT NULL default '',
  `libelle` varchar(36) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `valeur` (`serie`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Serie' AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `odb_ref_sexe`
-- 

CREATE TABLE IF NOT EXISTS `odb_ref_sexe` (
  `id` int(11) NOT NULL auto_increment,
  `sexe` char(3) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `valeur` (`sexe`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Sexe' AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `odb_ref_ville`
-- 

CREATE TABLE IF NOT EXISTS `odb_ref_ville` (
  `id` int(11) NOT NULL auto_increment,
  `ville` varchar(63) NOT NULL default '',
  `id_departement` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `ville` (`ville`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Villes' AUTO_INCREMENT=76 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `odb_repartition`
-- 

CREATE TABLE IF NOT EXISTS `odb_repartition` (
  `id_saisie` int(11) NOT NULL default '0',
  `id_table` char(15) NOT NULL,
  `id_anonyme` tinytext NOT NULL,
  `id_retrait` int(11) NOT NULL COMMENT 'Numero de retrait de l''attestation',
  `jury` smallint(6) default NULL,
  `annee` year(4) NOT NULL default '0000',
  `id_etablissement` smallint(6) NOT NULL default '0',
  `id_salle` int(11) NOT NULL default '0',
  `num_salle` smallint(6) NOT NULL default '0' COMMENT 'Numéro de la salle',
  `numero` smallint(6) NOT NULL default '0' COMMENT 'Numéro du candidat dans la salle',
  PRIMARY KEY  (`id_saisie`,`annee`),
  UNIQUE KEY `id_table` (`id_table`,`annee`),
  KEY `id_etablissement` (`id_etablissement`),
  KEY `id_salle` (`id_salle`),
  KEY `jury` (`jury`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
