<?php

include_spip('inc/dot_category');



function dot2_migrer_rubriques($blog_id){
	include_spip('inc/dot_category');
	$hierachie = dot_category_spiper_arbre(dot_category_arbre($blog_id));
	dot2_migrer_rubriques_enfants($hierachie,0);
}

function dot2_migrer_rubriques_enfants($arbre,$id_rubrique_mere){
	foreach($arbre as $cat_id =>$contenu){
		$id_rubrique = dot2_migrer_rubrique($cat_id,$id_rubrique_mere);
		if ($contenu[$cat_id]!='')
			dot2_migrer_rubriques_enfants($contenu[$cat_id],$id_rubrique);
	}	
	
	return $id_rubrique;
}

function dot2_migrer_rubrique($cat_id,$rubrique_mere){
	$crud = charger_fonction('crud','action');
	
	$contenu = sql_fetsel('cat_title,cat_lft,cat_desc','dc_category','`cat_id`='.$cat_id);
	$titre		= $contenu['cat_lft'].'0. '.$contenu['cat_title'];
	$resultat = $crud('create','rubrique','nulls',array('descriptif'=>'DC:'.$cat_id,'id_parent'=>$rubrique_mere,'titre'=>$titre,'texte'=>sale($contenu['cat_desc'])));
	$id_rubrique = $resultat['result']['id'];
	
	spip_log("Création de la rubrique $id_rubrique ($titre) rubrique parente : $rubrique_mere. Catégory originelle : $cat_id","dot2");
	return $id_rubrique;
}

function dot2_migrer_utilisateur($blog_id,$user_id){
	$user = sql_fetsel('user_super,user_url,user_name,user_firstname,user_displayname,user_email','dc_user',"`user_id`=".sql_quote($user_id));
	

	// ! \\ SPIP ne fonctionne pas comme Doctclear au niveau des noms donc on va essayer de convertir
	if ($user['user_displayname']){
		$nom	= $user['user_displayname'];
	}
	else if ($user['user_firstname'] or $user['user_name']){
		$nom	= $user['user_firstname'].' '.$user['user_name'];
	}
	else{
		$nom	= $user_id;
	}
	
	// Détermination du statut
	$permissions = sql_fetsel('permissions','dc_permissions',"`user_id`=".sql_quote($user_id)."and `blog_id`=".sql_quote($blog_id));
		if($user['user_super']==1){
			$statut = '0minirezo';
			$webmestre='oui';
		}
		else if (match($permissions['permissions'],'|admin')){
			$statut = '0minirezo';
			$webmestre='non';
		}
		else{
			$statut	= '1comite';
			$webmestre='non';
	
		}

	//Création d'un mot de passe
	include_spip('inc/acces');
	$pass = creer_pass_aleatoire(8, $nom);
	
	//On insère en BDD
	$crud = charger_fonction('crud','action');
	$resultat = $crud('create','auteurs',null,array('login'=>$user_id,'nom'=>$nom,'statut'=>$statut,'url_site'=>$user['user_url'],'email'=>$user['user_email'],'pass'=>$pass,'webmestre'=>$webmestre));
	$id_auteur = $resultat['result']['id'];
	spip_log("Création de l'auteur $id_auteur ($user_id)",'dot2');
	
	// On envoie le mail
	include_spip('formulaires/inscription');
	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	if (function_exists('envoyer_inscription'))
		$f = 'envoyer_inscription';
	else 	$f = 'envoyer_inscription_dist';
	list($sujet,$msg,$from,$head) = $f(array('login'=>$user_id,'pass'=>$pass),$nom,'',$id_auteur);
	if (!$envoyer_mail ($user['user_email'], $sujet, $msg, $from, $head)){
		spip_log("Impossible d'envoyer le mail pour l'auteur $id_auteur","dot2");	
	}
	return $id_auteur;
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