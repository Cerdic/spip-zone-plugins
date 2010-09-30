<?php

function dot2_migrer_commentaires($post_id,$id_article){
	$ressources = sql_select('comment_id,comment_dt,comment_author,comment_email,comment_site,comment_content,comment_ip,comment_status,comment_spam_status','dc_comment','`post_id`='.$post_id);
	$crud = charger_fonction('crud','action');
	while($post =sql_fetch($ressources)){
		$comment_id = $post['comment_id'];
		//Determinons le statut
		if ($post['comment_spam_status'] == 1){
			$statut = 'spam';
		}
		else if ($post['comment_status'] == 1){
			$statut = 'publie';
		}
		else {
			$statut	= 'off';	
		}
		
		// Salir le contenu
		$texte = sale($post['comment_content']);
		
		// Rajouter dans la base
		$resultat = $crud('create','forums','', array(
			'date_heure'=>$post['comment_dt'],
			'id_article'=>$id_article,
			'texte'		=>$texte,
			'auteur'	=>$post['comment_autor'],
			'email_auteur'=>$post['comment_email'],
			'url_site'	=>$post['comment_site'],
			'statut'	=>$statut,
			'ip'		=>$post['comment_ip']
			));
		$id_message = $resultat['result']['id'];
		spip_log("Importation du forum $id_message (ex $comment_id) pour l'article $id_article (ex $post_id)",'dot2_migration_forum');
		
	}
	
}

?>