<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// chargement des valeurs par defaut des champs du formulaire
function formulaires_recherche_a2a_charger($id_article){
	$recherche = _request('recherche');
	$recherche_titre = _request('recherche_titre');
	$type_liaison = _request('type_liaison');
	$id_article_orig = $id_article;

	return 
		array(
			'recherche' => $recherche,
			'recherche_titre' => $recherche_titre,
			'id_article_orig' => $id_article_orig,
			'type_liaison' => $type_liaison,
		);
}

function formulaires_recherche_a2a_verifier($id_article){
	$nv_type_liaison=_request('type_liaison');
	$types_liaions	= 	array_keys(lire_config('a2a/types_liaisons'));
	if ($nv_type_liaison){
		if (!in_array($nv_type_liaison,$types_liaions)){
			return array('message_erreur'=>_T('a2a:type_inexistant'));
		}
	}
	elseif(lire_config('a2a/type_obligatoire')){
		return array('message_erreur'=>_T('a2a:type_inexistant'));
	}
}

function formulaires_recherche_a2a_traiter($id_article){
	return true; // permettre d'editer encore le formulaire
}

?>
