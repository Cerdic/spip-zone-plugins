-- phpMyAdmin SQL Dump
-- version 2.9.1.1-Debian-3
-- http://www.phpmyadmin.net
-- 
-- Serveur: localhost
-- Généré le : Mercredi 04 Avril 2007 à 18:06
-- Version du serveur: 5.0.32
-- Version de PHP: 5.2.0-8+etch1
-- 
-- Base de données: `spip_192`
-- 

-- --------------------------------------------------------

-- 
-- Structure de la table `spip_produits`
-- 

CREATE TABLE `spip_produits` (
  `id_produit` bigint(20) NOT NULL,
  `token` text NOT NULL,
  `titre` text NOT NULL,
  `soustitre` text NOT NULL,
  `descriptif` text NOT NULL,
  `texte` longblob NOT NULL,
  `logo` text NOT NULL,
  `url_site` varchar(255) NOT NULL,
  `nom_site` tinytext NOT NULL,
  `id_categorie` bigint(20) NOT NULL,
  `id_mot` bigint(20) NOT NULL,
  `id_parent` bigint(20) NOT NULL,
  `id_secteur` bigint(20) NOT NULL,
  `id_gamme` bigint(20) NOT NULL,
  `prix_achat` bigint(20) NOT NULL,
  `prix_vente` bigint(20) NOT NULL,
  `tva` bigint(20) NOT NULL,
  `lang` varchar(10) NOT NULL,
  `lang_choisie` varchar(3) NOT NULL,
  `statut` varchar(10) NOT NULL,
  `date` datetime NOT NULL,
  `date_redac` datetime NOT NULL,
  `date_modif` datetime NOT NULL
) TYPE=MyISAM;
