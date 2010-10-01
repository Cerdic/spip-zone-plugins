<?php
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
		include_spip('migrer/dot_medias');
		$documents_lies	= array();
		$medias		= dot_lister_medias($r['post_content_xhtml']);
		$remplacer 	= dot_ajouter_medias($medias,$r['post_id']);
		foreach($remplacer as $depuis=>$vers){
			$r['post_content_xhtml']=str_replace($depuis,'@!@'.$vers.'@¡@',$r['post_content_xhtml']);
			$documents_lies[]=$vers;
		}
		$medias		= dot_lister_medias($r['post_excerpt_xhtml']);
		$remplacer 	= dot_ajouter_medias($medias,$r['post_id']);
		foreach($remplacer as $depuis=>$vers){
			$r['post_excerpt_xhtml']=str_replace($depuis,'@!@'.$vers.'@¡@',$r['post_excerpt_xhtml']);
			$documents_lies[]=$vers;
		}
	
		
	
	// Mise au sale du texte et de l'introduction : nota : il faudrait voir ce que ca donne si formaté en Dotclear
		$texte 		= sale($r['post_content_xhtml']);
		$descriptif	= sale($r['post_excerpt_xhtml']);
	//On n'échappe pas les @
		$texte		= str_replace('@!@','<ig',$texte);
		$texte		= str_replace('@¡@','>',$texte);
		$descriptif		= str_replace('@!@','<ig',$descriptif);
		$descriptif		= str_replace('@¡@','>',$descriptif);
	// Forums ouvert ?
		$r['post_open_comment'] == 1 ? $accepter_forum = 'pos' : $accepter_forum = 'non';
		
	// Statut
		$r['post_status']	== 1 ?	$statut =	'publie' : $r['post_statut'] == 0 ? $statut = 'refuse' : $statut = 'prop' ;

		
		
	
	// Création de l'article
		$crud = charger_fonction('crud','action');
		$resultat	= $crud('create','articles','',array(
			'titre'			=> $r['post_title'],
			'id_rubrique'	=> $id_rubrique,
			'descriptif'			=> $descriptif,
			'texte'			=> $texte,
			'ps'			=> 'DC:'.$r['post_id'],
			'date'			=> $r['post_dt'],
			'statut'		=> $statut,
			'lang'			=> $r['post_lang'],
			'accepter_forum'=> $accepter_forum
		
			));
		$id_article	= $resultat['result']['id'];
		$titre		= $r['post_title'];
		spip_log("Création de l'article $id_article ($titre)",'dot2_migration_article');
	//Remplacement
		sql_updateq('spip_articles',array(
			'texte'		=> str_replace('<ig','<img',$texte),
			'descriptif'=> str_replace('<ig','<img',$descriptif)
			),
			'`id_article`='.$id_article);
	// Lies les documents
		
		foreach($documents_lies as $id_doc){
				
				sql_insertq('spip_documents_liens',array(
					'id_objet'=>$id_article,
					'id_document'=>$id_doc,
					'objet'	=>'article',
					'vu'	=>'oui'
				));	

		}
	// Attribution de l'auteur
		sql_insertq('spip_auteurs_articles',array('id_article'=>$id_article,'id_auteur'=>$id_auteur));
	// dans le crud on ne peut dire quel auteur on veut. Du coup il lie automatique un auteur pour l'article -> on le supprime
		sql_delete('spip_auteurs_articles','`id_article`='.$id_article." AND `id_auteur`!=".$id_auteur);
	// Migration des mots
		$id_groupe = dot2_migrer_mots_article($r['post_id'],$id_article,$id_groupe);
	// Migration des commentaires
		dot2_migrer_commentaires($r['post_id'],$id_article);
	
	
		$documents_lies = array();
	

	}
	

	
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
			spip_log("Lier le mot $id_mot à l'article $id_article","dot2_migration_mot");	
		}
		else{
			$resultat = $crud('create','mots',null,array('id_groupe'=>$id_groupe,'titre'=>$titre));
			$id_mot	  = $resultat['result']['id'];
			spip_log("Créations du mot $id_mot ($titre)","dot2_migration_mot");
			sql_insertq('spip_mots_articles',array('id_article'=>$id_article,'id_mot'=>$id_mot));
			spip_log("Lier le mot $id_mot à l'article $id_article","dot2_migration_mot");
		}
	}
	
	
	return $id_groupe;
}
function remplacer_liens_internes_articles(){
	$req = sql_select('id_article,texte,descriptif,chapo','spip_articles');
;
	while ($art = sql_fetch($req)){
		spip_log($id_article,'liens');
		$texte = remplacer_liens_internes($art['texte']);
		$chapo = remplacer_liens_internes($art['chapo']);	
		$descriptif = remplacer_liens_internes($art['descriptif']);
		sql_updateq('spip_articles',array('texte'=>$texte,'descriptif'=>$descriptif,'chapo'=>$chapo),'`id_article`='.$art['id_article']);	
	}
}

function remplacer_liens_internes($texte){

	
	preg_match_all('#->/index.php\?post/([(\S )]*)]#',$texte,$match);

	foreach ($match[1] as $lien){

		$id_post = sql_getfetsel('post_id','dc_post','`post_url`='.sql_quote(urldecode($lien)));
		$id_article = sql_getfetsel('id_article','spip_articles','`ps`='.sql_quote('DC:'.$id_post));
		$texte = str_replace('/index.php?post/'.$lien,'art'.$id_article,$texte);	
	}
	return $texte;
}



?>