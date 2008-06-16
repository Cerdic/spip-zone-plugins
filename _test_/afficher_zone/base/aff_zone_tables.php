<?php 
if (!defined("_ECRIRE_INC_VERSION")) return;

// La table spip_mots_syndic_articles est sensée avoir été crée par le plugin mots_partout
// mais puisque celui-ci est encore loin d'être opérationnel en SVN, on la gère 

// maintenant la table mots_syndic_articles est créée par la dist (on le laisse pour les plus anciennes versions?)

  global $tables_principales;
  global $tables_auxiliaires;

$spip_mots_syndic_articles = array(
	"id_mot" 	=> "bigint(21) NOT NULL",
	"id_syndic_article" 	=> "bigint(21) NOT NULL");

$spip_mots_syndic_articles_key = array(
	"PRIMARY KEY" 	=> "id_syndic_article, id_mot",
	"KEY id_auteur" => "id_mot");

$tables_auxiliaires['spip_mots_syndic_articles'] = array(
	'field' => &$spip_mots_syndic_articles,
	'key' => &$spip_mots_syndic_articles_key);

global $tables_jointures;
$tables_jointures['spip_mots'][] = 'mots_syndic_articles';
$tables_jointures['spip_syndic_articles'][] = 'mots_syndic_articles';


?>