<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function comments_phpbb_new($flux)
{

	if(isset($flux['args']['table']) && $flux['args']['table'] == $GLOBALS['table_prefix'].'_'.table_objet('article'))
	{
		$col_id = id_table_objet('article');
		
	      $result = sql_select(array('titre','chapo','statut','id_secteur','date'),$flux['args']['table'],$col_id."='".$flux['args']['id_objet']."'");

		// l'article existe
		if ($article = sql_fetch($result))
		{
			if(!function_exists('update_forum'))
				include_spip('action/comments_phpbb_update');

			if($article['statut'] == 'publie')
			{
				

				if(!function_exists('traiter_raccourcis'))
					include_spip('inc/texte');
				if(!function_exists('textebrut'))
					include_spip('inc/filtres');
            if(!function_exists('_T'))
            include_spip('inc/utils');
            if(!function_exists('balise_INTRODUCTION_dist'))
            include_spip('public/balises');  

				$texte_post = textebrut($article['chapo']);
				/* TODO : problème avec les quotes qui sont convertis dans le post phpbb */
				$texte_post .= "<br /><a href=/spip.php?article".$flux['args']['id_objet'].">Lire la news</a>";
				$titre_post =  textebrut($article['titre']);
				
            if(!function_exists('datetime_mysql2unix'))
            include_spip('comments_phpbb_fonctions');
            
            $date = datetime_mysql2unix($article['date']);
				$forum_id = intval(lire_config('comments_phpbb/phpbb_forum'));
            $result = sql_select(
                  array('a.topic_id', 'b.topic_first_post_id'),
                  array(ARTICLES_PHPBB_TABLE." AS a",PHPBB_PREFIX."topics as b"),
                  array("id_article='".$flux['args']['id_objet']."'", "b.topic_id=a.topic_id"));
                  
            // L'article est modifié, on fait l'update de la table topics et on déplace dans le bon forum
				if($article_phpbb = sql_fetch($result))
				{
					$article_phpbb['topic_id'] = intval($article_phpbb['topic_id']);
					 sql_updateq(PHPBB_PREFIX.'topics',array(
					    "topic_title" => $titre_post,
					    "forum_id" => $forum_id,
                   'topic_time' => $date
					 ),
					 "topic_id='".$article_phpbb['topic_id']."'");
					
					 sql_updateq(PHPBB_PREFIX.'posts',array(
					     "post_text" => $texte_post,
					     "post_edit_user" => $GLOBALS['auteur_session']['nom'],
                    'post_time' => $date),
					     "post_id=".intval($article_phpbb['topic_first_post_id'])
					     
					  );
					

					update_forum($forum_id);
					update_forum(intval(lire_config('comments_phpbb/phpbb_tmpforum')));
				}
				// Il s'agit d'un nouvel article, on enregistre un nouveau topic et une entrée dans articles_phpbb
				else
				{
					$poster_id = intval(lire_config('comments_phpbb/phpbb_poster'));
					
					$query = sql_select("user_id,username",PHPBB_PREFIX.'users',"user_id='".$poster_id."'");
					$res = sql_fetch($query);
					$insert = array(
					      'topic_poster' => $res['user_id'],
					      'topic_title' => $titre_post,
					      'topic_first_poster_name' => $res['username'],
					      'topic_time' => $date,
					      'topic_poster' => $poster_id,
					      'topic_last_post_time' => $date,
					      'topic_last_poster_id' => $poster_id,
					      'topic_last_post_subject' => $titre_post,
					      'topic_last_poster_name' => $res['username'],
					      'forum_id' => $forum_id,
					      );
					$topic_id = sql_insertq(PHPBB_PREFIX.'topics',$insert);

					$post_id = sql_insertq(PHPBB_PREFIX.'posts',array(
						'poster_id' => $res['user_id'],
						'forum_id' => $forum_id,
						'post_username' => $res['username'],
						'post_text' => $texte_post,
						'post_time' => $date,
						'topic_id' => $topic_id));
				
					
					// update de la table topics du forum
					sql_updateq(PHPBB_PREFIX.'topics',array("topic_last_post_time"=>$date,"topic_first_post_id"=>$post_id,"topic_last_post_time"=>$date, "topic_last_post_id" => $topic_id),"topic_id='".$topic_id."'");
	        	   
               // nouvelle entrée dans articles_phpbb ou mise à jour si enregistrement existant
               $result = sql_select('topic_id',ARTICLES_PHPBB_TABLE, "id_article=".$flux['args']['id_objet']);
               
               if($article_phpbb = sql_fetch($result)) {
                  sql_updateq(ARTICLES_PHPBB_TABLE,array('topic_id'=>$topic_id), "id_article=".$flux['args']['id_objet']);
               }
               else {
                  sql_insertq(ARTICLES_PHPBB_TABLE,array('id_article'=>$flux['args']['id_objet'],'topic_id'=>$topic_id),"");
               }	
               
               // Update des infos de l'utilisateur robot
					sql_updateq(PHPBB_PREFIX.'users', array("user_posts"=>"user_posts+1","user_lastpost_time"=>$date),"user_id=".intval($poster_id));
	        	        	
					update_forum($forum_id);
					update_forum(intval(lire_config('comments_phpbb/phpbb_tmpforum')));
					
					
					
				}
				
			}
			// Statut différent de "publié", on déplace le topic dans le forum temporaire
			else
			{
				$result = sql_select('topic_id',ARTICLES_PHPBB_TABLE, "id_article=".$flux['args']['id_objet']);

				if($article_phpbb = sql_fetch($result))
				{
					$forum_id = intval(lire_config('comments_phpbb/phpbb_tmpforum'));
					sql_updateq(PHPBB_PREFIX.'topics',array("forum_id"=>$forum_id),"topic_id=".intval($article_phpbb['topic_id']));
					
					
					update_forum($forum_id);
					update_forum(intval(lire_config('comments_phpbb/phpbb_forum')));
				}
			}
		} 
	}

	return $flux;
}
?>
