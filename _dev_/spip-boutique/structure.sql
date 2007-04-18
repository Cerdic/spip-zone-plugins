
-- 
-- Structure de la table `spip_boutique_auteurs_categories`
-- 

CREATE TABLE `spip_boutique_auteurs_categories` (
  `id_auteur` bigint(20) NOT NULL,
  `id_categorie` bigint(20) NOT NULL,
  PRIMARY KEY  (`id_auteur`,`id_categorie`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Structure de la table `spip_boutique_auteurs_produits`
-- 

CREATE TABLE `spip_boutique_auteurs_produits` (
  `id_auteur` bigint(20) NOT NULL,
  `id_produit` bigint(20) NOT NULL,
  PRIMARY KEY  (`id_auteur`,`id_produit`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Structure de la table `spip_boutique_categories`
-- 

CREATE TABLE `spip_boutique_categories` (
  `id_categorie` bigint(20) NOT NULL auto_increment,
  `titre` text NOT NULL,
  `descriptif` text NOT NULL,
  `texte` longblob NOT NULL,
  `logo` text NOT NULL,
  `lang` varchar(10) NOT NULL,
  `id_parent` bigint(20) NOT NULL,
  `id_secteur` bigint(20) NOT NULL,
  `date` datetime NOT NULL,
  `date_modif` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id_categorie`)
) TYPE=MyISAM ;

-- --------------------------------------------------------

-- 
-- Structure de la table `spip_boutique_mots_categories`
-- 

CREATE TABLE `spip_boutique_mots_categories` (
  `id_mot` bigint(20) NOT NULL,
  `id_categorie` bigint(20) NOT NULL,
  PRIMARY KEY  (`id_mot`,`id_categorie`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Structure de la table `spip_boutique_mots_produits`
-- 

CREATE TABLE `spip_boutique_mots_produits` (
  `id_mot` bigint(20) NOT NULL,
  `id_produit` bigint(20) NOT NULL,
  PRIMARY KEY  (`id_mot`,`id_produit`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Structure de la table `spip_boutique_produits`
-- 

CREATE TABLE `spip_boutique_produits` (
  `id_produit` bigint(20) NOT NULL auto_increment,
  `token` text NOT NULL,
  `titre` text NOT NULL,
  `soustitre` text NOT NULL,
  `descriptif` text NOT NULL,
  `texte` longblob NOT NULL,
  `logo` text NOT NULL,
  `url_site` varchar(255) NOT NULL,
  `nom_site` tinytext NOT NULL,
  `id_categorie` bigint(20) NOT NULL,
  `id_parent` bigint(20) NOT NULL,
  `id_secteur` bigint(20) NOT NULL,
  `id_gamme` bigint(20) NOT NULL,
  `prix_achat` float NOT NULL,
  `prix_vente` float NOT NULL,
  `tva` float NOT NULL,
  `lang` varchar(10) NOT NULL,
  `lang_choisie` varchar(3) NOT NULL,
  `statut` varchar(10) NOT NULL,
  `date` datetime NOT NULL,
  `date_redac` datetime NOT NULL,
  `date_modif` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id_produit`)
) TYPE=MyISAM ;
