<?php

include_spip('inc/dot_category');
function dot2_migrer_utilisateur($user_id){
	$users = sql_select('user_super,user_url,user_name,user_firstname,user_displayname','dc_user',"`user_id`=".sql_quote($user_id));
	
	while($user = sql_fetch($users)){
		// ! \\ SPIP ne fonctionne pas comme Doctclear au niveau des noms donc on va essayer de convertir
		if ($user['user_displayname']){
			$nom	= $user['user_displayname'];
		}
		else{
			$nom	= $user['user_firstname'].' '.$user['user_name'];
		}
		print $nom;
	}
}

function dot2_migrer_mots_article($id_post,$id_article,$id_groupe=''){
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
		if ($id_mot = sql_getfetsel('id_mot','spip_mots',array('`titre`='.sql_quote($titre),'id_groupe='.$id_groupe))){
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

function dot2_migrer_sites($blog_id,$id_rubrique){
	$crud = charger_fonction('crud','action');
	$dc_link = sql_select('link_title,link_href,link_position','dc_link',array("`blog_id`=".sql_quote($blog_id)));
	
	while($site = sql_fetch($dc_link)){
		$nom_site = $site['link_position']."0. ".$site['link_title'];
		$url_site = $site['link_href'];
		
		#aller on ajoute en BDD !
		$resultat = $crud('create','syndic',null,array('id_rubrique'=>$id_rubrique,'nom_site'=>$nom_site,'url_site'=>$url_site));
		$id_site  = $resultat['result']['id'];
		spip_log("Ajout du site $id_site ($nom_site - $url_site)",'dot2');
	}
	
	return $id_rubrique;
}

?>