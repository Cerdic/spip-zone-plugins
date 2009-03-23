<?php

// Update des propriétés du forum
function update_forum($fid)
{
	// Nombre de topics et nombre de réponses pour le forum
	$where = array('forum_id='.sql_quote($fid));
	$result = sql_select(array('COUNT(topic_id) AS total_topics','SUM(topic_replies) AS total_replies'),PHPBB_PREFIX.'topics', $where);
	if($res = sql_fetch($result))
	{
		$num_posts = $res['total_topics'] + $res['total_replies'];
		
		/* Les infos du forum sont synchronisés avec celles du topic */
		$result = sql_select(array('topic_last_post_id',"topic_last_poster_name","topic_last_post_subject","topic_last_post_time"), PHPBB_PREFIX."topics", "forum_id=".intval($fid), "", "topic_last_post_time DESC","1");
 		if (sql_count($result)) {
		    $topic = sql_fetch($result);
			sql_updateq(PHPBB_PREFIX.'forums',
 			array(
 			 'forum_posts' => $res['total_topics'], 
 			 'forum_topics' => $num_posts, 
 			 'forum_last_post_id' => $topic['topic_last_post_id'], 
 			 'forum_last_post_subject' => $topic['topic_last_post_subject'], 
 			 'forum_last_poster_name' => $topic['topic_last_poster_name'],
 			 'forum_last_post_time' => $topic['topic_last_post_time']
 			 ), 
 			 "forum_id=".intval($fid));
		}
		else		 
			sql_updateq(PHPBB_PREFIX.'forums', array('forum_topics' => '0', 'num_posts' => '0', 'last_post' => 'NULL', 'last_poster' => 'NULL'), 'forum_id ='.sql_quote($fid));
//			spip_query('UPDATE '.PHPBB_BASE.'.'.PHPBB_PREFIX.'forums SET num_topics=0, num_posts=0, last_post=NULL, last_post_id=NULL, last_poster=NULL WHERE forum_id='.$fid);
	}

		
}
?>
