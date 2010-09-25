<?php
// Attention l'ordre est important : migrer 1) les rubriques / categories 2) les articles 3)  les sites puis appliquer "purge articles" et "purge rubrique"


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
		spip_log("Importation du forum $id_message (ex $comment_id) pour l'article $id_article (ex $post_id)",'dot2');
		
	}
	
}

function dot2_migrer_articles($blog_id,$rubrique_defaut='',$id_groupe=''){
	$ressources = sql_select('post_id,user_id,cat_id,post_dt,post_format,post_lang,post_title,post_excerpt_xhtml,post_content_xhtml,post_open_comment,post_status','dc_post','`blog_id`='.sql_quote($blog_id));

		
	while($r = sql_fetch($ressources)){
	// Déterminons la rubrique
		if($r['cat_id']==null){
			$id_rubrique	= $rubrique_defaut;
		}
		else{
			$id_rubrique	= sql_getfetsel('id_rubrique','spip_rubriques','`descriptif`='.sql_quote('DC:'.$id_rubrique));
		}
	
	
	// Créons l'auteur ou bien prenons l'auteur déja crée
		$id_auteur				= sql_getfetsel('id_auteur','spip_auteurs','`login`='.sql_quote($r['user_id']));
		if ($id_auteur == null){
			$id_auteur = dot2_migrer_utilisateur($blog_id,$r['user_id']);
			}
	//	Migration des documents
		include_spip('inc/dot_medias');
		$documents_lies	= array();
		$medias		= dot_lister_medias($r['post_content_xhtml']);
		$remplacer 	= dot_ajouter_medias($medias);
		foreach($remplacer as $depuis=>$vers){
			$r['post_content_xhtml']=str_replace($depuis,'@!@'.$vers.'@¡@',$r['post_content_xhtml']);
			$documents_lies[]=$vers;
		}
		$medias		= dot_lister_medias($r['post_excerpt_xhtml']);
		$remplacer 	= dot_ajouter_medias($medias);
		foreach($remplacer as $depuis=>$vers){
			$r['post_excerpt_xhtml']=str_replace($depuis,'@!@'.$vers.'@¡@',$r['post_excerpt_xhtml']);
			$documents_lies[]=$vers;
		}
		
	
	// Mise au sale du texte et de l'introduction : nota : il faudrait voir ce que ca donne si formaté en Dotclear
		$texte 		= sale($r['post_content_xhtml']);
		$descriptif	= sale($r['post_excerpt_xhtml']);
	//On n'échappe pas les @
		$texte		= str_replace('@!@','<img',$texte);
		$texte		= str_replace('@¡@','>',$texte);
		$descriptif		= str_replace('@!@','<img',$descriptif);
		$descriptif		= str_replace('@¡@','>',$descriptif);
	// Forums ouvert ?
		$r['post_open_comment'] == 1 ? $accepter_forum = 'pos' : $accepter_forum = 'non';
		
	// Statut
		$r['post_status']	== 1 ?	$statut =	'publie' : $statut = 'redac';

		
		
	
	// Création de l'article
		$crud = charger_fonction('crud','action');
		$resultat	= $crud('create','articles','',array(
			'titre'			=> $r['post_title'],
			'id_rubrique'	=> $id_rubrique,
			'descriptif'	=> $descriptif,
			'texte'			=> $texte,
			'date'			=> $r['post_dt'],
			'statut'		=> $statut,
			'lang'			=> $r['post_lang'],
			'accepter_forum'=> $accepter_forum
		
			));
		$id_article	= $resultat['result']['id'];
		$titre		= $r['post_title'];
		spip_log("Création de l'article $id_article ($titre)",'dot2');
	
	// Attribution de l'auteur
		sql_insertq('spip_auteurs_articles',array('id_article'=>$id_article,'id_auteur'=>$id_auteur));
		// dans le crud on ne peut dire quel auteur on veut. Du coup il lie automatique un auteur pour l'article -> on le supprime
		sql_delete('spip_auteurs_articles','`id_article`='.$id_article." AND `id_auteur`!=".$id_auteur);
	// Migration des mots
		$id_groupe = dot2_migrer_mots_article($r['post_id'],$id_article,$id_groupe);
	// Migration des commentaires
		dot2_migrer_commentaires($r['post_id'],$id_article);
	// Lies les documents
		foreach($documents_lies as $id_doc){
			sql_insertq('spip_documents_liens',array(
				'id_objet'=>$id_article,
				'id_document'=>$id_doc,
				'objet'	=>'article'
			));	
		}
	}
	

	
}

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
		$id_groupe=sql_getfetsel('id_groupe','spip_groupes_mots','`titre`='.sql_quote(_L('tags de dotclear')));
		
		if ($id_groupe==null){
			
			$id_groupe = sql_insertq('spip_groupes_mots',array('titre'=>_L('tags de dotclear'),'unseul'=>'non','obligatoire'=>'non','tables_liees'=>'articles','minirezo'=>'oui','comite'=>'oui','forum'=>'non'));
			spip_log("Création du groupe $id_groupe",'dot2');
		}
	}
	
	#les tags associés à l'articles
	$tags_post = sql_select('meta_id','dc_meta',array("`meta_type` = 'tag'","`post_id` = ".$id_post));
	
	while ($tag = sql_fetch($tags_post)){
		#si on a déja créé ce mot
		$titre = $tag['meta_id'];
		if ($id_mot = sql_getfetsel('id_mot','spip_mots','`titre`='.sql_quote($titre).'AND `id_groupe`='.$id_groupe)){
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