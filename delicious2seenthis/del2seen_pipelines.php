<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function del2seen_post_syndication($flux){
	$config = @unserialize($GLOBALS['meta']['del2seen']);
	if ($flux[1] == $config['id_site_delicious'] AND intval($flux[3])) {
		// préparer les données du seen
		$titre = $flux[2]['titre'];
		$link = $flux[2]['url'];
		$comment = $flux[2]['descriptif'];
		foreach($flux[2]['tags'] as $tag) {
			$tag = textebrut($tag);
			if (strlen($tag) > 0) {
				$tags .= "#$tag ";
			}
		}
		// poster le bouzin
		include_spip('inc/seenthis');
		$seenthis = new Seenthis();
		$message = $seenthis->create_message($titre, $link, '', $comment, $tags);
		$rep = $seenthis->post($message);
	}
	return $flux;
}

?>