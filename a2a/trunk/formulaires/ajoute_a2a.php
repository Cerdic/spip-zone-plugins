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
	include_spip('action/a2a');
	$action_a2a = charger_fonction('a2a_lier_article','action');
	if ($lier){
		$action_a2a($id_article_dest,$id_article_orig,_request('type_liaison'),'');	
	}
	if ($lier2){
		$action_a2a($id_article_dest,$id_article_orig,_request('type_liaison'),'both');		
	}
	return array("message_ok"=>"ok");
}

?>