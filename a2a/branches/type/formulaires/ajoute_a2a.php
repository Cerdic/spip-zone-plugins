<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// chargement des valeurs par defaut des champs du formulaire
function formulaires_ajoute_a2a_charger($id_article_orig,$id_article_dest){

	return 
		array(
			'id_article_orig' => $id_article_orig,
			'id_article_dest'=>$id_article_dest
		);
}

function formulaires_ajoute_a2a_verifier($id_article_orig,$id_article_dest){
	$nv_type_liaison=_request('type_liaison');
	$types_liaions	= 	array_keys(lister_types_liaisons());
	if ($nv_type_liaison){
		if (!in_array($nv_type_liaison,$types_liaions)){
			return array('message_erreur'=>_T('a2a:type_inexistant'));
		}
	}
	elseif(lire_config('a2a/type_obligatoire')){
		return array('message_erreur'=>_T('a2a:type_inexistant'));
	}
}

function formulaires_ajoute_a2a_traiter($id_article_orig,$id_article_dest){
	$lier  = _request('lier');
	$lier2 = _request('lier2');	
	if ($lier){
		a2a_lier_article($id_article_dest,$id_article_orig,'',_request('type_liaison'));	
	}
	if ($lier2){
		a2a_lier_article($id_article_dest,$id_article_orig,'both',_request('type_liaison'));		
	}
}

function a2a_lier_article($id_article_cible, $id_article_source, $type=null, $type_liaison=''){
	include_spip('inc/config');
	//on verifie que cet article n'est pas deja lie
	if (
		
		!((!lire_config('a2a/types_differents')
		and 
		sql_countsel('spip_articles_lies', array(
		'id_article=' . sql_quote($id_article_source),
		'id_article_lie=' . sql_quote($id_article_cible)))
		))
		
		or 
		
		!((lire_config('a2a/types_differents')
		and 
		sql_countsel('spip_articles_lies', array(
		'id_article=' . sql_quote($id_article_source),
		'id_article_lie=' . sql_quote($id_article_cible),'type_liaison='.$type_liaison))
		))
		){
			//on recupere le rang le plus haut pour definir celui de l'article a lier
			$rang = sql_getfetsel('MAX(rang)', 'spip_articles_lies', 'id_article='. sql_quote($id_article_source));
			//on ajoute le lien vers l'article
			if ($type_liaison)
			sql_insertq('spip_articles_lies', array(
				'id_article' => $id_article_source,
				'id_article_lie' => $id_article_cible,
				'rang' => ++$rang,
				'type_liaison' => $type_liaison,
				));
			else
				sql_insertq('spip_articles_lies', array(
				'id_article' => $id_article_source,
				'id_article_lie' => $id_article_cible,
				'rang' => ++$rang
				));
				
	}
	if(($type == 'both') && !sql_countsel('spip_articles_lies', array(
		'id_article=' . sql_quote($id_article_cible),
		'id_article_lie=' . sql_quote($id_article_source)))){
			//on recupere le rang le plus haut pour definir celui de l'article a lier
			$rang = sql_getfetsel('MAX(rang)', 'spip_articles_lies', 'id_article='. sql_quote($id_article_cible));
			//on ajoute le lien vers l'article
			sql_insertq('spip_articles_lies', array(
				'id_article' => $id_article_cible,
				'id_article_lie' => $id_article_source,
				'rang' => ++$rang,
				'type_liaison' => $type_liaison,
				));
	}
	return true;
}


?>