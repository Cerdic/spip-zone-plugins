<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_a2a_changer_typeliaison_charger_dist($id_article,$id_article_lie,$type_liaison){
	return array('type_liaison'=>$type_liaison,'id_article'=>$id_article,'id_article_lie'=>$id_article_lie);
	
	
}	
function formulaires_a2a_changer_typeliaison_verifier_dist($id_article,$id_article_lie,$type_liaison){
	$nv_type_liaison	=	_request('type_liaison');
	$types_liaions		= 	array_keys(lister_types_liaisons());
	
	if ($nv_type_liaison!=''){
		if (!in_array($nv_type_liaison,$types_liaions)){
			return array('message_erreur'=>_T('a2a:type_inexistant'));
		}
	}
	elseif(lire_config('a2a/type_obligatoire')){
		return array('message_erreur'=>_T('a2a:type_inexistant'));
	}
	if (!autoriser('modifier','article',$id_article)){
		return array('message_erreur'=>_T('a2a:pas_autoriser_changer'));	
	}
	return array();	
}

function formulaires_a2a_changer_typeliaison_traiter_dist($id_article,$id_article_lie,$type_liaison){
	$nv_type_liaison=_request('type_liaison');
	include_spip('base/abstract_sql');

	sql_updateq('spip_articles_lies',array('type_liaison'=>$nv_type_liaison),"id_article=".intval($id_article)." AND id_article_lie=".intval($id_article_lie));
	return array('message_ok'=>$nv_type_liaison,'editable'=>false);
	
}	

?>