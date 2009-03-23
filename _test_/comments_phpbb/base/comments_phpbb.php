<?php 
if (!defined("_ECRIRE_INC_VERSION")) return;
global $tables_principales;

function comments_phpbb_declarer_tables_principales($tables_principales){
  
  /* 
      Table utilisée par le plugin (crée à l'install)
    */

    // Définir les champs de la table
    $spip_articles_phpbb = array(
	"id_article" => "BIGINT(21) NOT NULL",
	"topic_id" => "INT(10) NOT NULL"
    );
    // Définir les clés
    $spip_articles_phpbb_key = array(
	"PRIMARY KEY" => "id_article"
    );

    // Associer les clés à la table
    $tables_principales['spip_articles_phpbb'] = array(
      'field' => &$spip_articles_phpbb,
      'key' => &$spip_articles_phpbb_key
    );


  /*
	Déclaration des tables du forum 
    */
    // table des utilisateurs
    $phpbb_users = array(
	    'user_id' => 'mediumint(8) NOT NULL',
	    'username' => 'VARCHAR(255) NOT NULL');
    $phpbb_users_key = array(
	    'PRIMARY KEY' => 'user_id');
    $tables_principales['phpbb_users'] = array(
	    'field' => &$phpbb_users,
	    'key' => &$phpbb_users_key);
    
    // table des forums
    $phpbb_forums = array(
	    'forum_id' => 'mediumint(8) NOT NULL',
	    'forum_name' => 'VARCHAR(255) NOT NULL');
	    
    $phpbb_forums_key = array(
	    'PRIMARY KEY' => 'forum_id');

    $tables_principales['phpbb_forums'] = array(
	    'field' => &$phpbb_forums,
	    'key' => &$phpbb_forums_key);

    // table des posts 
    $phpbb_posts = array(
	    'post_id' => 'mediumint(8) NOT NULL',
	    'topic_id' => 'mediumint(8) NOT NULL',
	    'forum_id' => 'mediumint(8) NOT NULL',
	    'poster_id' => 'mediumint(8) NOT NULL',
	    'post_time' => 'int(11) NOT NULL',
	    'post_username' => 'varchar(255) NOT NULL',
	    'post_subject' => 'varchar(255) NOT NULL',
	    'post_text' => 'mediumtext NOT NULL',
    );

    $phpbb_posts_key = array(
	    "PRIMARY KEY" => "post_id",
	    "KEY topic_id" => "topic_id"
    );


    $tables_principales['phpbb_posts'] = array(
	    'field' => &$phpbb_posts,
	    'key' => &$phpbb_posts_key);

// table des topics
    $phpbb_topics = array(
	    "topic_id" => "mediumint(8) NOT NULL",
	    "forum_id" => "mediumint(8) NOT NULL",
	    "topic_last_post_id" => "mediumint(8) NOT NULL");
	    
    $phpbb_topics_key = array(
	    'PRIMARY KEY' => 'topic_id');

    $tables_principales['phpbb_topics'] = array(
	    'field' => &$phpbb_topics,
	    'key' => &$phpbb_topics_key);
	    
        return $tables_principales;
}

#
function comments_phpbb_declarer_tables_interfaces($interface){
        // definir les jointures possibles
        $interface['tables_jointures']['phpbb_topic'][] = 'spip_articles_phpbb';
        $interface['tables_jointures']['spip_articles_phpbb'][] = 'phpbb_topic';
        return $interface;

}

?>
