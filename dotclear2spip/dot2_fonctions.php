<?php

function dot2_migrer_mot_article($id_post,$id_article,$id_groupe=''){
	$crud = charger_fonction('crud','action');
	
	#créer le groupe le cas échéant
	if ($id_groupe == ''){
		$id_groupe = sql_insertq('spip_groupes_mots',array('titre'=>_L('tags de dotclear'),'unseul'=>'non','obligatoire'=>'non','table_liees'=>'articles','minirezo'=>'oui','comite'=>'oui','forum'=>'non'));
		spip_log("Création du groupe $id_groupe",'dot2');	
	}
	
	#les tags associés à l'articles
	$tags_post = sql_select('meta_id','dc_meta',array("`meta_type` = 'tag'","`post_id` = ".$id_post));
	
	while ($tag = sql_fetch($tags_post)){
		#si on a déja créé ce mot
		$titre = $tag['meta_id'];
		if ($id_mot = sql_getfetsel('id_mot','spip_mots',array('`titre`=\''.$titre.'\'','id_groupe='.$id_groupe))){
			sql_insertq('spip_mots_articles',array('id_article'=>$id_article,'id_mot'=>$id_mot));
			spip_log("Lier le mot $id_mot à l'article $id_article","dot2");	
		}
		else{
			$resultat = $crud('create','mots',null,array('id_groupe'=>$id_groupe,'titre'=>$titre));
			$id_mot	  = $resultat['result']['id'];
			spip_log("Créations du mot $id_mot ($titre)","dot2");
			sql_insertq('spip_mots_articles',array('id_article'=>$id_article,'id_mot'=>$id_mot));
			spip_log("Lier le mot $id_mot à l'article $id_article","dot2");
		}
	}
	
	
	return $id_groupe;
}


?>